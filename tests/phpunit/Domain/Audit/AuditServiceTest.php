<?php
/**
 * Tests for AuditService.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Audit;

use AvatarSteward\Domain\Audit\AuditService;
use AvatarSteward\Domain\Audit\AuditRepository;
use AvatarSteward\Infrastructure\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Test AuditService functionality.
 */
class AuditServiceTest extends TestCase {

	/**
	 * Audit service instance.
	 *
	 * @var AuditService
	 */
	private AuditService $service;

	/**
	 * Mock repository instance.
	 *
	 * @var AuditRepository
	 */
	private $repository;

	/**
	 * Mock logger instance.
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * Setup test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->repository = $this->createMock( AuditRepository::class );
		$this->logger     = $this->createMock( Logger::class );
		$this->service    = new AuditService( $this->repository, $this->logger );
	}

	/**
	 * Test logging avatar upload.
	 *
	 * @return void
	 */
	public function test_log_avatar_upload(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['user_id'] === 123
							&& $data['event_type'] === AuditService::EVENT_TYPE_AVATAR
							&& $data['event_action'] === 'avatar_uploaded'
							&& $data['object_id'] === 456;
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_avatar_upload( 123, 456 );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging avatar deletion.
	 *
	 * @return void
	 */
	public function test_log_avatar_deletion(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_action'] === 'avatar_deleted';
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_avatar_deletion( 123, 456 );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging moderation approval.
	 *
	 * @return void
	 */
	public function test_log_moderation_approval(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_type'] === AuditService::EVENT_TYPE_MODERATION
							&& $data['event_action'] === 'avatar_approved'
							&& isset( $data['metadata']['moderator_id'] );
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_moderation_approval( 123, 456, 789 );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging moderation rejection.
	 *
	 * @return void
	 */
	public function test_log_moderation_rejection(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_action'] === 'avatar_rejected'
							&& isset( $data['metadata']['reason'] )
							&& $data['metadata']['reason'] === 'Inappropriate content';
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_moderation_rejection( 123, 456, 789, 'Inappropriate content' );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging metadata change.
	 *
	 * @return void
	 */
	public function test_log_metadata_change(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_type'] === AuditService::EVENT_TYPE_METADATA
							&& $data['old_value'] === 'old'
							&& $data['new_value'] === 'new';
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_metadata_change( 123, 'test_key', 'old', 'new' );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging library selection.
	 *
	 * @return void
	 */
	public function test_log_library_selection(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_type'] === AuditService::EVENT_TYPE_LIBRARY
							&& isset( $data['metadata']['library_id'] );
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_library_selection( 123, 456, 789 );

		$this->assertTrue( $result );
	}

	/**
	 * Test logging social import.
	 *
	 * @return void
	 */
	public function test_log_social_import(): void {
		$this->repository->expects( $this->once() )
			->method( 'insert' )
			->with(
				$this->callback(
					function ( $data ) {
						return $data['event_type'] === AuditService::EVENT_TYPE_SOCIAL
							&& isset( $data['metadata']['provider'] )
							&& $data['metadata']['provider'] === 'twitter';
					}
				)
			)
			->willReturn( 1 );

		$result = $this->service->log_social_import( 123, 'twitter', 456 );

		$this->assertTrue( $result );
	}

	/**
	 * Test getting logs.
	 *
	 * @return void
	 */
	public function test_get_logs(): void {
		$expected_logs = array(
			(object) array(
				'id'           => 1,
				'user_id'      => 123,
				'event_type'   => 'avatar',
				'event_action' => 'avatar_uploaded',
			),
		);

		$this->repository->expects( $this->once() )
			->method( 'get_logs' )
			->willReturn( $expected_logs );

		$logs = $this->service->get_logs();

		$this->assertSame( $expected_logs, $logs );
	}

	/**
	 * Test counting logs.
	 *
	 * @return void
	 */
	public function test_count_logs(): void {
		$this->repository->expects( $this->once() )
			->method( 'count_logs' )
			->willReturn( 42 );

		$count = $this->service->count_logs();

		$this->assertSame( 42, $count );
	}

	/**
	 * Test CSV export.
	 *
	 * @return void
	 */
	public function test_export_to_csv(): void {
		$logs = array(
			(object) array(
				'id'           => 1,
				'user_id'      => 123,
				'event_type'   => 'avatar',
				'event_action' => 'avatar_uploaded',
				'object_id'    => 456,
				'object_type'  => 'attachment',
				'old_value'    => null,
				'new_value'    => null,
				'ip_address'   => '127.0.0.1',
				'created_at'   => '2024-01-01 12:00:00',
			),
		);

		$this->repository->expects( $this->once() )
			->method( 'get_logs' )
			->willReturn( $logs );

		$csv = $this->service->export_to_csv();

		$this->assertStringContainsString( 'ID,User ID,Event Type', $csv );
		$this->assertStringContainsString( '1,123,avatar,avatar_uploaded', $csv );
	}

	/**
	 * Test JSON export.
	 *
	 * @return void
	 */
	public function test_export_to_json(): void {
		$logs = array(
			(object) array(
				'id'           => 1,
				'user_id'      => 123,
				'event_type'   => 'avatar',
				'event_action' => 'avatar_uploaded',
				'object_id'    => 456,
				'object_type'  => 'attachment',
				'old_value'    => null,
				'new_value'    => null,
				'ip_address'   => '127.0.0.1',
				'user_agent'   => 'Mozilla/5.0',
				'metadata'     => null,
				'created_at'   => '2024-01-01 12:00:00',
			),
		);

		$this->repository->expects( $this->once() )
			->method( 'get_logs' )
			->willReturn( $logs );

		$json = $this->service->export_to_json();
		$data = json_decode( $json, true );

		$this->assertIsArray( $data );
		$this->assertCount( 1, $data );
		$this->assertSame( 1, $data[0]['id'] );
		$this->assertSame( 'avatar_uploaded', $data[0]['event_action'] );
	}

	/**
	 * Test purging old logs.
	 *
	 * @return void
	 */
	public function test_purge_old_logs(): void {
		$this->repository->expects( $this->once() )
			->method( 'purge_old_logs' )
			->with( 90 )
			->willReturn( 10 );

		$deleted = $this->service->purge_old_logs( 90 );

		$this->assertSame( 10, $deleted );
	}

	/**
	 * Test getting event types.
	 *
	 * @return void
	 */
	public function test_get_event_types(): void {
		$types = array( 'avatar', 'moderation', 'metadata' );

		$this->repository->expects( $this->once() )
			->method( 'get_event_types' )
			->willReturn( $types );

		$result = $this->service->get_event_types();

		$this->assertSame( $types, $result );
	}

	/**
	 * Test get repository.
	 *
	 * @return void
	 */
	public function test_get_repository(): void {
		$repository = $this->service->get_repository();

		$this->assertSame( $this->repository, $repository );
	}
}
