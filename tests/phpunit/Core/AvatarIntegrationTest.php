<?php
/**
 * Integration test for WordPress avatar system.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Integration test case for WordPress avatar compatibility.
 */
final class AvatarIntegrationTest extends TestCase {

	/**
	 * Avatar handler instance.
	 *
	 * @var AvatarHandler
	 */
	private AvatarHandler $handler;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$upload_service = new UploadService();
		$this->handler = new AvatarHandler( $upload_service );
		$this->handler->init();
	}

	/**
	 * Test that filter is properly registered on init.
	 */
	public function test_filters_are_registered() {
		$this->assertTrue( method_exists( $this->handler, 'init' ) );
		$this->assertTrue( method_exists( $this->handler, 'filter_avatar_data' ) );
		$this->assertTrue( method_exists( $this->handler, 'filter_avatar_url' ) );
	}

	/**
	 * Test avatar data structure matches WordPress expectations.
	 */
	public function test_avatar_data_structure() {
		$args = array(
			'url'          => 'https://gravatar.com/avatar/test',
			'size'         => 96,
			'default'      => 'mystery',
			'force_default' => false,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		// Verify structure is maintained.
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
		$this->assertArrayHasKey( 'size', $result );
		$this->assertEquals( 96, $result['size'] );
	}

	/**
	 * Test that found_avatar flag is set when local avatar exists.
	 */
	public function test_found_avatar_flag_behavior() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		// When no local avatar exists, found_avatar should not be set.
		$result = $this->handler->filter_avatar_data( $args, 999999 );
		$this->assertArrayNotHasKey( 'found_avatar', $result );
	}

	/**
	 * Test URL structure for different sizes.
	 */
	public function test_url_structure_for_different_sizes() {
		$url = 'https://gravatar.com/avatar/test';
		
		// Test small size.
		$result_small = $this->handler->filter_avatar_url( 
			$url, 
			1, 
			array( 'size' => 48 ) 
		);
		$this->assertIsString( $result_small );

		// Test medium size.
		$result_medium = $this->handler->filter_avatar_url(
			$url,
			1,
			array( 'size' => 150 )
		);
		$this->assertIsString( $result_medium );

		// Test large size.
		$result_large = $this->handler->filter_avatar_url(
			$url,
			1,
			array( 'size' => 500 )
		);
		$this->assertIsString( $result_large );
	}

	/**
	 * Test that default size is used when size is not provided.
	 */
	public function test_default_size_handling() {
		$args_no_size = array(
			'url' => 'https://gravatar.com/avatar/test',
		);

		// Should handle missing size gracefully.
		$result = $this->handler->filter_avatar_data( $args_no_size, 1 );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test comment object structure compatibility.
	 */
	public function test_comment_object_compatibility() {
		// Simulate WP_Comment structure.
		$comment = (object) array(
			'comment_ID'        => 1,
			'comment_post_ID'   => 10,
			'comment_author'    => 'Test User',
			'comment_author_email' => 'test@example.com',
			'user_id'           => 5,
			'comment_date'      => '2024-01-01 12:00:00',
		);

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 64,
		);

		$result = $this->handler->filter_avatar_data( $args, $comment );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test user object structure compatibility.
	 */
	public function test_user_object_compatibility() {
		// Simulate WP_User structure.
		$user = (object) array(
			'ID'              => 3,
			'user_login'      => 'testuser',
			'user_nicename'   => 'Test User',
			'user_email'      => 'test@example.com',
			'user_registered' => '2024-01-01 00:00:00',
		);

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, $user );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test post object structure compatibility.
	 */
	public function test_post_object_compatibility() {
		// Simulate WP_Post structure.
		$post = (object) array(
			'ID'           => 42,
			'post_author'  => 7,
			'post_title'   => 'Test Post',
			'post_content' => 'Test content',
			'post_date'    => '2024-01-01 12:00:00',
			'post_status'  => 'publish',
		);

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, $post );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test handling of invalid identifier types.
	 */
	public function test_invalid_identifier_handling() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		// Test with null.
		$result_null = $this->handler->filter_avatar_data( $args, null );
		$this->assertEquals( $args, $result_null );

		// Test with empty string.
		$result_empty = $this->handler->filter_avatar_data( $args, '' );
		$this->assertEquals( $args, $result_empty );

		// Test with zero.
		$result_zero = $this->handler->filter_avatar_data( $args, 0 );
		$this->assertEquals( $args, $result_zero );

		// Test with negative number.
		$result_negative = $this->handler->filter_avatar_data( $args, -1 );
		$this->assertEquals( $args, $result_negative );
	}

	/**
	 * Test that original URL is preserved when no local avatar.
	 */
	public function test_original_url_preservation() {
		$original_url = 'https://gravatar.com/avatar/d4c74594d841139328695756648b6bd6?s=96&d=mystery';
		$args = array(
			'url'  => $original_url,
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );
		// When no local avatar exists, original URL should be preserved.
		$this->assertEquals( $original_url, $result['url'] );
	}

	/**
	 * Test email-based identifier.
	 */
	public function test_email_identifier() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		// Test with valid email format.
		$result = $this->handler->filter_avatar_data( $args, 'test@example.com' );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test that size parameter is properly passed through.
	 */
	public function test_size_parameter_passthrough() {
		$sizes = array( 32, 48, 64, 96, 128, 150, 256, 512 );

		foreach ( $sizes as $size ) {
			$args = array(
				'url'  => 'https://gravatar.com/avatar/test',
				'size' => $size,
			);

			$result = $this->handler->filter_avatar_data( $args, 1 );
			$this->assertEquals( $size, $result['size'], "Size $size not preserved" );
		}
	}
}
