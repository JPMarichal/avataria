<?php
/**
 * Social Provider Interface.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Integrations;

/**
 * Interface for social media avatar providers.
 *
 * Following Strategy Pattern for extensible social integrations (RF-P02).
 */
interface SocialProviderInterface {

	/**
	 * Get the provider name.
	 *
	 * @return string Provider name (e.g., 'twitter', 'facebook').
	 */
	public function get_name(): string;

	/**
	 * Get the provider display label.
	 *
	 * @return string Display label for UI (e.g., 'Twitter', 'Facebook').
	 */
	public function get_label(): string;

	/**
	 * Get the OAuth authorization URL.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $redirect_url Callback URL after authorization.
	 * @return string|null OAuth authorization URL or null if configuration is invalid.
	 */
	public function get_authorization_url( int $user_id, string $redirect_url ): ?string;

	/**
	 * Handle OAuth callback and exchange code for access token.
	 *
	 * @param string $code Authorization code from OAuth callback.
	 * @param int    $user_id WordPress user ID.
	 * @return bool True if connection was successful.
	 */
	public function handle_callback( string $code, int $user_id ): bool;

	/**
	 * Import avatar from social provider.
	 *
	 * Downloads and saves the user's profile picture from the social network.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool True if import was successful.
	 */
	public function import_avatar( int $user_id ): bool;

	/**
	 * Disconnect the social provider from user account.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool True if disconnection was successful.
	 */
	public function disconnect( int $user_id ): bool;

	/**
	 * Check if user is connected to this provider.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool True if connected.
	 */
	public function is_connected( int $user_id ): bool;

	/**
	 * Validate provider configuration.
	 *
	 * @return bool True if API credentials are properly configured.
	 */
	public function is_configured(): bool;
}
