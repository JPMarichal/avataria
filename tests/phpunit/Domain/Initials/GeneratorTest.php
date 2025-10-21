<?php
/**
 * Tests for Initials Generator class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Initials\Generator;

/**
 * Test case for Generator class.
 */
final class GeneratorTest extends TestCase {

	/**
	 * Generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->generator = new Generator();
	}

	/**
	 * Test that Generator class can be instantiated.
	 */
	public function test_generator_can_be_instantiated() {
		$this->assertInstanceOf( Generator::class, $this->generator );
	}

	/**
	 * Test generating avatar returns SVG string.
	 */
	public function test_generate_returns_svg_string() {
		$svg = $this->generator->generate( 'John Doe' );

		$this->assertIsString( $svg );
		$this->assertStringContainsString( '<svg', $svg );
		$this->assertStringContainsString( '</svg>', $svg );
	}

	/**
	 * Test extracting initials from two-word name.
	 */
	public function test_extract_initials_two_words() {
		$initials = $this->generator->extract_initials( 'John Doe' );
		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test extracting initials from single-word name.
	 */
	public function test_extract_initials_single_word() {
		$initials = $this->generator->extract_initials( 'Madonna' );
		$this->assertSame( 'MA', $initials );
	}

	/**
	 * Test extracting initials from three-word name.
	 */
	public function test_extract_initials_three_words() {
		$initials = $this->generator->extract_initials( 'John Paul Jones' );
		$this->assertSame( 'JJ', $initials );
	}

	/**
	 * Test extracting initials from empty name.
	 */
	public function test_extract_initials_empty_name() {
		$initials = $this->generator->extract_initials( '' );
		$this->assertSame( '?', $initials );
	}

	/**
	 * Test extracting initials from whitespace-only name.
	 */
	public function test_extract_initials_whitespace_only() {
		$initials = $this->generator->extract_initials( '   ' );
		$this->assertSame( '?', $initials );
	}

	/**
	 * Test extracting initials from name with special characters.
	 */
	public function test_extract_initials_special_characters() {
		$initials = $this->generator->extract_initials( 'John@123 Doe#456' );
		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test extracting initials from name with only special characters.
	 */
	public function test_extract_initials_only_special_characters() {
		$initials = $this->generator->extract_initials( '@#$%' );
		$this->assertSame( '?', $initials );
	}

	/**
	 * Test extracting initials from Unicode name.
	 */
	public function test_extract_initials_unicode() {
		$initials = $this->generator->extract_initials( 'José García' );
		$this->assertSame( 'JG', $initials );
	}

	/**
	 * Test extracting initials from Chinese name.
	 */
	public function test_extract_initials_chinese() {
		$initials = $this->generator->extract_initials( '李明' );
		$this->assertSame( '李明', $initials );
	}

	/**
	 * Test extracting initials from single character name.
	 */
	public function test_extract_initials_single_character() {
		$initials = $this->generator->extract_initials( 'A' );
		$this->assertSame( 'A', $initials );
	}

	/**
	 * Test extracting initials from name with numbers.
	 */
	public function test_extract_initials_with_numbers() {
		$initials = $this->generator->extract_initials( 'User123' );
		$this->assertSame( 'US', $initials );
	}

	/**
	 * Test extracting initials from name with extra spaces.
	 */
	public function test_extract_initials_extra_spaces() {
		$initials = $this->generator->extract_initials( '  John   Doe  ' );
		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test color consistency for same name.
	 */
	public function test_get_color_for_name_consistency() {
		$color1 = $this->generator->get_color_for_name( 'John Doe' );
		$color2 = $this->generator->get_color_for_name( 'John Doe' );

		$this->assertSame( $color1, $color2 );
	}

	/**
	 * Test color is from palette.
	 */
	public function test_get_color_for_name_from_palette() {
		$color   = $this->generator->get_color_for_name( 'John Doe' );
		$palette = $this->generator->get_color_palette();

		$this->assertContains( $color, $palette );
	}

	/**
	 * Test different names get colors.
	 */
	public function test_get_color_for_name_returns_valid_color() {
		$color = $this->generator->get_color_for_name( 'John Doe' );

		$this->assertIsString( $color );
		$this->assertRegExp( '/^#[0-9a-f]{6}$/i', $color );
	}

	/**
	 * Test color is case-insensitive.
	 */
	public function test_get_color_for_name_case_insensitive() {
		$color1 = $this->generator->get_color_for_name( 'John Doe' );
		$color2 = $this->generator->get_color_for_name( 'john doe' );
		$color3 = $this->generator->get_color_for_name( 'JOHN DOE' );

		$this->assertSame( $color1, $color2 );
		$this->assertSame( $color1, $color3 );
	}

	/**
	 * Test default size generation.
	 */
	public function test_generate_default_size() {
		$svg = $this->generator->generate( 'John Doe' );

		$this->assertStringContainsString( 'width="96"', $svg );
		$this->assertStringContainsString( 'height="96"', $svg );
	}

	/**
	 * Test custom size generation.
	 */
	public function test_generate_custom_size() {
		$svg = $this->generator->generate( 'John Doe', 128 );

		$this->assertStringContainsString( 'width="128"', $svg );
		$this->assertStringContainsString( 'height="128"', $svg );
	}

	/**
	 * Test minimum size constraint.
	 */
	public function test_generate_minimum_size() {
		$svg = $this->generator->generate( 'John Doe', 16 );

		// Should be constrained to minimum (32).
		$this->assertStringContainsString( 'width="32"', $svg );
		$this->assertStringContainsString( 'height="32"', $svg );
	}

	/**
	 * Test maximum size constraint.
	 */
	public function test_generate_maximum_size() {
		$svg = $this->generator->generate( 'John Doe', 1024 );

		// Should be constrained to maximum (512).
		$this->assertStringContainsString( 'width="512"', $svg );
		$this->assertStringContainsString( 'height="512"', $svg );
	}

	/**
	 * Test SVG contains initials.
	 */
	public function test_svg_contains_initials() {
		$svg = $this->generator->generate( 'John Doe' );

		$this->assertStringContainsString( 'JD', $svg );
	}

	/**
	 * Test SVG contains background color.
	 */
	public function test_svg_contains_background_color() {
		$svg = $this->generator->generate( 'John Doe' );

		// Should contain a fill attribute.
		$this->assertStringContainsString( 'fill=', $svg );
	}

	/**
	 * Test custom color palette configuration.
	 */
	public function test_custom_color_palette() {
		$custom_palette = array( '#ff0000', '#00ff00', '#0000ff' );
		$generator      = new Generator( array( 'color_palette' => $custom_palette ) );

		$color = $generator->get_color_for_name( 'John Doe' );
		$this->assertContains( $color, $custom_palette );
	}

	/**
	 * Test custom font family configuration.
	 */
	public function test_custom_font_family() {
		$custom_font = 'Verdana, Geneva, sans-serif';
		$generator   = new Generator( array( 'font_family' => $custom_font ) );

		$this->assertSame( $custom_font, $generator->get_font_family() );

		$svg = $generator->generate( 'John Doe' );
		$this->assertStringContainsString( 'Verdana', $svg );
	}

	/**
	 * Test custom text color configuration.
	 */
	public function test_custom_text_color() {
		$custom_color = '#000000';
		$generator    = new Generator( array( 'text_color' => $custom_color ) );

		$svg = $generator->generate( 'John Doe' );
		$this->assertStringContainsString( $custom_color, $svg );
	}

	/**
	 * Test custom default size configuration.
	 */
	public function test_custom_default_size() {
		$generator = new Generator( array( 'default_size' => 200 ) );

		$svg = $generator->generate( 'John Doe' );
		$this->assertStringContainsString( 'width="200"', $svg );
		$this->assertStringContainsString( 'height="200"', $svg );
	}

	/**
	 * Test XML escaping in SVG output.
	 */
	public function test_xml_escaping() {
		$svg = $this->generator->generate( '<script>alert("XSS")</script>' );

		// Should not contain raw script tags.
		$this->assertStringNotContainsString( '<script>', $svg );
		$this->assertStringNotContainsString( '</script>', $svg );
	}

	/**
	 * Test getters return expected values.
	 */
	public function test_getters() {
		$this->assertIsArray( $this->generator->get_color_palette() );
		$this->assertIsString( $this->generator->get_font_family() );
		$this->assertSame( 32, $this->generator->get_min_size() );
		$this->assertSame( 512, $this->generator->get_max_size() );
	}

	/**
	 * Test performance is acceptable (< 10ms).
	 */
	public function test_performance() {
		$iterations = 100;
		$start_time = microtime( true );

		for ( $i = 0; $i < $iterations; $i++ ) {
			$this->generator->generate( "User $i" );
		}

		$end_time = microtime( true );
		$duration = ( $end_time - $start_time ) * 1000; // Convert to milliseconds.
		$avg_time = $duration / $iterations;

		// Average time per generation should be less than 10ms.
		$this->assertLessThan( 10, $avg_time, "Average generation time: {$avg_time}ms" );
	}

	/**
	 * Test SVG is valid XML.
	 */
	public function test_svg_is_valid_xml() {
		$svg = $this->generator->generate( 'John Doe' );

		// Should be able to parse as XML.
		$previous_use_errors = libxml_use_internal_errors( true );
		$doc                 = simplexml_load_string( $svg );
		$errors              = libxml_get_errors();
		libxml_clear_errors();
		libxml_use_internal_errors( $previous_use_errors );

		$this->assertNotFalse( $doc, 'SVG should be valid XML. Errors: ' . print_r( $errors, true ) );
		$this->assertEmpty( $errors, 'SVG should have no XML parsing errors' );
	}
}
