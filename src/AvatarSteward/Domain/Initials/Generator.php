<?php
/**
 * Initials Avatar Generator.
 *
 * Generates SVG avatars with user initials when no uploaded image is available.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Initials;

/**
 * Generator class for creating initials-based avatars.
 */
class Generator {

	/**
	 * Default size for avatars.
	 *
	 * @var int
	 */
	private int $default_size = 96;

	/**
	 * Minimum allowed size.
	 *
	 * @var int
	 */
	private int $min_size = 32;

	/**
	 * Maximum allowed size.
	 *
	 * @var int
	 */
	private int $max_size = 512;

	/**
	 * Color palette for backgrounds.
	 * Using accessible colors with good contrast ratios.
	 *
	 * @var array<string>
	 */
	private array $color_palette = array(
		'#1abc9c', // Turquoise.
		'#2ecc71', // Emerald.
		'#3498db', // Peter River.
		'#9b59b6', // Amethyst.
		'#34495e', // Wet Asphalt.
		'#16a085', // Green Sea.
		'#27ae60', // Nephritis.
		'#2980b9', // Belize Hole.
		'#8e44ad', // Wisteria.
		'#2c3e50', // Midnight Blue.
		'#f39c12', // Orange.
		'#e74c3c', // Alizarin.
		'#c0392b', // Pomegranate.
		'#d35400', // Pumpkin.
		'#7f8c8d', // Asbestos.
	);

	/**
	 * Font family for text.
	 *
	 * @var string
	 */
	private string $font_family = 'Arial, Helvetica, sans-serif';

	/**
	 * Text color (white for good contrast).
	 *
	 * @var string
	 */
	private string $text_color = '#ffffff';

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $config Optional configuration array.
	 */
	public function __construct( array $config = array() ) {
		if ( isset( $config['color_palette'] ) && is_array( $config['color_palette'] ) ) {
			$this->color_palette = $config['color_palette'];
		}

		if ( isset( $config['font_family'] ) && is_string( $config['font_family'] ) ) {
			$this->font_family = $config['font_family'];
		}

		if ( isset( $config['text_color'] ) && is_string( $config['text_color'] ) ) {
			$this->text_color = $config['text_color'];
		}

		if ( isset( $config['default_size'] ) && is_int( $config['default_size'] ) ) {
			$this->default_size = $this->validate_size( $config['default_size'] );
		}
	}

	/**
	 * Generate an SVG avatar from a name.
	 *
	 * @param string $name The user's display name.
	 * @param int    $size The desired size in pixels (width and height).
	 * @return string The SVG markup as a string.
	 */
	public function generate( string $name, int $size = 0 ): string {
		if ( $size <= 0 ) {
			$size = $this->default_size;
		}

		$size     = $this->validate_size( $size );
		$initials = $this->extract_initials( $name );
		$bg_color = $this->get_color_for_name( $name );

		return $this->build_svg( $initials, $bg_color, $size );
	}

	/**
	 * Extract initials from a name.
	 *
	 * @param string $name The user's display name.
	 * @return string The extracted initials (1-2 characters).
	 */
	public function extract_initials( string $name ): string {
		// Trim whitespace.
		$name = trim( $name );

		// Handle empty names.
		if ( empty( $name ) ) {
			return '?';
		}

		// Remove special characters but keep spaces, letters and numbers.
		$name = preg_replace( '/[^\p{L}\p{N}\s]/u', '', $name );

		// Handle names that become empty after cleaning.
		if ( empty( $name ) ) {
			return '?';
		}

		// Split by whitespace.
		$parts = preg_split( '/\s+/u', $name );

		// Filter out empty parts and re-index array.
		$parts = array_values( array_filter( $parts ) );

		if ( empty( $parts ) ) {
			return '?';
		}

		$initials = '';

		if ( count( $parts ) === 1 ) {
			// Single word: take first two characters.
			$word     = mb_strtoupper( $parts[0], 'UTF-8' );
			$initials = mb_substr( $word, 0, 2, 'UTF-8' );
		} else {
			// Multiple words: take first character of first and last word.
			$first    = mb_strtoupper( $parts[0], 'UTF-8' );
			$last     = mb_strtoupper( end( $parts ), 'UTF-8' );
			$initials = mb_substr( $first, 0, 1, 'UTF-8' ) . mb_substr( $last, 0, 1, 'UTF-8' );
		}

		return $initials;
	}

	/**
	 * Get a consistent color for a given name.
	 *
	 * Uses hash-based selection to ensure the same name always gets the same color.
	 *
	 * @param string $name The user's display name.
	 * @return string The hex color code.
	 */
	public function get_color_for_name( string $name ): string {
		// Use crc32 for consistent hashing (faster than md5/sha1).
		$hash  = crc32( strtolower( trim( $name ) ) );
		$index = abs( $hash ) % count( $this->color_palette );

		return $this->color_palette[ $index ];
	}

	/**
	 * Validate and constrain size to allowed range.
	 *
	 * @param int $size The requested size.
	 * @return int The validated size.
	 */
	private function validate_size( int $size ): int {
		if ( $size < $this->min_size ) {
			return $this->min_size;
		}

		if ( $size > $this->max_size ) {
			return $this->max_size;
		}

		return $size;
	}

	/**
	 * Build the SVG markup.
	 *
	 * @param string $initials The initials to display.
	 * @param string $bg_color The background color.
	 * @param int    $size     The size of the avatar.
	 * @return string The complete SVG markup.
	 */
	private function build_svg( string $initials, string $bg_color, int $size ): string {
		// Calculate font size as a percentage of avatar size.
		$font_size = (int) ( $size * 0.42 );

		// Escape for XML/SVG.
		$initials_safe    = $this->escape_xml( $initials );
		$bg_color_safe    = $this->escape_xml( $bg_color );
		$text_color_safe  = $this->escape_xml( $this->text_color );
		$font_family_safe = $this->escape_xml( $this->font_family );

		// Build SVG.
		$svg = sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d">' .
			'<rect width="%d" height="%d" fill="%s"/>' .
			'<text x="50%%" y="50%%" font-family="%s" font-size="%d" fill="%s" text-anchor="middle" dy=".35em">%s</text>' .
			'</svg>',
			$size,
			$size,
			$size,
			$size,
			$size,
			$size,
			$bg_color_safe,
			$font_family_safe,
			$font_size,
			$text_color_safe,
			$initials_safe
		);

		return $svg;
	}

	/**
	 * Escape text for XML/SVG context.
	 *
	 * @param string $text The text to escape.
	 * @return string The escaped text.
	 */
	private function escape_xml( string $text ): string {
		return htmlspecialchars( $text, ENT_XML1 | ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Get the color palette.
	 *
	 * @return array<string> The color palette array.
	 */
	public function get_color_palette(): array {
		return $this->color_palette;
	}

	/**
	 * Get the font family.
	 *
	 * @return string The font family.
	 */
	public function get_font_family(): string {
		return $this->font_family;
	}

	/**
	 * Get the minimum size.
	 *
	 * @return int The minimum size.
	 */
	public function get_min_size(): int {
		return $this->min_size;
	}

	/**
	 * Get the maximum size.
	 *
	 * @return int The maximum size.
	 */
	public function get_max_size(): int {
		return $this->max_size;
	}
}
