<?php
/**
 * Audit Repository for database operations.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Audit;

/**
 * Class AuditRepository
 *
 * Manages database operations for audit logs.
 */
class AuditRepository {

	/**
	 * Table name for audit logs.
	 *
	 * @var string
	 */
	private string $table_name;

	/**
	 * WordPress database instance.
	 *
	 * @var \wpdb
	 */
	private \wpdb $wpdb;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb       = $wpdb;
		$this->table_name = $wpdb->prefix . 'avatar_steward_audit_logs';
	}

	/**
	 * Create the audit logs table.
	 *
	 * @return void
	 */
	public function create_table(): void {
		$charset_collate = $this->wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			event_type varchar(50) NOT NULL,
			event_action varchar(100) NOT NULL,
			object_id bigint(20) unsigned DEFAULT NULL,
			object_type varchar(50) DEFAULT NULL,
			old_value text DEFAULT NULL,
			new_value text DEFAULT NULL,
			ip_address varchar(45) DEFAULT NULL,
			user_agent varchar(255) DEFAULT NULL,
			metadata text DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY event_type (event_type),
			KEY event_action (event_action),
			KEY created_at (created_at),
			KEY user_event_date (user_id, event_type, created_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Insert a new audit log entry.
	 *
	 * @param array<string, mixed> $data Log entry data.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( array $data ) {
		$defaults = array(
			'user_id'      => 0,
			'event_type'   => '',
			'event_action' => '',
			'object_id'    => null,
			'object_type'  => null,
			'old_value'    => null,
			'new_value'    => null,
			'ip_address'   => $this->get_client_ip(),
			'user_agent'   => $this->get_user_agent(),
			'metadata'     => null,
			'created_at'   => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		// Serialize metadata if it's an array.
		if ( is_array( $data['metadata'] ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			$data['metadata'] = json_encode( $data['metadata'] );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		return $this->wpdb->insert(
			$this->table_name,
			$data,
			array(
				'%d', // user_id.
				'%s', // event_type.
				'%s', // event_action.
				'%d', // object_id.
				'%s', // object_type.
				'%s', // old_value.
				'%s', // new_value.
				'%s', // ip_address.
				'%s', // user_agent.
				'%s', // metadata.
				'%s', // created_at.
			)
		);
	}

	/**
	 * Get audit logs with filters.
	 *
	 * @param array<string, mixed> $args Query arguments.
	 * @return array<object> List of audit log entries.
	 */
	public function get_logs( array $args = array() ): array {
		$defaults = array(
			'user_id'      => null,
			'event_type'   => null,
			'event_action' => null,
			'date_from'    => null,
			'date_to'      => null,
			'limit'        => 50,
			'offset'       => 0,
			'orderby'      => 'created_at',
			'order'        => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		$where = array( '1=1' );

		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $this->wpdb->prepare( 'user_id = %d', $args['user_id'] );
		}

		if ( ! empty( $args['event_type'] ) ) {
			$where[] = $this->wpdb->prepare( 'event_type = %s', $args['event_type'] );
		}

		if ( ! empty( $args['event_action'] ) ) {
			$where[] = $this->wpdb->prepare( 'event_action = %s', $args['event_action'] );
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where[] = $this->wpdb->prepare( 'created_at >= %s', $args['date_from'] );
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[] = $this->wpdb->prepare( 'created_at <= %s', $args['date_to'] );
		}

		$where_clause = implode( ' AND ', $where );

		$orderby = in_array( $args['orderby'], array( 'id', 'user_id', 'event_type', 'created_at' ), true )
			? $args['orderby']
			: 'created_at';

		$order = ( 'ASC' === strtoupper( $args['order'] ) ) ? 'ASC' : 'DESC';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
				$args['limit'],
				$args['offset']
			)
		);

		return $results ? $results : array();
	}

	/**
	 * Get total count of logs matching filters.
	 *
	 * @param array<string, mixed> $args Query arguments.
	 * @return int Total count.
	 */
	public function count_logs( array $args = array() ): int {
		$defaults = array(
			'user_id'      => null,
			'event_type'   => null,
			'event_action' => null,
			'date_from'    => null,
			'date_to'      => null,
		);

		$args = wp_parse_args( $args, $defaults );

		$where = array( '1=1' );

		if ( ! empty( $args['user_id'] ) ) {
			$where[] = $this->wpdb->prepare( 'user_id = %d', $args['user_id'] );
		}

		if ( ! empty( $args['event_type'] ) ) {
			$where[] = $this->wpdb->prepare( 'event_type = %s', $args['event_type'] );
		}

		if ( ! empty( $args['event_action'] ) ) {
			$where[] = $this->wpdb->prepare( 'event_action = %s', $args['event_action'] );
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where[] = $this->wpdb->prepare( 'created_at >= %s', $args['date_from'] );
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[] = $this->wpdb->prepare( 'created_at <= %s', $args['date_to'] );
		}

		$where_clause = implode( ' AND ', $where );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$count = $this->wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->table_name} WHERE {$where_clause}"
		);

		return (int) $count;
	}

	/**
	 * Delete logs older than specified days.
	 *
	 * @param int $days Number of days to retain.
	 * @return int|false Number of rows deleted, or false on error.
	 */
	public function purge_old_logs( int $days = 90 ) {
		$cutoff_date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $this->wpdb->query(
			$this->wpdb->prepare(
				"DELETE FROM {$this->table_name} WHERE created_at < %s",
				$cutoff_date
			)
		);
	}

	/**
	 * Get unique event types.
	 *
	 * @return array<string> List of event types.
	 */
	public function get_event_types(): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $this->wpdb->get_col(
			"SELECT DISTINCT event_type FROM {$this->table_name} ORDER BY event_type ASC"
		);

		return $results ? $results : array();
	}

	/**
	 * Get client IP address.
	 *
	 * @return string IP address.
	 */
	private function get_client_ip(): string {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		return $ip;
	}

	/**
	 * Get user agent string.
	 *
	 * @return string User agent.
	 */
	private function get_user_agent(): string {
		return ! empty( $_SERVER['HTTP_USER_AGENT'] )
			? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) )
			: '';
	}

	/**
	 * Get table name.
	 *
	 * @return string Table name.
	 */
	public function get_table_name(): string {
		return $this->table_name;
	}
}
