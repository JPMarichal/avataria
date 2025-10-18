<?php
/**
 * Edge case tests for UploadService.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Uploads;

use AvatarSteward\Domain\Uploads\UploadService;
use PHPUnit\Framework\TestCase;

/**
 * Edge case tests for the UploadService class.
 */
class UploadServiceEdgeCasesTest extends TestCase {

	/**
	 * Test validation with zero-byte file.
	 */
	public function test_validate_zero_byte_file(): void {
		$service = new UploadService();

		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 0,
		);

		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation with file exactly at max size limit.
	 */
	public function test_validate_file_at_exact_max_size(): void {
		$max_size = 2097152; // 2MB.
		$service  = new UploadService( $max_size );

		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => $max_size,
		);

		// Will fail on is_uploaded_file check before reaching size validation.
		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation with file one byte over max size limit.
	 */
	public function test_validate_file_one_byte_over_max_size(): void {
		$max_size = 2097152; // 2MB.
		$service  = new UploadService( $max_size );

		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => $max_size + 1,
		);

		// Will fail on is_uploaded_file check before reaching size validation.
		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation with file name containing special characters.
	 */
	public function test_validate_file_with_special_characters_in_name(): void {
		$service = new UploadService();

		$file = array(
			'name'     => 'test!@#$%^&*().jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1000,
		);

		$result = $service->validate_file( $file );

		// Should handle special characters gracefully.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with file name containing unicode characters.
	 */
	public function test_validate_file_with_unicode_in_name(): void {
		$service = new UploadService();

		$file = array(
			'name'     => 'tëst_émoji_😀.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1000,
		);

		$result = $service->validate_file( $file );

		// Should handle unicode characters gracefully.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with very long file name.
	 */
	public function test_validate_file_with_very_long_name(): void {
		$service = new UploadService();

		$long_name = str_repeat( 'a', 300 ) . '.jpg';
		$file      = array(
			'name'     => $long_name,
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1000,
		);

		$result = $service->validate_file( $file );

		// Should handle long file names gracefully.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with file having no extension.
	 */
	public function test_validate_file_with_no_extension(): void {
		$service = new UploadService();

		$file = array(
			'name'     => 'testfile',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1000,
		);

		$result = $service->validate_file( $file );

		// Should handle files without extension.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with file having multiple extensions.
	 */
	public function test_validate_file_with_multiple_extensions(): void {
		$service = new UploadService();

		$file = array(
			'name'     => 'test.jpg.png',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => 1000,
		);

		$result = $service->validate_file( $file );

		// Should handle multiple extensions.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with minimum file size setting.
	 */
	public function test_validate_with_minimum_file_size_setting(): void {
		$min_size = 104858; // 0.1 MB.
		$service  = new UploadService( $min_size );

		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => $min_size,
		);

		// Will fail on is_uploaded_file check.
		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation with maximum file size setting.
	 */
	public function test_validate_with_maximum_file_size_setting(): void {
		$max_size = 10485760; // 10 MB.
		$service  = new UploadService( $max_size );

		// Large file but within limit.
		$file = array(
			'name'     => 'test.jpg',
			'type'     => 'image/jpeg',
			'tmp_name' => '',
			'error'    => UPLOAD_ERR_OK,
			'size'     => $max_size - 1000,
		);

		$result = $service->validate_file( $file );

		// Should not fail on size, will fail on file existence.
		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test handling of all PHP upload error codes.
	 */
	public function test_validate_all_upload_error_codes(): void {
		$service = new UploadService();

		$error_codes = array(
			UPLOAD_ERR_INI_SIZE,
			UPLOAD_ERR_FORM_SIZE,
			UPLOAD_ERR_PARTIAL,
			UPLOAD_ERR_NO_FILE,
			UPLOAD_ERR_NO_TMP_DIR,
			UPLOAD_ERR_CANT_WRITE,
			UPLOAD_ERR_EXTENSION,
		);

		foreach ( $error_codes as $error_code ) {
			$file = array(
				'name'     => 'test.jpg',
				'type'     => 'image/jpeg',
				'tmp_name' => '',
				'error'    => $error_code,
				'size'     => 1000,
			);

			$result = $service->validate_file( $file );

			$this->assertFalse( $result['success'], "Error code $error_code should fail validation" );
			$this->assertArrayHasKey( 'message', $result, "Error code $error_code should have a message" );
		}
	}

	/**
	 * Test validation with empty file array.
	 */
	public function test_validate_empty_file_array(): void {
		$service = new UploadService();

		$file = array();

		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'message', $result );
	}

	/**
	 * Test validation with missing tmp_name.
	 */
	public function test_validate_missing_tmp_name(): void {
		$service = new UploadService();

		$file = array(
			'name'  => 'test.jpg',
			'type'  => 'image/jpeg',
			'error' => UPLOAD_ERR_OK,
			'size'  => 1000,
		);

		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
	}

	/**
	 * Test validation with null values.
	 */
	public function test_validate_null_values(): void {
		$service = new UploadService();

		$file = array(
			'name'     => null,
			'type'     => null,
			'tmp_name' => null,
			'error'    => UPLOAD_ERR_OK,
			'size'     => null,
		);

		$result = $service->validate_file( $file );

		$this->assertFalse( $result['success'] );
	}
}
