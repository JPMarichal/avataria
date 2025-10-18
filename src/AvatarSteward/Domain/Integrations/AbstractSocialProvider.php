<?php
/**
 * Abstract Social Provider base class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Integrations;

/**
 * Abstract base class for social media providers.
 *
 * Provides common functionality for OAuth flows and token management.
 */
abstract class AbstractSocialProvider implements SocialProviderInterface {

	/**
	 * Provider name.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Provider display label.
	 *
	 * @var string
	 */
	protected string $label;

	/**
	 * Meta key prefix for storing tokens.
	 *
	 * @var string
	 */
	protected string $meta_prefix = 'avatarsteward_social_';

	/**
	 * Get the provider name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the provider display label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Check if user is connected to this provider.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	public function is_connected( int $user_id ): bool {
		$token = $this->get_access_token( $user_id );
		return ! empty( $token );
	}

	/**
	 * Disconnect the social provider from user account.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	public function disconnect( int $user_id ): bool {
		$this->delete_access_token( $user_id );
		$this->delete_user_data( $user_id );

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_social_disconnected', $user_id, $this->name );
		}

		return true;
	}

	/**
	 * Get stored access token for user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return string|null Access token or null if not found.
	 */
	protected function get_access_token( int $user_id ): ?string {
		$meta_key = $this->meta_prefix . $this->name . '_token';
		$token    = get_user_meta( $user_id, $meta_key, true );
		return ! empty( $token ) ? (string) $token : null;
	}

	/**
	 * Store access token for user.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $token Access token.
	 * @return bool
	 */
	protected function store_access_token( int $user_id, string $token ): bool {
		$meta_key = $this->meta_prefix . $this->name . '_token';
		return update_user_meta( $user_id, $meta_key, $token ) !== false;
	}

	/**
	 * Delete stored access token for user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	protected function delete_access_token( int $user_id ): bool {
		$meta_key = $this->meta_prefix . $this->name . '_token';
		return delete_user_meta( $user_id, $meta_key );
	}

	/**
	 * Store provider-specific user data.
	 *
	 * @param int   $user_id WordPress user ID.
	 * @param array $data User data from provider.
	 * @return bool
	 */
	protected function store_user_data( int $user_id, array $data ): bool {
		$meta_key = $this->meta_prefix . $this->name . '_data';
		return update_user_meta( $user_id, $meta_key, $data ) !== false;
	}

	/**
	 * Get stored provider-specific user data.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array|null User data or null if not found.
	 */
	protected function get_user_data( int $user_id ): ?array {
		$meta_key = $this->meta_prefix . $this->name . '_data';
		$data     = get_user_meta( $user_id, $meta_key, true );
		return is_array( $data ) && ! empty( $data ) ? $data : null;
	}

	/**
	 * Delete stored provider-specific user data.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	protected function delete_user_data( int $user_id ): bool {
		$meta_key = $this->meta_prefix . $this->name . '_data';
		return delete_user_meta( $user_id, $meta_key );
	}

	/**
	 * Make HTTP request with error handling.
	 *
	 * @param string $url Request URL.
	 * @param array  $args Request arguments for wp_remote_request.
	 * @return array|null Response data or null on error.
	 */
	protected function make_request( string $url, array $args = array() ): ?array {
		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			// Log error.
			if ( function_exists( 'error_log' ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					sprintf(
						'Avatar Steward: %s API request failed: %s',
						$this->label,
						$response->get_error_message()
					)
				);
			}
			return null;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code >= 300 ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		return is_array( $data ) ? $data : null;
	}

	/**
	 * Download image from URL and save to WordPress media library.
	 *
	 * @param string $image_url Image URL.
	 * @param int    $user_id WordPress user ID.
	 * @return int|null Attachment ID or null on failure.
	 */
	protected function download_and_save_image( string $image_url, int $user_id ): ?int {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$tmp = download_url( $image_url );

		if ( is_wp_error( $tmp ) ) {
			return null;
		}

		$file_array = array(
			'name'     => 'avatar-' . $this->name . '-' . $user_id . '.jpg',
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file_array, 0 );

		if ( is_wp_error( $attachment_id ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			@unlink( $file_array['tmp_name'] );
			return null;
		}

		return $attachment_id;
	}
}
