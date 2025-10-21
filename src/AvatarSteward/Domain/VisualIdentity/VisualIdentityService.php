<?php
/**
 * Visual Identity Service.
 *
 * Manages color palettes and visual styles for the Avatar Steward plugin.
 * Provides a centralized service for accessing visual identity elements.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\VisualIdentity;

use AvatarSteward\Domain\Initials\Generator;

/**
 * VisualIdentityService class for managing palettes and styles.
 */
class VisualIdentityService {

	/**
	 * Initials generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Cache group for transients.
	 *
	 * @var string
	 */
	private const CACHE_GROUP = 'avatar_steward_visual_identity';

	/**
	 * Cache expiration time (1 hour).
	 *
	 * @var int
	 */
	private const CACHE_EXPIRATION = 3600;

	/**
	 * Constructor.
	 *
	 * @param Generator|null $generator Optional generator instance.
	 */
	public function __construct( ?Generator $generator = null ) {
		$this->generator = $generator ?? new Generator();
	}

	/**
	 * Get all color palettes.
	 *
	 * Returns all available color palettes for avatar generation.
	 *
	 * @return array<string, mixed> Array of palette data.
	 */
	public function get_palettes(): array {
		$cache_key = 'palettes';
		$cached    = $this->get_cached( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$palettes = array(
			'avatar_initials' => array(
				'name'        => __( 'Avatar Initials Palette', 'avatar-steward' ),
				'description' => __( 'Default color palette used for generating initial-based avatars', 'avatar-steward' ),
				'colors'      => $this->generator->get_color_palette(),
				'usage'       => 'initials_avatars',
			),
			'primary'         => array(
				'name'        => __( 'Primary Brand Colors', 'avatar-steward' ),
				'description' => __( 'Main brand colors for UI elements', 'avatar-steward' ),
				'colors'      => $this->get_primary_colors(),
				'usage'       => 'ui_elements',
			),
			'status'          => array(
				'name'        => __( 'Status Colors', 'avatar-steward' ),
				'description' => __( 'Colors for indicating status and states', 'avatar-steward' ),
				'colors'      => $this->get_status_colors(),
				'usage'       => 'status_indicators',
			),
		);

		/**
		 * Filter the available color palettes.
		 *
		 * @param array $palettes Array of palette data.
		 */
		$palettes = apply_filters( 'avatar_steward_palettes', $palettes );

		$this->set_cached( $cache_key, $palettes );

		return $palettes;
	}

	/**
	 * Get a specific palette by key.
	 *
	 * @param string $palette_key The palette identifier.
	 * @return array<string, mixed>|null Palette data or null if not found.
	 */
	public function get_palette( string $palette_key ): ?array {
		$palettes = $this->get_palettes();

		return $palettes[ $palette_key ] ?? null;
	}

	/**
	 * Get visual styles configuration.
	 *
	 * Returns style settings for avatars and UI elements.
	 *
	 * @return array<string, mixed> Array of style configurations.
	 */
	public function get_styles(): array {
		$cache_key = 'styles';
		$cached    = $this->get_cached( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$styles = array(
			'avatar'     => array(
				'name'        => __( 'Avatar Styles', 'avatar-steward' ),
				'description' => __( 'Default styling for avatars', 'avatar-steward' ),
				'properties'  => array(
					'border_radius' => '50%',
					'min_size'      => $this->generator->get_min_size(),
					'max_size'      => $this->generator->get_max_size(),
					'default_size'  => 96,
					'font_family'   => $this->generator->get_font_family(),
					'text_color'    => '#ffffff',
				),
			),
			'typography' => array(
				'name'        => __( 'Typography', 'avatar-steward' ),
				'description' => __( 'Font settings for initials and labels', 'avatar-steward' ),
				'properties'  => array(
					'font_family'    => $this->generator->get_font_family(),
					'font_weight'    => 'normal',
					'text_transform' => 'uppercase',
				),
			),
			'layout'     => array(
				'name'        => __( 'Layout Styles', 'avatar-steward' ),
				'description' => __( 'Spacing and layout properties', 'avatar-steward' ),
				'properties'  => array(
					'spacing_small'  => '8px',
					'spacing_medium' => '16px',
					'spacing_large'  => '24px',
				),
			),
		);

		/**
		 * Filter the available visual styles.
		 *
		 * @param array $styles Array of style configurations.
		 */
		$styles = apply_filters( 'avatar_steward_styles', $styles );

		$this->set_cached( $cache_key, $styles );

		return $styles;
	}

	/**
	 * Get a specific style by key.
	 *
	 * @param string $style_key The style identifier.
	 * @return array<string, mixed>|null Style data or null if not found.
	 */
	public function get_style( string $style_key ): ?array {
		$styles = $this->get_styles();

		return $styles[ $style_key ] ?? null;
	}

	/**
	 * Get complete visual identity configuration.
	 *
	 * Returns both palettes and styles in a single call.
	 *
	 * @return array<string, mixed> Complete visual identity data.
	 */
	public function get_visual_identity(): array {
		return array(
			'version'  => '1.0.0',
			'palettes' => $this->get_palettes(),
			'styles'   => $this->get_styles(),
		);
	}

	/**
	 * Clear all cached visual identity data.
	 *
	 * @return bool True on success.
	 */
	public function clear_cache(): bool {
		delete_transient( $this->get_cache_key( 'palettes' ) );
		delete_transient( $this->get_cache_key( 'styles' ) );

		return true;
	}

	/**
	 * Get primary brand colors.
	 *
	 * @return array<string> Array of color hex codes.
	 */
	private function get_primary_colors(): array {
		return array(
			'#0073aa', // WordPress blue.
			'#00a0d2', // Light blue.
			'#007cba', // Medium blue.
			'#005177', // Dark blue.
		);
	}

	/**
	 * Get status indicator colors.
	 *
	 * @return array<string, string> Array of status colors with labels.
	 */
	private function get_status_colors(): array {
		return array(
			'success' => '#46b450',
			'warning' => '#f0b849',
			'error'   => '#dc3232',
			'info'    => '#00a0d2',
			'neutral' => '#dcdcde',
		);
	}

	/**
	 * Get cached value.
	 *
	 * @param string $key Cache key.
	 * @return mixed|false Cached value or false if not found.
	 */
	private function get_cached( string $key ) {
		if ( function_exists( 'get_transient' ) ) {
			return get_transient( $this->get_cache_key( $key ) );
		}

		return false;
	}

	/**
	 * Set cached value.
	 *
	 * @param string $key   Cache key.
	 * @param mixed  $value Value to cache.
	 * @return bool True on success.
	 */
	private function set_cached( string $key, $value ): bool {
		if ( function_exists( 'set_transient' ) ) {
			return set_transient( $this->get_cache_key( $key ), $value, self::CACHE_EXPIRATION );
		}

		return false;
	}

	/**
	 * Get full cache key with group prefix.
	 *
	 * @param string $key Cache key.
	 * @return string Full cache key.
	 */
	private function get_cache_key( string $key ): string {
		return self::CACHE_GROUP . '_' . $key;
	}
}
