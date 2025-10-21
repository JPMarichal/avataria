<?php
/**
 * Audit Service.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Audit;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Class AuditService
 *
 * Service for recording and managing audit logs.
 */
class AuditService {

	/**
	 * Event type constants.
	 */
	public const EVENT_TYPE_AVATAR     = 'avatar';
	public const EVENT_TYPE_MODERATION = 'moderation';
	public const EVENT_TYPE_METADATA   = 'metadata';
	public const EVENT_TYPE_LIBRARY    = 'library';
	public const EVENT_TYPE_SOCIAL     = 'social';
	public const EVENT_TYPE_SYSTEM     = 'system';

	/**
	 * Audit repository instance.
	 *
	 * @var AuditRepository
	 */
	private AuditRepository $repository;

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param AuditRepository      $repository Audit repository instance.
	 * @param LoggerInterface|null $logger     Logger instance (optional).
	 */
	public function __construct( AuditRepository $repository, ?LoggerInterface $logger = null ) {
		$this->repository = $repository;
		$this->logger     = $logger;
	}

	/**
	 * Log an avatar upload event.
	 *
	 * @param int   $user_id       User ID.
	 * @param int   $attachment_id Attachment ID.
	 * @param array $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_avatar_upload( int $user_id, int $attachment_id, array $metadata = array() ): bool {
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_AVATAR,
			'avatar_uploaded',
			$attachment_id,
			'attachment',
			null,
			null,
			$metadata
		);
	}

	/**
	 * Log an avatar deletion event.
	 *
	 * @param int   $user_id       User ID.
	 * @param int   $attachment_id Attachment ID.
	 * @param array $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_avatar_deletion( int $user_id, int $attachment_id, array $metadata = array() ): bool {
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_AVATAR,
			'avatar_deleted',
			$attachment_id,
			'attachment',
			null,
			null,
			$metadata
		);
	}

	/**
	 * Log an avatar change event.
	 *
	 * @param int   $user_id           User ID.
	 * @param int   $old_attachment_id Old attachment ID.
	 * @param int   $new_attachment_id New attachment ID.
	 * @param array $metadata          Additional metadata.
	 * @return bool Success status.
	 */
	public function log_avatar_change( int $user_id, int $old_attachment_id, int $new_attachment_id, array $metadata = array() ): bool {
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_AVATAR,
			'avatar_changed',
			$new_attachment_id,
			'attachment',
			(string) $old_attachment_id,
			(string) $new_attachment_id,
			$metadata
		);
	}

	/**
	 * Log a moderation approval event.
	 *
	 * @param int   $user_id       User ID whose avatar was approved.
	 * @param int   $moderator_id  Moderator user ID.
	 * @param int   $attachment_id Attachment ID.
	 * @param array $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_moderation_approval( int $user_id, int $moderator_id, int $attachment_id, array $metadata = array() ): bool {
		$metadata['moderator_id'] = $moderator_id;
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_MODERATION,
			'avatar_approved',
			$attachment_id,
			'attachment',
			'pending',
			'approved',
			$metadata
		);
	}

	/**
	 * Log a moderation rejection event.
	 *
	 * @param int    $user_id       User ID whose avatar was rejected.
	 * @param int    $moderator_id  Moderator user ID.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $reason        Rejection reason.
	 * @param array  $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_moderation_rejection( int $user_id, int $moderator_id, int $attachment_id, string $reason, array $metadata = array() ): bool {
		$metadata['moderator_id'] = $moderator_id;
		$metadata['reason']       = $reason;
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_MODERATION,
			'avatar_rejected',
			$attachment_id,
			'attachment',
			'pending',
			'rejected',
			$metadata
		);
	}

	/**
	 * Log a metadata change event.
	 *
	 * @param int    $user_id   User ID.
	 * @param string $meta_key  Meta key that changed.
	 * @param mixed  $old_value Old value.
	 * @param mixed  $new_value New value.
	 * @param array  $metadata  Additional metadata.
	 * @return bool Success status.
	 */
	public function log_metadata_change( int $user_id, string $meta_key, $old_value, $new_value, array $metadata = array() ): bool {
		$metadata['meta_key'] = $meta_key;
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_METADATA,
			'metadata_changed',
			null,
			'user_meta',
			is_scalar( $old_value ) ? (string) $old_value : wp_json_encode( $old_value ),
			is_scalar( $new_value ) ? (string) $new_value : wp_json_encode( $new_value ),
			$metadata
		);
	}

	/**
	 * Log a library avatar selection event.
	 *
	 * @param int   $user_id       User ID.
	 * @param int   $library_id    Library avatar ID.
	 * @param int   $attachment_id Resulting attachment ID.
	 * @param array $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_library_selection( int $user_id, int $library_id, int $attachment_id, array $metadata = array() ): bool {
		$metadata['library_id'] = $library_id;
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_LIBRARY,
			'library_avatar_selected',
			$attachment_id,
			'attachment',
			null,
			null,
			$metadata
		);
	}

	/**
	 * Log a social media import event.
	 *
	 * @param int    $user_id       User ID.
	 * @param string $provider      Social provider name.
	 * @param int    $attachment_id Resulting attachment ID.
	 * @param array  $metadata      Additional metadata.
	 * @return bool Success status.
	 */
	public function log_social_import( int $user_id, string $provider, int $attachment_id, array $metadata = array() ): bool {
		$metadata['provider'] = $provider;
		return $this->log_event(
			$user_id,
			self::EVENT_TYPE_SOCIAL,
			'social_import',
			$attachment_id,
			'attachment',
			null,
			null,
			$metadata
		);
	}

	/**
	 * Log a generic audit event.
	 *
	 * @param int         $user_id      User ID.
	 * @param string      $event_type   Event type.
	 * @param string      $event_action Event action.
	 * @param int|null    $object_id    Object ID.
	 * @param string|null $object_type  Object type.
	 * @param string|null $old_value    Old value.
	 * @param string|null $new_value    New value.
	 * @param array       $metadata     Additional metadata.
	 * @return bool Success status.
	 */
	public function log_event(
		int $user_id,
		string $event_type,
		string $event_action,
		?int $object_id = null,
		?string $object_type = null,
		?string $old_value = null,
		?string $new_value = null,
		array $metadata = array()
	): bool {
		$result = $this->repository->insert(
			array(
				'user_id'      => $user_id,
				'event_type'   => $event_type,
				'event_action' => $event_action,
				'object_id'    => $object_id,
				'object_type'  => $object_type,
				'old_value'    => $old_value,
				'new_value'    => $new_value,
				'metadata'     => $metadata,
			)
		);

		// Also log to the standard logger if available.
		if ( $this->logger ) {
			$this->logger->info(
				sprintf( 'Audit: %s - %s', $event_type, $event_action ),
				array(
					'user_id'      => $user_id,
					'event_type'   => $event_type,
					'event_action' => $event_action,
					'object_id'    => $object_id,
					'object_type'  => $object_type,
				)
			);
		}

		return false !== $result;
	}

	/**
	 * Get audit logs with filters.
	 *
	 * @param array $args Query arguments.
	 * @return array List of audit log entries.
	 */
	public function get_logs( array $args = array() ): array {
		return $this->repository->get_logs( $args );
	}

	/**
	 * Get total count of logs.
	 *
	 * @param array $args Query arguments.
	 * @return int Total count.
	 */
	public function count_logs( array $args = array() ): int {
		return $this->repository->count_logs( $args );
	}

	/**
	 * Export logs to CSV format.
	 *
	 * @param array $args Query arguments.
	 * @return string CSV content.
	 */
	public function export_to_csv( array $args = array() ): string {
		$logs = $this->repository->get_logs( array_merge( $args, array( 'limit' => 10000 ) ) );

		$csv = '';

		// Header row.
		$csv .= "ID,User ID,Event Type,Event Action,Object ID,Object Type,Old Value,New Value,IP Address,Created At\n";

		// Data rows.
		foreach ( $logs as $log ) {
			$csv .= sprintf(
				"%d,%d,%s,%s,%s,%s,%s,%s,%s,%s\n",
				$log->id,
				$log->user_id,
				$this->escape_csv( $log->event_type ),
				$this->escape_csv( $log->event_action ),
				$log->object_id ? $log->object_id : '',
				$log->object_type ? $this->escape_csv( $log->object_type ) : '',
				$log->old_value ? $this->escape_csv( $log->old_value ) : '',
				$log->new_value ? $this->escape_csv( $log->new_value ) : '',
				$log->ip_address ? $this->escape_csv( $log->ip_address ) : '',
				$log->created_at
			);
		}

		return $csv;
	}

	/**
	 * Export logs to JSON format.
	 *
	 * @param array $args Query arguments.
	 * @return string JSON content.
	 */
	public function export_to_json( array $args = array() ): string {
		$logs = $this->repository->get_logs( array_merge( $args, array( 'limit' => 10000 ) ) );

		$export = array();
		foreach ( $logs as $log ) {
			$item = array(
				'id'           => (int) $log->id,
				'user_id'      => (int) $log->user_id,
				'event_type'   => $log->event_type,
				'event_action' => $log->event_action,
				'object_id'    => $log->object_id ? (int) $log->object_id : null,
				'object_type'  => $log->object_type,
				'old_value'    => $log->old_value,
				'new_value'    => $log->new_value,
				'ip_address'   => $log->ip_address,
				'user_agent'   => $log->user_agent,
				'created_at'   => $log->created_at,
			);

			// Decode metadata if present.
			if ( ! empty( $log->metadata ) ) {
				$metadata = json_decode( $log->metadata, true );
				if ( is_array( $metadata ) ) {
					$item['metadata'] = $metadata;
				}
			}

			$export[] = $item;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		return json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}

	/**
	 * Purge old logs based on retention settings.
	 *
	 * @param int $days Number of days to retain.
	 * @return int Number of logs deleted.
	 */
	public function purge_old_logs( int $days = 90 ): int {
		$result = $this->repository->purge_old_logs( $days );
		return is_int( $result ) ? $result : 0;
	}

	/**
	 * Get available event types.
	 *
	 * @return array List of event types.
	 */
	public function get_event_types(): array {
		return $this->repository->get_event_types();
	}

	/**
	 * Escape CSV field value.
	 *
	 * @param string $value Value to escape.
	 * @return string Escaped value.
	 */
	private function escape_csv( string $value ): string {
		// Escape double quotes and wrap in quotes if contains special chars.
		if ( strpos( $value, '"' ) !== false || strpos( $value, ',' ) !== false || strpos( $value, "\n" ) !== false ) {
			return '"' . str_replace( '"', '""', $value ) . '"';
		}
		return $value;
	}

	/**
	 * Get audit repository instance.
	 *
	 * @return AuditRepository Repository instance.
	 */
	public function get_repository(): AuditRepository {
		return $this->repository;
	}
}
