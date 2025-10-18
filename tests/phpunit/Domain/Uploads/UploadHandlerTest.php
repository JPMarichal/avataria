<?php
/**
 * Tests for UploadHandler class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Uploads\UploadHandler;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Test case for UploadHandler class.
 */
final class UploadHandlerTest extends TestCase {

	/**
	 * Test that UploadHandler class exists.
	 */
	public function test_upload_handler_class_exists() {
		$this->assertTrue( class_exists( UploadHandler::class ) );
	}

	/**
	 * Test that UploadHandler can be instantiated.
	 */
	public function test_upload_handler_can_be_instantiated() {
		$service = new UploadService();
		$handler = new UploadHandler( $service );
		$this->assertInstanceOf( UploadHandler::class, $handler );
	}

	/**
	 * Test that handler has register_hooks method.
	 */
	public function test_handler_has_register_hooks_method() {
		$service = new UploadService();
		$handler = new UploadHandler( $service );
		$this->assertTrue( method_exists( $handler, 'register_hooks' ) );
	}

	/**
	 * Test that handler has handle_profile_update method.
	 */
	public function test_handler_has_handle_profile_update_method() {
		$service = new UploadService();
		$handler = new UploadHandler( $service );
		$this->assertTrue( method_exists( $handler, 'handle_profile_update' ) );
	}
}
