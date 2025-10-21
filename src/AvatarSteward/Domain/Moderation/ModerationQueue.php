<?php
/**
 * Moderation Queue Service.
 *
 * Manages the queue of avatars pending moderation approval.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Moderation;

/**
 * Class ModerationQueue
 *
 * Handles retrieval and management of avatars in the moderation queue.
 */
class ModerationQueue {

	/**
	 * Meta key for avatar moderation status.
	 *
	 * @var string
	 */
	private const STATUS_META_KEY = 'avatar_steward_moderation_status';

	/**
	 * Meta key for avatar moderation history.
	 *
	 * @var string
	 */
	private const HISTORY_META_KEY = 'avatar_steward_moderation_history';

	/**
	 * Meta key for previous avatar (used when rejecting).
	 *
	 * @var string
	 */
	private const PREVIOUS_AVATAR_META_KEY = 'avatar_steward_previous_avatar';

	/**
	 * Moderation status constants.
	 */
	public const STATUS_PENDING  = 'pending';
	public const STATUS_APPROVED = 'approved';
	public const STATUS_REJECTED = 'rejected';

	/**
	 * Get all avatars pending moderation.
	 *
	 * @param array $args Query arguments (status, role, search, limit, offset).
	 * @return array List of users with pending avatars.
	 */
	public function get_pending_avatars( array $args = array() ): array {
		$defaults = array(
			'status' => self::STATUS_PENDING,
			'role'   => '',
			'search' => '',
			'limit'  => 20,
			'offset' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		// Build user query args.
		$query_args = array(
			'meta_key'   => self::STATUS_META_KEY,
			'meta_value' => $args['status'],
			'number'     => $args['limit'],
			'offset'     => $args['offset'],
			'fields'     => 'all_with_meta',
		);

		if ( ! empty( $args['role'] ) ) {
			$query_args['role'] = $args['role'];
		}

		if ( ! empty( $args['search'] ) ) {
			$query_args['search']         = '*' . $args['search'] . '*';
			$query_args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
		}

		$user_query = new \WP_User_Query( $query_args );
		$users      = $user_query->get_results();

		$result = array();
		foreach ( $users as $user ) {
			$avatar_id = get_user_meta( $user->ID, 'avatar_steward_avatar', true );
			if ( $avatar_id ) {
				$result[] = array(
					'user_id'      => $user->ID,
					'user_login'   => $user->user_login,
					'display_name' => $user->display_name,
					'user_email'   => $user->user_email,
					'roles'        => $user->roles,
					'avatar_id'    => $avatar_id,
					'avatar_url'   => wp_get_attachment_url( $avatar_id ),
					'status'       => $args['status'],
					'uploaded_at'  => get_post_field( 'post_date', $avatar_id ),
				);
			}
		}

		return $result;
	}

	/**
	 * Get total count of avatars by status.
	 *
	 * @param string $status Moderation status.
	 * @return int Count of avatars.
	 */
	public function get_count_by_status( string $status = self::STATUS_PENDING ): int {
		$query_args = array(
			'meta_key'    => self::STATUS_META_KEY,
			'meta_value'  => $status,
			'count_total' => true,
			'fields'      => 'ID',
		);

		$user_query = new \WP_User_Query( $query_args );
		return $user_query->get_total();
	}

	/**
	 * Set moderation status for a user's avatar.
	 *
	 * @param int    $user_id User ID.
	 * @param string $status  Moderation status.
	 * @return bool Success status.
	 */
	public function set_status( int $user_id, string $status ): bool {
		$valid_statuses = array( self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED );

		if ( ! in_array( $status, $valid_statuses, true ) ) {
			return false;
		}

		return (bool) update_user_meta( $user_id, self::STATUS_META_KEY, $status );
	}

	/**
	 * Get moderation status for a user's avatar.
	 *
	 * @param int $user_id User ID.
	 * @return string Moderation status.
	 */
	public function get_status( int $user_id ): string {
		$status = get_user_meta( $user_id, self::STATUS_META_KEY, true );
		return $status ?: self::STATUS_APPROVED;
	}

	/**
	 * Add entry to moderation history.
	 *
	 * @param int    $user_id      User ID.
	 * @param string $action       Action performed (approved/rejected).
	 * @param int    $moderator_id Moderator user ID.
	 * @param string $comment      Optional comment.
	 * @return bool Success status.
	 */
	public function add_history_entry( int $user_id, string $action, int $moderator_id, string $comment = '' ): bool {
		$history = get_user_meta( $user_id, self::HISTORY_META_KEY, true );
		if ( ! is_array( $history ) ) {
			$history = array();
		}

		$entry = array(
			'action'       => $action,
			'moderator_id' => $moderator_id,
			'timestamp'    => current_time( 'timestamp' ),
			'comment'      => $comment,
		);

		$history[] = $entry;

		return (bool) update_user_meta( $user_id, self::HISTORY_META_KEY, $history );
	}

	/**
	 * Get moderation history for a user.
	 *
	 * @param int $user_id User ID.
	 * @return array Moderation history.
	 */
	public function get_history( int $user_id ): array {
		$history = get_user_meta( $user_id, self::HISTORY_META_KEY, true );
		return is_array( $history ) ? $history : array();
	}

	/**
	 * Store previous avatar before setting new one.
	 *
	 * @param int $user_id   User ID.
	 * @param int $avatar_id Avatar attachment ID.
	 * @return bool Success status.
	 */
	public function store_previous_avatar( int $user_id, int $avatar_id ): bool {
		return (bool) update_user_meta( $user_id, self::PREVIOUS_AVATAR_META_KEY, $avatar_id );
	}

	/**
	 * Get previous avatar for a user.
	 *
	 * @param int $user_id User ID.
	 * @return int|false Avatar attachment ID or false if none.
	 */
	public function get_previous_avatar( int $user_id ) {
		$avatar_id = get_user_meta( $user_id, self::PREVIOUS_AVATAR_META_KEY, true );
		return $avatar_id ? (int) $avatar_id : false;
	}

	/**
	 * Clear previous avatar for a user.
	 *
	 * @param int $user_id User ID.
	 * @return bool Success status.
	 */
	public function clear_previous_avatar( int $user_id ): bool {
		return delete_user_meta( $user_id, self::PREVIOUS_AVATAR_META_KEY );
	}
}
