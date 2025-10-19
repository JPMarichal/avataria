<?php
/**
 * Migration Page class.
 *
 * Handles the Avatar Steward migration admin page.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Migration\MigrationService;

/**
 * Class MigrationPage
 *
 * Manages the Avatar Steward migration page for importing avatars from other plugins.
 */
class MigrationPage {

	/**
	 * Migration service instance.
	 *
	 * @var MigrationService
	 */
	private MigrationService $migration_service;

	/**
	 * Constructor.
	 *
	 * @param MigrationService $migration_service Migration service instance.
	 */
	public function __construct( MigrationService $migration_service ) {
		$this->migration_service = $migration_service;
	}

	/**
	 * Initialize the migration page.
	 *
	 * @return void
	 */
	public function init(): void {
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
	}

	/**
	 * Register the migration page in the admin menu.
	 *
	 * @return void
	 */
	public function register_menu_page(): void {
		if ( ! function_exists( 'add_management_page' ) ) {
			return;
		}

		add_management_page(
			__( 'Avatar Migration', 'avatar-steward' ),
			__( 'Avatar Migration', 'avatar-steward' ),
			'manage_options',
			'avatar-steward-migration',
			array( $this, 'render_migration_page' )
		);
	}

	/**
	 * Render the migration page.
	 *
	 * @return void
	 */
	public function render_migration_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle form submission.
		$results = null;
		if ( isset( $_POST['migration_type'] ) && check_admin_referer( 'avatar_steward_migration' ) ) {
			$migration_type = sanitize_text_field( wp_unslash( $_POST['migration_type'] ) );
			$results        = $this->run_migration( $migration_type );
		}

		// Get migration statistics.
		$stats = $this->migration_service->get_migration_stats();

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<p><?php esc_html_e( 'Migrate existing avatars from other plugins or services to Avatar Steward.', 'avatar-steward' ); ?></p>
			<div class="notice notice-warning">
				<p><strong><?php esc_html_e( 'WARNING: Always test migrations on a staging site before running on production!', 'avatar-steward' ); ?></strong></p>
				<p><?php esc_html_e( 'It is recommended to backup your database before starting any migration.', 'avatar-steward' ); ?></p>
			</div>

			<?php if ( $results ) : ?>
				<?php $this->render_migration_results( $results ); ?>
			<?php endif; ?>

			<?php $this->render_migration_stats( $stats ); ?>

			<hr>

			<h2><?php esc_html_e( 'Run Migration', 'avatar-steward' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'avatar_steward_migration' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="migration_type"><?php esc_html_e( 'Migration Source', 'avatar-steward' ); ?></label>
						</th>
						<td>
							<select name="migration_type" id="migration_type" required>
								<option value=""><?php esc_html_e( 'Select a source...', 'avatar-steward' ); ?></option>
								<option value="simple_local_avatars">
									<?php
									printf(
										/* translators: %d: number of available avatars */
										esc_html__( 'Simple Local Avatars (%d available)', 'avatar-steward' ),
										intval( $stats['available_simple'] ?? 0 )
									);
									?>
								</option>
								<option value="wp_user_avatar">
									<?php
									printf(
										/* translators: %d: number of available avatars */
										esc_html__( 'WP User Avatar (%d available)', 'avatar-steward' ),
										intval( $stats['available_wp_user'] ?? 0 )
									);
									?>
								</option>
								<option value="gravatar">
									<?php esc_html_e( 'Gravatar (download and import)', 'avatar-steward' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Select the source from which you want to migrate avatars. Only users without existing Avatar Steward avatars will be processed.', 'avatar-steward' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<?php submit_button( __( 'Start Migration', 'avatar-steward' ), 'primary', 'submit', true ); ?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Migration Information', 'avatar-steward' ); ?></h2>
			
			<h3><?php esc_html_e( 'What gets migrated:', 'avatar-steward' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'User avatar associations from other plugins', 'avatar-steward' ); ?></li>
				<li><?php esc_html_e( 'Uploaded avatar files (they stay in the same location in media library)', 'avatar-steward' ); ?></li>
				<li><?php esc_html_e( 'Metadata indicating migration source', 'avatar-steward' ); ?></li>
			</ul>

			<h3><?php esc_html_e( 'What does NOT get migrated:', 'avatar-steward' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Old plugin settings (reconfigure in Avatar Steward settings)', 'avatar-steward' ); ?></li>
				<li><?php esc_html_e( 'Historical logs from old plugins', 'avatar-steward' ); ?></li>
				<li><?php esc_html_e( 'Plugin-specific custom fields', 'avatar-steward' ); ?></li>
			</ul>

			<h3><?php esc_html_e( 'Migration Sources:', 'avatar-steward' ); ?></h3>
			<dl>
				<dt><strong><?php esc_html_e( 'Simple Local Avatars', 'avatar-steward' ); ?></strong></dt>
				<dd><?php esc_html_e( 'Migrates existing avatar associations from the Simple Local Avatars plugin. Avatar files remain in the media library.', 'avatar-steward' ); ?></dd>
				
				<dt><strong><?php esc_html_e( 'WP User Avatar', 'avatar-steward' ); ?></strong></dt>
				<dd><?php esc_html_e( 'Migrates avatar associations from the WP User Avatar plugin. Avatar files remain in the media library.', 'avatar-steward' ); ?></dd>
				
				<dt><strong><?php esc_html_e( 'Gravatar', 'avatar-steward' ); ?></strong></dt>
				<dd><?php esc_html_e( 'Downloads Gravatars from gravatar.com for all users and saves them locally. Only downloads for users who have a Gravatar set. This may take some time depending on the number of users.', 'avatar-steward' ); ?></dd>
			</dl>
		</div>
		<?php
	}

	/**
	 * Render migration results notice.
	 *
	 * @param array $results Migration results.
	 * @return void
	 */
	private function render_migration_results( array $results ): void {
		$notice_class = ! empty( $results['success'] ) ? 'notice-success' : 'notice-error';
		?>
		<div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible">
			<p>
				<?php
				if ( ! empty( $results['success'] ) ) {
					if ( isset( $results['imported'] ) ) {
						// Gravatar import results.
						printf(
							/* translators: 1: imported count, 2: skipped count, 3: failed count, 4: total count */
							esc_html__( 'Migration complete! Imported: %1$d, Skipped: %2$d, Failed: %3$d, Total users: %4$d', 'avatar-steward' ),
							intval( $results['imported'] ),
							intval( $results['skipped'] ),
							intval( $results['failed'] ),
							intval( $results['total'] )
						);
					} else {
						// Plugin migration results.
						printf(
							/* translators: 1: migrated count, 2: skipped count, 3: total count */
							esc_html__( 'Migration complete! Migrated: %1$d, Skipped: %2$d, Total users: %3$d', 'avatar-steward' ),
							intval( $results['migrated'] ),
							intval( $results['skipped'] ),
							intval( $results['total'] )
						);
					}
				} else {
					echo esc_html( $results['error'] ?? __( 'Migration failed with unknown error.', 'avatar-steward' ) );
				}
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render migration statistics table.
	 *
	 * @param array $stats Migration statistics.
	 * @return void
	 */
	private function render_migration_stats( array $stats ): void {
		?>
		<h2><?php esc_html_e( 'Current Statistics', 'avatar-steward' ); ?></h2>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Metric', 'avatar-steward' ); ?></th>
					<th><?php esc_html_e( 'Count', 'avatar-steward' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Total Users', 'avatar-steward' ); ?></td>
					<td><?php echo intval( $stats['total_users'] ?? 0 ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Users with Avatar Steward avatars', 'avatar-steward' ); ?></td>
					<td><?php echo intval( $stats['with_avatars'] ?? 0 ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Migrated from Simple Local Avatars', 'avatar-steward' ); ?></td>
					<td><?php echo intval( $stats['from_simple_local'] ?? 0 ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Migrated from WP User Avatar', 'avatar-steward' ); ?></td>
					<td><?php echo intval( $stats['from_wp_user_avatar'] ?? 0 ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Imported from Gravatar', 'avatar-steward' ); ?></td>
					<td><?php echo intval( $stats['from_gravatar'] ?? 0 ); ?></td>
				</tr>
				<tr style="border-top: 2px solid #ddd;">
					<td><strong><?php esc_html_e( 'Available Simple Local Avatars (not yet migrated)', 'avatar-steward' ); ?></strong></td>
					<td><strong><?php echo intval( $stats['available_simple'] ?? 0 ); ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Available WP User Avatars (not yet migrated)', 'avatar-steward' ); ?></strong></td>
					<td><strong><?php echo intval( $stats['available_wp_user'] ?? 0 ); ?></strong></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Run migration based on selected type.
	 *
	 * @param string $migration_type Migration type identifier.
	 * @return array Migration results.
	 */
	private function run_migration( string $migration_type ): array {
		switch ( $migration_type ) {
			case 'simple_local_avatars':
				return $this->migration_service->migrate_from_simple_local_avatars();

			case 'wp_user_avatar':
				return $this->migration_service->migrate_from_wp_user_avatar();

			case 'gravatar':
				return $this->migration_service->import_from_gravatar( false );

			default:
				return array(
					'success' => false,
					'error'   => __( 'Invalid migration type selected.', 'avatar-steward' ),
				);
		}
	}
}
