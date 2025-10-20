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

if ( ! function_exists( 'get_userdata' ) ) {
	/**
	 * Mock get_userdata function.
	 *
	 * @param int $user_id User ID.
	 * @return object|false User object or false.
	 */
	function get_userdata( $user_id ) {
		if ( $user_id <= 0 ) {
			return false;
		}

		// Return a mock user object.
		$user = new stdClass();
		$user->ID = $user_id;
		$user->user_login = 'testuser' . $user_id;
		$user->user_email = 'user' . $user_id . '@example.com';
		$user->display_name = 'Test User ' . $user_id;
		$user->first_name = 'Test';
		$user->last_name = 'User';

		return $user;
	}
}

if ( ! function_exists( 'get_user_by' ) ) {
	/**
	 * Mock get_user_by function.
	 *
	 * @param string $field Field name.
	 * @param mixed  $value Field value.
	 * @return object|false User object or false.
	 */
	function get_user_by( $field, $value ) {
		// Simple mock: return a user object for any email.
		if ( 'email' === $field && ! empty( $value ) ) {
			$user = new stdClass();
			$user->ID = 1;
			$user->user_login = 'testuser';
			$user->user_email = $value;
			$user->display_name = 'Test User';
			return $user;
		}

		return false;
	}
}

if ( ! function_exists( 'get_post' ) ) {
	/**
	 * Mock get_post function.
	 *
	 * @param int $post_id Post ID.
	 * @return object|null Post object or null.
	 */
	function get_post( $post_id ) {
		if ( $post_id <= 0 ) {
			return null;
		}

		$post = new stdClass();
		$post->ID = $post_id;
		$post->post_title = 'Test Post';
		$post->post_author = 1;

		return $post;
	}
}

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	/**
	 * Mock wp_get_attachment_image_url function.
	 *
	 * @param int          $attachment_id Attachment ID.
	 * @param string|array $size          Image size.
	 * @return string|false Image URL or false.
	 */
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {
		if ( $attachment_id <= 0 ) {
			return false;
		}

		return 'http://example.com/wp-content/uploads/avatar-' . $attachment_id . '.jpg';
	}
}
