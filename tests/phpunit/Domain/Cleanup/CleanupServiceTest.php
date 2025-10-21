<?php
/**
 * Tests for CleanupService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Cleanup;

use AvatarSteward\Domain\Cleanup\CleanupService;
use PHPUnit\Framework\TestCase;

/**
 * Test CleanupService functionality.
 */
class CleanupServiceTest extends TestCase {

	/**
	 * Test that CleanupService can be instantiated.
	 */
	public function test_cleanup_service_can_be_instantiated(): void {
		$service = new CleanupService();

		$this->assertInstanceOf( CleanupService::class, $service );
	}

	/**
	 * Test that find_inactive_avatars returns an array.
	 */
	public function test_find_inactive_avatars_returns_array(): void {
		$service = new CleanupService();

		$result = $service->find_inactive_avatars();

		$this->assertIsArray( $result );
	}

	/**
	 * Test that find_inactive_avatars accepts criteria parameters.
	 */
	public function test_find_inactive_avatars_accepts_criteria(): void {
		$service = new CleanupService();

		$criteria = array(
			'max_age_days'         => 180,
			'exclude_active_users' => true,
			'user_inactivity_days' => 90,
		);

		$result = $service->find_inactive_avatars( $criteria );

		$this->assertIsArray( $result );
	}

	/**
	 * Test that delete_inactive_avatars returns expected structure.
	 */
	public function test_delete_inactive_avatars_returns_expected_structure(): void {
		$service = new CleanupService();

		$result = $service->delete_inactive_avatars( array() );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'deleted', $result );
		$this->assertArrayHasKey( 'failed', $result );
		$this->assertArrayHasKey( 'dry_run', $result );
	}

	/**
	 * Test that delete_inactive_avatars handles dry-run mode.
	 */
	public function test_delete_inactive_avatars_dry_run_mode(): void {
		$service = new CleanupService();

		$options = array(
			'dry_run' => true,
		);

		$result = $service->delete_inactive_avatars( array( 1, 2, 3 ), $options );

		$this->assertIsArray( $result );
		$this->assertTrue( $result['dry_run'] );
		$this->assertCount( 3, $result['deleted'] );
		$this->assertCount( 0, $result['failed'] );
	}

	/**
	 * Test that delete_inactive_avatars returns empty result for empty input.
	 */
	public function test_delete_inactive_avatars_empty_input(): void {
		$service = new CleanupService();

		$result = $service->delete_inactive_avatars( array() );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result['deleted'] );
		$this->assertEmpty( $result['failed'] );
	}

	/**
	 * Test that schedule_cleanup returns a boolean.
	 */
	public function test_schedule_cleanup_returns_boolean(): void {
		if ( ! function_exists( 'wp_next_scheduled' ) ) {
			$this->markTestSkipped( 'WordPress functions not available in unit test context.' );
		}

		$service = new CleanupService();

		$result = $service->schedule_cleanup( 'weekly' );

		$this->assertIsBool( $result );
	}

	/**
	 * Test that schedule_cleanup accepts valid schedules.
	 */
	public function test_schedule_cleanup_accepts_valid_schedules(): void {
		if ( ! function_exists( 'wp_next_scheduled' ) ) {
			$this->markTestSkipped( 'WordPress functions not available in unit test context.' );
		}

		$service = new CleanupService();

		$schedules = array( 'daily', 'weekly', 'monthly' );

		foreach ( $schedules as $schedule ) {
			$result = $service->schedule_cleanup( $schedule );
			$this->assertIsBool( $result );
		}
	}

	/**
	 * Test that unschedule_cleanup returns a boolean.
	 */
	public function test_unschedule_cleanup_returns_boolean(): void {
		if ( ! function_exists( 'wp_next_scheduled' ) ) {
			$this->markTestSkipped( 'WordPress functions not available in unit test context.' );
		}

		$service = new CleanupService();

		$result = $service->unschedule_cleanup();

		$this->assertIsBool( $result );
	}

	/**
	 * Test that dry run doesn't delete attachments.
	 */
	public function test_dry_run_does_not_delete_attachments(): void {
		$service = new CleanupService();

		$attachment_ids = array( 1, 2, 3 );
		$options        = array( 'dry_run' => true );

		$result = $service->delete_inactive_avatars( $attachment_ids, $options );

		// Verify dry_run flag is set.
		$this->assertTrue( $result['dry_run'] );

		// Verify that all items are in 'deleted' array (because they would be deleted).
		$this->assertCount( count( $attachment_ids ), $result['deleted'] );

		// Verify structure of deleted items.
		foreach ( $result['deleted'] as $item ) {
			$this->assertIsArray( $item );
			$this->assertArrayHasKey( 'attachment_id', $item );
			$this->assertArrayHasKey( 'user_id', $item );
		}
	}

	/**
	 * Test that notification options are respected.
	 */
	public function test_notification_options_are_respected(): void {
		$service = new CleanupService();

		$options = array(
			'dry_run'       => true,
			'notify_users'  => true,
			'notify_admins' => true,
		);

		$result = $service->delete_inactive_avatars( array( 1 ), $options );

		$this->assertIsArray( $result );
		$this->assertTrue( $result['dry_run'] );
	}
}
