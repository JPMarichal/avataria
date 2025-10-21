<?php
/**
 * Badge Service class for managing verification badges.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Library;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Handles verification badge management for avatars and users.
 */
class BadgeService {

	/**
	 * Post meta key for avatar badge.
	 *
	 * @var string
	 */
	const META_AVATAR_BADGE = 'avatar_steward_badge';

	/**
	 * User meta key for user badge.
	 *
	 * @var string
	 */
	const META_USER_BADGE = 'avatar_steward_user_badge';

	/**
	 * Badge types.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	const BADGE_TYPES = array(
		'verified'  => array(
			'name'        => 'Verified',
			'description' => 'Verified user or content',
			'icon'        => 'dashicons-yes-alt',
			'color'       => '#0073aa',
		),
		'moderator' => array(
			'name'        => 'Moderator',
			'description' => 'Site moderator',
			'icon'        => 'dashicons-shield',
			'color'       => '#d63638',
		),
		'author'    => array(
			'name'        => 'Author',
			'description' => 'Content author',
			'icon'        => 'dashicons-edit',
			'color'       => '#00a32a',
		),
		'premium'   => array(
			'name'        => 'Premium',
			'description' => 'Premium member',
			'icon'        => 'dashicons-star-filled',
			'color'       => '#f0b849',
		),
	);

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface|null $logger Optional logger instance.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Assign a badge to an avatar.
	 *
	 * @param int    $attachment_id The attachment ID.
	 * @param string $badge_type    Badge type (verified, moderator, author, premium, custom).
	 * @param array  $custom_data   Optional custom badge data.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function assign_badge_to_avatar( int $attachment_id, string $badge_type, array $custom_data = array() ): bool {
		if ( $this->logger ) {
			$this->logger->info(
				'Assigning badge to avatar',
				array(
					'attachment_id' => $attachment_id,
					'badge_type'    => $badge_type,
				)
			);
		}

		// Verify attachment exists.
		if ( ! function_exists( 'get_post' ) || ! get_post( $attachment_id ) ) {
			if ( $this->logger ) {
				$this->logger->error( 'Attachment not found', array( 'attachment_id' => $attachment_id ) );
			}
			return false;
		}

		// Validate badge type.
		if ( ! $this->is_valid_badge_type( $badge_type ) && 'custom' !== $badge_type ) {
			if ( $this->logger ) {
				$this->logger->error( 'Invalid badge type', array( 'badge_type' => $badge_type ) );
			}
			return false;
		}

		$badge_data = array(
			'type'     => sanitize_text_field( $badge_type ),
			'assigned' => current_time( 'mysql' ),
			'assigner' => get_current_user_id(),
		);

		// Add custom data for custom badges.
		if ( 'custom' === $badge_type && ! empty( $custom_data ) ) {
			$badge_data['name']        = isset( $custom_data['name'] ) ? sanitize_text_field( $custom_data['name'] ) : '';
			$badge_data['description'] = isset( $custom_data['description'] ) ? sanitize_text_field( $custom_data['description'] ) : '';
			$badge_data['icon']        = isset( $custom_data['icon'] ) ? sanitize_text_field( $custom_data['icon'] ) : '';
			$badge_data['color']       = isset( $custom_data['color'] ) ? sanitize_hex_color( $custom_data['color'] ) : '';
		}

		update_post_meta( $attachment_id, self::META_AVATAR_BADGE, $badge_data );

		if ( $this->logger ) {
			$this->logger->info( 'Badge assigned successfully', array( 'attachment_id' => $attachment_id ) );
		}

		return true;
	}

	/**
	 * Assign a badge to a user.
	 *
	 * @param int    $user_id     The user ID.
	 * @param string $badge_type  Badge type.
	 * @param array  $custom_data Optional custom badge data.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function assign_badge_to_user( int $user_id, string $badge_type, array $custom_data = array() ): bool {
		if ( $this->logger ) {
			$this->logger->info(
				'Assigning badge to user',
				array(
					'user_id'    => $user_id,
					'badge_type' => $badge_type,
				)
			);
		}

		// Verify user exists.
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			if ( $this->logger ) {
				$this->logger->error( 'User not found', array( 'user_id' => $user_id ) );
			}
			return false;
		}

		// Validate badge type.
		if ( ! $this->is_valid_badge_type( $badge_type ) && 'custom' !== $badge_type ) {
			if ( $this->logger ) {
				$this->logger->error( 'Invalid badge type', array( 'badge_type' => $badge_type ) );
			}
			return false;
		}

		$badge_data = array(
			'type'     => sanitize_text_field( $badge_type ),
			'assigned' => current_time( 'mysql' ),
			'assigner' => get_current_user_id(),
		);

		// Add custom data for custom badges.
		if ( 'custom' === $badge_type && ! empty( $custom_data ) ) {
			$badge_data['name']        = isset( $custom_data['name'] ) ? sanitize_text_field( $custom_data['name'] ) : '';
			$badge_data['description'] = isset( $custom_data['description'] ) ? sanitize_text_field( $custom_data['description'] ) : '';
			$badge_data['icon']        = isset( $custom_data['icon'] ) ? sanitize_text_field( $custom_data['icon'] ) : '';
			$badge_data['color']       = isset( $custom_data['color'] ) ? sanitize_hex_color( $custom_data['color'] ) : '';
		}

		update_user_meta( $user_id, self::META_USER_BADGE, $badge_data );

		if ( $this->logger ) {
			$this->logger->info( 'Badge assigned successfully', array( 'user_id' => $user_id ) );
		}

		return true;
	}

	/**
	 * Remove badge from avatar.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove_badge_from_avatar( int $attachment_id ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Removing badge from avatar', array( 'attachment_id' => $attachment_id ) );
		}

		delete_post_meta( $attachment_id, self::META_AVATAR_BADGE );

		return true;
	}

	/**
	 * Remove badge from user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove_badge_from_user( int $user_id ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Removing badge from user', array( 'user_id' => $user_id ) );
		}

		delete_user_meta( $user_id, self::META_USER_BADGE );

		return true;
	}

	/**
	 * Get badge for avatar.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|null Badge data or null if not found.
	 */
	public function get_avatar_badge( int $attachment_id ): ?array {
		$badge_data = get_post_meta( $attachment_id, self::META_AVATAR_BADGE, true );

		if ( empty( $badge_data ) || ! is_array( $badge_data ) ) {
			return null;
		}

		// Merge with badge type defaults if available.
		if ( isset( $badge_data['type'] ) && isset( self::BADGE_TYPES[ $badge_data['type'] ] ) ) {
			$badge_data = array_merge( self::BADGE_TYPES[ $badge_data['type'] ], $badge_data );
		}

		return $badge_data;
	}

	/**
	 * Get badge for user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return array|null Badge data or null if not found.
	 */
	public function get_user_badge( int $user_id ): ?array {
		$badge_data = get_user_meta( $user_id, self::META_USER_BADGE, true );

		if ( empty( $badge_data ) || ! is_array( $badge_data ) ) {
			return null;
		}

		// Merge with badge type defaults if available.
		if ( isset( $badge_data['type'] ) && isset( self::BADGE_TYPES[ $badge_data['type'] ] ) ) {
			$badge_data = array_merge( self::BADGE_TYPES[ $badge_data['type'] ], $badge_data );
		}

		return $badge_data;
	}

	/**
	 * Get all available badge types.
	 *
	 * @return array<string, array<string, mixed>> Badge types.
	 */
	public function get_badge_types(): array {
		return self::BADGE_TYPES;
	}

	/**
	 * Check if badge type is valid.
	 *
	 * @param string $badge_type Badge type.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid_badge_type( string $badge_type ): bool {
		return isset( self::BADGE_TYPES[ $badge_type ] );
	}

	/**
	 * Auto-assign badges based on user role.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function auto_assign_badge_by_role( int $user_id ): bool {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}

		$role = $user->roles[0] ?? '';

		// Map roles to badge types.
		$role_badge_map = array(
			'administrator' => 'moderator',
			'editor'        => 'moderator',
			'author'        => 'author',
		);

		if ( isset( $role_badge_map[ $role ] ) ) {
			return $this->assign_badge_to_user( $user_id, $role_badge_map[ $role ] );
		}

		return false;
	}

	/**
	 * Render badge HTML for display.
	 *
	 * @param array $badge_data Badge data array.
	 * @param int   $size       Badge size in pixels.
	 *
	 * @return string Badge HTML.
	 */
	public function render_badge( array $badge_data, int $size = 20 ): string {
		if ( empty( $badge_data ) ) {
			return '';
		}

		$icon  = isset( $badge_data['icon'] ) ? esc_attr( $badge_data['icon'] ) : 'dashicons-yes';
		$color = isset( $badge_data['color'] ) ? esc_attr( $badge_data['color'] ) : '#0073aa';
		$name  = isset( $badge_data['name'] ) ? esc_attr( $badge_data['name'] ) : 'Badge';

		$style = sprintf(
			'color: %s; font-size: %dpx; width: %dpx; height: %dpx;',
			$color,
			$size,
			$size,
			$size
		);

		return sprintf(
			'<span class="avatar-steward-badge dashicons %s" style="%s" title="%s"></span>',
			$icon,
			$style,
			$name
		);
	}
}
