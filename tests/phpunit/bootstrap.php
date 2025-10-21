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
	define( 'AVATAR_STEWARD_PLUGIN_FILE', dirname( dirname( __DIR__ ) ) . '/avatar-steward.php' );
}

if ( ! defined( 'AVATAR_STEWARD_PLUGIN_DIR' ) ) {
	define( 'AVATAR_STEWARD_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) . '/' );
}

if ( ! defined( 'AVATAR_STEWARD_PLUGIN_URL' ) ) {
	define( 'AVATAR_STEWARD_PLUGIN_URL', 'http://localhost/' );
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

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * Mock add_filter function.
	 *
	 * @param string   $hook     The name of the filter.
	 * @param callable $callback The callback function.
	 * @param int      $priority The priority.
	 * @param int      $args     The number of arguments.
	 */
	function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {
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
		return $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	/**
	 * Mock update_option function.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  Option value.
	 * @return bool True on success.
	 */
	function update_option( $option, $value ) {
		return true;
	}
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
	/**
	 * Mock wp_create_nonce function.
	 *
	 * @param string $action Action name.
	 * @return string Nonce value.
	 */
	function wp_create_nonce( $action = '' ) {
		return 'test-nonce-' . md5( $action );
	}
}

if ( ! function_exists( 'wp_generate_password' ) ) {
	/**
	 * Mock wp_generate_password function.
	 *
	 * @param int  $length              Password length.
	 * @param bool $special_chars       Include special characters.
	 * @param bool $extra_special_chars Include extra special characters.
	 * @return string Generated password.
	 */
	function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		return str_repeat( 'a', $length );
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
	 * @param string $path   Path relative to admin URL.
	 * @param string $scheme URL scheme.
	 * @return string Admin URL.
	 */
	function admin_url( $path = '', $scheme = 'admin' ) {
		return 'https://example.com/wp-admin/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'wp_remote_request' ) ) {
	/**
	 * Mock wp_remote_request function.
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Request arguments.
	 * @return array|WP_Error Response array or WP_Error on failure.
	 */
	function wp_remote_request( $url, $args = array() ) {
		return array(
			'response' => array( 'code' => 200 ),
			'body'     => '{}',
		);
	}
}

if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
	/**
	 * Mock wp_remote_retrieve_response_code function.
	 *
	 * @param array|WP_Error $response Response array.
	 * @return int Response code.
	 */
	function wp_remote_retrieve_response_code( $response ) {
		return $response['response']['code'] ?? 200;
	}
}

if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
	/**
	 * Mock wp_remote_retrieve_body function.
	 *
	 * @param array|WP_Error $response Response array.
	 * @return string Response body.
	 */
	function wp_remote_retrieve_body( $response ) {
		return $response['body'] ?? '';
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	/**
	 * Mock is_wp_error function.
	 *
	 * @param mixed $thing Variable to check.
	 * @return bool False for testing.
	 */
	function is_wp_error( $thing ) {
		return false;
	}
}

if ( ! function_exists( 'add_query_arg' ) ) {
	/**
	 * Mock add_query_arg function.
	 *
	 * @param array|string $args Query arguments.
	 * @param string|null  $url  URL to add arguments to.
	 * @return string Modified URL.
	 */
	function add_query_arg( $args, $url = null ) {
		if ( is_null( $url ) ) {
			$url = '';
		}
		$separator = strpos( $url, '?' ) === false ? '?' : '&';
		return $url . $separator . http_build_query( $args );
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

if ( ! function_exists( 'wp_die' ) ) {
	/**
	 * Mock wp_die function.
	 *
	 * @param string $message Error message.
	 * @param string $title   Error title.
	 * @param array  $args    Additional arguments.
	 */
	function wp_die( $message = '', $title = '', $args = array() ) {
		// Mock implementation for testing.
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	/**
	 * Mock wp_parse_args function.
	 *
	 * @param array $args     Arguments to parse.
	 * @param array $defaults Default arguments.
	 * @return array Merged arguments.
	 */
	function wp_parse_args( $args, $defaults = array() ) {
		return array_merge( $defaults, $args );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	/**
	 * Mock wp_kses_post function.
	 *
	 * @param string $data Data to sanitize.
	 * @return string Sanitized data.
	 */
	function wp_kses_post( $data ) {
		return strip_tags( $data, '<a><b><i><strong><em><p><br>' );
	}
}

