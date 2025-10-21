<?php
/**
 * Tests for LibraryService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Library;

use AvatarSteward\Domain\Library\LibraryService;
use PHPUnit\Framework\TestCase;

/**
 * Test LibraryService functionality.
 */
class LibraryServiceTest extends TestCase {

	/**
	 * Test that LibraryService can be instantiated.
	 */
	public function test_library_service_can_be_instantiated(): void {
		$service = new LibraryService();

		$this->assertInstanceOf( LibraryService::class, $service );
	}

	/**
	 * Test adding an avatar to the library.
	 */
	public function test_add_to_library_returns_true_on_success(): void {
		$service = new LibraryService();

		// Mock attachment exists.
		$result = $service->add_to_library(
			1,
			array(
				'author'  => 'Test Author',
				'license' => 'GPL',
				'sector'  => 'Technology',
				'tags'    => array( 'test', 'avatar' ),
			)
		);

		$this->assertTrue( $result );
	}

	/**
	 * Test that add_to_library returns false for non-existent attachment.
	 */
	public function test_add_to_library_returns_false_for_invalid_attachment(): void {
		$service = new LibraryService();

		$result = $service->add_to_library( 99999 );

		$this->assertFalse( $result );
	}

	/**
	 * Test removing an avatar from the library.
	 */
	public function test_remove_from_library_returns_true(): void {
		$service = new LibraryService();

		$result = $service->remove_from_library( 1 );

		$this->assertTrue( $result );
	}

	/**
	 * Test getting library avatars returns expected structure.
	 */
	public function test_get_library_avatars_returns_expected_structure(): void {
		$service = new LibraryService();

		$result = $service->get_library_avatars();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'items', $result );
		$this->assertArrayHasKey( 'total', $result );
		$this->assertArrayHasKey( 'page', $result );
		$this->assertArrayHasKey( 'per_page', $result );
		$this->assertArrayHasKey( 'total_pages', $result );
	}

	/**
	 * Test pagination in library avatars.
	 */
	public function test_get_library_avatars_respects_pagination(): void {
		$service = new LibraryService();

		$result = $service->get_library_avatars(
			array(
				'page'     => 2,
				'per_page' => 10,
			)
		);

		$this->assertEquals( 2, $result['page'] );
		$this->assertEquals( 10, $result['per_page'] );
	}

	/**
	 * Test search filter in library avatars.
	 */
	public function test_get_library_avatars_accepts_search_filter(): void {
		$service = new LibraryService();

		$result = $service->get_library_avatars(
			array(
				'search' => 'test',
			)
		);

		$this->assertIsArray( $result['items'] );
	}

	/**
	 * Test metadata filters in library avatars.
	 */
	public function test_get_library_avatars_accepts_metadata_filters(): void {
		$service = new LibraryService();

		$result = $service->get_library_avatars(
			array(
				'author'  => 'Test Author',
				'license' => 'GPL',
				'sector'  => 'Technology',
			)
		);

		$this->assertIsArray( $result['items'] );
	}

	/**
	 * Test getting a single library avatar.
	 */
	public function test_get_library_avatar_returns_null_for_non_library_avatar(): void {
		$service = new LibraryService();

		$result = $service->get_library_avatar( 1 );

		$this->assertNull( $result );
	}

	/**
	 * Test getting sectors.
	 */
	public function test_get_sectors_returns_array(): void {
		$service = new LibraryService();

		$sectors = $service->get_sectors();

		$this->assertIsArray( $sectors );
	}

	/**
	 * Test getting licenses.
	 */
	public function test_get_licenses_returns_array(): void {
		$service = new LibraryService();

		$licenses = $service->get_licenses();

		$this->assertIsArray( $licenses );
	}

	/**
	 * Test importing sectoral templates.
	 */
	public function test_import_sectoral_templates_returns_result_structure(): void {
		$service = new LibraryService();

		$result = $service->import_sectoral_templates( 'Technology', array() );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertArrayHasKey( 'failed', $result );
		$this->assertArrayHasKey( 'errors', $result );
	}

	/**
	 * Test importing with non-existent files.
	 */
	public function test_import_sectoral_templates_handles_missing_files(): void {
		$service = new LibraryService();

		$result = $service->import_sectoral_templates(
			'Technology',
			array( '/non/existent/file.jpg' )
		);

		$this->assertEquals( 0, $result['success'] );
		$this->assertEquals( 1, $result['failed'] );
		$this->assertNotEmpty( $result['errors'] );
	}

	/**
	 * Test that constants are defined.
	 */
	public function test_constants_are_defined(): void {
		$this->assertEquals( 'avatar_author', LibraryService::TAXONOMY_AUTHOR );
		$this->assertEquals( 'avatar_license', LibraryService::TAXONOMY_LICENSE );
		$this->assertEquals( 'avatar_tags', LibraryService::TAXONOMY_TAGS );
		$this->assertEquals( 'avatar_sector', LibraryService::TAXONOMY_SECTOR );
		$this->assertEquals( 'avatar_steward_library_avatar', LibraryService::META_IS_LIBRARY_AVATAR );
		$this->assertEquals( 'avatar_library', LibraryService::CACHE_GROUP );
		$this->assertEquals( 3600, LibraryService::CACHE_EXPIRATION );
	}
}
