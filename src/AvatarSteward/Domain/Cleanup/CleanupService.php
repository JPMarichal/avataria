<?php
/**
 * Cleanup Service class for managing inactive avatar deletion.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Cleanup;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Handles identification and deletion of inactive avatars.
 */
class CleanupService {

	/**
	 * User meta key for storing local avatar ID.
	 *
	 * @var string
	 */
	private const META_KEY = 'avatar_steward_avatar';

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface|null $logger Optional logger instance.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Find inactive avatars based on criteria.
	 *
	 * @param array<string, mixed> $criteria Criteria for identifying inactive avatars.
	 *                                       - 'max_age_days': int - Maximum age in days (default: 365).
	 *                                       - 'exclude_active_users': bool - Exclude avatars of active users (default: true).
	 *                                       - 'user_inactivity_days': int - Days of user inactivity (default: 180).
	 *
	 * @return array<int> Array of attachment IDs.
	 */
	public function find_inactive_avatars( array $criteria = array() ): array {
		$defaults = array(
			'max_age_days'         => 365,
			'exclude_active_users' => true,
			'user_inactivity_days' => 180,
		);

		$criteria = wp_parse_args( $criteria, $defaults );

		if ( $this->logger ) {
			$this->logger->info( 'Finding inactive avatars', $criteria );
		}

		$inactive_avatars = array();

		// Get all users with avatars.
		$users_with_avatars = $this->get_users_with_avatars();

		foreach ( $users_with_avatars as $user_id => $attachment_id ) {
			if ( $this->is_avatar_inactive( $user_id, $attachment_id, $criteria ) ) {
				$inactive_avatars[] = $attachment_id;
			}
		}

		if ( $this->logger ) {
			$this->logger->info( 'Found inactive avatars', array( 'count' => count( $inactive_avatars ) ) );
		}

		return $inactive_avatars;
	}

	/**
	 * Delete inactive avatars.
	 *
	 * @param array<int>           $attachment_ids Array of attachment IDs to delete.
	 * @param array<string, mixed> $options        Options for deletion.
	 *                                             - 'dry_run': bool - If true, only return what would be deleted (default: false).
	 *                                             - 'notify_users': bool - Send notifications to users (default: false).
	 *                                             - 'notify_admins': bool - Send notifications to admins (default: false).
	 *
	 * @return array<string, mixed> Result with 'deleted', 'failed', and 'dry_run' keys.
	 */
	public function delete_inactive_avatars( array $attachment_ids, array $options = array() ): array {
		$defaults = array(
			'dry_run'       => false,
			'notify_users'  => false,
			'notify_admins' => false,
		);

		$options = wp_parse_args( $options, $defaults );

		$result = array(
			'deleted' => array(),
			'failed'  => array(),
			'dry_run' => $options['dry_run'],
		);

		if ( empty( $attachment_ids ) ) {
			return $result;
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Deleting inactive avatars',
				array(
					'count'   => count( $attachment_ids ),
					'dry_run' => $options['dry_run'],
				)
			);
		}

		foreach ( $attachment_ids as $attachment_id ) {
			// Get user ID before deletion.
			$user_id = $this->get_user_by_avatar( $attachment_id );

			if ( $options['dry_run'] ) {
				$result['deleted'][] = array(
					'attachment_id' => $attachment_id,
					'user_id'       => $user_id,
				);
				continue;
			}

			// Send notifications before deletion.
			if ( $options['notify_users'] && $user_id ) {
				$this->notify_user_before_deletion( $user_id, $attachment_id );
			}

			// Delete avatar and user meta.
			if ( $this->delete_avatar( $attachment_id, $user_id ) ) {
				$result['deleted'][] = array(
					'attachment_id' => $attachment_id,
					'user_id'       => $user_id,
				);
			} else {
				$result['failed'][] = array(
					'attachment_id' => $attachment_id,
					'user_id'       => $user_id,
				);
			}
		}

		// Send admin notification.
		if ( $options['notify_admins'] && ! $options['dry_run'] ) {
			$this->notify_admins_after_deletion( $result );
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Completed avatar deletion',
				array(
					'deleted' => count( $result['deleted'] ),
					'failed'  => count( $result['failed'] ),
				)
			);
		}

		return $result;
	}

	/**
	 * Schedule the cleanup cron job.
	 *
	 * @param string $recurrence Recurrence schedule (daily, weekly, monthly). Default: weekly.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function schedule_cleanup( string $recurrence = 'weekly' ): bool {
		$hook = 'avatarsteward_cleanup_inactive_avatars';

		// Unschedule existing event if any.
		$timestamp = wp_next_scheduled( $hook );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook );
		}

		// Schedule new event.
		$result = wp_schedule_event( time(), $recurrence, $hook );

		if ( $this->logger ) {
			$this->logger->info(
				'Scheduled cleanup cron job',
				array(
					'recurrence' => $recurrence,
					'success'    => false !== $result,
				)
			);
		}

		return false !== $result;
	}

	/**
	 * Unschedule the cleanup cron job.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function unschedule_cleanup(): bool {
		$hook      = 'avatarsteward_cleanup_inactive_avatars';
		$timestamp = wp_next_scheduled( $hook );

		if ( $timestamp ) {
			$result = wp_unschedule_event( $timestamp, $hook );

			if ( $this->logger ) {
				$this->logger->info( 'Unscheduled cleanup cron job' );
			}

			return $result;
		}

		return true;
	}

	/**
	 * Get all users with avatars.
	 *
	 * @return array<int, int> Array of user_id => attachment_id.
	 */
	private function get_users_with_avatars(): array {
		global $wpdb;

		if ( ! isset( $wpdb->usermeta ) ) {
			return array();
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value != ''",
				self::META_KEY
			),
			ARRAY_A
		);

		$users_with_avatars = array();
		if ( is_array( $results ) ) {
			foreach ( $results as $row ) {
				$users_with_avatars[ (int) $row['user_id'] ] = (int) $row['meta_value'];
			}
		}

		return $users_with_avatars;
	}

	/**
	 * Check if an avatar is inactive based on criteria.
	 *
	 * @param int                  $user_id       User ID.
	 * @param int                  $attachment_id Attachment ID.
	 * @param array<string, mixed> $criteria      Criteria for inactivity.
	 *
	 * @return bool True if inactive, false otherwise.
	 */
	private function is_avatar_inactive( int $user_id, int $attachment_id, array $criteria ): bool {
		// Check if attachment exists.
		$attachment = get_post( $attachment_id );
		if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
			return false;
		}

		// Check avatar age.
		$post_date    = strtotime( $attachment->post_date );
		$current_time = time();
		$age_in_days  = ( $current_time - $post_date ) / DAY_IN_SECONDS;
		$max_age_days = (int) $criteria['max_age_days'];

		if ( $age_in_days < $max_age_days ) {
			return false;
		}

		// Check user activity if required.
		if ( $criteria['exclude_active_users'] ) {
			$user = get_user_by( 'id', $user_id );
			if ( ! $user ) {
				// User doesn't exist, avatar is inactive.
				return true;
			}

			// Check last login or activity.
			$last_login = get_user_meta( $user_id, 'last_login', true );
			if ( $last_login ) {
				$last_login_time = strtotime( $last_login );
				$inactivity_days = ( $current_time - $last_login_time ) / DAY_IN_SECONDS;

				if ( $inactivity_days < (int) $criteria['user_inactivity_days'] ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get user ID by avatar attachment ID.
	 *
	 * @param int $attachment_id Attachment ID.
	 *
	 * @return int|null User ID or null if not found.
	 */
	private function get_user_by_avatar( int $attachment_id ): ?int {
		global $wpdb;

		if ( ! isset( $wpdb->usermeta ) ) {
			return null;
		}

		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %d LIMIT 1",
				self::META_KEY,
				$attachment_id
			)
		);

		return $user_id ? (int) $user_id : null;
	}

	/**
	 * Delete an avatar and associated user meta.
	 *
	 * @param int      $attachment_id Attachment ID.
	 * @param int|null $user_id       User ID (optional).
	 *
	 * @return bool True on success, false on failure.
	 */
	private function delete_avatar( int $attachment_id, ?int $user_id ): bool {
		// Delete user meta if user ID provided.
		if ( $user_id ) {
			delete_user_meta( $user_id, self::META_KEY );
		}

		// Delete attachment.
		$deleted = wp_delete_attachment( $attachment_id, true );

		return false !== $deleted;
	}

	/**
	 * Notify user before avatar deletion.
	 *
	 * @param int $user_id       User ID.
	 * @param int $attachment_id Attachment ID (reserved for future use).
	 *
	 * @return bool True on success, false on failure.
	 */
	private function notify_user_before_deletion( int $user_id, int $attachment_id ): bool { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		$subject = __( 'Your avatar will be deleted', 'avatar-steward' );
		/* translators: 1: User display name, 2: Site name */
		$message_template = __(
			'Hello %1$s,

Your avatar on %2$s has been marked for deletion due to inactivity. If you wish to keep your avatar, please log in to your account.

Thank you,',
			'avatar-steward'
		);
		$message          = sprintf(
			$message_template,
			$user->display_name,
			get_bloginfo( 'name' )
		);

		$result = wp_mail( $user->user_email, $subject, $message );

		if ( $this->logger ) {
			$this->logger->info(
				'Sent deletion notification to user',
				array(
					'user_id' => $user_id,
					'success' => $result,
				)
			);
		}

		return $result;
	}

	/**
	 * Notify admins after avatar deletion.
	 *
	 * @param array<string, mixed> $result Deletion result.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function notify_admins_after_deletion( array $result ): bool {
		$admin_email = get_option( 'admin_email' );
		if ( ! $admin_email ) {
			return false;
		}

		$deleted_count = count( $result['deleted'] );
		$failed_count  = count( $result['failed'] );

		$subject = __( 'Avatar Cleanup Report', 'avatar-steward' );
		/* translators: 1: Number of deleted avatars, 2: Number of failed deletions, 3: Site name */
		$message_template = __(
			'Avatar Cleanup Report for %3$s

Deleted: %1$d avatars
Failed: %2$d avatars

',
			'avatar-steward'
		);
		$message          = sprintf(
			$message_template,
			$deleted_count,
			$failed_count,
			get_bloginfo( 'name' )
		);

		$result_mail = wp_mail( $admin_email, $subject, $message );

		if ( $this->logger ) {
			$this->logger->info(
				'Sent cleanup report to admin',
				array(
					'deleted' => $deleted_count,
					'failed'  => $failed_count,
					'success' => $result_mail,
				)
			);
		}

		return $result_mail;
	}
}
