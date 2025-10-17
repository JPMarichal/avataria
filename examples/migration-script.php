<?php
/**
 * Example: Migration Script from Gravatar and Other Avatar Plugins
 *
 * This script helps migrate user avatars from Gravatar, WP User Avatar,
 * or other avatar systems to Avatar Steward.
 *
 * WARNING: This is a one-time migration script. Test on a staging site first!
 *
 * @package AvatarSteward
 * @subpackage Examples
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migrate from Simple Local Avatars plugin.
 *
 * Converts existing avatar metadata from Simple Local Avatars format
 * to Avatar Steward format.
 */
function migrate_from_simple_local_avatars() {
	// Get all users.
	$users = get_users( array( 'fields' => 'ID' ) );

	$migrated = 0;
	$skipped  = 0;

	foreach ( $users as $user_id ) {
		// Check if user has Simple Local Avatars data.
		$old_avatar = get_user_meta( $user_id, 'simple_local_avatar', true );

		if ( empty( $old_avatar ) ) {
			++$skipped;
			continue;
		}

		// Check if already migrated.
		$existing = get_user_meta( $user_id, 'avatarsteward_local_avatar', true );
		if ( ! empty( $existing ) ) {
			++$skipped;
			continue;
		}

		// Migrate the avatar data.
		update_user_meta( $user_id, 'avatarsteward_local_avatar', $old_avatar );

		// Optionally, mark as migrated without deleting old data.
		update_user_meta( $user_id, 'avatarsteward_migrated_from', 'simple_local_avatars' );

		++$migrated;

		// Log the migration.
		error_log( sprintf( 'Migrated avatar for user ID %d from Simple Local Avatars', $user_id ) );
	}

	return array(
		'migrated' => $migrated,
		'skipped'  => $skipped,
		'total'    => count( $users ),
	);
}

/**
 * Download and import Gravatar for all users.
 *
 * Fetches each user's Gravatar and saves it locally.
 * Only imports if the user doesn't already have a local avatar.
 *
 * @param bool $force Force reimport even if local avatar exists.
 */
function import_gravatars( $force = false ) {
	// Get all users.
	$users = get_users();

	$imported = 0;
	$skipped  = 0;
	$failed   = 0;

	foreach ( $users as $user ) {
		// Skip if user already has a local avatar (unless forcing).
		if ( ! $force && get_user_meta( $user->ID, 'avatarsteward_local_avatar', true ) ) {
			++$skipped;
			continue;
		}

		// Get Gravatar URL.
		$hash         = md5( strtolower( trim( $user->user_email ) ) );
		$gravatar_url = sprintf( 'https://www.gravatar.com/avatar/%s?s=512&d=404', $hash );

		// Try to download the Gravatar.
		$response = wp_remote_get( $gravatar_url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) || 404 === wp_remote_retrieve_response_code( $response ) ) {
			// No Gravatar found or error.
			++$skipped;
			continue;
		}

		// Get image data.
		$image_data = wp_remote_retrieve_body( $response );

		if ( empty( $image_data ) ) {
			++$failed;
			continue;
		}

		// Save as local avatar.
		$upload_dir = wp_upload_dir();
		$filename   = sprintf( 'avatar-%d-%s.jpg', $user->ID, md5( $user->user_email ) );
		$filepath   = $upload_dir['path'] . '/' . $filename;

		// Save file.
		$saved = file_put_contents( $filepath, $image_data );

		if ( false === $saved ) {
			++$failed;
			continue;
		}

		// Create attachment.
		$attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => sprintf( 'Avatar for %s', $user->display_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $filepath );

		if ( is_wp_error( $attach_id ) ) {
			++$failed;
			continue;
		}

		// Generate metadata.
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// Link to user.
		update_user_meta( $user->ID, 'avatarsteward_local_avatar', $attach_id );
		update_user_meta( $user->ID, 'avatarsteward_migrated_from', 'gravatar' );

		++$imported;

		// Log the import.
		error_log( sprintf( 'Imported Gravatar for user %s (ID: %d)', $user->user_login, $user->ID ) );
	}

	return array(
		'imported' => $imported,
		'skipped'  => $skipped,
		'failed'   => $failed,
		'total'    => count( $users ),
	);
}

/**
 * Migrate from WP User Avatar plugin.
 *
 * Converts avatar data from WP User Avatar plugin format.
 */
function migrate_from_wp_user_avatar() {
	$users = get_users( array( 'fields' => 'ID' ) );

	$migrated = 0;
	$skipped  = 0;

	foreach ( $users as $user_id ) {
		// WP User Avatar stores attachment ID in this meta key.
		$old_avatar_id = get_user_meta( $user_id, 'wp_user_avatar', true );

		if ( empty( $old_avatar_id ) ) {
			++$skipped;
			continue;
		}

		// Check if already migrated.
		$existing = get_user_meta( $user_id, 'avatarsteward_local_avatar', true );
		if ( ! empty( $existing ) ) {
			++$skipped;
			continue;
		}

		// Migrate the avatar attachment ID.
		update_user_meta( $user_id, 'avatarsteward_local_avatar', $old_avatar_id );
		update_user_meta( $user_id, 'avatarsteward_migrated_from', 'wp_user_avatar' );

		++$migrated;

		error_log( sprintf( 'Migrated avatar for user ID %d from WP User Avatar', $user_id ) );
	}

	return array(
		'migrated' => $migrated,
		'skipped'  => $skipped,
		'total'    => count( $users ),
	);
}

/**
 * Admin page to run migrations.
 *
 * Add this to create a simple admin interface for running migrations.
 */
function avatarsteward_migration_admin_page() {
	add_management_page(
		__( 'Avatar Steward Migration', 'avatar-steward' ),
		__( 'Avatar Migration', 'avatar-steward' ),
		'manage_options',
		'avatarsteward-migration',
		'avatarsteward_migration_page_content'
	);
}
add_action( 'admin_menu', 'avatarsteward_migration_admin_page' );

/**
 * Migration page content.
 */
function avatarsteward_migration_page_content() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'avatar-steward' ) );
	}

	// Handle form submission.
	if ( isset( $_POST['migration_type'] ) && check_admin_referer( 'avatarsteward_migration' ) ) {
		$migration_type = sanitize_text_field( wp_unslash( $_POST['migration_type'] ) );

		switch ( $migration_type ) {
			case 'simple_local_avatars':
				$results = migrate_from_simple_local_avatars();
				break;
			case 'wp_user_avatar':
				$results = migrate_from_wp_user_avatar();
				break;
			case 'gravatar':
				$results = import_gravatars( false );
				break;
			default:
				$results = array( 'error' => __( 'Invalid migration type', 'avatar-steward' ) );
		}

		echo '<div class="notice notice-success"><p>';
		if ( isset( $results['error'] ) ) {
			echo esc_html( $results['error'] );
		} else {
			printf(
				/* translators: 1: migrated count, 2: skipped count, 3: total count */
				esc_html__( 'Migration complete! Migrated: %1$d, Skipped: %2$d, Total users: %3$d', 'avatar-steward' ),
				intval( $results['migrated'] ?? $results['imported'] ?? 0 ),
				intval( $results['skipped'] ?? 0 ),
				intval( $results['total'] ?? 0 )
			);
		}
		echo '</p></div>';
	}

	// Display form.
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Avatar Steward - Migration Tool', 'avatar-steward' ); ?></h1>
		
		<p><?php esc_html_e( 'Migrate existing avatars from other plugins or services.', 'avatar-steward' ); ?></p>
		<p><strong><?php esc_html_e( 'WARNING: Test on a staging site first!', 'avatar-steward' ); ?></strong></p>
		
		<form method="post">
			<?php wp_nonce_field( 'avatarsteward_migration' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="migration_type"><?php esc_html_e( 'Migration Source', 'avatar-steward' ); ?></label>
					</th>
					<td>
						<select name="migration_type" id="migration_type">
							<option value="simple_local_avatars"><?php esc_html_e( 'Simple Local Avatars', 'avatar-steward' ); ?></option>
							<option value="wp_user_avatar"><?php esc_html_e( 'WP User Avatar', 'avatar-steward' ); ?></option>
							<option value="gravatar"><?php esc_html_e( 'Gravatar (download and import)', 'avatar-steward' ); ?></option>
						</select>
						<p class="description">
							<?php esc_html_e( 'Select the source you want to migrate from.', 'avatar-steward' ); ?>
						</p>
					</td>
				</tr>
			</table>
			
			<?php submit_button( __( 'Start Migration', 'avatar-steward' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * How to use this migration script:
 *
 * 1. BACKUP YOUR DATABASE first!
 * 2. Test on a staging site before running on production.
 * 3. Copy this file to your theme's functions.php or a custom plugin.
 * 4. Go to Tools > Avatar Migration in WordPress admin.
 * 5. Select the migration source and click "Start Migration".
 * 6. Review the results and test avatar display.
 * 7. Remove this code after successful migration.
 *
 * What gets migrated:
 * - User avatar associations
 * - Uploaded avatar files (they stay in the same location)
 * - Metadata indicating migration source
 *
 * What doesn't get migrated:
 * - Old plugin settings (you'll need to reconfigure in Avatar Steward)
 * - Historical logs from old plugins
 * - Plugin-specific custom fields
 *
 * Rollback:
 * If migration fails, you can restore from backup or manually delete
 * the 'avatarsteward_local_avatar' user meta entries.
 */
