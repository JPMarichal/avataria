<?php
/**
 * Tests for DecisionService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Moderation\DecisionService;
use AvatarSteward\Domain\Moderation\ModerationQueue;

/**
 * Test case for DecisionService class.
 */
final class DecisionServiceTest extends TestCase {

	/**
	 * Test that DecisionService class exists.
	 */
	public function test_decision_service_class_exists() {
		$this->assertTrue( class_exists( DecisionService::class ) );
	}

	/**
	 * Test that DecisionService can be instantiated.
	 */
	public function test_decision_service_can_be_instantiated() {
		$queue   = new ModerationQueue();
		$service = new DecisionService( $queue );
		$this->assertInstanceOf( DecisionService::class, $service );
	}

	/**
	 * Test that approve method returns expected structure.
	 */
	public function test_approve_returns_expected_structure() {
		$queue   = new ModerationQueue();
		$service = new DecisionService( $queue );
		$result  = $service->approve( 999999, 1, 'Test comment' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertIsBool( $result['success'] );
		$this->assertIsString( $result['message'] );
	}

	/**
	 * Test that reject method returns expected structure.
	 */
	public function test_reject_returns_expected_structure() {
		$queue   = new ModerationQueue();
		$service = new DecisionService( $queue );
		$result  = $service->reject( 999999, 1, 'Test comment' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertIsBool( $result['success'] );
		$this->assertIsString( $result['message'] );
	}

	/**
	 * Test that bulk_approve method returns expected structure.
	 */
	public function test_bulk_approve_returns_expected_structure() {
		$queue    = new ModerationQueue();
		$service  = new DecisionService( $queue );
		$user_ids = array( 999998, 999999 );
		$result   = $service->bulk_approve( $user_ids, 1, 'Bulk test' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertArrayHasKey( 'approved', $result );
		$this->assertArrayHasKey( 'failed', $result );
		$this->assertIsInt( $result['approved'] );
		$this->assertIsInt( $result['failed'] );
	}

	/**
	 * Test that bulk_reject method returns expected structure.
	 */
	public function test_bulk_reject_returns_expected_structure() {
		$queue    = new ModerationQueue();
		$service  = new DecisionService( $queue );
		$user_ids = array( 999998, 999999 );
		$result   = $service->bulk_reject( $user_ids, 1, 'Bulk test' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'success', $result );
		$this->assertArrayHasKey( 'message', $result );
		$this->assertArrayHasKey( 'rejected', $result );
		$this->assertArrayHasKey( 'failed', $result );
		$this->assertIsInt( $result['rejected'] );
		$this->assertIsInt( $result['failed'] );
	}

	/**
	 * Test that approve fails for non-existent user.
	 */
	public function test_approve_fails_for_nonexistent_user() {
		$queue   = new ModerationQueue();
		$service = new DecisionService( $queue );
		$result  = $service->approve( 999999, 1 );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', strtolower( $result['message'] ) );
	}

	/**
	 * Test that reject fails for non-existent user.
	 */
	public function test_reject_fails_for_nonexistent_user() {
		$queue   = new ModerationQueue();
		$service = new DecisionService( $queue );
		$result  = $service->reject( 999999, 1 );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', strtolower( $result['message'] ) );
	}
}
