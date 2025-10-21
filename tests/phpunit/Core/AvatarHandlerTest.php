<?php
/**
 * Test case for AvatarHandler class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Test case for AvatarHandler class.
 */
final class AvatarHandlerTest extends TestCase {

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
	}

	/**
	 * Test that AvatarHandler class exists.
	 */
	public function test_avatar_handler_class_exists() {
		$this->assertTrue( class_exists( AvatarHandler::class ) );
	}

	/**
	 * Test that AvatarHandler can be instantiated.
	 */
	public function test_avatar_handler_can_be_instantiated() {
		$upload_service = new UploadService();
		$handler = new AvatarHandler( $upload_service );
		$this->assertInstanceOf( AvatarHandler::class, $handler );
	}

	/**
	 * Test that init method exists and can be called.
	 */
	public function test_avatar_handler_has_init_method() {
		$this->assertTrue( method_exists( $this->handler, 'init' ) );
		$this->handler->init();
		$this->assertTrue( true ); // If we get here, init() didn't throw an error.
	}

	/**
	 * Test filter_avatar_data method exists.
	 */
	public function test_filter_avatar_data_method_exists() {
		$this->assertTrue( method_exists( $this->handler, 'filter_avatar_data' ) );
	}

	/**
	 * Test filter_avatar_data returns array when user ID is not found.
	 */
	public function test_filter_avatar_data_returns_unchanged_args_for_invalid_user() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 'invalid@example.com' );

		$this->assertIsArray( $result );
		$this->assertEquals( $args['url'], $result['url'] );
	}

	/**
	 * Test filter_avatar_data with numeric user ID.
	 */
	public function test_filter_avatar_data_with_numeric_user_id() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test filter_avatar_url method exists.
	 */
	public function test_filter_avatar_url_method_exists() {
		$this->assertTrue( method_exists( $this->handler, 'filter_avatar_url' ) );
	}

	/**
	 * Test filter_avatar_url returns string.
	 */
	public function test_filter_avatar_url_returns_string() {
		$url    = 'https://gravatar.com/avatar/test';
		$args   = array( 'size' => 96 );
		$result = $this->handler->filter_avatar_url( $url, 1, $args );

		$this->assertIsString( $result );
	}

	/**
	 * Test filter_avatar_url returns original URL when no local avatar.
	 */
	public function test_filter_avatar_url_returns_original_url_when_no_local_avatar() {
		$url    = 'https://gravatar.com/avatar/test';
		$args   = array( 'size' => 96 );
		$result = $this->handler->filter_avatar_url( $url, 1, $args );

		$this->assertEquals( $url, $result );
	}

	/**
	 * Test set_local_avatar method exists.
	 */
	public function test_set_local_avatar_method_exists() {
		$this->assertTrue( method_exists( $this->handler, 'set_local_avatar' ) );
	}

	/**
	 * Test delete_local_avatar method exists.
	 */
	public function test_delete_local_avatar_method_exists() {
		$this->assertTrue( method_exists( $this->handler, 'delete_local_avatar' ) );
	}

	/**
	 * Test has_local_avatar method exists.
	 */
	public function test_has_local_avatar_method_exists() {
		$this->assertTrue( method_exists( $this->handler, 'has_local_avatar' ) );
	}

	/**
	 * Test has_local_avatar returns false for user without avatar.
	 */
	public function test_has_local_avatar_returns_false_for_user_without_avatar() {
		$result = $this->handler->has_local_avatar( 1 );

		$this->assertIsBool( $result );
		$this->assertFalse( $result );
	}

	/**
	 * Test set_local_avatar returns boolean.
	 */
	public function test_set_local_avatar_returns_boolean() {
		$result = $this->handler->set_local_avatar( 1, 123 );

		$this->assertIsBool( $result );
	}

	/**
	 * Test delete_local_avatar returns boolean.
	 */
	public function test_delete_local_avatar_returns_boolean() {
		$result = $this->handler->delete_local_avatar( 1 );

		$this->assertIsBool( $result );
	}

	/**
	 * Test filter_avatar_data with WP_Comment-like object.
	 */
	public function test_filter_avatar_data_with_comment_object() {
		$comment = (object) array(
			'user_id'      => 5,
			'comment_ID'   => 10,
			'comment_post_ID' => 1,
		);

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, $comment );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test filter_avatar_data with WP_User-like object.
	 */
	public function test_filter_avatar_data_with_user_object() {
		$user = (object) array(
			'ID'         => 3,
			'user_login' => 'testuser',
			'user_email' => 'test@example.com',
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
	 * Test filter_avatar_data with WP_Post-like object.
	 */
	public function test_filter_avatar_data_with_post_object() {
		$post = (object) array(
			'ID'          => 42,
			'post_author' => 7,
			'post_title'  => 'Test Post',
		);

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, $post );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}
}
