<?php
/**
 * Example: Avatar Cleanup Demo
 *
 * This file demonstrates how to use the CleanupService
 * to identify and manage inactive avatars.
 *
 * This is example code for demonstration purposes only.
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
 * phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
 * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
 *
 * @package AvatarSteward
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AvatarSteward\Domain\Cleanup\CleanupService;

/**
 * Example 1: Find inactive avatars (dry-run)
 *
 * This shows how to preview which avatars would be deleted
 * without actually deleting them.
 */
function example_find_inactive_avatars() {
	$cleanup_service = new CleanupService();

	// Define criteria for inactive avatars.
	$criteria = array(
		'max_age_days'         => 365,  // Avatars older than 1 year.
		'exclude_active_users' => true, // Only delete if user is inactive.
		'user_inactivity_days' => 180,  // User hasn't logged in for 6 months.
	);

	// Find inactive avatars.
	$inactive_avatars = $cleanup_service->find_inactive_avatars( $criteria );

	echo 'Found ' . count( $inactive_avatars ) . ' inactive avatars:' . "\n";
	print_r( $inactive_avatars );
}

/**
 * Example 2: Preview deletion (dry-run mode)
 *
 * Preview what would be deleted without actually deleting anything.
 */
function example_dry_run_deletion() {
	$cleanup_service = new CleanupService();

	// Find inactive avatars.
	$criteria         = array(
		'max_age_days'         => 180,
		'exclude_active_users' => true,
		'user_inactivity_days' => 90,
	);
	$inactive_avatars = $cleanup_service->find_inactive_avatars( $criteria );

	// Perform dry-run.
	$options = array(
		'dry_run'       => true,  // Don't actually delete.
		'notify_users'  => false, // Don't send notifications in dry-run.
		'notify_admins' => false,
	);

	$result = $cleanup_service->delete_inactive_avatars( $inactive_avatars, $options );

	echo 'Dry-run results:' . "\n";
	echo 'Would delete: ' . count( $result['deleted'] ) . ' avatars' . "\n";
	echo 'Would fail: ' . count( $result['failed'] ) . ' avatars' . "\n";
	print_r( $result );
}

/**
 * Example 3: Delete inactive avatars with notifications
 *
 * Actually delete inactive avatars and send notifications.
 * WARNING: This will permanently delete avatars!
 */
function example_delete_with_notifications() {
	$cleanup_service = new CleanupService();

	// Find inactive avatars.
	$criteria         = array(
		'max_age_days'         => 730, // 2 years old.
		'exclude_active_users' => true,
		'user_inactivity_days' => 365, // 1 year inactive.
	);
	$inactive_avatars = $cleanup_service->find_inactive_avatars( $criteria );

	// Delete with notifications.
	$options = array(
		'dry_run'       => false, // Actually delete.
		'notify_users'  => true,  // Send email to users.
		'notify_admins' => true,  // Send report to admins.
	);

	$result = $cleanup_service->delete_inactive_avatars( $inactive_avatars, $options );

	echo 'Deletion results:' . "\n";
	echo 'Deleted: ' . count( $result['deleted'] ) . ' avatars' . "\n";
	echo 'Failed: ' . count( $result['failed'] ) . ' avatars' . "\n";
}

/**
 * Example 4: Schedule automatic cleanup
 *
 * Set up WP-Cron to run cleanup automatically.
 */
function example_schedule_cleanup() {
	$cleanup_service = new CleanupService();

	// Schedule weekly cleanup.
	$cleanup_service->schedule_cleanup( 'weekly' );

	echo 'Cleanup scheduled to run weekly' . "\n";

	// Check next scheduled run.
	$next_run = wp_next_scheduled( 'avatarsteward_cleanup_inactive_avatars' );
	if ( $next_run ) {
		echo 'Next run: ' . wp_date( 'Y-m-d H:i:s', $next_run ) . "\n";
	}
}

/**
 * Example 5: Unschedule automatic cleanup
 */
function example_unschedule_cleanup() {
	$cleanup_service = new CleanupService();

	$cleanup_service->unschedule_cleanup();

	echo 'Cleanup unscheduled' . "\n";
}

/**
 * Example 6: Manual cleanup with custom criteria
 *
 * Run a one-time cleanup with specific criteria.
 */
function example_manual_cleanup_custom_criteria() {
	$cleanup_service = new CleanupService();

	// Custom criteria: very old avatars only.
	$criteria = array(
		'max_age_days'         => 1095, // 3 years old.
		'exclude_active_users' => false, // Delete even if user is active.
		'user_inactivity_days' => 0,
	);

	$inactive_avatars = $cleanup_service->find_inactive_avatars( $criteria );

	echo 'Found ' . count( $inactive_avatars ) . ' avatars older than 3 years' . "\n";

	// Dry-run first to preview.
	$result = $cleanup_service->delete_inactive_avatars(
		$inactive_avatars,
		array( 'dry_run' => true )
	);

	echo 'Would delete: ' . count( $result['deleted'] ) . ' avatars' . "\n";
}

/**
 * Example 7: Integration with WordPress admin
 *
 * This shows how the cleanup settings are stored in WordPress options.
 */
function example_get_cleanup_settings() {
	$options = get_option( 'avatar_steward_options', array() );

	$cleanup_settings = array(
		'enabled'              => isset( $options['cleanup_enabled'] ) ? $options['cleanup_enabled'] : false,
		'schedule'             => isset( $options['cleanup_schedule'] ) ? $options['cleanup_schedule'] : 'weekly',
		'max_age_days'         => isset( $options['cleanup_max_age_days'] ) ? $options['cleanup_max_age_days'] : 365,
		'user_inactivity_days' => isset( $options['cleanup_user_inactivity_days'] ) ? $options['cleanup_user_inactivity_days'] : 180,
		'notify_users'         => isset( $options['cleanup_notify_users'] ) ? $options['cleanup_notify_users'] : true,
		'notify_admins'        => isset( $options['cleanup_notify_admins'] ) ? $options['cleanup_notify_admins'] : true,
	);

	echo 'Current cleanup settings:' . "\n";
	print_r( $cleanup_settings );
}

// Usage instructions.
echo "Avatar Steward Cleanup Examples\n";
echo "================================\n\n";
echo "Uncomment one of the example functions below to test:\n\n";

// Uncomment to run examples.
// phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar
// example_find_inactive_avatars();
// example_dry_run_deletion();
// example_schedule_cleanup();
// example_get_cleanup_settings();

// WARNING: These examples will delete data.
// example_delete_with_notifications();
// example_manual_cleanup_custom_criteria();
// phpcs:enable Squiz.Commenting.InlineComment.InvalidEndChar
