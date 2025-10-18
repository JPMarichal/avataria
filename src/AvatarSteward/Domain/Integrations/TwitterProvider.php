<?php
/**
 * Twitter/X Social Provider.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Integrations;

/**
 * Twitter/X OAuth integration for avatar import.
 *
 * Implements OAuth 2.0 with PKCE for Twitter API v2.
 */
class TwitterProvider extends AbstractSocialProvider {

	/**
	 * Twitter API base URL.
	 *
	 * @var string
	 */
	private const API_BASE = 'https://api.twitter.com/2/';

	/**
	 * Twitter OAuth authorization URL.
	 *
	 * @var string
	 */
	private const AUTH_URL = 'https://twitter.com/i/oauth2/authorize';

	/**
	 * Twitter OAuth token URL.
	 *
	 * @var string
	 */
	private const TOKEN_URL = 'https://api.twitter.com/2/oauth2/token';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name  = 'twitter';
		$this->label = 'Twitter / X';
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

		$client_id = get_option( 'avatarsteward_twitter_client_id', '' );
		$state     = wp_create_nonce( 'twitter_oauth_' . $user_id );

		// Store state for validation.
		set_transient( 'avatarsteward_twitter_state_' . $user_id, $state, HOUR_IN_SECONDS );

		// Generate PKCE code challenge.
		$code_verifier = $this->generate_code_verifier();
		set_transient( 'avatarsteward_twitter_verifier_' . $user_id, $code_verifier, HOUR_IN_SECONDS );

		$code_challenge = $this->generate_code_challenge( $code_verifier );

		$params = array(
			'response_type'         => 'code',
			'client_id'             => $client_id,
			'redirect_uri'          => $redirect_url,
			'scope'                 => 'users.read tweet.read',
			'state'                 => $state,
			'code_challenge'        => $code_challenge,
			'code_challenge_method' => 'S256',
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

		$code_verifier = get_transient( 'avatarsteward_twitter_verifier_' . $user_id );
		if ( ! $code_verifier ) {
			return false;
		}

		$client_id     = get_option( 'avatarsteward_twitter_client_id', '' );
		$client_secret = get_option( 'avatarsteward_twitter_client_secret', '' );
		$redirect_url  = $this->get_redirect_url();

		$response = $this->make_request(
			self::TOKEN_URL,
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'code'          => $code,
					'grant_type'    => 'authorization_code',
					'redirect_uri'  => $redirect_url,
					'code_verifier' => $code_verifier,
				),
			)
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
		delete_transient( 'avatarsteward_twitter_state_' . $user_id );
		delete_transient( 'avatarsteward_twitter_verifier_' . $user_id );

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_social_connected', $user_id, $this->name );
		}

		return true;
	}

	/**
	 * Import avatar from Twitter.
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

		if ( empty( $user_data['profile_image_url'] ) ) {
			return false;
		}

		// Get higher resolution image.
		$image_url = str_replace( '_normal', '_400x400', $user_data['profile_image_url'] );

		$attachment_id = $this->download_and_save_image( $image_url, $user_id );
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
		$client_id     = get_option( 'avatarsteward_twitter_client_id', '' );
		$client_secret = get_option( 'avatarsteward_twitter_client_secret', '' );

		return ! empty( $client_id ) && ! empty( $client_secret );
	}

	/**
	 * Fetch user data from Twitter API.
	 *
	 * @param string $token Access token.
	 * @return array|null User data or null on failure.
	 */
	private function fetch_user_data( string $token ): ?array {
		$response = $this->make_request(
			self::API_BASE . 'users/me?user.fields=profile_image_url',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);

		return $response['data'] ?? null;
	}

	/**
	 * Generate PKCE code verifier.
	 *
	 * @return string Code verifier.
	 */
	private function generate_code_verifier(): string {
		$random = wp_generate_password( 64, false );
		return rtrim( strtr( base64_encode( $random ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Generate PKCE code challenge from verifier.
	 *
	 * @param string $verifier Code verifier.
	 * @return string Code challenge.
	 */
	private function generate_code_challenge( string $verifier ): string {
		$hash = hash( 'sha256', $verifier, true );
		return rtrim( strtr( base64_encode( $hash ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Get redirect URL for OAuth callback.
	 *
	 * @return string Redirect URL.
	 */
	private function get_redirect_url(): string {
		return admin_url( 'profile.php?avatarsteward_oauth=twitter' );
	}
}
