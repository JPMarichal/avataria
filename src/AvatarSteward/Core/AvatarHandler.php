<?php
/**
 * Avatar Handler class.
 *
 * Handles avatar override logic, replacing WordPress default avatars
 * with locally uploaded avatars.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Core;

use AvatarSteward\Domain\LowBandwidth\BandwidthOptimizer;

/**
 * AvatarHandler class for managing avatar display.
 */
class AvatarHandler {

	/**
	 * User meta key for storing local avatar ID.
	 *
	 * @var string
	 */
	private const META_KEY = 'avatar_steward_avatar';

	/**
	 * Bandwidth optimizer instance.
	 *
	 * @var BandwidthOptimizer|null
	 */
	private ?BandwidthOptimizer $optimizer = null;

	/**
	 * Set the bandwidth optimizer.
	 *
	 * @param BandwidthOptimizer $optimizer Bandwidth optimizer instance.
	 * @return void
	 */
	public function set_optimizer( BandwidthOptimizer $optimizer ): void {
		$this->optimizer = $optimizer;
	}

	/**
	 * Initialize the avatar handler.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( function_exists( 'add_filter' ) ) {
			add_filter( 'pre_get_avatar_data', array( $this, 'filter_avatar_data' ), 10, 2 );
			add_filter( 'get_avatar_url', array( $this, 'filter_avatar_url' ), 10, 3 );
		}
	}

	/**
	 * Filter avatar data to use local avatars.
	 *
	 * This filter is called by WordPress before get_avatar() renders.
	 * It allows us to substitute the avatar URL with a local one.
	 *
	 * @param array $args        Arguments passed to get_avatar_data(), including 'url'.
	 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
	 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @return array Modified arguments.
	 */
	public function filter_avatar_data( array $args, $id_or_email ): array {
		$user_id = $this->get_user_id_from_identifier( $id_or_email );

		if ( ! $user_id ) {
			return $args;
		}

		$local_avatar_url = $this->get_local_avatar_url( $user_id, $args['size'] ?? 96 );

		if ( $local_avatar_url ) {
			$args['url']          = $local_avatar_url;
			$args['found_avatar'] = true;
		}

		return $args;
	}

	/**
	 * Filter avatar URL to use local avatars.
	 *
	 * This filter is called by get_avatar_url() and provides additional
	 * compatibility for plugins that use this function directly.
	 *
	 * @param string $url         The URL of the avatar.
	 * @param mixed  $id_or_email The Gravatar to retrieve.
	 * @param array  $args        Arguments passed to get_avatar_url().
	 * @return string Modified avatar URL.
	 */
	public function filter_avatar_url( string $url, $id_or_email, array $args ): string {
		$user_id = $this->get_user_id_from_identifier( $id_or_email );

		if ( ! $user_id ) {
			return $url;
		}

		$size             = $args['size'] ?? 96;
		$local_avatar_url = $this->get_local_avatar_url( $user_id, $size );

		return $local_avatar_url ? $local_avatar_url : $url;
	}

	/**
	 * Get user ID from various identifier types.
	 *
	 * Handles different types of identifiers that WordPress can pass to
	 * avatar functions: user ID, email, WP_User, WP_Post, WP_Comment objects.
	 *
	 * @param mixed $id_or_email The identifier to extract user ID from.
	 * @return int|null User ID or null if not found.
	 */
	private function get_user_id_from_identifier( $id_or_email ): ?int {
		$user_id = null;

		if ( is_numeric( $id_or_email ) ) {
			$user_id = (int) $id_or_email;
		} elseif ( is_string( $id_or_email ) && function_exists( 'get_user_by' ) ) {
			$user = get_user_by( 'email', $id_or_email );
			if ( $user ) {
				$user_id = $user->ID;
			}
		} elseif ( is_object( $id_or_email ) ) {
			if ( isset( $id_or_email->user_id ) && $id_or_email->user_id > 0 ) {
				// WP_Comment object.
				$user_id = (int) $id_or_email->user_id;
			} elseif ( isset( $id_or_email->ID ) ) {
				// WP_User or WP_Post object.
				if ( isset( $id_or_email->user_login ) ) {
					// WP_User object.
					$user_id = (int) $id_or_email->ID;
				} elseif ( isset( $id_or_email->post_author ) ) {
					// WP_Post object.
					$user_id = (int) $id_or_email->post_author;
				}
			}
		}

		return $user_id && $user_id > 0 ? $user_id : null;
	}

	/**
	 * Get local avatar URL for a user.
	 *
	 * Retrieves the URL of the locally uploaded avatar for a user,
	 * or returns null if no local avatar exists.
	 * If low-bandwidth mode is enabled and the avatar exceeds threshold,
	 * returns an SVG data URI instead.
	 *
	 * @param int $user_id User ID.
	 * @param int $size    Requested avatar size in pixels.
	 * @return string|null Avatar URL or null if not found.
	 */
	private function get_local_avatar_url( int $user_id, int $size ): ?string {
		if ( ! function_exists( 'get_user_meta' ) || ! function_exists( 'wp_get_attachment_image_url' ) ) {
			return null;
		}

		$avatar_id = get_user_meta( $user_id, self::META_KEY, true );

		if ( ! $avatar_id ) {
			return null;
		}

		// Check if low-bandwidth mode should be used.
		if ( $this->optimizer && $this->optimizer->is_enabled() && $this->optimizer->exceeds_threshold( (int) $avatar_id ) ) {
			return $this->optimizer->generate_svg_avatar( $user_id, $size );
		}

		// Get the appropriate image size.
		$image_size = $this->get_image_size_for_dimension( $size );

		// Get the avatar URL.
		$avatar_url = wp_get_attachment_image_url( (int) $avatar_id, $image_size );

		return $avatar_url ? $avatar_url : null;
	}

	/**
	 * Get appropriate WordPress image size for given dimension.
	 *
	 * Maps requested pixel size to WordPress image size names.
	 *
	 * @param int $size Requested size in pixels.
	 * @return string WordPress image size name.
	 */
	private function get_image_size_for_dimension( int $size ): string {
		if ( $size <= 96 ) {
			return 'thumbnail'; // Usually 150x150.
		} elseif ( $size <= 300 ) {
			return 'medium'; // Usually 300x300.
		}

		return 'medium_large'; // Usually 768x768.
	}

	/**
	 * Set local avatar for a user.
	 *
	 * Stores the attachment ID of the local avatar in user meta.
	 *
	 * @param int $user_id       User ID.
	 * @param int $attachment_id Attachment ID of the avatar image.
	 * @return bool True on success, false on failure.
	 */
	public function set_local_avatar( int $user_id, int $attachment_id ): bool {
		if ( ! function_exists( 'update_user_meta' ) ) {
			return false;
		}

		return (bool) update_user_meta( $user_id, self::META_KEY, $attachment_id );
	}

	/**
	 * Delete local avatar for a user.
	 *
	 * Removes the local avatar assignment from user meta.
	 *
	 * @param int $user_id User ID.
	 * @return bool True on success, false on failure.
	 */
	public function delete_local_avatar( int $user_id ): bool {
		if ( ! function_exists( 'delete_user_meta' ) ) {
			return false;
		}

		return delete_user_meta( $user_id, self::META_KEY );
	}

	/**
	 * Check if user has a local avatar.
	 *
	 * @param int $user_id User ID.
	 * @return bool True if user has local avatar, false otherwise.
	 */
	public function has_local_avatar( int $user_id ): bool {
		if ( ! function_exists( 'get_user_meta' ) ) {
			return false;
		}

		$avatar_id = get_user_meta( $user_id, self::META_KEY, true );

		return ! empty( $avatar_id );
	}
}
