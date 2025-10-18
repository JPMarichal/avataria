<?php
/**
 * Tests for MigrationService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Migration\MigrationService;

/**
 * Test case for MigrationService class.
 */
final class MigrationServiceTest extends TestCase {

	/**
	 * Service instance.
	 *
	 * @var MigrationService
	 */
	private MigrationService $service;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->service = new MigrationService();
	}

	/**
	 * Test that MigrationService class exists.
	 */
	public function test_migration_service_class_exists() {
		$this->assertTrue( class_exists( MigrationService::class ) );
	}

	/**
	 * Test that MigrationService can be instantiated.
	 */
	public function test_migration_service_can_be_instantiated() {
		$service = new MigrationService();
		$this->assertInstanceOf( MigrationService::class, $service );
	}

	/**
	 * Test migrate_from_simple_local_avatars returns error when WordPress functions not available.
	 */
	public function test_migrate_from_simple_local_avatars_returns_error_without_wordpress() {
		$result = $this->service->migrate_from_simple_local_avatars();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'error', $result );
		$this->assertArrayHasKey( 'migrated', $result );
		$this->assertArrayHasKey( 'skipped', $result );
		$this->assertArrayHasKey( 'total', $result );
	}

	/**
	 * Test migrate_from_wp_user_avatar returns error when WordPress functions not available.
	 */
	public function test_migrate_from_wp_user_avatar_returns_error_without_wordpress() {
		$result = $this->service->migrate_from_wp_user_avatar();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'error', $result );
		$this->assertArrayHasKey( 'migrated', $result );
		$this->assertArrayHasKey( 'skipped', $result );
		$this->assertArrayHasKey( 'total', $result );
	}

	/**
	 * Test import_from_gravatar returns error when WordPress functions not available.
	 */
	public function test_import_from_gravatar_returns_error_without_wordpress() {
		$result = $this->service->import_from_gravatar();

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertFalse( $result['success'] );
		$this->assertArrayHasKey( 'error', $result );
		$this->assertArrayHasKey( 'imported', $result );
		$this->assertArrayHasKey( 'skipped', $result );
		$this->assertArrayHasKey( 'failed', $result );
		$this->assertArrayHasKey( 'total', $result );
	}

	/**
	 * Test import_from_gravatar accepts force parameter.
	 */
	public function test_import_from_gravatar_accepts_force_parameter() {
		$result_without_force = $this->service->import_from_gravatar( false );
		$result_with_force    = $this->service->import_from_gravatar( true );

		$this->assertIsArray( $result_without_force );
		$this->assertIsArray( $result_with_force );
		$this->assertArrayHasKey( 'success', $result_without_force );
		$this->assertArrayHasKey( 'success', $result_with_force );
	}

	/**
	 * Test get_migration_stats returns empty array when WordPress functions not available.
	 */
	public function test_get_migration_stats_returns_empty_array_without_wordpress() {
		$stats = $this->service->get_migration_stats();

		$this->assertIsArray( $stats );
		$this->assertEmpty( $stats );
	}

	/**
	 * Test migration service methods return proper structure.
	 */
	public function test_migration_methods_return_proper_structure() {
		$simple_result    = $this->service->migrate_from_simple_local_avatars();
		$wp_user_result   = $this->service->migrate_from_wp_user_avatar();
		$gravatar_result  = $this->service->import_from_gravatar();

		// Test Simple Local Avatars result structure.
		$this->assertArrayHasKey( 'success', $simple_result );
		$this->assertArrayHasKey( 'migrated', $simple_result );
		$this->assertArrayHasKey( 'skipped', $simple_result );
		$this->assertArrayHasKey( 'total', $simple_result );

		// Test WP User Avatar result structure.
		$this->assertArrayHasKey( 'success', $wp_user_result );
		$this->assertArrayHasKey( 'migrated', $wp_user_result );
		$this->assertArrayHasKey( 'skipped', $wp_user_result );
		$this->assertArrayHasKey( 'total', $wp_user_result );

		// Test Gravatar result structure.
		$this->assertArrayHasKey( 'success', $gravatar_result );
		$this->assertArrayHasKey( 'imported', $gravatar_result );
		$this->assertArrayHasKey( 'skipped', $gravatar_result );
		$this->assertArrayHasKey( 'failed', $gravatar_result );
		$this->assertArrayHasKey( 'total', $gravatar_result );
	}

	/**
	 * Test all migration methods return integer counts.
	 */
	public function test_migration_methods_return_integer_counts() {
		$simple_result = $this->service->migrate_from_simple_local_avatars();

		$this->assertIsInt( $simple_result['migrated'] );
		$this->assertIsInt( $simple_result['skipped'] );
		$this->assertIsInt( $simple_result['total'] );
		$this->assertGreaterThanOrEqual( 0, $simple_result['migrated'] );
		$this->assertGreaterThanOrEqual( 0, $simple_result['skipped'] );
		$this->assertGreaterThanOrEqual( 0, $simple_result['total'] );
	}
}
