<?php
/**
 * Edge case tests for Initials Generator.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Initials;

use AvatarSteward\Domain\Initials\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Edge case tests for the Initials Generator class.
 */
class GeneratorEdgeCasesTest extends TestCase {

	/**
	 * Test with empty string.
	 */
	public function test_generate_with_empty_string(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '' );

		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with only whitespace.
	 */
	public function test_generate_with_only_whitespace(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '   ' );

		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with only spaces and tabs.
	 */
	public function test_generate_with_spaces_and_tabs(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( " \t\n\r " );

		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with single character.
	 */
	public function test_generate_with_single_character(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'A' );

		$this->assertSame( 'A', $initials );
	}

	/**
	 * Test with only numbers.
	 */
	public function test_generate_with_only_numbers(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '123' );

		// Single word numbers return first two characters.
		$this->assertSame( '12', $initials );
	}

	/**
	 * Test with special characters only.
	 */
	public function test_generate_with_special_characters_only(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '!@#$%^&*()' );

		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with emoji only.
	 */
	public function test_generate_with_emoji_only(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '😀' );

		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with name containing emoji.
	 */
	public function test_generate_with_name_containing_emoji(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John 😀 Doe' );

		// Should extract initials ignoring emoji.
		$this->assertNotEmpty( $initials );
		$this->assertRegExp( '/^[A-Z]{1,2}$/', $initials );
	}

	/**
	 * Test with unicode characters (accented letters).
	 */
	public function test_generate_with_accented_letters(): void {
		$generator = new Generator();
		
		// Spanish.
		$initials = $generator->extract_initials( 'José García' );
		$this->assertNotEmpty( $initials );
		
		// French.
		$initials = $generator->extract_initials( 'François Édouard' );
		$this->assertNotEmpty( $initials );
		
		// German.
		$initials = $generator->extract_initials( 'Müller Schröder' );
		$this->assertNotEmpty( $initials );
	}

	/**
	 * Test with very long name (>100 characters).
	 */
	public function test_generate_with_very_long_name(): void {
		$generator = new Generator();
		$long_name = str_repeat( 'Abcdef ', 20 ); // 140 characters.
		$initials  = $generator->extract_initials( $long_name );

		$this->assertNotEmpty( $initials );
		$this->assertLessThanOrEqual( 2, strlen( $initials ) );
	}

	/**
	 * Test with name containing multiple spaces.
	 */
	public function test_generate_with_multiple_spaces(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John    Doe' );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with name containing leading/trailing spaces.
	 */
	public function test_generate_with_leading_trailing_spaces(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '  John Doe  ' );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with name containing newlines.
	 */
	public function test_generate_with_newlines(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( "John\nDoe" );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with name containing tabs.
	 */
	public function test_generate_with_tabs(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( "John\tDoe" );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with lowercase name.
	 */
	public function test_generate_with_lowercase_name(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'john doe' );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with mixed case name.
	 */
	public function test_generate_with_mixed_case_name(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'JoHn DoE' );

		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with name containing numbers and letters.
	 */
	public function test_generate_with_numbers_and_letters(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John 2nd Doe' );

		$this->assertNotEmpty( $initials );
	}

	/**
	 * Test with name containing hyphens.
	 */
	public function test_generate_with_hyphens(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'Mary-Jane Watson' );

		$this->assertNotEmpty( $initials );
	}

	/**
	 * Test with name containing apostrophes.
	 */
	public function test_generate_with_apostrophes(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( "O'Brien" );

		$this->assertNotEmpty( $initials );
	}

	/**
	 * Test with three-part name.
	 */
	public function test_generate_with_three_part_name(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John Michael Doe' );

		// Should return first and last (JD).
		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with four-part name.
	 */
	public function test_generate_with_four_part_name(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John Michael Andrew Doe' );

		// Should return first and last (JD).
		$this->assertSame( 'JD', $initials );
	}

	/**
	 * Test with single word (mononym).
	 */
	public function test_generate_with_mononym(): void {
		$generator = new Generator();
		
		// Single words return first two characters.
		$this->assertSame( 'MA', $generator->extract_initials( 'Madonna' ) );
		$this->assertSame( 'PR', $generator->extract_initials( 'Prince' ) );
		$this->assertSame( 'BO', $generator->extract_initials( 'Bono' ) );
	}

	/**
	 * Test color generation consistency.
	 */
	public function test_color_generation_consistency(): void {
		$generator = new Generator();

		// Same name should produce same color.
		$color1 = $generator->get_color_for_name( 'John Doe' );
		$color2 = $generator->get_color_for_name( 'John Doe' );

		$this->assertSame( $color1, $color2 );
	}

	/**
	 * Test color generation for different names.
	 */
	public function test_color_generation_different_names(): void {
		$generator = new Generator();

		$color1 = $generator->get_color_for_name( 'John Doe' );
		$color2 = $generator->get_color_for_name( 'Jane Smith' );

		// Different names might produce different colors (not guaranteed).
		$this->assertNotEmpty( $color1 );
		$this->assertNotEmpty( $color2 );
		$this->assertRegExp( '/^#[0-9A-F]{6}$/i', $color1 );
		$this->assertRegExp( '/^#[0-9A-F]{6}$/i', $color2 );
	}

	/**
	 * Test color generation for empty name.
	 */
	public function test_color_generation_empty_name(): void {
		$generator = new Generator();
		$color     = $generator->get_color_for_name( '' );

		$this->assertNotEmpty( $color );
		$this->assertRegExp( '/^#[0-9A-F]{6}$/i', $color );
	}

	/**
	 * Test with null-like strings.
	 */
	public function test_generate_with_null_string(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '0' );

		// '0' is cleaned by regex and becomes empty.
		$this->assertSame( '?', $initials );
	}

	/**
	 * Test with HTML entities in name.
	 */
	public function test_generate_with_html_entities(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( 'John &amp; Doe' );

		$this->assertNotEmpty( $initials );
	}

	/**
	 * Test with SQL injection attempt.
	 */
	public function test_generate_with_sql_injection(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( "'; DROP TABLE users; --" );

		// Should handle safely, special chars removed.
		// After cleaning, becomes " DROP TABLE users " -> multiple words -> "DU".
		$this->assertSame( 'DU', $initials );
	}

	/**
	 * Test with XSS attempt.
	 */
	public function test_generate_with_xss_attempt(): void {
		$generator = new Generator();
		$initials  = $generator->extract_initials( '<script>alert("xss")</script>' );

		$this->assertNotEmpty( $initials );
	}
}
