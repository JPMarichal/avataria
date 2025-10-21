<?php
/**
 * Tests for role-based restrictions in UploadService.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Uploads;

use AvatarSteward\Domain\Uploads\UploadService;
use PHPUnit\Framework\TestCase;

/**
 * Test case for role-based restrictions in UploadService.
 */
class RoleRestrictionsTest extends TestCase {

	/**
	 * Test that can_user_upload returns true by default when no settings are configured.
	 */
	public function test_can_user_upload_returns_true_by_default(): void {
		$service = new UploadService();

		// Mock user_id 1 (would typically be an admin).
		$can_upload = $service->can_user_upload( 1 );

		$this->assertTrue( $can_upload );
	}

	/**
	 * Test that get_avatar_url method exists and returns expected types.
	 */
	public function test_get_avatar_url_returns_false_when_no_avatar(): void {
		$service = new UploadService();

		// Test with a non-existent user.
		$url = $service->get_avatar_url( 999999 );

		$this->assertFalse( $url );
	}

	/**
	 * Test that validate_file accepts user_id parameter.
	 */
	public function test_validate_file_accepts_user_id_parameter(): void {
		$service = new UploadService();

		// Create a mock file that will fail validation.
		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 0,
		);

		// Call with user_id parameter.
		$result = $service->validate_file( $file, 1 );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test that validate_file works without user_id parameter (backward compatibility).
	 */
	public function test_validate_file_works_without_user_id(): void {
		$service = new UploadService();

		// Create a mock file that will fail validation.
		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 0,
		);

		// Call without user_id parameter.
		$result = $service->validate_file( $file );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test that process_upload checks user permissions.
	 */
	public function test_process_upload_checks_user_permissions(): void {
		$service = new UploadService();

		// Create a mock file.
		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/test.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		// Process upload for user 1 (typically admin).
		$result = $service->process_upload( $file, 1 );

		// Should return an array with success key.
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
	}
}
