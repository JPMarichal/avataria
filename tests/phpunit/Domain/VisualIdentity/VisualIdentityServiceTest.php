<?php
/**
 * Tests for VisualIdentityService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\VisualIdentity;

use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;
use AvatarSteward\Domain\Initials\Generator;
use PHPUnit\Framework\TestCase;

/**
 * Test case for VisualIdentityService.
 */
class VisualIdentityServiceTest extends TestCase {

	/**
	 * Service instance.
	 *
	 * @var VisualIdentityService
	 */
	private VisualIdentityService $service;

	/**
	 * Set up test fixtures.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->service = new VisualIdentityService();
	}

	/**
	 * Test that get_palettes returns an array.
	 */
	public function test_get_palettes_returns_array(): void {
		$palettes = $this->service->get_palettes();

		$this->assertIsArray( $palettes );
		$this->assertNotEmpty( $palettes );
	}

	/**
	 * Test that palettes contain required keys.
	 */
	public function test_palettes_have_required_structure(): void {
		$palettes = $this->service->get_palettes();

		$this->assertArrayHasKey( 'avatar_initials', $palettes );
		$this->assertArrayHasKey( 'primary', $palettes );
		$this->assertArrayHasKey( 'status', $palettes );

		// Check structure of avatar_initials palette.
		$avatar_palette = $palettes['avatar_initials'];
		$this->assertArrayHasKey( 'name', $avatar_palette );
		$this->assertArrayHasKey( 'description', $avatar_palette );
		$this->assertArrayHasKey( 'colors', $avatar_palette );
		$this->assertArrayHasKey( 'usage', $avatar_palette );
	}

	/**
	 * Test that avatar_initials palette contains colors.
	 */
	public function test_avatar_initials_palette_has_colors(): void {
		$palettes       = $this->service->get_palettes();
		$avatar_palette = $palettes['avatar_initials'];

		$this->assertIsArray( $avatar_palette['colors'] );
		$this->assertNotEmpty( $avatar_palette['colors'] );

		// Check that colors are hex codes.
		foreach ( $avatar_palette['colors'] as $color ) {
			$this->assertMatchesRegularExpression( '/^#[0-9a-fA-F]{6}$/', $color );
		}
	}

	/**
	 * Test that status palette contains correct keys.
	 */
	public function test_status_palette_structure(): void {
		$palettes       = $this->service->get_palettes();
		$status_palette = $palettes['status'];

		$this->assertIsArray( $status_palette['colors'] );

		$expected_keys = array( 'success', 'warning', 'error', 'info', 'neutral' );
		foreach ( $expected_keys as $key ) {
			$this->assertArrayHasKey( $key, $status_palette['colors'] );
		}
	}

	/**
	 * Test get_palette with valid key.
	 */
	public function test_get_palette_with_valid_key(): void {
		$palette = $this->service->get_palette( 'avatar_initials' );

		$this->assertIsArray( $palette );
		$this->assertArrayHasKey( 'colors', $palette );
	}

	/**
	 * Test get_palette with invalid key.
	 */
	public function test_get_palette_with_invalid_key(): void {
		$palette = $this->service->get_palette( 'nonexistent' );

		$this->assertNull( $palette );
	}

	/**
	 * Test that get_styles returns an array.
	 */
	public function test_get_styles_returns_array(): void {
		$styles = $this->service->get_styles();

		$this->assertIsArray( $styles );
		$this->assertNotEmpty( $styles );
	}

	/**
	 * Test that styles contain required keys.
	 */
	public function test_styles_have_required_structure(): void {
		$styles = $this->service->get_styles();

		$this->assertArrayHasKey( 'avatar', $styles );
		$this->assertArrayHasKey( 'typography', $styles );
		$this->assertArrayHasKey( 'layout', $styles );

		// Check structure of avatar style.
		$avatar_style = $styles['avatar'];
		$this->assertArrayHasKey( 'name', $avatar_style );
		$this->assertArrayHasKey( 'description', $avatar_style );
		$this->assertArrayHasKey( 'properties', $avatar_style );
	}

	/**
	 * Test avatar style properties.
	 */
	public function test_avatar_style_properties(): void {
		$styles       = $this->service->get_styles();
		$avatar_style = $styles['avatar'];

		$this->assertIsArray( $avatar_style['properties'] );

		$expected_properties = array(
			'border_radius',
			'min_size',
			'max_size',
			'default_size',
			'font_family',
			'text_color',
		);

		foreach ( $expected_properties as $property ) {
			$this->assertArrayHasKey( $property, $avatar_style['properties'] );
		}
	}

	/**
	 * Test get_style with valid key.
	 */
	public function test_get_style_with_valid_key(): void {
		$style = $this->service->get_style( 'avatar' );

		$this->assertIsArray( $style );
		$this->assertArrayHasKey( 'properties', $style );
	}

	/**
	 * Test get_style with invalid key.
	 */
	public function test_get_style_with_invalid_key(): void {
		$style = $this->service->get_style( 'nonexistent' );

		$this->assertNull( $style );
	}

	/**
	 * Test get_visual_identity returns complete data.
	 */
	public function test_get_visual_identity_returns_complete_data(): void {
		$data = $this->service->get_visual_identity();

		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'version', $data );
		$this->assertArrayHasKey( 'palettes', $data );
		$this->assertArrayHasKey( 'styles', $data );

		$this->assertIsString( $data['version'] );
		$this->assertIsArray( $data['palettes'] );
		$this->assertIsArray( $data['styles'] );
	}

	/**
	 * Test that version follows semver format.
	 */
	public function test_version_follows_semver(): void {
		$data = $this->service->get_visual_identity();

		$this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', $data['version'] );
	}

	/**
	 * Test clear_cache returns boolean.
	 */
	public function test_clear_cache_returns_boolean(): void {
		$result = $this->service->clear_cache();

		$this->assertIsBool( $result );
	}

	/**
	 * Test that service uses generator instance.
	 */
	public function test_service_uses_generator(): void {
		$generator = $this->createMock( Generator::class );
		$generator->method( 'get_color_palette' )->willReturn( array( '#000000' ) );
		$generator->method( 'get_font_family' )->willReturn( 'Test Font' );
		$generator->method( 'get_min_size' )->willReturn( 32 );
		$generator->method( 'get_max_size' )->willReturn( 512 );

		$service = new VisualIdentityService( $generator );
		$data    = $service->get_visual_identity();

		$this->assertEquals( array( '#000000' ), $data['palettes']['avatar_initials']['colors'] );
		$this->assertEquals( 'Test Font', $data['styles']['avatar']['properties']['font_family'] );
	}
}
