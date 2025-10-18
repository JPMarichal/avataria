<?php
/**
 * License Activation Admin Page.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Licensing\LicenseManager;

/**
 * Admin page for Pro license activation.
 */
class LicensePage {

	/**
	 * License manager instance.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $license_manager;

	/**
	 * Page slug.
	 */
	private const PAGE_SLUG = 'avatar-steward-license';

	/**
	 * Constructor.
	 *
	 * @param LicenseManager $license_manager License manager instance.
	 */
	public function __construct( LicenseManager $license_manager ) {
		$this->license_manager = $license_manager;
	}

	/**
	 * Initialize the page.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'admin_menu', array( $this, 'register_page' ) );
			add_action( 'admin_post_avatar_steward_activate_license', array( $this, 'handle_activation' ) );
			add_action( 'admin_post_avatar_steward_deactivate_license', array( $this, 'handle_deactivation' ) );
		}
	}

	/**
	 * Register the admin page.
	 *
	 * @return void
	 */
	public function register_page(): void {
		add_submenu_page(
			'options-general.php',
			__( 'Avatar Steward Pro License', 'avatar-steward' ),
			__( 'Avatar Steward License', 'avatar-steward' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the license page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'avatar-steward' ) );
		}

		$license_info = $this->license_manager->get_license_info();
		$status       = $license_info['status'];
		$is_active    = $this->license_manager->is_pro_active();

		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Avatar Steward Pro License', 'avatar-steward' ); ?></h1>

			<?php $this->render_admin_notices(); ?>

			<div class="card" style="max-width: 800px;">
				<h2><?php echo esc_html__( 'License Status', 'avatar-steward' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row"><?php echo esc_html__( 'Status', 'avatar-steward' ); ?></th>
						<td>
							<?php $this->render_status_badge( $status ); ?>
						</td>
					</tr>

					<?php if ( $is_active ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html__( 'License Key', 'avatar-steward' ); ?></th>
							<td>
								<code><?php echo esc_html( $license_info['key'] ?? '' ); ?></code>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Activated On', 'avatar-steward' ); ?></th>
							<td><?php echo esc_html( $license_info['activated_at'] ?? '' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Domain', 'avatar-steward' ); ?></th>
							<td><?php echo esc_html( $license_info['domain'] ?? '' ); ?></td>
						</tr>
					<?php endif; ?>
				</table>

				<?php if ( $is_active ) : ?>
					<?php $this->render_deactivation_form(); ?>
				<?php else : ?>
					<?php $this->render_activation_form(); ?>
				<?php endif; ?>
			</div>

			<div class="card" style="max-width: 800px; margin-top: 20px;">
				<h2><?php echo esc_html__( 'Pro Features', 'avatar-steward' ); ?></h2>
				<p><?php echo esc_html__( 'Activate your Pro license to unlock these features:', 'avatar-steward' ); ?></p>
				<ul style="list-style: disc; margin-left: 20px;">
					<li><?php echo esc_html__( 'Avatar Library with predefined avatars', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Social Media Integration (Twitter, Facebook)', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Moderation Panel for avatar approval', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Multiple Avatars per User', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Advanced Upload Restrictions', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Role-based Avatar Permissions', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Auto-deletion of Inactive Avatars', 'avatar-steward' ); ?></li>
					<li><?php echo esc_html__( 'Audit Logs and Compliance Reports', 'avatar-steward' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Render status badge.
	 *
	 * @param string $status License status.
	 * @return void
	 */
	private function render_status_badge( string $status ): void {
		$badge_class = 'badge-default';
		$badge_text  = ucfirst( $status );

		switch ( $status ) {
			case LicenseManager::STATUS_ACTIVE:
				$badge_class = 'badge-success';
				$badge_text  = __( 'Active', 'avatar-steward' );
				break;
			case LicenseManager::STATUS_INACTIVE:
				$badge_class = 'badge-secondary';
				$badge_text  = __( 'Inactive', 'avatar-steward' );
				break;
			case LicenseManager::STATUS_EXPIRED:
				$badge_class = 'badge-warning';
				$badge_text  = __( 'Expired', 'avatar-steward' );
				break;
			case LicenseManager::STATUS_INVALID:
				$badge_class = 'badge-error';
				$badge_text  = __( 'Invalid', 'avatar-steward' );
				break;
		}

		?>
		<span class="avatar-steward-license-badge <?php echo esc_attr( $badge_class ); ?>">
			<?php echo esc_html( $badge_text ); ?>
		</span>
		<style>
			.avatar-steward-license-badge {
				display: inline-block;
				padding: 4px 12px;
				border-radius: 3px;
				font-weight: 600;
				font-size: 12px;
				text-transform: uppercase;
			}
			.badge-success {
				background-color: #46b450;
				color: #fff;
			}
			.badge-secondary {
				background-color: #dcdcde;
				color: #50575e;
			}
			.badge-warning {
				background-color: #f0b849;
				color: #fff;
			}
			.badge-error {
				background-color: #dc3232;
				color: #fff;
			}
		</style>
		<?php
	}

	/**
	 * Render activation form.
	 *
	 * @return void
	 */
	private function render_activation_form(): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'avatar_steward_activate_license' ); ?>
			<input type="hidden" name="action" value="avatar_steward_activate_license" />
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="license_key"><?php echo esc_html__( 'License Key', 'avatar-steward' ); ?></label>
					</th>
					<td>
						<input 
							type="text" 
							id="license_key" 
							name="license_key" 
							class="regular-text" 
							placeholder="XXXX-XXXX-XXXX-XXXX"
							pattern="[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}"
							required
							style="text-transform: uppercase;"
						/>
						<p class="description">
							<?php echo esc_html__( 'Enter your license key in the format: XXXX-XXXX-XXXX-XXXX', 'avatar-steward' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary">
					<?php echo esc_html__( 'Activate License', 'avatar-steward' ); ?>
				</button>
			</p>
		</form>
		<?php
	}

	/**
	 * Render deactivation form.
	 *
	 * @return void
	 */
	private function render_deactivation_form(): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" 
			onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to deactivate your license? Pro features will be disabled.', 'avatar-steward' ) ); ?>');">
			<?php wp_nonce_field( 'avatar_steward_deactivate_license' ); ?>
			<input type="hidden" name="action" value="avatar_steward_deactivate_license" />
			
			<p class="submit">
				<button type="submit" class="button button-secondary">
					<?php echo esc_html__( 'Deactivate License', 'avatar-steward' ); ?>
				</button>
			</p>
		</form>
		<?php
	}

	/**
	 * Handle license activation.
	 *
	 * @return void
	 */
	public function handle_activation(): void {
		check_admin_referer( 'avatar_steward_activate_license' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'avatar-steward' ) );
		}

		$license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';
		$result      = $this->license_manager->activate( $license_key );

		$redirect_url = add_query_arg(
			array(
				'page'    => self::PAGE_SLUG,
				'action'  => 'activated',
				'success' => $result['success'] ? '1' : '0',
				'message' => rawurlencode( $result['message'] ),
			),
			admin_url( 'options-general.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Handle license deactivation.
	 *
	 * @return void
	 */
	public function handle_deactivation(): void {
		check_admin_referer( 'avatar_steward_deactivate_license' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'avatar-steward' ) );
		}

		$result = $this->license_manager->deactivate();

		$redirect_url = add_query_arg(
			array(
				'page'    => self::PAGE_SLUG,
				'action'  => 'deactivated',
				'success' => $result['success'] ? '1' : '0',
				'message' => rawurlencode( $result['message'] ),
			),
			admin_url( 'options-general.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Render admin notices based on URL parameters.
	 *
	 * @return void
	 */
	private function render_admin_notices(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Reading GET parameters for display only.
		if ( ! isset( $_GET['action'] ) || ! isset( $_GET['success'] ) ) {
			return;
		}

		$success = '1' === $_GET['success'];
		$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
		// phpcs:enable

		if ( empty( $message ) ) {
			return;
		}

		$notice_class = $success ? 'notice-success' : 'notice-error';
		?>
		<div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
}
