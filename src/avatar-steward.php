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

$avatar_steward_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( $avatar_steward_autoload ) ) {
	require_once $avatar_steward_autoload;
}

if ( ! class_exists( AvatarSteward\Plugin::class ) ) {
	require_once __DIR__ . '/AvatarSteward/Plugin.php';
}

AvatarSteward\Plugin::instance();
