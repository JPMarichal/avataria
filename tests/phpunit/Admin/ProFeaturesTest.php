<?php
/**
 * Tests for Pro features in SettingsPage.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Admin;

use PHPUnit\Framework\TestCase;
use AvatarSteward\Admin\SettingsPage;
use AvatarSteward\Domain\Licensing\LicenseManager;

/**
 * Test case for Pro features in SettingsPage.
 */
class ProFeaturesTest extends TestCase {

	/**
	 * Test that SettingsPage can be instantiated with LicenseManager.
	 */
	public function test_settings_page_accepts_license_manager(): void {
		$license_manager = $this->createMock( LicenseManager::class );
		$settings_page   = new SettingsPage( $license_manager );

		$this->assertInstanceOf( SettingsPage::class, $settings_page );
	}

	/**
	 * Test that default settings include Pro feature keys.
	 */
	public function test_default_settings_include_pro_features(): void {
		$settings_page    = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertArrayHasKey( 'role_file_size_limits', $default_settings );
		$this->assertArrayHasKey( 'role_format_restrictions', $default_settings );
		$this->assertArrayHasKey( 'avatar_expiration_enabled', $default_settings );
		$this->assertArrayHasKey( 'avatar_expiration_days', $default_settings );
	}

	/**
	 * Test that role_file_size_limits defaults to empty array.
	 */
	public function test_role_file_size_limits_defaults_to_empty_array(): void {
		$settings_page    = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertIsArray( $default_settings['role_file_size_limits'] );
		$this->assertEmpty( $default_settings['role_file_size_limits'] );
	}

	/**
	 * Test that role_format_restrictions defaults to empty array.
	 */
	public function test_role_format_restrictions_defaults_to_empty_array(): void {
		$settings_page    = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertIsArray( $default_settings['role_format_restrictions'] );
		$this->assertEmpty( $default_settings['role_format_restrictions'] );
	}

	/**
	 * Test that avatar_expiration_enabled defaults to false.
	 */
	public function test_avatar_expiration_enabled_defaults_to_false(): void {
		$settings_page    = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertFalse( $default_settings['avatar_expiration_enabled'] );
	}

	/**
	 * Test that avatar_expiration_days defaults to 365.
	 */
	public function test_avatar_expiration_days_defaults_to_365(): void {
		$settings_page    = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertEquals( 365, $default_settings['avatar_expiration_days'] );
	}

	/**
	 * Test that sanitize_settings handles role_file_size_limits.
	 */
	public function test_sanitize_settings_handles_role_file_size_limits(): void {
		// Create a mock LicenseManager that reports Pro is active.
		$license_manager = $this->createMock( LicenseManager::class );
		$license_manager->method( 'is_pro_active' )->willReturn( true );

		$settings_page = new SettingsPage( $license_manager );

		$input = array(
			'role_file_size_limits' => array(
				'administrator' => '5.0',
				'editor'        => '3.5',
			),
		);

		$sanitized = $settings_page->sanitize_settings( $input );

		$this->assertArrayHasKey( 'role_file_size_limits', $sanitized );
		$this->assertEquals( 5.0, $sanitized['role_file_size_limits']['administrator'] );
		$this->assertEquals( 3.5, $sanitized['role_file_size_limits']['editor'] );
	}

	/**
	 * Test that sanitize_settings handles role_format_restrictions.
	 */
	public function test_sanitize_settings_handles_role_format_restrictions(): void {
		// Create a mock LicenseManager that reports Pro is active.
		$license_manager = $this->createMock( LicenseManager::class );
		$license_manager->method( 'is_pro_active' )->willReturn( true );

		$settings_page = new SettingsPage( $license_manager );

		$input = array(
			'role_format_restrictions' => array(
				'administrator' => array( 'image/jpeg', 'image/png', 'image/webp' ),
				'subscriber'    => array( 'image/jpeg' ),
			),
		);

		$sanitized = $settings_page->sanitize_settings( $input );

		$this->assertArrayHasKey( 'role_format_restrictions', $sanitized );
		$this->assertContains( 'image/jpeg', $sanitized['role_format_restrictions']['administrator'] );
		$this->assertContains( 'image/png', $sanitized['role_format_restrictions']['administrator'] );
		$this->assertContains( 'image/webp', $sanitized['role_format_restrictions']['administrator'] );
		$this->assertContains( 'image/jpeg', $sanitized['role_format_restrictions']['subscriber'] );
	}

	/**
	 * Test that sanitize_settings handles avatar expiration settings.
	 */
	public function test_sanitize_settings_handles_avatar_expiration(): void {
		// Create a mock LicenseManager that reports Pro is active.
		$license_manager = $this->createMock( LicenseManager::class );
		$license_manager->method( 'is_pro_active' )->willReturn( true );

		$settings_page = new SettingsPage( $license_manager );

		$input = array(
			'avatar_expiration_enabled' => '1',
			'avatar_expiration_days'    => '180',
		);

		$sanitized = $settings_page->sanitize_settings( $input );

		$this->assertTrue( $sanitized['avatar_expiration_enabled'] );
		$this->assertEquals( 180, $sanitized['avatar_expiration_days'] );
	}

	/**
	 * Test that sanitize_settings enforces min/max for expiration days.
	 */
	public function test_sanitize_settings_enforces_expiration_days_limits(): void {
		// Create a mock LicenseManager that reports Pro is active.
		$license_manager = $this->createMock( LicenseManager::class );
		$license_manager->method( 'is_pro_active' )->willReturn( true );

		$settings_page = new SettingsPage( $license_manager );

		// Test minimum.
		$input      = array( 'avatar_expiration_days' => '0' );
		$sanitized  = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 1, $sanitized['avatar_expiration_days'] );

		// Test maximum.
		$input     = array( 'avatar_expiration_days' => '5000' );
		$sanitized = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 3650, $sanitized['avatar_expiration_days'] );
	}

	/**
	 * Test that Pro features are not sanitized when Pro is not active.
	 */
	public function test_pro_features_not_sanitized_when_pro_inactive(): void {
		// Create a mock LicenseManager that reports Pro is NOT active.
		$license_manager = $this->createMock( LicenseManager::class );
		$license_manager->method( 'is_pro_active' )->willReturn( false );

		$settings_page = new SettingsPage( $license_manager );

		$input = array(
			'role_file_size_limits'     => array( 'administrator' => '5.0' ),
			'role_format_restrictions'  => array( 'administrator' => array( 'image/jpeg' ) ),
			'avatar_expiration_enabled' => '1',
			'avatar_expiration_days'    => '180',
		);

		$sanitized = $settings_page->sanitize_settings( $input );

		// Pro features should not be present in sanitized output when Pro is not active.
		$this->assertArrayNotHasKey( 'role_file_size_limits', $sanitized );
		$this->assertArrayNotHasKey( 'role_format_restrictions', $sanitized );
		$this->assertArrayNotHasKey( 'avatar_expiration_enabled', $sanitized );
		$this->assertArrayNotHasKey( 'avatar_expiration_days', $sanitized );
	}
}
