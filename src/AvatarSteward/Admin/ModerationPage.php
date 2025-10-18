<?php
/**
 * Moderation Page class.
 *
 * Handles the Avatar Steward moderation page in WordPress admin.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Moderation\ModerationQueue;
use AvatarSteward\Domain\Moderation\DecisionService;

/**
 * Class ModerationPage
 *
 * Manages the Avatar Steward moderation panel.
 */
class ModerationPage {

	/**
	 * Moderation queue instance.
	 *
	 * @var ModerationQueue
	 */
	private ModerationQueue $queue;

	/**
	 * Decision service instance.
	 *
	 * @var DecisionService
	 */
	private DecisionService $decision_service;

	/**
	 * Constructor.
	 *
	 * @param ModerationQueue $queue            Moderation queue instance.
	 * @param DecisionService $decision_service Decision service instance.
	 */
	public function __construct( ModerationQueue $queue, DecisionService $decision_service ) {
		$this->queue            = $queue;
		$this->decision_service = $decision_service;
	}

	/**
	 * Initialize the moderation page.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_post_avatarsteward_moderate', array( $this, 'handle_moderation_action' ) );
	}

	/**
	 * Register the moderation page in the admin menu.
	 *
	 * @return void
	 */
	public function register_menu_page(): void {
		if ( ! function_exists( 'add_menu_page' ) ) {
			return;
		}

		$pending_count = $this->queue->get_count_by_status( ModerationQueue::STATUS_PENDING );
		$menu_title    = __( 'Avatar Moderation', 'avatar-steward' );

		if ( $pending_count > 0 ) {
			$menu_title .= sprintf( ' <span class="awaiting-mod">%d</span>', $pending_count );
		}

		add_menu_page(
			__( 'Avatar Moderation', 'avatar-steward' ),
			$menu_title,
			'moderate_comments',
			'avatar-steward-moderation',
			array( $this, 'render_moderation_page' ),
			'dashicons-id-alt',
			30
		);
	}

	/**
	 * Render the moderation page.
	 *
	 * @return void
	 */
	public function render_moderation_page(): void {
		if ( ! current_user_can( 'moderate_comments' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'avatar-steward' ) );
		}

		// Get filter parameters.
		$current_status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : ModerationQueue::STATUS_PENDING;
		$current_role   = isset( $_GET['role'] ) ? sanitize_text_field( wp_unslash( $_GET['role'] ) ) : '';
		$search_query   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$paged          = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
		$per_page       = 20;

		// Get avatars.
		$avatars = $this->queue->get_pending_avatars(
			array(
				'status' => $current_status,
				'role'   => $current_role,
				'search' => $search_query,
				'limit'  => $per_page,
				'offset' => ( $paged - 1 ) * $per_page,
			)
		);

		// Get counts for status tabs.
		$pending_count  = $this->queue->get_count_by_status( ModerationQueue::STATUS_PENDING );
		$approved_count = $this->queue->get_count_by_status( ModerationQueue::STATUS_APPROVED );
		$rejected_count = $this->queue->get_count_by_status( ModerationQueue::STATUS_REJECTED );

		// Get total for pagination.
		$total_items = $this->queue->get_count_by_status( $current_status );

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Avatar Moderation', 'avatar-steward' ); ?></h1>

			<?php
			if ( isset( $_GET['message'] ) ) {
				$message = sanitize_text_field( wp_unslash( $_GET['message'] ) );
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo esc_html( $message ); ?></p>
				</div>
				<?php
			}
			?>

			<hr class="wp-header-end">

			<!-- Status Filter Tabs -->
			<ul class="subsubsub">
				<li>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=avatar-steward-moderation&status=' . ModerationQueue::STATUS_PENDING ) ); ?>" 
					   class="<?php echo $current_status === ModerationQueue::STATUS_PENDING ? 'current' : ''; ?>">
						<?php
						printf(
							/* translators: %s: number of pending avatars */
							esc_html__( 'Pending (%s)', 'avatar-steward' ),
							esc_html( number_format_i18n( $pending_count ) )
						);
						?>
					</a> |
				</li>
				<li>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=avatar-steward-moderation&status=' . ModerationQueue::STATUS_APPROVED ) ); ?>" 
					   class="<?php echo $current_status === ModerationQueue::STATUS_APPROVED ? 'current' : ''; ?>">
						<?php
						printf(
							/* translators: %s: number of approved avatars */
							esc_html__( 'Approved (%s)', 'avatar-steward' ),
							esc_html( number_format_i18n( $approved_count ) )
						);
						?>
					</a> |
				</li>
				<li>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=avatar-steward-moderation&status=' . ModerationQueue::STATUS_REJECTED ) ); ?>" 
					   class="<?php echo $current_status === ModerationQueue::STATUS_REJECTED ? 'current' : ''; ?>">
						<?php
						printf(
							/* translators: %s: number of rejected avatars */
							esc_html__( 'Rejected (%s)', 'avatar-steward' ),
							esc_html( number_format_i18n( $rejected_count ) )
						);
						?>
					</a>
				</li>
			</ul>

			<!-- Search and Filter Form -->
			<p class="search-box">
				<form method="get" action="">
					<input type="hidden" name="page" value="avatar-steward-moderation">
					<input type="hidden" name="status" value="<?php echo esc_attr( $current_status ); ?>">
					<label class="screen-reader-text" for="user-search-input"><?php esc_html_e( 'Search Users:', 'avatar-steward' ); ?></label>
					<input type="search" id="user-search-input" name="s" value="<?php echo esc_attr( $search_query ); ?>">
					<?php submit_button( __( 'Search Users', 'avatar-steward' ), '', '', false, array( 'id' => 'search-submit' ) ); ?>
				</form>
			</p>

			<!-- Moderation Table -->
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'avatarsteward_moderate_action', 'avatarsteward_moderate_nonce' ); ?>
				<input type="hidden" name="action" value="avatarsteward_moderate">
				<input type="hidden" name="current_page" value="<?php echo esc_url( admin_url( 'admin.php?page=avatar-steward-moderation&status=' . $current_status ) ); ?>">

				<div class="tablenav top">
					<div class="alignleft actions bulkactions">
						<label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'avatar-steward' ); ?></label>
						<select name="bulk_action" id="bulk-action-selector-top">
							<option value="-1"><?php esc_html_e( 'Bulk Actions', 'avatar-steward' ); ?></option>
							<option value="approve"><?php esc_html_e( 'Approve', 'avatar-steward' ); ?></option>
							<option value="reject"><?php esc_html_e( 'Reject', 'avatar-steward' ); ?></option>
						</select>
						<?php submit_button( __( 'Apply', 'avatar-steward' ), 'action', '', false ); ?>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped table-view-list">
					<thead>
						<tr>
							<td class="manage-column column-cb check-column">
								<input id="cb-select-all-1" type="checkbox">
							</td>
							<th scope="col" class="manage-column"><?php esc_html_e( 'Avatar', 'avatar-steward' ); ?></th>
							<th scope="col" class="manage-column"><?php esc_html_e( 'User', 'avatar-steward' ); ?></th>
							<th scope="col" class="manage-column"><?php esc_html_e( 'Role', 'avatar-steward' ); ?></th>
							<th scope="col" class="manage-column"><?php esc_html_e( 'Uploaded', 'avatar-steward' ); ?></th>
							<th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'avatar-steward' ); ?></th>
							<th scope="col" class="manage-column"><?php esc_html_e( 'Actions', 'avatar-steward' ); ?></th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php if ( empty( $avatars ) ) : ?>
							<tr>
								<td colspan="7">
									<?php esc_html_e( 'No avatars found.', 'avatar-steward' ); ?>
								</td>
							</tr>
						<?php else : ?>
							<?php foreach ( $avatars as $avatar ) : ?>
								<tr>
									<th scope="row" class="check-column">
										<input type="checkbox" name="user_ids[]" value="<?php echo esc_attr( $avatar['user_id'] ); ?>">
									</th>
									<td>
										<?php if ( $avatar['avatar_url'] ) : ?>
											<img src="<?php echo esc_url( $avatar['avatar_url'] ); ?>" 
												 alt="<?php echo esc_attr( $avatar['display_name'] ); ?>" 
												 width="64" height="64" style="border-radius: 50%;">
										<?php endif; ?>
									</td>
									<td>
										<strong><?php echo esc_html( $avatar['display_name'] ); ?></strong><br>
										<small><?php echo esc_html( $avatar['user_email'] ); ?></small>
									</td>
									<td>
										<?php
										if ( ! empty( $avatar['roles'] ) ) {
											echo esc_html( ucfirst( $avatar['roles'][0] ) );
										}
										?>
									</td>
									<td>
										<?php echo esc_html( human_time_diff( strtotime( $avatar['uploaded_at'] ), current_time( 'timestamp' ) ) ); ?> ago
									</td>
									<td>
										<?php
										$status_class = 'pending' === $avatar['status'] ? 'notice-warning' : ( 'approved' === $avatar['status'] ? 'notice-success' : 'notice-error' );
										?>
										<span class="notice <?php echo esc_attr( $status_class ); ?> inline" style="padding: 2px 8px; margin: 0;">
											<?php echo esc_html( ucfirst( $avatar['status'] ) ); ?>
										</span>
									</td>
									<td>
										<?php if ( ModerationQueue::STATUS_PENDING === $avatar['status'] ) : ?>
											<button type="submit" name="moderate_action" value="approve_<?php echo esc_attr( $avatar['user_id'] ); ?>" 
													class="button button-small button-primary">
												<?php esc_html_e( 'Approve', 'avatar-steward' ); ?>
											</button>
											<button type="submit" name="moderate_action" value="reject_<?php echo esc_attr( $avatar['user_id'] ); ?>" 
													class="button button-small button-link-delete">
												<?php esc_html_e( 'Reject', 'avatar-steward' ); ?>
											</button>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<!-- Pagination -->
				<?php
				if ( $total_items > $per_page ) {
					$total_pages = ceil( $total_items / $per_page );
					?>
					<div class="tablenav bottom">
						<div class="tablenav-pages">
							<span class="displaying-num">
								<?php
								printf(
									/* translators: %s: number of items */
									esc_html__( '%s items', 'avatar-steward' ),
									esc_html( number_format_i18n( $total_items ) )
								);
								?>
							</span>
							<?php
							$base_url = admin_url( 'admin.php?page=avatar-steward-moderation&status=' . $current_status );
							if ( $search_query ) {
								$base_url .= '&s=' . urlencode( $search_query );
							}

							echo wp_kses(
								paginate_links(
									array(
										'base'      => $base_url . '%_%',
										'format'    => '&paged=%#%',
										'current'   => $paged,
										'total'     => $total_pages,
										'prev_text' => '&laquo;',
										'next_text' => '&raquo;',
									)
								),
								array(
									'a'    => array(
										'class' => array(),
										'href'  => array(),
									),
									'span' => array(
										'class'      => array(),
										'aria-current' => array(),
									),
								)
							);
							?>
						</div>
					</div>
					<?php
				}
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle moderation actions.
	 *
	 * @return void
	 */
	public function handle_moderation_action(): void {
		// Verify nonce.
		if ( ! isset( $_POST['avatarsteward_moderate_nonce'] ) || 
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avatarsteward_moderate_nonce'] ) ), 'avatarsteward_moderate_action' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'avatar-steward' ) );
		}

		// Check permissions.
		if ( ! current_user_can( 'moderate_comments' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to perform this action.', 'avatar-steward' ) );
		}

		$moderator_id = get_current_user_id();
		$redirect_url = isset( $_POST['current_page'] ) ? esc_url_raw( wp_unslash( $_POST['current_page'] ) ) : admin_url( 'admin.php?page=avatar-steward-moderation' );

		// Handle bulk actions.
		if ( isset( $_POST['bulk_action'] ) && '-1' !== $_POST['bulk_action'] && ! empty( $_POST['user_ids'] ) ) {
			$bulk_action = sanitize_text_field( wp_unslash( $_POST['bulk_action'] ) );
			$user_ids    = array_map( 'intval', wp_unslash( $_POST['user_ids'] ) );

			if ( 'approve' === $bulk_action ) {
				$result = $this->decision_service->bulk_approve( $user_ids, $moderator_id );
				$redirect_url = add_query_arg( 'message', rawurlencode( $result['message'] ), $redirect_url );
			} elseif ( 'reject' === $bulk_action ) {
				$result = $this->decision_service->bulk_reject( $user_ids, $moderator_id );
				$redirect_url = add_query_arg( 'message', rawurlencode( $result['message'] ), $redirect_url );
			}
		}

		// Handle individual actions.
		if ( isset( $_POST['moderate_action'] ) ) {
			$action_parts = explode( '_', sanitize_text_field( wp_unslash( $_POST['moderate_action'] ) ), 2 );
			if ( count( $action_parts ) === 2 ) {
				$action  = $action_parts[0];
				$user_id = (int) $action_parts[1];

				if ( 'approve' === $action ) {
					$result = $this->decision_service->approve( $user_id, $moderator_id );
					$redirect_url = add_query_arg( 'message', rawurlencode( $result['message'] ), $redirect_url );
				} elseif ( 'reject' === $action ) {
					$result = $this->decision_service->reject( $user_id, $moderator_id );
					$redirect_url = add_query_arg( 'message', rawurlencode( $result['message'] ), $redirect_url );
				}
			}
		}

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
