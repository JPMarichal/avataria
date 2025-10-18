<?php
/**
 * Decision Service for Moderation.
 *
 * Handles approval and rejection decisions for avatar moderation.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Moderation;

/**
 * Class DecisionService
 *
 * Processes moderation decisions (approve/reject) for avatars.
 */
class DecisionService {

	/**
	 * Moderation queue instance.
	 *
	 * @var ModerationQueue
	 */
	private ModerationQueue $queue;

	/**
	 * Constructor.
	 *
	 * @param ModerationQueue $queue Moderation queue instance.
	 */
	public function __construct( ModerationQueue $queue ) {
		$this->queue = $queue;
	}

	/**
	 * Approve an avatar.
	 *
	 * @param int    $user_id   User ID.
	 * @param int    $moderator_id Moderator user ID.
	 * @param string $comment   Optional comment.
	 * @return array Result with success status and message.
	 */
	public function approve( int $user_id, int $moderator_id, string $comment = '' ): array {
		// Verify user exists.
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return array(
				'success' => false,
				'message' => __( 'User not found.', 'avatar-steward' ),
			);
		}

		// Update status to approved.
		$updated = $this->queue->set_status( $user_id, ModerationQueue::STATUS_APPROVED );

		if ( ! $updated ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to update moderation status.', 'avatar-steward' ),
			);
		}

		// Add to history.
		$this->queue->add_history_entry( $user_id, 'approved', $moderator_id, $comment );

		// Clear previous avatar backup since approval is confirmed.
		$this->queue->clear_previous_avatar( $user_id );

		// Fire action hook for extensibility.
		do_action( 'avatarsteward/avatar_approved', $user_id, $moderator_id, $comment );

		return array(
			'success' => true,
			'message' => __( 'Avatar approved successfully.', 'avatar-steward' ),
		);
	}

	/**
	 * Reject an avatar and revert to previous.
	 *
	 * @param int    $user_id      User ID.
	 * @param int    $moderator_id Moderator user ID.
	 * @param string $comment      Optional comment.
	 * @return array Result with success status and message.
	 */
	public function reject( int $user_id, int $moderator_id, string $comment = '' ): array {
		// Verify user exists.
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return array(
				'success' => false,
				'message' => __( 'User not found.', 'avatar-steward' ),
			);
		}

		// Get current avatar to delete.
		$current_avatar = get_user_meta( $user_id, 'avatar_steward_avatar', true );

		// Get previous avatar to restore.
		$previous_avatar = $this->queue->get_previous_avatar( $user_id );

		// Update status to rejected.
		$updated = $this->queue->set_status( $user_id, ModerationQueue::STATUS_REJECTED );

		if ( ! $updated ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to update moderation status.', 'avatar-steward' ),
			);
		}

		// Restore previous avatar if it exists.
		if ( $previous_avatar ) {
			update_user_meta( $user_id, 'avatar_steward_avatar', $previous_avatar );
		} else {
			// No previous avatar, remove current one.
			delete_user_meta( $user_id, 'avatar_steward_avatar' );
		}

		// Delete the rejected avatar attachment if it exists.
		if ( $current_avatar && is_numeric( $current_avatar ) ) {
			wp_delete_attachment( (int) $current_avatar, true );
		}

		// Add to history.
		$this->queue->add_history_entry( $user_id, 'rejected', $moderator_id, $comment );

		// Clear previous avatar backup.
		$this->queue->clear_previous_avatar( $user_id );

		// Fire action hook for extensibility.
		do_action( 'avatarsteward/avatar_rejected', $user_id, $moderator_id, $comment );

		return array(
			'success' => true,
			'message' => __( 'Avatar rejected and reverted to previous.', 'avatar-steward' ),
		);
	}

	/**
	 * Bulk approve avatars.
	 *
	 * @param array $user_ids      Array of user IDs.
	 * @param int   $moderator_id  Moderator user ID.
	 * @param string $comment      Optional comment.
	 * @return array Result with counts.
	 */
	public function bulk_approve( array $user_ids, int $moderator_id, string $comment = '' ): array {
		$success_count = 0;
		$failed_count  = 0;

		foreach ( $user_ids as $user_id ) {
			$result = $this->approve( (int) $user_id, $moderator_id, $comment );
			if ( $result['success'] ) {
				$success_count++;
			} else {
				$failed_count++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf(
				/* translators: %1$d: number of approved avatars, %2$d: number of failed approvals */
				__( '%1$d avatar(s) approved, %2$d failed.', 'avatar-steward' ),
				$success_count,
				$failed_count
			),
			'approved' => $success_count,
			'failed'   => $failed_count,
		);
	}

	/**
	 * Bulk reject avatars.
	 *
	 * @param array  $user_ids     Array of user IDs.
	 * @param int    $moderator_id Moderator user ID.
	 * @param string $comment      Optional comment.
	 * @return array Result with counts.
	 */
	public function bulk_reject( array $user_ids, int $moderator_id, string $comment = '' ): array {
		$success_count = 0;
		$failed_count  = 0;

		foreach ( $user_ids as $user_id ) {
			$result = $this->reject( (int) $user_id, $moderator_id, $comment );
			if ( $result['success'] ) {
				$success_count++;
			} else {
				$failed_count++;
			}
		}

		return array(
			'success' => true,
			'message' => sprintf(
				/* translators: %1$d: number of rejected avatars, %2$d: number of failed rejections */
				__( '%1$d avatar(s) rejected, %2$d failed.', 'avatar-steward' ),
				$success_count,
				$failed_count
			),
			'rejected' => $success_count,
			'failed'   => $failed_count,
		);
	}
}
