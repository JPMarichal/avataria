<?php
/**
 * Example: Role-Based Avatar Upload Restrictions
 *
 * This example demonstrates how to restrict avatar upload functionality
 * based on user roles. You can customize who can upload, edit, or delete avatars.
 *
 * @package AvatarSteward
 * @subpackage Examples
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restrict avatar uploads to specific roles.
 *
 * This function prevents users from certain roles from uploading avatars.
 * Only allowed roles will see the upload interface.
 *
 * @param bool $can_upload Whether the user can upload (default true).
 * @param int  $user_id    User ID attempting to upload.
 * @return bool Whether upload is allowed.
 */
function my_restrict_avatar_upload( $can_upload, $user_id ) {
	// Define which roles can upload avatars.
	$allowed_roles = array(
		'administrator',
		'editor',
		'author',
		'contributor',
	);

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return false;
	}

	// Check if user has at least one allowed role.
	foreach ( $allowed_roles as $role ) {
		if ( in_array( $role, $user->roles, true ) ) {
			return true;
		}
	}

	// Deny upload for users without allowed roles.
	return false;
}
add_filter( 'avatarsteward/upload/can_upload', 'my_restrict_avatar_upload', 10, 2 );

/**
 * Set maximum file size per role.
 *
 * Different user roles can have different file size limits.
 *
 * @param int $max_size Maximum file size in bytes.
 * @param int $user_id  User ID.
 * @return int Modified maximum file size.
 */
function my_role_based_file_size( $max_size, $user_id ) {
	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return $max_size;
	}

	// Admins can upload larger files (2 MB).
	if ( in_array( 'administrator', $user->roles, true ) ) {
		return 2 * 1024 * 1024; // 2 MB.
	}

	// Premium members get 1 MB.
	if ( in_array( 'premium_member', $user->roles, true ) ) {
		return 1 * 1024 * 1024; // 1 MB.
	}

	// Regular users get 512 KB.
	return 512 * 1024; // 512 KB.
}
add_filter( 'avatarsteward/upload/max_file_size', 'my_role_based_file_size', 10, 2 );

/**
 * Restrict allowed image formats per role.
 *
 * Control which image formats different user roles can upload.
 *
 * @param array $allowed_types Allowed MIME types.
 * @param int   $user_id       User ID.
 * @return array Modified allowed types.
 */
function my_role_based_formats( $allowed_types, $user_id ) {
	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return $allowed_types;
	}

	// Admins can upload any supported format.
	if ( in_array( 'administrator', $user->roles, true ) ) {
		return array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
	}

	// Regular users only get JPEG and PNG.
	return array( 'image/jpeg', 'image/png' );
}
add_filter( 'avatarsteward/upload/allowed_types', 'my_role_based_formats', 10, 2 );

/**
 * Prevent certain roles from deleting their avatars.
 *
 * Example: Force certain users to always have an avatar.
 *
 * @param bool $can_delete Whether the user can delete (default true).
 * @param int  $user_id    User ID.
 * @return bool Whether deletion is allowed.
 */
function my_prevent_avatar_deletion( $can_delete, $user_id ) {
	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return $can_delete;
	}

	// Store managers must always have an avatar.
	if ( in_array( 'shop_manager', $user->roles, true ) ) {
		return false;
	}

	return $can_delete;
}
add_filter( 'avatarsteward/upload/can_delete', 'my_prevent_avatar_deletion', 10, 2 );

/**
 * Require approval for uploads from certain roles.
 *
 * Send new avatars to moderation queue based on user role.
 *
 * @param bool $requires_moderation Whether moderation is required.
 * @param int  $user_id             User ID.
 * @return bool Whether moderation is required.
 */
function my_role_based_moderation( $requires_moderation, $user_id ) {
	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return $requires_moderation;
	}

	// Trust administrators - no moderation needed.
	if ( in_array( 'administrator', $user->roles, true ) ) {
		return false;
	}

	// Trust editors - no moderation needed.
	if ( in_array( 'editor', $user->roles, true ) ) {
		return false;
	}

	// All other roles require moderation.
	return true;
}
add_filter( 'avatarsteward/moderation/requires_approval', 'my_role_based_moderation', 10, 2 );

/**
 * Custom error message for restricted users.
 *
 * Display a friendly message when uploads are not allowed.
 *
 * @return string Custom error message.
 */
function my_custom_restriction_message() {
	return __( 'Avatar uploads are currently restricted to contributing members. Please contact support to upgrade your account.', 'your-textdomain' );
}
add_filter( 'avatarsteward/upload/restriction_message', 'my_custom_restriction_message', 10, 2 );

/**
 * How to use this example:
 *
 * 1. Copy this code to your theme's functions.php or a custom plugin.
 * 2. Adjust the role names to match your site's user roles.
 * 3. Customize file sizes, formats, and restrictions as needed.
 * 4. Test with users from different roles to verify behavior.
 *
 * Expected results:
 * - Only allowed roles see the avatar upload interface.
 * - File size and format restrictions apply per role.
 * - Restricted users see a clear error message.
 * - Uploads from untrusted roles go to moderation queue.
 *
 * Tips:
 * - Use WordPress core roles or custom roles from membership plugins.
 * - Test thoroughly to avoid locking users out unintentionally.
 * - Provide clear communication about upload policies.
 * - Consider creating a custom role specifically for avatar uploads.
 *
 * Common role names in WordPress:
 * - administrator
 * - editor
 * - author
 * - contributor
 * - subscriber
 *
 * WooCommerce adds:
 * - shop_manager
 * - customer
 */
