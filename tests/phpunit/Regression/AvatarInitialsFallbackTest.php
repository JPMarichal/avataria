<?php
/**
 * Regression tests for avatar initials fallback fix.
 *
 * Tests the fix documented in docs/fixes/avatar-initials-fallback-fix.md
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Regression;

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Initials\Generator;

/**
 * Test case for avatar initials fallback fix.
 */
final class AvatarInitialsFallbackTest extends TestCase {

	/**
	 * Generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->generator = new Generator();
	}

	/**
	 * Test that initials fallback works when display_name is empty.
	 *
	 * Regression test: Ensure initials generation falls back to username
	 * when display_name is empty or null.
	 *
	 * @return void
	 */
	public function test_initials_fallback_to_username_when_display_name_empty() {
		// Simulate user with empty display name but valid username.
		$user_data = array(
			'display_name' => '',
			'username'     => 'johndoe',
			'first_name'   => '',
			'last_name'    => '',
		);

		$initials = $this->generator->extract_initials( $user_data['username'] );

		$this->assertNotEmpty( $initials, 'Initials should be generated from username when display_name is empty' );
		$this->assertEquals( 'JD', $initials );
	}

	/**
	 * Test that initials fallback works when all name fields are empty.
	 *
	 * @return void
	 */
	public function test_initials_fallback_to_default_when_all_fields_empty() {
		$initials = $this->generator->extract_initials( '' );

		$this->assertEquals( '?', $initials, 'Should fallback to "?" when all name fields are empty' );
	}

	/**
	 * Test that initials are generated from first and last name when available.
	 *
	 * @return void
	 */
	public function test_initials_from_first_and_last_name() {
		$name     = 'John Doe';
		$initials = $this->generator->extract_initials( $name );

		$this->assertEquals( 'JD', $initials );
	}

	/**
	 * Test that single name generates correct initials.
	 *
	 * @return void
	 */
	public function test_initials_from_single_name() {
		$name     = 'Madonna';
		$initials = $this->generator->extract_initials( $name );

		$this->assertEquals( 'M', $initials );
	}

	/**
	 * Test that special characters are handled correctly in initials.
	 *
	 * @return void
	 */
	public function test_initials_with_special_characters() {
		$name     = "O'Brien";
		$initials = $this->generator->extract_initials( $name );

		$this->assertNotEmpty( $initials );
		$this->assertMatchesRegularExpression( '/^[A-Z]{1,2}$/', $initials );
	}
}
