<?php
/**
 * Base integration test case for Avatar Steward.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Integration\Helpers;

use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests with setup/teardown helpers.
 */
abstract class IntegrationTestCase extends TestCase {

	/**
	 * Setup test environment before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->clean_test_data();
	}

	/**
	 * Clean up test environment after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		$this->clean_test_data();
		parent::tearDown();
	}

	/**
	 * Clean all test data from options and meta.
	 *
	 * @return void
	 */
	protected function clean_test_data(): void {
		global $wp_test_options;
		if ( isset( $wp_test_options ) ) {
			$wp_test_options = array();
		}
	}

	/**
	 * Create a test user.
	 *
	 * @param string $role User role.
	 * @param array  $args Additional user arguments.
	 * @return int User ID.
	 */
	protected function create_test_user( string $role = 'subscriber', array $args = array() ): int {
		static $user_counter = 1;
		
		$defaults = array(
			'user_login' => 'testuser' . $user_counter++,
			'user_email' => 'test' . $user_counter . '@example.com',
			'role'       => $role,
		);
		
		$user_data = array_merge( $defaults, $args );
		
		// Mock user ID.
		return $user_counter;
	}

	/**
	 * Create a test attachment (avatar).
	 *
	 * @param int   $user_id User ID.
	 * @param array $args    Additional attachment arguments.
	 * @return int Attachment ID.
	 */
	protected function create_test_avatar( int $user_id, array $args = array() ): int {
		static $attachment_counter = 100;
		
		// Mock attachment creation.
		$attachment_id = $attachment_counter++;
		
		// Store in user meta.
		update_user_meta( $user_id, 'avatar_steward_avatar', $attachment_id );
		
		return $attachment_id;
	}

	/**
	 * Set Pro license as active.
	 *
	 * @return void
	 */
	protected function activate_pro_license(): void {
		update_option( 'avatar_steward_license', array(
			'key'          => 'TEST-LICENSE-KEY-12345',
			'activated_at' => time(),
			'domain'       => 'example.com',
			'activated_by' => 1,
		), false );
		update_option( 'avatar_steward_license_status', 'active', false );
	}

	/**
	 * Deactivate Pro license.
	 *
	 * @return void
	 */
	protected function deactivate_pro_license(): void {
		delete_option( 'avatar_steward_license' );
		delete_option( 'avatar_steward_license_status' );
	}

	/**
	 * Enable moderation mode.
	 *
	 * @return void
	 */
	protected function enable_moderation(): void {
		update_option( 'avatar_steward_moderation_enabled', '1', false );
	}

	/**
	 * Disable moderation mode.
	 *
	 * @return void
	 */
	protected function disable_moderation(): void {
		update_option( 'avatar_steward_moderation_enabled', '0', false );
	}

	/**
	 * Mock WordPress function if not defined.
	 *
	 * @param string   $function_name Function name.
	 * @param callable $callback      Callback to execute.
	 * @return void
	 */
	protected function mock_wp_function( string $function_name, callable $callback ): void {
		// In a real WordPress environment, we would use filters or hooks.
		// For unit tests, functions are already mocked in bootstrap.php.
	}
}
