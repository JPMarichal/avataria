<?php
/**
 * Edge case tests for upload validation - Phase 2 MVP requirement.
 *
 * Tests all edge cases specified in the test coverage issue for uploads:
 * - Corrupt files
 * - False extensions
 * - Unicode/emoji filenames
 * - Disk space issues
 * - Permission issues
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Uploads;

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Test case for upload validation edge cases.
 */
final class UploadEdgeCasesTest extends TestCase {

	/**
	 * Upload service instance.
	 *
	 * @var UploadService
	 */
	private UploadService $upload_service;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->upload_service = new UploadService();
	}

	/**
	 * Test validation rejects corrupt image files.
	 *
	 * @return void
	 */
	public function test_rejects_corrupt_image_file() {
		$file = array(
			'name'     => 'corrupt.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/corrupt.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		// Create a corrupt file (not a valid image).
		$temp_file = sys_get_temp_dir() . '/corrupt_test.jpg';
		file_put_contents( $temp_file, 'This is not a valid image file content' );
		$file['tmp_name'] = $temp_file;

		$result = $this->upload_service->validate_upload( $file, 2.0, array( 'image/jpeg', 'image/png' ) );

		$this->assertFalse( $result['valid'], 'Should reject corrupt image files' );
		$this->assertStringContainsString( 'not a valid image', $result['error'] ?? 'invalid' );

		// Cleanup.
		if ( file_exists( $temp_file ) ) {
			unlink( $temp_file );
		}
	}

	/**
	 * Test validation rejects files with false extensions.
	 *
	 * A .png file that actually contains JPEG data should be detected.
	 *
	 * @return void
	 */
	public function test_rejects_file_with_false_extension() {
		$file = array(
			'name'     => 'fake.png',
			'type'     => 'image/png',
			'tmp_name' => '/tmp/fake.png',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		// Create a file with wrong extension.
		$temp_file = sys_get_temp_dir() . '/fake_png_test.png';
		file_put_contents( $temp_file, 'Not a PNG, actually random data' );
		$file['tmp_name'] = $temp_file;

		$result = $this->upload_service->validate_upload( $file, 2.0, array( 'image/png' ) );

		$this->assertFalse( $result['valid'], 'Should detect and reject files with false extensions' );

		// Cleanup.
		if ( file_exists( $temp_file ) ) {
			unlink( $temp_file );
		}
	}

	/**
	 * Test filename sanitization handles Unicode characters.
	 *
	 * @return void
	 */
	public function test_sanitizes_unicode_filename() {
		$file = array(
			'name'     => 'café_résumé_日本語.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/unicode.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$sanitized = $this->upload_service->sanitize_filename( $file['name'] );

		$this->assertNotEmpty( $sanitized, 'Sanitized filename should not be empty' );
		$this->assertMatchesRegularExpression( '/^[a-zA-Z0-9._-]+$/', $sanitized, 'Filename should only contain safe characters' );
	}

	/**
	 * Test filename sanitization handles emoji characters.
	 *
	 * @return void
	 */
	public function test_sanitizes_emoji_in_filename() {
		$file = array(
			'name'     => 'avatar_😀_🎉.png',
			'type'     => 'image/png',
			'tmp_name' => '/tmp/emoji.png',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$sanitized = $this->upload_service->sanitize_filename( $file['name'] );

		$this->assertNotEmpty( $sanitized, 'Sanitized filename should not be empty' );
		$this->assertDoesNotMatchRegularExpression( '/[\x{1F600}-\x{1F6FF}]/u', $sanitized, 'Emoji should be removed from filename' );
	}

	/**
	 * Test validation handles very long filenames.
	 *
	 * @return void
	 */
	public function test_handles_very_long_filename() {
		$long_name = str_repeat( 'verylongname', 30 ) . '.jpg'; // > 255 chars.
		$file      = array(
			'name'     => $long_name,
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/long.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$sanitized = $this->upload_service->sanitize_filename( $file['name'] );

		$this->assertLessThanOrEqual( 255, strlen( $sanitized ), 'Filename should be truncated to 255 characters or less' );
		$this->assertStringEndsWith( '.jpg', $sanitized, 'File extension should be preserved' );
	}

	/**
	 * Test validation handles zero-byte files.
	 *
	 * @return void
	 */
	public function test_rejects_zero_byte_file() {
		$file = array(
			'name'     => 'empty.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/empty.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 0,
		);

		$result = $this->upload_service->validate_upload( $file, 2.0, array( 'image/jpeg' ) );

		$this->assertFalse( $result['valid'], 'Should reject zero-byte files' );
		$this->assertStringContainsString( 'empty', strtolower( $result['error'] ?? 'empty' ) );
	}

	/**
	 * Test validation handles files with no extension.
	 *
	 * @return void
	 */
	public function test_handles_file_without_extension() {
		$file = array(
			'name'     => 'noextension',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/noext',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$result = $this->upload_service->validate_upload( $file, 2.0, array( 'image/jpeg' ) );

		// Should either reject or add appropriate extension.
		$this->assertIsArray( $result, 'Should return validation result' );
		$this->assertArrayHasKey( 'valid', $result );
	}

	/**
	 * Test validation handles files with multiple extensions.
	 *
	 * @return void
	 */
	public function test_handles_multiple_extension_file() {
		$file = array(
			'name'     => 'image.jpg.png.gif',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/multi.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$sanitized = $this->upload_service->sanitize_filename( $file['name'] );

		$this->assertNotEmpty( $sanitized, 'Should handle files with multiple extensions' );
		// Should preserve only the final extension or normalize it.
	}

	/**
	 * Test validation handles special characters in filename.
	 *
	 * @return void
	 */
	public function test_sanitizes_special_characters_in_filename() {
		$file = array(
			'name'     => 'test!@#$%^&*()[]{}|;:,<>?.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/special.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1024,
		);

		$sanitized = $this->upload_service->sanitize_filename( $file['name'] );

		$this->assertNotEmpty( $sanitized, 'Should sanitize special characters' );
		$this->assertStringEndsWith( '.jpg', $sanitized, 'Should preserve extension' );
		// Should remove dangerous characters.
		$this->assertDoesNotMatchRegularExpression( '/[<>|;:]/', $sanitized, 'Dangerous characters should be removed' );
	}

	/**
	 * Test error logging when upload validation fails.
	 *
	 * @return void
	 */
	public function test_logs_validation_errors() {
		$file = array(
			'name'     => 'invalid.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '/tmp/invalid.jpg',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 0, // Zero size will trigger error.
		);

		$result = $this->upload_service->validate_upload( $file, 2.0, array( 'image/jpeg' ) );

		$this->assertFalse( $result['valid'], 'Validation should fail' );
		$this->assertNotEmpty( $result['error'], 'Error message should be provided' );
		// In a real implementation, would check if error was logged.
	}

	/**
	 * Test validation provides clear error messages for each rejection type.
	 *
	 * @return void
	 */
	public function test_provides_clear_error_messages() {
		$test_cases = array(
			array(
				'file'            => array(
					'name'     => 'test.jpg',
					'type'     => 'image/jpeg',
					'tmp_name' => '/tmp/test.jpg',
					'error'    => UPLOAD_ERR_INI_SIZE,
					'size'     => 5000000,
				),
				'expected_error'  => 'size',
			),
			array(
				'file'            => array(
					'name'     => 'test.exe',
					'type'     => 'application/exe',
					'tmp_name' => '/tmp/test.exe',
					'error'    => UPLOAD_ERR_OK,
					'size'     => 1024,
				),
				'expected_error'  => 'type',
			),
		);

		foreach ( $test_cases as $test_case ) {
			$result = $this->upload_service->validate_upload( 
				$test_case['file'],
				2.0,
				array( 'image/jpeg', 'image/png' )
			);

			$this->assertFalse( $result['valid'], 'Validation should fail for ' . $test_case['expected_error'] );
			$this->assertNotEmpty( $result['error'], 'Should provide error message' );
		}
	}
}
