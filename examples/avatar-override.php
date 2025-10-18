<?php
/**
 * Avatar Steward - Avatar Override Example
 *
 * This example demonstrates how Avatar Steward overrides the default
 * WordPress avatar system to use locally uploaded avatars.
 *
 * @package AvatarSteward
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Example 1: Setting a local avatar for a user
 *
 * This shows how to programmatically assign an avatar to a user.
 * Normally this would be done through the WordPress user profile page.
 */
function avatarsteward_example_set_avatar( $user_id, $attachment_id ) {
	$plugin  = AvatarSteward\Plugin::instance();
	$handler = $plugin->get_avatar_handler();

	if ( $handler ) {
		$success = $handler->set_local_avatar( $user_id, $attachment_id );

		if ( $success ) {
			// Avatar successfully set.
			return true;
		}
	}

	return false;
}

/**
 * Example 2: Checking if a user has a local avatar
 *
 * This function checks whether a user has uploaded a local avatar.
 */
function avatarsteward_example_check_avatar( $user_id ) {
	$plugin  = AvatarSteward\Plugin::instance();
	$handler = $plugin->get_avatar_handler();

	if ( $handler ) {
		return $handler->has_local_avatar( $user_id );
	}

	return false;
}

/**
 * Example 3: Removing a local avatar
 *
 * This shows how to remove a user's local avatar, causing them
 * to fall back to Gravatar or the default avatar.
 */
function avatarsteward_example_remove_avatar( $user_id ) {
	$plugin  = AvatarSteward\Plugin::instance();
	$handler = $plugin->get_avatar_handler();

	if ( $handler ) {
		return $handler->delete_local_avatar( $user_id );
	}

	return false;
}

/**
 * Example 4: Using WordPress native functions
 *
 * After Avatar Steward is active, you can continue using standard
 * WordPress avatar functions. They will automatically use local avatars
 * when available.
 */
function avatarsteward_example_display_avatar() {
	// Get avatar for user ID 1 at 96px size.
	$avatar = get_avatar( 1, 96 );
	echo $avatar;

	// Get avatar URL directly.
	$avatar_url = get_avatar_url( 1, array( 'size' => 150 ) );
	echo '<img src="' . esc_url( $avatar_url ) . '" alt="User Avatar" />';

	// Get avatar in comments.
	$comments = get_comments( array( 'post_id' => 1 ) );
	foreach ( $comments as $comment ) {
		echo get_avatar( $comment, 48 );
	}
}

/**
 * Example 5: Custom avatar sizes
 *
 * The avatar system automatically selects the appropriate image size
 * based on the requested dimensions:
 * - <= 96px: thumbnail (usually 150x150)
 * - <= 300px: medium (usually 300x300)
 * - > 300px: medium_large (usually 768x768)
 */
function avatarsteward_example_custom_sizes() {
	// Small avatar for comment lists.
	echo get_avatar( get_current_user_id(), 48 );

	// Medium avatar for profile pages.
	echo get_avatar( get_current_user_id(), 128 );

	// Large avatar for author pages.
	echo get_avatar( get_current_user_id(), 256 );
}

/**
 * Example 6: Fallback to Gravatar
 *
 * When a user doesn't have a local avatar, the system automatically
 * falls back to Gravatar. No special code is needed.
 */
function avatarsteward_example_fallback() {
	$user_id = 123; // User without local avatar.

	// This will show Gravatar if no local avatar exists.
	echo get_avatar( $user_id, 96 );
}

/**
 * Example 7: Working with different identifier types
 *
 * Avatar Steward supports all WordPress avatar identifier types:
 * - User ID (int)
 * - Email address (string)
 * - WP_User object
 * - WP_Comment object
 * - WP_Post object
 */
function avatarsteward_example_identifiers() {
	// By user ID.
	echo get_avatar( 1, 96 );

	// By email address.
	echo get_avatar( 'user@example.com', 96 );

	// By WP_User object.
	$user = get_user_by( 'id', 1 );
	echo get_avatar( $user, 96 );

	// By WP_Comment object (shows commenter's avatar).
	$comments = get_comments( array( 'number' => 1 ) );
	if ( ! empty( $comments ) ) {
		echo get_avatar( $comments[0], 96 );
	}

	// By WP_Post object (shows author's avatar).
	$post = get_post( 1 );
	echo get_avatar( $post, 96 );
}

/**
 * Example 8: Integration with themes
 *
 * Avatar Steward works seamlessly with WordPress themes.
 * No theme modifications are needed.
 */
function avatarsteward_example_theme_integration() {
	// In author.php template.
	the_author_meta( 'display_name' );
	echo get_avatar( get_the_author_meta( 'ID' ), 128 );

	// In comments.php template.
	wp_list_comments(
		array(
			'avatar_size' => 64,
			'style'       => 'ul',
		)
	);
}

/**
 * Example 9: Programmatic avatar upload
 *
 * This example shows how to handle avatar uploads programmatically,
 * such as when importing users or syncing from external systems.
 */
function avatarsteward_example_programmatic_upload( $user_id, $image_path ) {
	// First, upload the image to WordPress media library.
	$file_array = array(
		'name'     => basename( $image_path ),
		'tmp_name' => $image_path,
	);

	// Handle the upload.
	$attachment_id = media_handle_sideload( $file_array, 0 );

	if ( is_wp_error( $attachment_id ) ) {
		return false;
	}

	// Set as user's avatar.
	$plugin  = AvatarSteward\Plugin::instance();
	$handler = $plugin->get_avatar_handler();

	if ( $handler ) {
		return $handler->set_local_avatar( $user_id, $attachment_id );
	}

	return false;
}

/**
 * Example 10: Bulk operations
 *
 * This example shows how to perform bulk operations on user avatars.
 */
function avatarsteward_example_bulk_operations() {
	$plugin  = AvatarSteward\Plugin::instance();
	$handler = $plugin->get_avatar_handler();

	if ( ! $handler ) {
		return;
	}

	// Get all users.
	$users = get_users( array( 'number' => -1 ) );

	$users_with_avatars = 0;
	$users_without_avatars = 0;

	foreach ( $users as $user ) {
		if ( $handler->has_local_avatar( $user->ID ) ) {
			++$users_with_avatars;
		} else {
			++$users_without_avatars;
		}
	}

	printf(
		'Users with local avatars: %d, Users without: %d',
		$users_with_avatars,
		$users_without_avatars
	);
}
