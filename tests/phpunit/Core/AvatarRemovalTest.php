<?php
/**
 * Test case for avatar removal and initials fallback.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Uploads\UploadService;
use AvatarSteward\Domain\Initials\Generator;

/**
 * Test case for avatar removal scenarios and initials fallback.
 */
final class AvatarRemovalTest extends TestCase {

	/**
	 * Avatar handler instance.
	 *
	 * @var AvatarHandler
	 */
	private AvatarHandler $handler;

	/**
	 * Initials generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$upload_service = new UploadService();
		$this->handler = new AvatarHandler( $upload_service );
		$this->generator = new Generator();
		$this->handler->set_generator( $this->generator );
		$this->handler->init();
	}

	/**
	 * Test that filter_avatar_data returns initials avatar when no local avatar exists.
	 */
	public function test_filter_avatar_data_returns_initials_when_no_local_avatar() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		// Should have found_avatar flag set.
		$this->assertArrayHasKey( 'found_avatar', $result );
		$this->assertTrue( $result['found_avatar'] );

		// Should have a data URL for SVG.
		$this->assertArrayHasKey( 'url', $result );
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result['url'] );
	}

	/**
	 * Test that filter_avatar_url returns initials avatar when no local avatar exists.
	 */
	public function test_filter_avatar_url_returns_initials_when_no_local_avatar() {
		$url = 'https://gravatar.com/avatar/test';
		$args = array( 'size' => 96 );

		$result = $this->handler->filter_avatar_url( $url, 1, $args );

		// Should return a data URL for SVG, not the original URL.
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result );
		$this->assertNotEquals( $url, $result );
	}

	/**
	 * Test that filter_avatar_url does not return broken URL like "2x".
	 */
	public function test_filter_avatar_url_does_not_return_broken_url() {
		$broken_url = 'http://localhost:8080/wp-admin/2x';
		$args = array( 'size' => 96 );

		$result = $this->handler->filter_avatar_url( $broken_url, 1, $args );

		// Should not return the broken URL.
		$this->assertNotEquals( $broken_url, $result );

		// Should return a valid data URL.
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result );
	}

	/**
	 * Test that initials avatar URL is valid SVG data URL.
	 */
	public function test_initials_avatar_url_is_valid_svg() {
		$url = 'https://gravatar.com/avatar/test';
		$args = array( 'size' => 96 );

		$result = $this->handler->filter_avatar_url( $url, 1, $args );

		// Decode the data URL to check if it's valid SVG.
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result );

		// Extract the SVG content.
		$svg_content = rawurldecode( substr( $result, strlen( 'data:image/svg+xml;charset=utf-8,' ) ) );

		// Should contain SVG tags.
		$this->assertStringContainsString( '<svg', $svg_content );
		$this->assertStringContainsString( '</svg>', $svg_content );
		$this->assertStringContainsString( '<rect', $svg_content );
		$this->assertStringContainsString( '<text', $svg_content );
	}

	/**
	 * Test that different sizes generate appropriate SVG sizes.
	 */
	public function test_initials_avatar_respects_size_parameter() {
		$sizes = array( 32, 64, 96, 128, 256 );

		foreach ( $sizes as $size ) {
			$url = 'https://gravatar.com/avatar/test';
			$args = array( 'size' => $size );

			$result = $this->handler->filter_avatar_url( $url, 1, $args );

			// Extract SVG content.
			$svg_content = rawurldecode( substr( $result, strlen( 'data:image/svg+xml;charset=utf-8,' ) ) );

			// Check that SVG has correct width and height.
			$this->assertStringContainsString( "width=\"$size\"", $svg_content, "SVG width should be $size" );
			$this->assertStringContainsString( "height=\"$size\"", $svg_content, "SVG height should be $size" );
		}
	}

	/**
	 * Test avatar removal flow with filter_avatar_data.
	 */
	public function test_avatar_removal_flow_with_filter_avatar_data() {
		$user_id = 123;

		// Initially, user has no avatar.
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, $user_id );

		// Should return initials avatar.
		$this->assertArrayHasKey( 'found_avatar', $result );
		$this->assertTrue( $result['found_avatar'] );
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result['url'] );
	}

	/**
	 * Test avatar removal flow with filter_avatar_url.
	 */
	public function test_avatar_removal_flow_with_filter_avatar_url() {
		$user_id = 456;
		$url = 'https://gravatar.com/avatar/test';
		$args = array( 'size' => 96 );

		// Initially, user has no avatar.
		$result = $this->handler->filter_avatar_url( $url, $user_id, $args );

		// Should return initials avatar, not the original URL.
		$this->assertStringStartsWith( 'data:image/svg+xml;charset=utf-8,', $result );
		$this->assertNotEquals( $url, $result );
	}

	/**
	 * Test that handler returns original URL if generator is not set.
	 */
	public function test_returns_original_url_if_no_generator() {
		// Create handler without generator.
		$upload_service = new UploadService();
		$handler_no_gen = new AvatarHandler( $upload_service );
		$handler_no_gen->init();

		$url = 'https://gravatar.com/avatar/test';
		$args = array( 'size' => 96 );

		$result = $handler_no_gen->filter_avatar_url( $url, 1, $args );

		// Should return original URL when no generator is available.
		$this->assertEquals( $url, $result );
	}

	/**
	 * Test that handler gracefully handles invalid user IDs.
	 */
	public function test_handles_invalid_user_ids_gracefully() {
		$url = 'https://gravatar.com/avatar/test';
		$args = array( 'size' => 96 );

		// Test with zero.
		$result_zero = $this->handler->filter_avatar_url( $url, 0, $args );
		$this->assertEquals( $url, $result_zero );

		// Test with negative.
		$result_negative = $this->handler->filter_avatar_url( $url, -1, $args );
		$this->assertEquals( $url, $result_negative );

		// Test with null.
		$result_null = $this->handler->filter_avatar_url( $url, null, $args );
		$this->assertEquals( $url, $result_null );
	}
}
