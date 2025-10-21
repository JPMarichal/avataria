<?php
/**
 * Tests for VisualIdentityController class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Infrastructure\REST;

use AvatarSteward\Infrastructure\REST\VisualIdentityController;
use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;
use PHPUnit\Framework\TestCase;
use WP_REST_Request;

/**
 * Test case for VisualIdentityController.
 */
class VisualIdentityControllerTest extends TestCase {

	/**
	 * Controller instance.
	 *
	 * @var VisualIdentityController
	 */
	private VisualIdentityController $controller;

	/**
	 * Mock service instance.
	 *
	 * @var VisualIdentityService
	 */
	private $service;

	/**
	 * Set up test fixtures.
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->service    = $this->createMock( VisualIdentityService::class );
		$this->controller = new VisualIdentityController( $this->service );
	}

	/**
	 * Test namespace is correct.
	 */
	public function test_namespace_is_correct(): void {
		$reflection = new \ReflectionClass( $this->controller );
		$property   = $reflection->getProperty( 'namespace' );
		$property->setAccessible( true );

		$this->assertEquals( 'avatar-steward/v1', $property->getValue( $this->controller ) );
	}

	/**
	 * Test rest_base is correct.
	 */
	public function test_rest_base_is_correct(): void {
		$reflection = new \ReflectionClass( $this->controller );
		$property   = $reflection->getProperty( 'rest_base' );
		$property->setAccessible( true );

		$this->assertEquals( 'visual-identity', $property->getValue( $this->controller ) );
	}

	/**
	 * Test get_visual_identity returns response.
	 */
	public function test_get_visual_identity_returns_response(): void {
		$mock_data = array(
			'version'  => '1.0.0',
			'palettes' => array(),
			'styles'   => array(),
		);

		$this->service->method( 'get_visual_identity' )->willReturn( $mock_data );

		$request  = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity' );
		$response = $this->controller->get_visual_identity( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $mock_data, $response->get_data() );
	}

	/**
	 * Test get_palettes returns response.
	 */
	public function test_get_palettes_returns_response(): void {
		$mock_palettes = array(
			'avatar_initials' => array(
				'name'   => 'Test Palette',
				'colors' => array( '#000000' ),
			),
		);

		$this->service->method( 'get_palettes' )->willReturn( $mock_palettes );

		$request  = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/palettes' );
		$response = $this->controller->get_palettes( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $mock_palettes, $response->get_data() );
	}

	/**
	 * Test get_palette with valid key returns response.
	 */
	public function test_get_palette_with_valid_key(): void {
		$mock_palette = array(
			'name'   => 'Test Palette',
			'colors' => array( '#000000' ),
		);

		$this->service->method( 'get_palette' )->with( 'avatar_initials' )->willReturn( $mock_palette );

		$request = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/palettes/avatar_initials' );
		$request->set_param( 'palette_key', 'avatar_initials' );

		$response = $this->controller->get_palette( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $mock_palette, $response->get_data() );
	}

	/**
	 * Test get_palette with invalid key returns error.
	 */
	public function test_get_palette_with_invalid_key_returns_error(): void {
		$this->service->method( 'get_palette' )->with( 'invalid' )->willReturn( null );

		$request = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/palettes/invalid' );
		$request->set_param( 'palette_key', 'invalid' );

		$response = $this->controller->get_palette( $request );

		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertEquals( 'avatar_steward_palette_not_found', $response->get_error_code() );
	}

	/**
	 * Test get_styles returns response.
	 */
	public function test_get_styles_returns_response(): void {
		$mock_styles = array(
			'avatar' => array(
				'name'       => 'Avatar Styles',
				'properties' => array(),
			),
		);

		$this->service->method( 'get_styles' )->willReturn( $mock_styles );

		$request  = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/styles' );
		$response = $this->controller->get_styles( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $mock_styles, $response->get_data() );
	}

	/**
	 * Test get_style with valid key returns response.
	 */
	public function test_get_style_with_valid_key(): void {
		$mock_style = array(
			'name'       => 'Avatar Styles',
			'properties' => array(),
		);

		$this->service->method( 'get_style' )->with( 'avatar' )->willReturn( $mock_style );

		$request = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/styles/avatar' );
		$request->set_param( 'style_key', 'avatar' );

		$response = $this->controller->get_style( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $mock_style, $response->get_data() );
	}

	/**
	 * Test get_style with invalid key returns error.
	 */
	public function test_get_style_with_invalid_key_returns_error(): void {
		$this->service->method( 'get_style' )->with( 'invalid' )->willReturn( null );

		$request = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/styles/invalid' );
		$request->set_param( 'style_key', 'invalid' );

		$response = $this->controller->get_style( $request );

		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertEquals( 'avatar_steward_style_not_found', $response->get_error_code() );
	}

	/**
	 * Test clear_cache returns success response.
	 */
	public function test_clear_cache_returns_success(): void {
		$this->service->method( 'clear_cache' )->willReturn( true );

		$request  = new WP_REST_Request( 'DELETE', '/avatar-steward/v1/visual-identity/cache' );
		$response = $this->controller->clear_cache( $request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );

		$data = $response->get_data();
		$this->assertTrue( $data['success'] );
	}

	/**
	 * Test clear_cache returns error on failure.
	 */
	public function test_clear_cache_returns_error_on_failure(): void {
		$this->service->method( 'clear_cache' )->willReturn( false );

		$request  = new WP_REST_Request( 'DELETE', '/avatar-steward/v1/visual-identity/cache' );
		$response = $this->controller->clear_cache( $request );

		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertEquals( 'avatar_steward_cache_clear_failed', $response->get_error_code() );
	}

	/**
	 * Test public permissions check returns true.
	 */
	public function test_public_permissions_check_returns_true(): void {
		$request = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity' );
		$result  = $this->controller->get_public_permissions_check( $request );

		$this->assertTrue( $result );
	}

	/**
	 * Test response has cache headers.
	 */
	public function test_response_has_cache_headers(): void {
		$this->service->method( 'get_palettes' )->willReturn( array() );

		$request  = new WP_REST_Request( 'GET', '/avatar-steward/v1/visual-identity/palettes' );
		$response = $this->controller->get_palettes( $request );

		$headers = $response->get_headers();

		$this->assertArrayHasKey( 'Cache-Control', $headers );
		$this->assertEquals( 'public, max-age=3600', $headers['Cache-Control'] );
	}
}
