<?php
/**
 * Audit Page class.
 *
 * Handles the Avatar Steward audit logs page in WordPress admin.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Audit\AuditService;
use AvatarSteward\Domain\Licensing\LicenseManager;

/**
 * Class AuditPage
 *
 * Manages the Avatar Steward audit logs panel.
 */
class AuditPage {

	/**
	 * Audit service instance.
	 *
	 * @var AuditService
	 */
	private AuditService $audit_service;

	/**
	 * License manager instance.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $license_manager;

	/**
	 * Constructor.
	 *
	 * @param AuditService   $audit_service   Audit service instance.
	 * @param LicenseManager $license_manager License manager instance.
	 */
	public function __construct( AuditService $audit_service, LicenseManager $license_manager ) {
		$this->audit_service   = $audit_service;
		$this->license_manager = $license_manager;
	}

	/**
	 * Initialize the audit page.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_post_avatarsteward_purge_logs', array( $this, 'handle_purge_logs' ) );
		add_action( 'admin_init', array( $this, 'handle_export' ) );
	}

	/**
	 * Register the audit page in the admin menu.
	 *
	 * @return void
	 */
	public function register_menu_page(): void {
		if ( ! function_exists( 'add_submenu_page' ) ) {
			return;
		}

		add_submenu_page(
			'avatar-steward-settings',
			__( 'Audit Logs', 'avatar-steward' ),
			__( 'Audit Logs', 'avatar-steward' ),
			'manage_options',
			'avatar-steward-audit',
			array( $this, 'render_audit_page' )
		);
	}

	/**
	 * Render the audit page.
	 *
	 * @return void
	 */
	public function render_audit_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'avatar-steward' ) );
		}

		// Check Pro license.
		if ( ! $this->license_manager->is_pro_active() ) {
			$this->render_pro_required_message();
			return;
		}

		$this->enqueue_audit_styles();

		// Get filter parameters.
		$user_id      = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : null;
		$event_type   = isset( $_GET['event_type'] ) ? sanitize_text_field( wp_unslash( $_GET['event_type'] ) ) : null;
		$date_from    = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : null;
		$date_to      = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : null;
		$current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		$per_page     = 50;

		// Build query args.
		$args = array(
			'limit'  => $per_page,
			'offset' => ( $current_page - 1 ) * $per_page,
		);

		if ( $user_id ) {
			$args['user_id'] = $user_id;
		}
		if ( $event_type ) {
			$args['event_type'] = $event_type;
		}
		if ( $date_from ) {
			$args['date_from'] = $date_from . ' 00:00:00';
		}
		if ( $date_to ) {
			$args['date_to'] = $date_to . ' 23:59:59';
		}

		// Get logs and count.
		$logs        = $this->audit_service->get_logs( $args );
		$total_logs  = $this->audit_service->count_logs( $args );
		$total_pages = ceil( $total_logs / $per_page );

		// Get available event types for filter.
		$event_types = $this->audit_service->get_event_types();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Audit Logs', 'avatar-steward' ); ?></h1>

			<?php $this->render_filters( $user_id, $event_type, $date_from, $date_to, $event_types ); ?>

			<?php $this->render_export_buttons( $args ); ?>

			<?php $this->render_logs_table( $logs ); ?>

			<?php $this->render_pagination( $current_page, $total_pages, $total_logs ); ?>

			<?php $this->render_purge_section(); ?>
		</div>
		<?php
	}

	/**
	 * Render filter form.
	 *
	 * @param int|null    $user_id     Current user ID filter.
	 * @param string|null $event_type  Current event type filter.
	 * @param string|null $date_from   Current date from filter.
	 * @param string|null $date_to     Current date to filter.
	 * @param array       $event_types Available event types.
	 * @return void
	 */
	private function render_filters( ?int $user_id, ?string $event_type, ?string $date_from, ?string $date_to, array $event_types ): void {
		?>
		<div class="audit-filters" style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; border-radius: 4px;">
			<h2><?php esc_html_e( 'Filters', 'avatar-steward' ); ?></h2>
			<form method="get" action="">
				<input type="hidden" name="page" value="avatar-steward-audit" />
				
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
					<div>
						<label for="user_id"><?php esc_html_e( 'User ID', 'avatar-steward' ); ?></label>
						<input type="number" id="user_id" name="user_id" value="<?php echo esc_attr( $user_id ? $user_id : '' ); ?>" class="regular-text" />
					</div>

					<div>
						<label for="event_type"><?php esc_html_e( 'Event Type', 'avatar-steward' ); ?></label>
						<select id="event_type" name="event_type" class="regular-text">
							<option value=""><?php esc_html_e( 'All Types', 'avatar-steward' ); ?></option>
							<?php foreach ( $event_types as $type ) : ?>
								<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $event_type, $type ); ?>>
									<?php echo esc_html( ucfirst( $type ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div>
						<label for="date_from"><?php esc_html_e( 'Date From', 'avatar-steward' ); ?></label>
						<input type="date" id="date_from" name="date_from" value="<?php echo esc_attr( $date_from ? $date_from : '' ); ?>" class="regular-text" />
					</div>

					<div>
						<label for="date_to"><?php esc_html_e( 'Date To', 'avatar-steward' ); ?></label>
						<input type="date" id="date_to" name="date_to" value="<?php echo esc_attr( $date_to ? $date_to : '' ); ?>" class="regular-text" />
					</div>
				</div>

				<button type="submit" class="button button-primary"><?php esc_html_e( 'Apply Filters', 'avatar-steward' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=avatar-steward-audit' ) ); ?>" class="button"><?php esc_html_e( 'Clear Filters', 'avatar-steward' ); ?></a>
			</form>
		</div>
		<?php
	}

	/**
	 * Render export buttons.
	 *
	 * @param array $args Current query args.
	 * @return void
	 */
	private function render_export_buttons( array $args ): void {
		$export_url_csv  = add_query_arg(
			array_merge( $args, array( 'format' => 'csv' ) ),
			admin_url( 'admin.php?page=avatar-steward-audit&action=export' )
		);
		$export_url_json = add_query_arg(
			array_merge( $args, array( 'format' => 'json' ) ),
			admin_url( 'admin.php?page=avatar-steward-audit&action=export' )
		);
		?>
		<div style="margin: 15px 0;">
			<a href="<?php echo esc_url( wp_nonce_url( $export_url_csv, 'avatarsteward_export_audit' ) ); ?>" class="button">
				<?php esc_html_e( 'Export CSV', 'avatar-steward' ); ?>
			</a>
			<a href="<?php echo esc_url( wp_nonce_url( $export_url_json, 'avatarsteward_export_audit' ) ); ?>" class="button">
				<?php esc_html_e( 'Export JSON', 'avatar-steward' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render logs table.
	 *
	 * @param array $logs List of audit logs.
	 * @return void
	 */
	private function render_logs_table( array $logs ): void {
		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'User', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'Event Type', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'Action', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'Object', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'IP Address', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'Date', 'avatar-steward' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $logs ) ) : ?>
					<tr>
						<td colspan="7"><?php esc_html_e( 'No audit logs found.', 'avatar-steward' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $log->id ); ?></td>
							<td>
								<?php
								$user = get_userdata( $log->user_id );
								if ( $user ) {
									echo esc_html( $user->display_name . ' (' . $user->user_login . ')' );
								} else {
									echo esc_html( 'User #' . $log->user_id );
								}
								?>
							</td>
							<td><?php echo esc_html( ucfirst( $log->event_type ) ); ?></td>
							<td><?php echo esc_html( $log->event_action ); ?></td>
							<td>
								<?php
								if ( $log->object_id ) {
									echo esc_html( $log->object_type . ' #' . $log->object_id );
								} else {
									echo '—';
								}
								?>
							</td>
							<td><?php echo esc_html( $log->ip_address ? $log->ip_address : '—' ); ?></td>
							<td><?php echo esc_html( $log->created_at ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Render pagination.
	 *
	 * @param int $current_page Current page number.
	 * @param int $total_pages  Total number of pages.
	 * @param int $total_logs   Total number of logs.
	 * @return void
	 */
	private function render_pagination( int $current_page, int $total_pages, int $total_logs ): void {
		if ( $total_pages <= 1 ) {
			return;
		}
		?>
		<div class="tablenav" style="margin: 20px 0;">
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php
					// Translators: %s: Number of items.
					echo esc_html( sprintf( _n( '%s item', '%s items', $total_logs, 'avatar-steward' ), number_format_i18n( $total_logs ) ) );
					?>
				</span>
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => __( '&laquo;', 'avatar-steward' ),
							'next_text' => __( '&raquo;', 'avatar-steward' ),
							'total'     => $total_pages,
							'current'   => $current_page,
						)
					)
				);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render log purge section.
	 *
	 * @return void
	 */
	private function render_purge_section(): void {
		$retention_days = get_option( 'avatar_steward_audit_retention_days', 90 );
		?>
		<div class="audit-purge" style="background: #fff; padding: 15px; margin: 20px 0; border: 1px solid #ccd0d4; border-radius: 4px;">
			<h2><?php esc_html_e( 'Log Retention', 'avatar-steward' ); ?></h2>
			<p><?php esc_html_e( 'Logs older than the retention period will be automatically deleted.', 'avatar-steward' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="avatarsteward_purge_logs" />
				<?php wp_nonce_field( 'avatarsteward_purge_logs', 'avatarsteward_purge_nonce' ); ?>
				
				<label for="retention_days"><?php esc_html_e( 'Retention Period (days):', 'avatar-steward' ); ?></label>
				<input type="number" id="retention_days" name="retention_days" value="<?php echo esc_attr( $retention_days ); ?>" min="1" max="365" />
				
				<button type="submit" name="save_retention" class="button button-secondary">
					<?php esc_html_e( 'Save Retention Period', 'avatar-steward' ); ?>
				</button>
				
				<button type="submit" name="purge_now" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to purge old logs? This action cannot be undone.', 'avatar-steward' ); ?>')">
					<?php esc_html_e( 'Purge Old Logs Now', 'avatar-steward' ); ?>
				</button>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle export action.
	 *
	 * @return void
	 */
	public function handle_export(): void {
		if ( ! isset( $_GET['page'] ) || 'avatar-steward-audit' !== $_GET['page'] ) {
			return;
		}

		if ( ! isset( $_GET['action'] ) || 'export' !== $_GET['action'] ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'avatar-steward' ) );
		}

		check_admin_referer( 'avatarsteward_export_audit' );

		if ( ! $this->license_manager->is_pro_active() ) {
			wp_die( esc_html__( 'Export requires an active Pro license.', 'avatar-steward' ) );
		}

		$format = isset( $_GET['format'] ) ? sanitize_text_field( wp_unslash( $_GET['format'] ) ) : 'csv';

		// Build query args from GET parameters.
		$args = array();
		if ( isset( $_GET['user_id'] ) ) {
			$args['user_id'] = absint( $_GET['user_id'] );
		}
		if ( isset( $_GET['event_type'] ) ) {
			$args['event_type'] = sanitize_text_field( wp_unslash( $_GET['event_type'] ) );
		}
		if ( isset( $_GET['date_from'] ) ) {
			$args['date_from'] = sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) . ' 00:00:00';
		}
		if ( isset( $_GET['date_to'] ) ) {
			$args['date_to'] = sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) . ' 23:59:59';
		}

		if ( 'csv' === $format ) {
			$content  = $this->audit_service->export_to_csv( $args );
			$filename = 'audit-logs-' . gmdate( 'Y-m-d' ) . '.csv';
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Expires: 0' );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $content;
			exit;
		} elseif ( 'json' === $format ) {
			$content  = $this->audit_service->export_to_json( $args );
			$filename = 'audit-logs-' . gmdate( 'Y-m-d' ) . '.json';
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( 'Cache-Control: no-cache, must-revalidate' );
			header( 'Expires: 0' );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $content;
			exit;
		}

		wp_die( esc_html__( 'Invalid export format.', 'avatar-steward' ) );
	}

	/**
	 * Handle log purge action.
	 *
	 * @return void
	 */
	public function handle_purge_logs(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'avatar-steward' ) );
		}

		check_admin_referer( 'avatarsteward_purge_logs', 'avatarsteward_purge_nonce' );

		if ( isset( $_POST['save_retention'] ) ) {
			$retention_days = isset( $_POST['retention_days'] ) ? absint( $_POST['retention_days'] ) : 90;
			update_option( 'avatar_steward_audit_retention_days', $retention_days );
			wp_safe_redirect( add_query_arg( 'retention_saved', '1', admin_url( 'admin.php?page=avatar-steward-audit' ) ) );
			exit;
		}

		if ( isset( $_POST['purge_now'] ) ) {
			$retention_days = get_option( 'avatar_steward_audit_retention_days', 90 );
			$deleted        = $this->audit_service->purge_old_logs( $retention_days );
			wp_safe_redirect( add_query_arg( 'logs_purged', $deleted, admin_url( 'admin.php?page=avatar-steward-audit' ) ) );
			exit;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=avatar-steward-audit' ) );
		exit;
	}

	/**
	 * Render Pro required message.
	 *
	 * @return void
	 */
	private function render_pro_required_message(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Audit Logs', 'avatar-steward' ); ?></h1>
			<div class="notice notice-warning">
				<p><?php esc_html_e( 'Audit logs are a Pro feature. Please activate your Pro license to access this functionality.', 'avatar-steward' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue audit page styles.
	 *
	 * @return void
	 */
	private function enqueue_audit_styles(): void {
		?>
		<style>
			.audit-filters label {
				display: block;
				margin-bottom: 5px;
				font-weight: 600;
			}
			.audit-filters input,
			.audit-filters select {
				width: 100%;
			}
		</style>
		<?php
	}
}
