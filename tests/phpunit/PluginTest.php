<?php
/**
 * Sample test to verify PHPUnit setup.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Plugin;

/**
 * Test case for Plugin class.
 */
final class PluginTest extends TestCase {

	/**
	 * Test that Plugin class exists and can be instantiated.
	 */
	public function test_plugin_class_exists() {
		$this->assertTrue( class_exists( Plugin::class ) );
	}

	/**
	 * Test that Plugin::instance returns a Plugin instance.
	 */
	public function test_plugin_instance_returns_plugin() {
		$instance = Plugin::instance();
		$this->assertInstanceOf( Plugin::class, $instance );
	}

	/**
	 * Test that Plugin::instance returns the same instance (singleton).
	 */
	public function test_plugin_instance_is_singleton() {
		$instance1 = Plugin::instance();
		$instance2 = Plugin::instance();
		$this->assertSame( $instance1, $instance2 );
	}

	/**
	 * Test that Plugin has a boot method.
	 */
	public function test_plugin_has_boot_method() {
		$instance = Plugin::instance();
		$this->assertTrue( method_exists( $instance, 'boot' ) );
	}

	/**
	 * Test that Plugin has a get_settings_page method.
	 */
	public function test_plugin_has_get_settings_page_method() {
		$instance = Plugin::instance();
		$this->assertTrue( method_exists( $instance, 'get_settings_page' ) );
	}

	/**
	 * Test that Plugin initializes settings page after boot.
	 */
	public function test_plugin_initializes_settings_page_after_boot() {
		$instance = Plugin::instance();
		$instance->boot();
		$settings_page = $instance->get_settings_page();
		$this->assertInstanceOf( \AvatarSteward\Admin\SettingsPage::class, $settings_page );
	}
}
