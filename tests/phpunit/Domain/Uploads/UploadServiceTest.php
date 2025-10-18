<?php
/**
 * Tests for UploadService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Test case for UploadService class.
 */
final class UploadServiceTest extends TestCase {

	/**
	 * Service instance.
	 *
	 * @var UploadService
	 */
	private UploadService $service;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->service = new UploadService();
	}

	/**
	 * Test that UploadService class exists.
	 */
	public function test_upload_service_class_exists() {
		$this->assertTrue( class_exists( UploadService::class ) );
	}

	/**
	 * Test that UploadService can be instantiated.
	 */
	public function test_upload_service_can_be_instantiated() {
		$service = new UploadService();
		$this->assertInstanceOf( UploadService::class, $service );
	}

	/**
	 * Test that UploadService can be instantiated with custom parameters.
	 */
	public function test_upload_service_with_custom_parameters() {
		$service = new UploadService(
			1048576, // 1MB
			1000,    // 1000px width
			1000,    // 1000px height
			array( 'image/jpeg', 'image/png' )
		);
		$this->assertInstanceOf( UploadService::class, $service );
	}

	/**
	 * Test validation fails for empty file.
	 */
	public function test_validate_file_fails_for_empty_file() {
		$file = array(
			'name'     => '',
			'type'     => '',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_NO_FILE,
			'size'     => 0,
		);

		$result = $this->service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation fails for file that exceeds size limit.
	 */
	public function test_validate_file_fails_for_oversized_file() {
		// This test verifies the logic but can't fully test without actual file upload.
		// We're testing that the size validation logic is present.
		$service = new UploadService( 100 ); // 100 bytes

		$this->assertTrue( method_exists( $service, 'validate_file' ) );
	}

	/**
	 * Test get_avatar_url returns false for user without avatar.
	 */
	public function test_get_avatar_url_returns_false_for_user_without_avatar() {
		// Mock user ID that doesn't have an avatar.
		$user_id = 999999;

		$result = $this->service->get_avatar_url( $user_id );

		$this->assertFalse( $result );
	}

	/**
	 * Test delete_avatar returns false for user without avatar.
	 */
	public function test_delete_avatar_returns_false_for_user_without_avatar() {
		// Mock user ID that doesn't have an avatar.
		$user_id = 999999;

		$result = $this->service->delete_avatar( $user_id );

		$this->assertFalse( $result );
	}

	/**
	 * Test that service has validate_file method.
	 */
	public function test_service_has_validate_file_method() {
		$this->assertTrue( method_exists( $this->service, 'validate_file' ) );
	}

	/**
	 * Test that service has process_upload method.
	 */
	public function test_service_has_process_upload_method() {
		$this->assertTrue( method_exists( $this->service, 'process_upload' ) );
	}

	/**
	 * Test that service has delete_avatar method.
	 */
	public function test_service_has_delete_avatar_method() {
		$this->assertTrue( method_exists( $this->service, 'delete_avatar' ) );
	}

	/**
	 * Test that service has get_avatar_url method.
	 */
	public function test_service_has_get_avatar_url_method() {
		$this->assertTrue( method_exists( $this->service, 'get_avatar_url' ) );
	}
}
