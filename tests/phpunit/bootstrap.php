<?php
/**
 * PHPUnit bootstrap file for Avatar Steward.
 *
 * @package AvatarSteward
 */

// Composer autoloader.
require_once dirname( dirname( __DIR__ ) ) . '/vendor/autoload.php';

// Define plugin constants for tests.
if ( ! defined( 'AVATAR_STEWARD_PLUGIN_FILE' ) ) {
	define( 'AVATAR_STEWARD_PLUGIN_FILE', dirname( dirname( __DIR__ ) ) . '/src/avatar-steward.php' );
}

if ( ! defined( 'AVATAR_STEWARD_VERSION' ) ) {
	define( 'AVATAR_STEWARD_VERSION', '0.1.0' );
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

// Mock WordPress functions for unit testing when WordPress is not available.
if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Mock add_action function.
	 *
	 * @param string   $hook     The name of the action.
	 * @param callable $callback The callback function.
	 * @param int      $priority The priority.
	 * @param int      $args     The number of arguments.
	 */
	function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
		// Mock implementation for testing.
	}
}

if ( ! function_exists( 'do_action' ) ) {
	/**
	 * Mock do_action function.
	 *
	 * @param string $hook The name of the action.
	 * @param mixed  ...$args Additional arguments.
	 */
	function do_action( $hook, ...$args ) {
		// Mock implementation for testing.
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Mock esc_html function.
	 *
	 * @param string $text The text to escape.
	 * @return string The escaped text.
	 */
	function esc_html( $text ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * Mock esc_attr function.
	 *
	 * @param string $text The text to escape.
	 * @return string The escaped text.
	 */
	function esc_attr( $text ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	/**
	 * Mock esc_url function.
	 *
	 * @param string $url The URL to escape.
	 * @return string The escaped URL.
	 */
	function esc_url( $url ) {
		return filter_var( $url, FILTER_SANITIZE_URL );
	}
}
