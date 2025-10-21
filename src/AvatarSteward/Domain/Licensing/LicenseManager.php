<?php
/**
 * License Manager service for Avatar Steward Pro.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Licensing;

/**
 * Manages Pro license activation, validation, and storage.
 */
class LicenseManager {

	/**
	 * Option key for storing license data.
	 */
	private const LICENSE_OPTION_KEY = 'avatar_steward_license';

	/**
	 * Option key for storing license status.
	 */
	private const LICENSE_STATUS_KEY = 'avatar_steward_license_status';

	/**
	 * License status constants.
	 */
	public const STATUS_ACTIVE   = 'active';
	public const STATUS_INACTIVE = 'inactive';
	public const STATUS_EXPIRED  = 'expired';
	public const STATUS_INVALID  = 'invalid';

	/**
	 * Activate a license key.
	 *
	 * @param string $license_key The license key to activate.
	 * @return array{success: bool, message: string, status?: string} Activation result.
	 */
	public function activate( string $license_key ): array {
		$license_key = sanitize_text_field( trim( $license_key ) );

		if ( empty( $license_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'License key cannot be empty.', 'avatar-steward' ),
			);
		}

		// Validate license key format (simple validation for MVP).
		if ( ! $this->validate_license_format( $license_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid license key format.', 'avatar-steward' ),
			);
		}

		// Store license data.
		$license_data = array(
			'key'          => $license_key,
			'activated_at' => time(),
			'domain'       => $this->get_current_domain(),
			'activated_by' => get_current_user_id(),
		);

		update_option( self::LICENSE_OPTION_KEY, $license_data, false );
		update_option( self::LICENSE_STATUS_KEY, self::STATUS_ACTIVE, false );

		return array(
			'success' => true,
			'message' => __( 'License activated successfully.', 'avatar-steward' ),
			'status'  => self::STATUS_ACTIVE,
		);
	}

	/**
	 * Deactivate the current license.
	 *
	 * @return array{success: bool, message: string} Deactivation result.
	 */
	public function deactivate(): array {
		delete_option( self::LICENSE_OPTION_KEY );
		update_option( self::LICENSE_STATUS_KEY, self::STATUS_INACTIVE, false );

		return array(
			'success' => true,
			'message' => __( 'License deactivated successfully.', 'avatar-steward' ),
		);
	}

	/**
	 * Check if a valid Pro license is active.
	 *
	 * @return bool True if Pro license is active, false otherwise.
	 */
	public function is_pro_active(): bool {
		$status = $this->get_license_status();
		return self::STATUS_ACTIVE === $status;
	}

	/**
	 * Get current license status.
	 *
	 * @return string License status constant.
	 */
	public function get_license_status(): string {
		$status = get_option( self::LICENSE_STATUS_KEY, self::STATUS_INACTIVE );

		// Validate that the license data exists if status is active.
		if ( self::STATUS_ACTIVE === $status ) {
			$license_data = $this->get_license_data();
			if ( empty( $license_data ) || empty( $license_data['key'] ) ) {
				$this->deactivate();
				return self::STATUS_INACTIVE;
			}
		}

		return $status;
	}

	/**
	 * Get stored license data.
	 *
	 * @return array<string, mixed> License data array.
	 */
	public function get_license_data(): array {
		$data = get_option( self::LICENSE_OPTION_KEY, array() );
		return is_array( $data ) ? $data : array();
	}

	/**
	 * Validate license key format.
	 *
	 * For MVP: Simple format validation.
	 * Format: XXXX-XXXX-XXXX-XXXX (alphanumeric groups).
	 *
	 * @param string $license_key License key to validate.
	 * @return bool True if format is valid.
	 */
	private function validate_license_format( string $license_key ): bool {
		// Expected format: XXXX-XXXX-XXXX-XXXX (4 groups of 4 alphanumeric characters).
		$pattern = '/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/i';
		return (bool) preg_match( $pattern, $license_key );
	}

	/**
	 * Get the current domain.
	 *
	 * @return string Current domain/site URL.
	 */
	private function get_current_domain(): string {
		return function_exists( 'site_url' ) ? site_url() : '';
	}

	/**
	 * Validate that a specific feature is available.
	 *
	 * @param string $feature_name Name of the Pro feature.
	 * @return bool True if feature is available.
	 */
	public function can_use_pro_feature( string $feature_name ): bool {
		// Allow filtering for extensibility.
		$can_use = $this->is_pro_active();

		if ( function_exists( 'apply_filters' ) ) {
			$can_use = apply_filters(
				'avatar_steward_can_use_pro_feature',
				$can_use,
				$feature_name
			);
		}

		return (bool) $can_use;
	}

	/**
	 * Get license information for display.
	 *
	 * @return array{status: string, key?: string, activated_at?: string, domain?: string} License info.
	 */
	public function get_license_info(): array {
		$status = $this->get_license_status();
		$data   = $this->get_license_data();

		$info = array(
			'status' => $status,
		);

		if ( ! empty( $data ) && self::STATUS_ACTIVE === $status ) {
			$info['key']          = $this->mask_license_key( $data['key'] ?? '' );
			$info['activated_at'] = isset( $data['activated_at'] )
				? gmdate( 'Y-m-d H:i:s', $data['activated_at'] )
				: '';
			$info['domain']       = $data['domain'] ?? '';
		}

		return $info;
	}

	/**
	 * Mask license key for display (show only last 4 characters).
	 *
	 * @param string $key License key to mask.
	 * @return string Masked license key.
	 */
	private function mask_license_key( string $key ): string {
		if ( strlen( $key ) <= 4 ) {
			return '****';
		}

		$last_four = substr( $key, -4 );
		return '****-****-****-' . $last_four;
	}
}
