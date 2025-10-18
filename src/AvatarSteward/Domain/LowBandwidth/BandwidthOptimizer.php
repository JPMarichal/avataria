<?php
/**
 * Bandwidth Optimizer class.
 *
 * Handles low-bandwidth mode by serving SVG avatars when images exceed size threshold.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\LowBandwidth;

use AvatarSteward\Domain\Initials\Generator;

/**
 * BandwidthOptimizer class for managing low-bandwidth mode.
 */
class BandwidthOptimizer {

	/**
	 * Initials generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * File size threshold in bytes (default: 100KB).
	 *
	 * @var int
	 */
	private int $threshold;

	/**
	 * Whether low-bandwidth mode is enabled.
	 *
	 * @var bool
	 */
	private bool $enabled;

	/**
	 * Constructor.
	 *
	 * @param Generator $generator Initials generator instance.
	 * @param array     $config    Optional configuration array.
	 */
	public function __construct( Generator $generator, array $config = array() ) {
		$this->generator = $generator;
		$this->threshold = isset( $config['threshold'] ) ? (int) $config['threshold'] : 102400; // 100KB default.
		$this->enabled   = isset( $config['enabled'] ) ? (bool) $config['enabled'] : false;
	}

	/**
	 * Check if low-bandwidth mode is enabled.
	 *
	 * @return bool True if enabled, false otherwise.
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}

	/**
	 * Get the file size threshold.
	 *
	 * @return int Threshold in bytes.
	 */
	public function get_threshold(): int {
		return $this->threshold;
	}

	/**
	 * Check if an attachment exceeds the size threshold.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return bool True if exceeds threshold, false otherwise.
	 */
	public function exceeds_threshold( int $attachment_id ): bool {
		if ( ! $this->enabled ) {
			return false;
		}

		if ( ! function_exists( 'get_attached_file' ) ) {
			return false;
		}

		$file_path = get_attached_file( $attachment_id );

		if ( ! $file_path || ! file_exists( $file_path ) ) {
			return false;
		}

		$file_size = filesize( $file_path );

		return $file_size !== false && $file_size > $this->threshold;
	}

	/**
	 * Generate an SVG avatar for a user.
	 *
	 * @param int    $user_id User ID.
	 * @param int    $size    Avatar size in pixels.
	 * @param string $name    Optional display name. If not provided, fetched from user.
	 * @return string SVG markup as data URI.
	 */
	public function generate_svg_avatar( int $user_id, int $size = 96, string $name = '' ): string {
		// Get user name if not provided.
		if ( empty( $name ) && function_exists( 'get_userdata' ) ) {
			$user = get_userdata( $user_id );
			if ( $user ) {
				$name = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;
			}
		}

		// Fallback if still empty.
		if ( empty( $name ) ) {
			$name = 'User';
		}

		// Generate SVG.
		$svg = $this->generator->generate( $name, $size );

		// Convert to data URI.
		return $this->svg_to_data_uri( $svg );
	}

	/**
	 * Convert SVG markup to data URI.
	 *
	 * @param string $svg SVG markup.
	 * @return string Data URI string.
	 */
	public function svg_to_data_uri( string $svg ): string {
		// Encode SVG for data URI.
		$encoded = rawurlencode( $svg );

		return 'data:image/svg+xml,' . $encoded;
	}

	/**
	 * Get performance metrics for low-bandwidth mode.
	 *
	 * @param int $attachment_id Original avatar attachment ID.
	 * @param int $size          Avatar size.
	 * @return array Performance metrics.
	 */
	public function get_performance_metrics( int $attachment_id, int $size ): array {
		$metrics = array(
			'original_size' => 0,
			'svg_size'      => 0,
			'reduction'     => 0,
			'percentage'    => 0,
		);

		// Get original file size.
		if ( function_exists( 'get_attached_file' ) ) {
			$file_path = get_attached_file( $attachment_id );
			if ( $file_path && file_exists( $file_path ) ) {
				$metrics['original_size'] = filesize( $file_path );
			}
		}

		// Calculate SVG size (approximate).
		$svg                 = $this->generator->generate( 'Test User', $size );
		$metrics['svg_size'] = strlen( $svg );

		// Calculate reduction.
		if ( $metrics['original_size'] > 0 ) {
			$metrics['reduction']  = $metrics['original_size'] - $metrics['svg_size'];
			$metrics['percentage'] = ( $metrics['reduction'] / $metrics['original_size'] ) * 100;
		}

		return $metrics;
	}

	/**
	 * Enable low-bandwidth mode.
	 *
	 * @return void
	 */
	public function enable(): void {
		$this->enabled = true;
	}

	/**
	 * Disable low-bandwidth mode.
	 *
	 * @return void
	 */
	public function disable(): void {
		$this->enabled = false;
	}

	/**
	 * Set the file size threshold.
	 *
	 * @param int $threshold Threshold in bytes.
	 * @return void
	 */
	public function set_threshold( int $threshold ): void {
		$this->threshold = max( 0, $threshold );
	}
}
