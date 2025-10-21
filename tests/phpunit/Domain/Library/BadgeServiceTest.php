<?php
/**
 * Tests for BadgeService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Library;

use AvatarSteward\Domain\Library\BadgeService;
use PHPUnit\Framework\TestCase;

/**
 * Test case for BadgeService.
 */
class BadgeServiceTest extends TestCase {

	/**
	 * Badge service instance.
	 *
	 * @var BadgeService
	 */
	private BadgeService $badge_service;

	/**
	 * Set up test case.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->badge_service = new BadgeService();
	}

	/**
	 * Test that badge service can be instantiated.
	 */
	public function test_badge_service_instantiation(): void {
		$this->assertInstanceOf( BadgeService::class, $this->badge_service );
	}

	/**
	 * Test get_badge_types returns all badge types.
	 */
	public function test_get_badge_types(): void {
		$badge_types = $this->badge_service->get_badge_types();

		$this->assertIsArray( $badge_types );
		$this->assertNotEmpty( $badge_types );
		$this->assertArrayHasKey( 'verified', $badge_types );
		$this->assertArrayHasKey( 'moderator', $badge_types );
		$this->assertArrayHasKey( 'author', $badge_types );
		$this->assertArrayHasKey( 'premium', $badge_types );
	}

	/**
	 * Test is_valid_badge_type with valid types.
	 */
	public function test_is_valid_badge_type_valid(): void {
		$this->assertTrue( $this->badge_service->is_valid_badge_type( 'verified' ) );
		$this->assertTrue( $this->badge_service->is_valid_badge_type( 'moderator' ) );
		$this->assertTrue( $this->badge_service->is_valid_badge_type( 'author' ) );
		$this->assertTrue( $this->badge_service->is_valid_badge_type( 'premium' ) );
	}

	/**
	 * Test is_valid_badge_type with invalid type.
	 */
	public function test_is_valid_badge_type_invalid(): void {
		$this->assertFalse( $this->badge_service->is_valid_badge_type( 'invalid' ) );
		$this->assertFalse( $this->badge_service->is_valid_badge_type( 'custom' ) );
	}

	/**
	 * Test render_badge with valid data.
	 */
	public function test_render_badge(): void {
		$badge_data = array(
			'name'  => 'Verified',
			'icon'  => 'dashicons-yes-alt',
			'color' => '#0073aa',
		);

		$html = $this->badge_service->render_badge( $badge_data );

		$this->assertIsString( $html );
		$this->assertStringContainsString( 'avatar-steward-badge', $html );
		$this->assertStringContainsString( 'dashicons-yes-alt', $html );
		$this->assertStringContainsString( '#0073aa', $html );
		$this->assertStringContainsString( 'title="Verified"', $html );
	}

	/**
	 * Test render_badge with empty data.
	 */
	public function test_render_badge_empty(): void {
		$html = $this->badge_service->render_badge( array() );

		$this->assertSame( '', $html );
	}

	/**
	 * Test render_badge with custom size.
	 */
	public function test_render_badge_custom_size(): void {
		$badge_data = array(
			'name'  => 'Test',
			'icon'  => 'dashicons-test',
			'color' => '#ff0000',
		);

		$html = $this->badge_service->render_badge( $badge_data, 30 );

		$this->assertStringContainsString( 'font-size: 30px', $html );
		$this->assertStringContainsString( 'width: 30px', $html );
		$this->assertStringContainsString( 'height: 30px', $html );
	}

	/**
	 * Test badge type structure.
	 */
	public function test_badge_type_structure(): void {
		$badge_types = $this->badge_service->get_badge_types();

		foreach ( $badge_types as $type => $data ) {
			$this->assertArrayHasKey( 'name', $data );
			$this->assertArrayHasKey( 'description', $data );
			$this->assertArrayHasKey( 'icon', $data );
			$this->assertArrayHasKey( 'color', $data );
			$this->assertIsString( $data['name'] );
			$this->assertIsString( $data['description'] );
			$this->assertIsString( $data['icon'] );
			$this->assertIsString( $data['color'] );
		}
	}
}
