<?php
/**
 * Tests for SettingsPage class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Admin\SettingsPage;

/**
 * Test case for SettingsPage class.
 */
final class SettingsPageTest extends TestCase {

	/**
	 * Test that SettingsPage class exists.
	 */
	public function test_settings_page_class_exists() {
		$this->assertTrue( class_exists( SettingsPage::class ) );
	}

	/**
	 * Test that SettingsPage can be instantiated.
	 */
	public function test_settings_page_can_be_instantiated() {
		$settings_page = new SettingsPage();
		$this->assertInstanceOf( SettingsPage::class, $settings_page );
	}

	/**
	 * Test that SettingsPage has init method.
	 */
	public function test_settings_page_has_init_method() {
		$settings_page = new SettingsPage();
		$this->assertTrue( method_exists( $settings_page, 'init' ) );
	}

	/**
	 * Test that get_default_settings returns expected structure.
	 */
	public function test_get_default_settings_returns_expected_structure() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertIsArray( $default_settings );
		$this->assertArrayHasKey( 'max_file_size', $default_settings );
		$this->assertArrayHasKey( 'allowed_formats', $default_settings );
		$this->assertArrayHasKey( 'max_width', $default_settings );
		$this->assertArrayHasKey( 'max_height', $default_settings );
		$this->assertArrayHasKey( 'convert_to_webp', $default_settings );
		$this->assertArrayHasKey( 'allowed_roles', $default_settings );
		$this->assertArrayHasKey( 'require_approval', $default_settings );
		$this->assertArrayHasKey( 'delete_attachment_on_remove', $default_settings );
	}

	/**
	 * Test that default max_file_size is 2.0 MB.
	 */
	public function test_default_max_file_size_is_two_mb() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertEquals( 2.0, $default_settings['max_file_size'] );
	}

	/**
	 * Test that default allowed formats include JPEG and PNG.
	 */
	public function test_default_allowed_formats_include_jpeg_and_png() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertContains( 'image/jpeg', $default_settings['allowed_formats'] );
		$this->assertContains( 'image/png', $default_settings['allowed_formats'] );
	}

	/**
	 * Test that default max dimensions are 2048px.
	 */
	public function test_default_max_dimensions_are_2048px() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertEquals( 2048, $default_settings['max_width'] );
		$this->assertEquals( 2048, $default_settings['max_height'] );
	}

	/**
	 * Test that default convert_to_webp is false.
	 */
	public function test_default_convert_to_webp_is_false() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertFalse( $default_settings['convert_to_webp'] );
	}

	/**
	 * Test that default require_approval is false.
	 */
	public function test_default_require_approval_is_false() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertFalse( $default_settings['require_approval'] );
	}

	/**
	 * Test that default delete_attachment_on_remove is false.
	 */
	public function test_default_delete_attachment_on_remove_is_false() {
		$settings_page     = new SettingsPage();
		$default_settings = $settings_page->get_default_settings();

		$this->assertFalse( $default_settings['delete_attachment_on_remove'] );
	}

	/**
	 * Test that sanitize_settings validates max_file_size bounds.
	 */
	public function test_sanitize_settings_validates_max_file_size_bounds() {
		$settings_page = new SettingsPage();

		// Test lower bound.
		$input    = array( 'max_file_size' => 0.05 );
		$result   = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 0.1, $result['max_file_size'] );

		// Test upper bound.
		$input  = array( 'max_file_size' => 15.0 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 10.0, $result['max_file_size'] );

		// Test valid value.
		$input  = array( 'max_file_size' => 5.0 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 5.0, $result['max_file_size'] );
	}

	/**
	 * Test that sanitize_settings filters invalid formats.
	 */
	public function test_sanitize_settings_filters_invalid_formats() {
		$settings_page = new SettingsPage();

		$input = array(
			'allowed_formats' => array(
				'image/jpeg',
				'image/invalid',
				'image/png',
				'application/pdf',
			),
		);

		$result = $settings_page->sanitize_settings( $input );

		$this->assertContains( 'image/jpeg', $result['allowed_formats'] );
		$this->assertContains( 'image/png', $result['allowed_formats'] );
		$this->assertNotContains( 'image/invalid', $result['allowed_formats'] );
		$this->assertNotContains( 'application/pdf', $result['allowed_formats'] );
	}

	/**
	 * Test that sanitize_settings validates dimension bounds.
	 */
	public function test_sanitize_settings_validates_dimension_bounds() {
		$settings_page = new SettingsPage();

		// Test width lower bound.
		$input  = array( 'max_width' => 50 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 100, $result['max_width'] );

		// Test width upper bound.
		$input  = array( 'max_width' => 6000 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 5000, $result['max_width'] );

		// Test height lower bound.
		$input  = array( 'max_height' => 50 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 100, $result['max_height'] );

		// Test height upper bound.
		$input  = array( 'max_height' => 6000 );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertEquals( 5000, $result['max_height'] );
	}

	/**
	 * Test that sanitize_settings handles boolean fields correctly.
	 */
	public function test_sanitize_settings_handles_boolean_fields() {
		$settings_page = new SettingsPage();

		// Test convert_to_webp true.
		$input  = array( 'convert_to_webp' => '1' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertTrue( $result['convert_to_webp'] );

		// Test convert_to_webp false.
		$input  = array( 'convert_to_webp' => '' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertFalse( $result['convert_to_webp'] );

		// Test require_approval true.
		$input  = array( 'require_approval' => '1' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertTrue( $result['require_approval'] );

		// Test require_approval false.
		$input  = array( 'require_approval' => '' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertFalse( $result['require_approval'] );

		// Test delete_attachment_on_remove true.
		$input  = array( 'delete_attachment_on_remove' => '1' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertTrue( $result['delete_attachment_on_remove'] );

		// Test delete_attachment_on_remove false.
		$input  = array( 'delete_attachment_on_remove' => '' );
		$result = $settings_page->sanitize_settings( $input );
		$this->assertFalse( $result['delete_attachment_on_remove'] );
	}

	/**
	 * Test that sanitize_settings returns empty array for allowed_formats when not an array.
	 */
	public function test_sanitize_settings_handles_non_array_allowed_formats() {
		$settings_page = new SettingsPage();

		$input  = array( 'allowed_formats' => 'not-an-array' );
		$result = $settings_page->sanitize_settings( $input );

		$this->assertIsArray( $result['allowed_formats'] );
		$this->assertEmpty( $result['allowed_formats'] );
	}

	/**
	 * Test that sanitize_settings returns empty array for allowed_roles when not an array.
	 */
	public function test_sanitize_settings_handles_non_array_allowed_roles() {
		$settings_page = new SettingsPage();

		$input  = array( 'allowed_roles' => 'not-an-array' );
		$result = $settings_page->sanitize_settings( $input );

		$this->assertIsArray( $result['allowed_roles'] );
		$this->assertEmpty( $result['allowed_roles'] );
	}

	/**
	 * Test that get_option_name returns the correct option name.
	 */
	public function test_get_option_name_returns_correct_value() {
		$settings_page = new SettingsPage();
		$option_name   = $settings_page->get_option_name();

		$this->assertEquals( 'avatar_steward_options', $option_name );
	}

	/**
	 * Test that render methods exist and are public.
	 */
	public function test_render_methods_exist() {
		$settings_page = new SettingsPage();

		$this->assertTrue( method_exists( $settings_page, 'render_settings_page' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_upload_restrictions_section' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_max_file_size_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_allowed_formats_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_max_width_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_max_height_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_convert_to_webp_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_roles_permissions_section' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_allowed_roles_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_require_approval_field' ) );
		$this->assertTrue( method_exists( $settings_page, 'render_delete_attachment_on_remove_field' ) );
	}

	/**
	 * Test that register_menu_page and register_settings methods exist.
	 */
	public function test_registration_methods_exist() {
		$settings_page = new SettingsPage();

		$this->assertTrue( method_exists( $settings_page, 'register_menu_page' ) );
		$this->assertTrue( method_exists( $settings_page, 'register_settings' ) );
	}
}
