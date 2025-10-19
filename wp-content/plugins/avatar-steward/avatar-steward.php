<?php
/**
 * Plugin Name: Avatar Steward
 * Plugin URI: https://avatarsteward.example
 * Description: Local avatar management suite for WordPress.
 * Version: 0.1.0
 * Author: Avatar Steward Team
 * License: GPL-2.0-or-later
 *
 * @package AvatarSteward
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// This file is a legacy entry point. The main plugin file is now in the root directory.
// Include the main plugin file if it exists.
$main_plugin_file = dirname( __DIR__ ) . '/avatar-steward.php';

if ( file_exists( $main_plugin_file ) && ! defined( 'AVATAR_STEWARD_VERSION' ) ) {
	require_once $main_plugin_file;
} else {
	// Fallback for standalone usage
	$avatar_steward_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

	if ( is_readable( $avatar_steward_autoload ) ) {
		require_once $avatar_steward_autoload;
	}

	if ( ! class_exists( AvatarSteward\Plugin::class ) ) {
		require_once __DIR__ . '/AvatarSteward/Plugin.php';
	}

	if ( ! defined( 'AVATAR_STEWARD_VERSION' ) ) {
		AvatarSteward\Plugin::instance();
	}
}
