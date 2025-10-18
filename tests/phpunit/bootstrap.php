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

if ( ! function_exists( '__' ) ) {
	/**
	 * Mock translation function.
	 *
	 * @param string $text   The text to translate.
	 * @param string $domain The text domain.
	 * @return string The translated text.
	 */
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Mock translation and escape function.
	 *
	 * @param string $text   The text to translate.
	 * @param string $domain The text domain.
	 * @return string The translated and escaped text.
	 */
	function esc_html__( $text, $domain = 'default' ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	/**
	 * Mock translation, escape and echo function.
	 *
	 * @param string $text   The text to translate.
	 * @param string $domain The text domain.
	 */
	function esc_html_e( $text, $domain = 'default' ) {
		echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr_e' ) ) {
	/**
	 * Mock translation, escape and echo function for attributes.
	 *
	 * @param string $text   The text to translate.
	 * @param string $domain The text domain.
	 */
	function esc_attr_e( $text, $domain = 'default' ) {
		echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'get_user_meta' ) ) {
	/**
	 * Mock get_user_meta function.
	 *
	 * @param int    $user_id User ID.
	 * @param string $key     Meta key.
	 * @param bool   $single  Whether to return a single value.
	 * @return mixed Meta value.
	 */
	function get_user_meta( $user_id, $key = '', $single = false ) {
		return false;
	}
}

if ( ! function_exists( 'update_user_meta' ) ) {
	/**
	 * Mock update_user_meta function.
	 *
	 * @param int    $user_id    User ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 * @return bool True on success.
	 */
	function update_user_meta( $user_id, $meta_key, $meta_value ) {
		return true;
	}
}

if ( ! function_exists( 'delete_user_meta' ) ) {
	/**
	 * Mock delete_user_meta function.
	 *
	 * @param int    $user_id  User ID.
	 * @param string $meta_key Meta key.
	 * @return bool True on success.
	 */
	function delete_user_meta( $user_id, $meta_key ) {
		return true;
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	/**
	 * Mock current_user_can function.
	 *
	 * @param string $capability Capability name.
	 * @param mixed  ...$args    Additional arguments.
	 * @return bool Always returns true for testing.
	 */
	function current_user_can( $capability, ...$args ) {
		return true;
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	/**
	 * Mock wp_verify_nonce function.
	 *
	 * @param string $nonce  Nonce value.
	 * @param string $action Action name.
	 * @return bool Always returns true for testing.
	 */
	function wp_verify_nonce( $nonce, $action = '' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_nonce_field' ) ) {
	/**
	 * Mock wp_nonce_field function.
	 *
	 * @param string $action Action name.
	 * @param string $name   Field name.
	 * @param bool   $referer Whether to add referer field.
	 * @param bool   $echo    Whether to echo or return.
	 * @return string Nonce field HTML.
	 */
	function wp_nonce_field( $action = '', $name = '_wpnonce', $referer = true, $echo = true ) {
		$html = '<input type="hidden" name="' . esc_attr( $name ) . '" value="test-nonce" />';
		if ( $echo ) {
			echo $html;
		}
		return $html;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Mock sanitize_text_field function.
	 *
	 * @param string $str String to sanitize.
	 * @return string Sanitized string.
	 */
	function sanitize_text_field( $str ) {
		return strip_tags( $str );
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	/**
	 * Mock wp_unslash function.
	 *
	 * @param string|array $value Value to unslash.
	 * @return string|array Unslashed value.
	 */
	function wp_unslash( $value ) {
		return is_array( $value ) ? array_map( 'wp_unslash', $value ) : stripslashes( $value );
	}
}

if ( ! function_exists( 'get_current_screen' ) ) {
	/**
	 * Mock get_current_screen function.
	 *
	 * @return object|null Screen object.
	 */
	function get_current_screen() {
		return null;
	}
}

if ( ! function_exists( 'get_current_user_id' ) ) {
	/**
	 * Mock get_current_user_id function.
	 *
	 * @return int User ID.
	 */
	function get_current_user_id() {
		return 1;
	}
}

if ( ! function_exists( 'get_transient' ) ) {
	/**
	 * Mock get_transient function.
	 *
	 * @param string $transient Transient name.
	 * @return mixed Transient value.
	 */
	function get_transient( $transient ) {
		return false;
	}
}

if ( ! function_exists( 'set_transient' ) ) {
	/**
	 * Mock set_transient function.
	 *
	 * @param string $transient  Transient name.
	 * @param mixed  $value      Transient value.
	 * @param int    $expiration Expiration time in seconds.
	 * @return bool True on success.
	 */
	function set_transient( $transient, $value, $expiration = 0 ) {
		return true;
	}
}

if ( ! function_exists( 'delete_transient' ) ) {
	/**
	 * Mock delete_transient function.
	 *
	 * @param string $transient Transient name.
	 * @return bool True on success.
	 */
	function delete_transient( $transient ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_media' ) ) {
	/**
	 * Mock wp_enqueue_media function.
	 */
	function wp_enqueue_media() {
		// Mock implementation for testing.
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	/**
	 * Mock is_admin function.
	 *
	 * @return bool Always returns true for testing.
	 */
	function is_admin() {
		return true;
	}
}

if ( ! function_exists( 'number_format_i18n' ) ) {
	/**
	 * Mock number_format_i18n function.
	 *
	 * @param float $number   Number to format.
	 * @param int   $decimals Number of decimals.
	 * @return string Formatted number.
	 */
	function number_format_i18n( $number, $decimals = 0 ) {
		return number_format( $number, $decimals );
	}
}

if ( ! function_exists( 'get_option' ) ) {
	/**
	 * Mock get_option function.
	 *
	 * @param string $option  Option name.
	 * @param mixed  $default Default value.
	 * @return mixed Option value.
	 */
	function get_option( $option, $default = false ) {
		global $wp_test_options;
		if ( ! isset( $wp_test_options ) ) {
			$wp_test_options = array();
		}
		return $wp_test_options[ $option ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	/**
	 * Mock update_option function.
	 *
	 * @param string $option   Option name.
	 * @param mixed  $value    Option value.
	 * @param bool   $autoload Whether to autoload.
	 * @return bool True on success.
	 */
	function update_option( $option, $value, $autoload = null ) {
		global $wp_test_options;
		if ( ! isset( $wp_test_options ) ) {
			$wp_test_options = array();
		}
		$wp_test_options[ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	/**
	 * Mock delete_option function.
	 *
	 * @param string $option Option name.
	 * @return bool True on success.
	 */
	function delete_option( $option ) {
		global $wp_test_options;
		if ( ! isset( $wp_test_options ) ) {
			$wp_test_options = array();
		}
		unset( $wp_test_options[ $option ] );
		return true;
	}
}

if ( ! function_exists( 'site_url' ) ) {
	/**
	 * Mock site_url function.
	 *
	 * @param string $path   Optional path.
	 * @param string $scheme Optional scheme.
	 * @return string Site URL.
	 */
	function site_url( $path = '', $scheme = null ) {
		return 'https://example.com' . ( $path ? '/' . ltrim( $path, '/' ) : '' );
	}
}

if ( ! function_exists( 'admin_url' ) ) {
	/**
	 * Mock admin_url function.
	 *
	 * @param string $path   Optional path.
	 * @param string $scheme Optional scheme.
	 * @return string Admin URL.
	 */
	function admin_url( $path = '', $scheme = 'admin' ) {
		return 'https://example.com/wp-admin/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'esc_js' ) ) {
	/**
	 * Mock esc_js function.
	 *
	 * @param string $text Text to escape.
	 * @return string Escaped text.
	 */
	function esc_js( $text ) {
		return addslashes( $text );
	}
}

if ( ! function_exists( 'add_query_arg' ) ) {
	/**
	 * Mock add_query_arg function.
	 *
	 * @param array  $args Query arguments.
	 * @param string $url  Base URL.
	 * @return string URL with query arguments.
	 */
	function add_query_arg( $args, $url ) {
		$separator = strpos( $url, '?' ) !== false ? '&' : '?';
		return $url . $separator . http_build_query( $args );
	}
}

if ( ! function_exists( 'add_submenu_page' ) ) {
	/**
	 * Mock add_submenu_page function.
	 *
	 * @param string   $parent_slug  Parent slug.
	 * @param string   $page_title   Page title.
	 * @param string   $menu_title   Menu title.
	 * @param string   $capability   Capability.
	 * @param string   $menu_slug    Menu slug.
	 * @param callable $callback     Callback function.
	 * @return string Hook suffix.
	 */
	function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '' ) {
		return 'settings_page_' . $menu_slug;
	}
}

if ( ! function_exists( 'wp_die' ) ) {
	/**
	 * Mock wp_die function.
	 *
	 * @param string $message Error message.
	 */
	function wp_die( $message = '' ) {
		throw new Exception( $message );
	}
}

if ( ! function_exists( 'check_admin_referer' ) ) {
	/**
	 * Mock check_admin_referer function.
	 *
	 * @param string $action Action name.
	 * @param string $query_arg Query arg name.
	 * @return bool True on success.
	 */
	function check_admin_referer( $action = '', $query_arg = '_wpnonce' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_safe_redirect' ) ) {
	/**
	 * Mock wp_safe_redirect function.
	 *
	 * @param string $location Redirect location.
	 * @param int    $status   HTTP status code.
	 * @return bool True on success.
	 */
	function wp_safe_redirect( $location, $status = 302 ) {
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	/**
	 * Mock apply_filters function.
	 *
	 * @param string $hook_name Filter hook name.
	 * @param mixed  $value     Value to filter.
	 * @param mixed  ...$args   Additional arguments.
	 * @return mixed Filtered value.
	 */
	function apply_filters( $hook_name, $value, ...$args ) {
		return $value;
	}
}
