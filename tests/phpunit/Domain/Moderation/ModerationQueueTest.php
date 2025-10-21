<?php
/**
 * Tests for ModerationQueue class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Moderation\ModerationQueue;

/**
 * Test case for ModerationQueue class.
 */
final class ModerationQueueTest extends TestCase {

	/**
	 * Test that ModerationQueue class exists.
	 */
	public function test_moderation_queue_class_exists() {
		$this->assertTrue( class_exists( ModerationQueue::class ) );
	}

	/**
	 * Test that ModerationQueue can be instantiated.
	 */
	public function test_moderation_queue_can_be_instantiated() {
		$queue = new ModerationQueue();
		$this->assertInstanceOf( ModerationQueue::class, $queue );
	}

	/**
	 * Test that status constants are defined.
	 */
	public function test_status_constants_are_defined() {
		$this->assertEquals( 'pending', ModerationQueue::STATUS_PENDING );
		$this->assertEquals( 'approved', ModerationQueue::STATUS_APPROVED );
		$this->assertEquals( 'rejected', ModerationQueue::STATUS_REJECTED );
	}

	/**
	 * Test that get_pending_avatars returns an array.
	 */
	public function test_get_pending_avatars_returns_array() {
		$queue  = new ModerationQueue();
		$result = $queue->get_pending_avatars();

		$this->assertIsArray( $result );
	}

	/**
	 * Test that get_count_by_status returns an integer.
	 */
	public function test_get_count_by_status_returns_integer() {
		$queue  = new ModerationQueue();
		$result = $queue->get_count_by_status( ModerationQueue::STATUS_PENDING );

		$this->assertIsInt( $result );
		$this->assertGreaterThanOrEqual( 0, $result );
	}

	/**
	 * Test that set_status validates status values.
	 */
	public function test_set_status_validates_status_values() {
		$queue = new ModerationQueue();

		// Invalid status should return false.
		$result = $queue->set_status( 1, 'invalid_status' );
		$this->assertFalse( $result );
	}

	/**
	 * Test that get_status returns a string.
	 */
	public function test_get_status_returns_string() {
		$queue  = new ModerationQueue();
		$result = $queue->get_status( 1 );

		$this->assertIsString( $result );
	}

	/**
	 * Test that get_history returns an array.
	 */
	public function test_get_history_returns_array() {
		$queue  = new ModerationQueue();
		$result = $queue->get_history( 1 );

		$this->assertIsArray( $result );
	}

	/**
	 * Test that add_history_entry returns boolean.
	 */
	public function test_add_history_entry_returns_boolean() {
		$queue  = new ModerationQueue();
		$result = $queue->add_history_entry( 1, 'approved', 1, 'Test comment' );

		$this->assertIsBool( $result );
	}

	/**
	 * Test that get_previous_avatar returns correct type.
	 */
	public function test_get_previous_avatar_returns_correct_type() {
		$queue  = new ModerationQueue();
		$result = $queue->get_previous_avatar( 1 );

		$this->assertTrue( is_int( $result ) || false === $result );
	}
}
