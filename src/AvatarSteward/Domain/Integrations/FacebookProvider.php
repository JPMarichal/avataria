<?php
/**
 * Facebook Social Provider.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Integrations;

/**
 * Facebook OAuth integration for avatar import.
 *
 * Implements OAuth 2.0 for Facebook Graph API.
 */
class FacebookProvider extends AbstractSocialProvider {

	/**
	 * Facebook Graph API base URL.
	 *
	 * @var string
	 */
	private const API_BASE = 'https://graph.facebook.com/v18.0/';

	/**
	 * Facebook OAuth authorization URL.
	 *
	 * @var string
	 */
	private const AUTH_URL = 'https://www.facebook.com/v18.0/dialog/oauth';

	/**
	 * Facebook OAuth token URL.
	 *
	 * @var string
	 */
	private const TOKEN_URL = 'https://graph.facebook.com/v18.0/oauth/access_token';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name  = 'facebook';
		$this->label = 'Facebook';
	}

	/**
	 * Get the OAuth authorization URL.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $redirect_url Callback URL after authorization.
	 * @return string|null OAuth authorization URL or null if not configured.
	 */
	public function get_authorization_url( int $user_id, string $redirect_url ): ?string {
		if ( ! $this->is_configured() ) {
			return null;
		}

		$app_id = get_option( 'avatarsteward_facebook_app_id', '' );
		$state  = wp_create_nonce( 'facebook_oauth_' . $user_id );

		// Store state for validation.
		set_transient( 'avatarsteward_facebook_state_' . $user_id, $state, HOUR_IN_SECONDS );

		$params = array(
			'client_id'     => $app_id,
			'redirect_uri'  => $redirect_url,
			'scope'         => 'public_profile',
			'state'         => $state,
			'response_type' => 'code',
		);

		return self::AUTH_URL . '?' . http_build_query( $params );
	}

	/**
	 * Handle OAuth callback and exchange code for access token.
	 *
	 * @param string $code Authorization code from OAuth callback.
	 * @param int    $user_id WordPress user ID.
	 * @return bool True if connection was successful.
	 */
	public function handle_callback( string $code, int $user_id ): bool {
		if ( ! $this->is_configured() ) {
			return false;
		}

		$app_id       = get_option( 'avatarsteward_facebook_app_id', '' );
		$app_secret   = get_option( 'avatarsteward_facebook_app_secret', '' );
		$redirect_url = $this->get_redirect_url();

		$params = array(
			'client_id'     => $app_id,
			'client_secret' => $app_secret,
			'redirect_uri'  => $redirect_url,
			'code'          => $code,
		);

		$response = $this->make_request(
			self::TOKEN_URL . '?' . http_build_query( $params )
		);

		if ( ! $response || empty( $response['access_token'] ) ) {
			return false;
		}

		// Store access token.
		$this->store_access_token( $user_id, $response['access_token'] );

		// Fetch and store user data.
		$user_data = $this->fetch_user_data( $response['access_token'] );
		if ( $user_data ) {
			$this->store_user_data( $user_id, $user_data );
		}

		// Clean up temporary data.
		delete_transient( 'avatarsteward_facebook_state_' . $user_id );

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_social_connected', $user_id, $this->name );
		}

		return true;
	}

	/**
	 * Import avatar from Facebook.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool True if import was successful.
	 */
	public function import_avatar( int $user_id ): bool {
		$token = $this->get_access_token( $user_id );
		if ( ! $token ) {
			return false;
		}

		$user_data = $this->get_user_data( $user_id );
		if ( ! $user_data ) {
			$user_data = $this->fetch_user_data( $token );
			if ( ! $user_data ) {
				return false;
			}
			$this->store_user_data( $user_id, $user_data );
		}

		if ( empty( $user_data['id'] ) ) {
			return false;
		}

		// Get profile picture URL.
		$picture_url = $this->get_profile_picture_url( $user_data['id'], $token );
		if ( ! $picture_url ) {
			return false;
		}

		$attachment_id = $this->download_and_save_image( $picture_url, $user_id );
		if ( ! $attachment_id ) {
			return false;
		}

		// Set as user avatar using existing upload service pattern.
		update_user_meta( $user_id, 'avatarsteward_avatar', $attachment_id );

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_avatar_imported', $user_id, $this->name, $attachment_id );
		}

		return true;
	}

	/**
	 * Validate provider configuration.
	 *
	 * @return bool True if API credentials are properly configured.
	 */
	public function is_configured(): bool {
		$app_id     = get_option( 'avatarsteward_facebook_app_id', '' );
		$app_secret = get_option( 'avatarsteward_facebook_app_secret', '' );

		return ! empty( $app_id ) && ! empty( $app_secret );
	}

	/**
	 * Fetch user data from Facebook API.
	 *
	 * @param string $token Access token.
	 * @return array|null User data or null on failure.
	 */
	private function fetch_user_data( string $token ): ?array {
		$response = $this->make_request(
			self::API_BASE . 'me?fields=id,name&access_token=' . $token
		);

		return $response ?? null;
	}

	/**
	 * Get profile picture URL from Facebook API.
	 *
	 * @param string $user_id Facebook user ID.
	 * @param string $token Access token.
	 * @return string|null Picture URL or null on failure.
	 */
	private function get_profile_picture_url( string $user_id, string $token ): ?string {
		$response = $this->make_request(
			self::API_BASE . $user_id . '/picture?redirect=false&type=large&access_token=' . $token
		);

		return $response['data']['url'] ?? null;
	}

	/**
	 * Get redirect URL for OAuth callback.
	 *
	 * @return string Redirect URL.
	 */
	private function get_redirect_url(): string {
		return admin_url( 'profile.php?avatarsteward_oauth=facebook' );
	}
}
