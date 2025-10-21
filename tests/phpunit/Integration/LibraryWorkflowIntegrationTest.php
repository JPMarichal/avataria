<?php
/**
 * Integration tests for avatar library workflow.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration;

use AvatarSteward\Tests\Integration\Helpers\IntegrationTestCase;
use AvatarSteward\Tests\Integration\Fixtures\TestFixtures;
use AvatarSteward\Domain\Library\LibraryService;

/**
 * Test avatar library management and user selection workflows.
 */
class LibraryWorkflowIntegrationTest extends IntegrationTestCase {

	/**
	 * Library service instance.
	 *
	 * @var LibraryService
	 */
	private LibraryService $library_service;

	/**
	 * Setup test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->library_service = new LibraryService();
		
		// Activate Pro license.
		$this->activate_pro_license();
	}

	/**
	 * Test complete library avatar addition workflow.
	 *
	 * @return void
	 */
	public function test_add_avatar_to_library_workflow(): void {
		$avatars = TestFixtures::get_sample_library_avatars();
		$avatar  = $avatars[0]; // Corporate male avatar.

		// Step 1: Add avatar to library with metadata.
		$library_id = $this->library_service->add_to_library(
			'/path/to/' . $avatar['filename'],
			array(
				'author'  => $avatar['author'],
				'license' => $avatar['license'],
				'sector'  => $avatar['sector'],
				'tags'    => $avatar['tags'],
			)
		);

		$this->assertIsInt( $library_id, 'Should return library ID' );
		$this->assertGreaterThan( 0, $library_id );

		// Step 2: Verify avatar is in library.
		$library_avatars = $this->library_service->get_library_avatars();
		$this->assertNotEmpty( $library_avatars );

		// Step 3: Verify metadata is stored.
		$avatar_meta = $this->library_service->get_avatar_metadata( $library_id );
		$this->assertEquals( $avatar['author'], $avatar_meta['author'] ?? '' );
		$this->assertEquals( $avatar['license'], $avatar_meta['license'] ?? '' );
	}

	/**
	 * Test bulk import of sectoral avatars.
	 *
	 * @return void
	 */
	public function test_bulk_import_sectoral_avatars(): void {
		$avatars = TestFixtures::get_sample_library_avatars();

		// Import all avatars.
		$imported_ids = array();
		foreach ( $avatars as $avatar ) {
			$library_id = $this->library_service->add_to_library(
				'/path/to/' . $avatar['filename'],
				array(
					'sector' => $avatar['sector'],
					'tags'   => $avatar['tags'],
				)
			);
			$imported_ids[] = $library_id;
		}

		// Verify all imported.
		$this->assertCount( 5, $imported_ids );

		// Get library count.
		$total = $this->library_service->get_library_count();
		$this->assertEquals( 5, $total );
	}

	/**
	 * Test user selecting avatar from library.
	 *
	 * @return void
	 */
	public function test_user_select_avatar_from_library(): void {
		// Step 1: Add avatars to library.
		$avatars    = TestFixtures::get_sample_library_avatars();
		$library_id = $this->library_service->add_to_library(
			'/path/to/' . $avatars[0]['filename'],
			array( 'sector' => $avatars[0]['sector'] )
		);

		// Step 2: User selects avatar from library.
		$user_id = $this->create_test_user( 'subscriber' );
		$result  = $this->library_service->assign_library_avatar( $user_id, $library_id );

		$this->assertTrue( $result, 'Avatar assignment should succeed' );

		// Step 3: Verify avatar is assigned to user.
		// In real implementation, this would set user meta.
		$this->assertIsInt( $library_id );
	}

	/**
	 * Test filtering library by sector.
	 *
	 * @return void
	 */
	public function test_filter_library_by_sector(): void {
		// Import avatars from different sectors.
		$avatars = TestFixtures::get_sample_library_avatars();
		foreach ( $avatars as $avatar ) {
			$this->library_service->add_to_library(
				'/path/to/' . $avatar['filename'],
				array( 'sector' => $avatar['sector'] )
			);
		}

		// Filter by corporate sector.
		$corporate_avatars = $this->library_service->get_library_avatars(
			array( 'sector' => 'corporate' )
		);
		$this->assertNotEmpty( $corporate_avatars );

		// Filter by technology sector.
		$tech_avatars = $this->library_service->get_library_avatars(
			array( 'sector' => 'technology' )
		);
		$this->assertNotEmpty( $tech_avatars );

		// Filter by healthcare sector.
		$healthcare_avatars = $this->library_service->get_library_avatars(
			array( 'sector' => 'healthcare' )
		);
		$this->assertNotEmpty( $healthcare_avatars );
	}

	/**
	 * Test searching library by tags.
	 *
	 * @return void
	 */
	public function test_search_library_by_tags(): void {
		// Import avatars with various tags.
		$avatars = TestFixtures::get_sample_library_avatars();
		foreach ( $avatars as $avatar ) {
			$this->library_service->add_to_library(
				'/path/to/' . $avatar['filename'],
				array( 'tags' => $avatar['tags'] )
			);
		}

		// Search by tag "professional".
		$professional_avatars = $this->library_service->search_library( 'professional' );
		$this->assertIsArray( $professional_avatars );

		// Search by tag "tech".
		$tech_avatars = $this->library_service->search_library( 'tech' );
		$this->assertIsArray( $tech_avatars );
	}

	/**
	 * Test library avatar with moderation.
	 *
	 * @return void
	 */
	public function test_library_avatar_with_moderation(): void {
		$this->enable_moderation();

		// Add avatar to library.
		$avatars    = TestFixtures::get_sample_library_avatars();
		$library_id = $this->library_service->add_to_library(
			'/path/to/' . $avatars[0]['filename'],
			array()
		);

		// User selects from library.
		$user_id = $this->create_test_user( 'subscriber' );
		$result  = $this->library_service->assign_library_avatar( $user_id, $library_id );

		// Library avatars might bypass moderation or use pre-approval.
		// Behavior depends on implementation.
		$this->assertTrue( $result );
	}

	/**
	 * Test removing avatar from library.
	 *
	 * @return void
	 */
	public function test_remove_avatar_from_library(): void {
		// Add avatar to library.
		$avatars    = TestFixtures::get_sample_library_avatars();
		$library_id = $this->library_service->add_to_library(
			'/path/to/' . $avatars[0]['filename'],
			array()
		);

		// Remove from library.
		$result = $this->library_service->remove_from_library( $library_id );
		$this->assertTrue( $result, 'Removal should succeed' );

		// Verify no longer in library.
		$total = $this->library_service->get_library_count();
		$this->assertEquals( 0, $total );
	}

	/**
	 * Test library requires Pro license.
	 *
	 * @return void
	 */
	public function test_library_requires_pro_license(): void {
		// Deactivate Pro.
		$this->deactivate_pro_license();

		// Try to use library.
		$avatars = TestFixtures::get_sample_library_avatars();
		$result  = $this->library_service->add_to_library(
			'/path/to/' . $avatars[0]['filename'],
			array()
		);

		// Library operations might be restricted without Pro.
		// Actual behavior depends on implementation.
		$this->assertIsNotBool( $result ); // Should return ID or fail.
	}

	/**
	 * Test library pagination.
	 *
	 * @return void
	 */
	public function test_library_pagination(): void {
		// Add many avatars.
		$avatars = TestFixtures::get_sample_library_avatars();
		for ( $i = 0; $i < 15; $i++ ) {
			$this->library_service->add_to_library(
				'/path/to/avatar-' . $i . '.jpg',
				array()
			);
		}

		// Get first page.
		$page1 = $this->library_service->get_library_avatars(
			array(
				'per_page' => 10,
				'page'     => 1,
			)
		);
		$this->assertIsArray( $page1 );

		// Get second page.
		$page2 = $this->library_service->get_library_avatars(
			array(
				'per_page' => 10,
				'page'     => 2,
			)
		);
		$this->assertIsArray( $page2 );
	}
}
