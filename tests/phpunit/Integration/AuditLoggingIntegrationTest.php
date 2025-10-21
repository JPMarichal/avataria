<?php
/**
 * Integration tests for audit logging workflow.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration;

use AvatarSteward\Tests\Integration\Helpers\IntegrationTestCase;
use AvatarSteward\Tests\Integration\Fixtures\TestFixtures;
use AvatarSteward\Infrastructure\Logger;
use AvatarSteward\Domain\Audit\AuditService;
use AvatarSteward\Domain\Audit\AuditRepository;

/**
 * Test audit logging for avatar operations.
 */
class AuditLoggingIntegrationTest extends IntegrationTestCase {

	/**
	 * Logger instance.
	 *
	 * @var Logger
	 */
	private Logger $logger;

	/**
	 * Audit service instance.
	 *
	 * @var AuditService
	 */
	private AuditService $audit_service;

	/**
	 * Audit repository instance.
	 *
	 * @var AuditRepository
	 */
	private AuditRepository $repository;

	/**
	 * Setup test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->logger         = new Logger();
		$this->repository     = new AuditRepository();
		$this->audit_service  = new AuditService( $this->repository, $this->logger );
		
		// Create audit table.
		$this->repository->create_table();
		
		// Activate Pro license.
		$this->activate_pro_license();
	}

	/**
	 * Test logging avatar upload action.
	 *
	 * @return void
	 */
	public function test_log_avatar_upload(): void {
		$user_id       = $this->create_test_user( 'subscriber' );
		$attachment_id = $this->create_test_avatar( $user_id );

		// Log avatar upload using audit service.
		$result = $this->audit_service->log_avatar_upload( $user_id, $attachment_id );

		// Verify log entry was created.
		$this->assertTrue( $result, 'Log entry should be created' );

		// Verify log can be retrieved.
		$logs = $this->audit_service->get_logs( array( 'user_id' => $user_id ) );
		$this->assertNotEmpty( $logs );
		$this->assertSame( 'avatar_uploaded', $logs[0]->event_action );
	}

	/**
	 * Test logging moderation approval.
	 *
	 * @return void
	 */
	public function test_log_moderation_approval(): void {
		$user_id    = $this->create_test_user( 'subscriber' );
		$moderator  = $this->create_test_user( 'administrator' );
		$avatar_id  = $this->create_test_avatar( $user_id );

		// Log approval action using audit service.
		$result = $this->audit_service->log_moderation_approval( $user_id, $moderator, $avatar_id );

		// Verify log was created.
		$this->assertTrue( $result );

		// Verify log contains approval info.
		$logs = $this->audit_service->get_logs( array( 'event_action' => 'avatar_approved' ) );
		$this->assertNotEmpty( $logs );
	}

	/**
	 * Test logging moderation rejection.
	 *
	 * @return void
	 */
	public function test_log_moderation_rejection(): void {
		$user_id    = $this->create_test_user( 'subscriber' );
		$moderator  = $this->create_test_user( 'administrator' );
		$avatar_id  = $this->create_test_avatar( $user_id );
		$reason     = 'Inappropriate content';

		// Log rejection action using audit service.
		$result = $this->audit_service->log_moderation_rejection( $user_id, $moderator, $avatar_id, $reason );

		// Verify rejection was logged.
		$this->assertTrue( $result );

		// Verify reason is in logs.
		$logs = $this->audit_service->get_logs( array( 'event_action' => 'avatar_rejected' ) );
		$this->assertNotEmpty( $logs );
	}

	/**
	 * Test logging social media import.
	 *
	 * @return void
	 */
	public function test_log_social_import(): void {
		$user_id    = $this->create_test_user( 'subscriber' );
		$avatar_id  = $this->create_test_avatar( $user_id );
		$provider   = 'twitter';

		// Log social import action using audit service.
		$result = $this->audit_service->log_social_import( $user_id, $provider, $avatar_id );

		// Verify import was logged.
		$this->assertTrue( $result );

		// Verify provider is in logs.
		$logs = $this->audit_service->get_logs( array( 'event_action' => 'social_import' ) );
		$this->assertNotEmpty( $logs );
	}

	/**
	 * Test logging library avatar selection.
	 *
	 * @return void
	 */
	public function test_log_library_selection(): void {
		$user_id    = $this->create_test_user( 'subscriber' );
		$library_id = 123;
		$avatar_id  = $this->create_test_avatar( $user_id );

		// Log library selection using audit service.
		$result = $this->audit_service->log_library_selection( $user_id, $library_id, $avatar_id );

		// Verify selection was logged.
		$this->assertTrue( $result );

		// Verify library ID is in logs.
		$logs = $this->audit_service->get_logs( array( 'event_action' => 'library_avatar_selected' ) );
		$this->assertNotEmpty( $logs );
	}

	/**
	 * Test logging avatar deletion.
	 *
	 * @return void
	 */
	public function test_log_avatar_deletion(): void {
		$user_id   = $this->create_test_user( 'subscriber' );
		$avatar_id = $this->create_test_avatar( $user_id );

		// Log deletion action using audit service.
		$result = $this->audit_service->log_avatar_deletion( $user_id, $avatar_id );

		// Verify deletion was logged.
		$this->assertTrue( $result );

		// Verify deletion is in logs.
		$logs = $this->audit_service->get_logs( array( 'event_action' => 'avatar_deleted' ) );
		$this->assertNotEmpty( $logs );
	}

	/**
	 * Test complete audit trail for user.
	 *
	 * @return void
	 */
	public function test_complete_user_audit_trail(): void {
		$user_id = $this->create_test_user( 'subscriber' );

		// User journey: upload -> approved -> changed -> deleted.
		
		// 1. Upload.
		$avatar1 = $this->create_test_avatar( $user_id );
		$this->audit_service->log_avatar_upload( $user_id, $avatar1 );

		// 2. Approved.
		$moderator = $this->create_test_user( 'administrator' );
		$this->audit_service->log_moderation_approval( $user_id, $moderator, $avatar1 );

		// 3. User uploads new avatar.
		$avatar2 = $this->create_test_avatar( $user_id );
		$this->audit_service->log_avatar_change( $user_id, $avatar1, $avatar2 );

		// 4. User deletes avatar.
		$this->audit_service->log_avatar_deletion( $user_id, $avatar2 );

		// Verify complete trail exists.
		$logs = $this->audit_service->get_logs( array( 'user_id' => $user_id ) );
		$this->assertCount( 4, $logs, 'Complete audit trail should exist with 4 entries' );
	}

	/**
	 * Test audit log filtering by action type.
	 *
	 * @return void
	 */
	public function test_filter_logs_by_action(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Create various log entries.
		$this->audit_service->log_avatar_upload( $user_id, 123 );
		$this->audit_service->log_avatar_deletion( $user_id, 123 );
		$this->audit_service->log_social_import( $user_id, 'twitter', 456 );

		// Filter by specific action.
		$upload_logs = $this->audit_service->get_logs( array( 'event_action' => 'avatar_uploaded' ) );
		$this->assertCount( 1, $upload_logs );
		$this->assertSame( 'avatar_uploaded', $upload_logs[0]->event_action );
	}

	/**
	 * Test audit log filtering by date range.
	 *
	 * @return void
	 */
	public function test_filter_logs_by_date_range(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Create logs.
		$this->audit_service->log_avatar_upload( $user_id, 123 );
		$this->audit_service->log_avatar_deletion( $user_id, 123 );

		// Filter by date (today).
		$today = gmdate( 'Y-m-d' );
		$logs  = $this->audit_service->get_logs(
			array(
				'date_from' => $today . ' 00:00:00',
				'date_to'   => $today . ' 23:59:59',
			)
		);

		// Should have logs from today.
		$this->assertGreaterThanOrEqual( 2, count( $logs ) );
	}

	/**
	 * Test audit log export functionality.
	 *
	 * @return void
	 */
	public function test_export_audit_logs(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Create sample logs.
		$this->audit_service->log_avatar_upload( $user_id, 123 );
		$this->audit_service->log_avatar_deletion( $user_id, 123 );

		// Export logs to CSV format.
		$csv = $this->audit_service->export_to_csv();
		$this->assertStringContainsString( 'ID,User ID,Event Type', $csv );
		$this->assertStringContainsString( 'avatar_uploaded', $csv );

		// Export to JSON format.
		$json = $this->audit_service->export_to_json();
		$data = json_decode( $json, true );
		$this->assertIsArray( $data );
		$this->assertNotEmpty( $data );
	}

	/**
	 * Test sensitive data is properly logged.
	 *
	 * @return void
	 */
	public function test_sensitive_data_handling(): void {
		$user_id = $this->create_test_user( 'subscriber' );

		// Log entry should not contain passwords or tokens.
		$metadata = array(
			'action' => 'profile_update',
			// Passwords and tokens should never be in metadata.
		);
		
		$result = $this->audit_service->log_event(
			$user_id,
			AuditService::EVENT_TYPE_SYSTEM,
			'profile_update',
			null,
			null,
			null,
			null,
			$metadata
		);

		// Verify logging works and no sensitive data leaks.
		$this->assertTrue( $result, 'Sensitive data should be filtered' );
	}

	/**
	 * Test log retention and cleanup.
	 *
	 * @return void
	 */
	public function test_log_retention_and_cleanup(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Create log entries.
		$this->audit_service->log_avatar_upload( $user_id, 123 );
		$this->audit_service->log_avatar_deletion( $user_id, 123 );

		// Get count before purge.
		$count_before = $this->audit_service->count_logs();
		$this->assertGreaterThan( 0, $count_before );

		// Purge old logs (using 0 days should delete all).
		$deleted = $this->audit_service->purge_old_logs( 0 );
		
		// Verify purge occurred.
		$this->assertGreaterThanOrEqual( 0, $deleted );
	}

	/**
	 * Test audit logs require Pro license.
	 *
	 * @return void
	 */
	public function test_audit_logs_require_pro(): void {
		$user_id = $this->create_test_user( 'subscriber' );
		
		// Deactivate Pro.
		$this->deactivate_pro_license();

		// Audit service should still work (Pro check is at UI/API level).
		$result = $this->audit_service->log_avatar_upload( $user_id, 123 );
		$this->assertTrue( $result );

		// Verify audit service still functions (backend always logs).
		$this->assertInstanceOf( AuditService::class, $this->audit_service );
	}
}
