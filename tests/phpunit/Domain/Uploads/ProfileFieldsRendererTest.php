<?php
/**
 * Tests for ProfileFieldsRenderer class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Uploads\ProfileFieldsRenderer;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Test case for ProfileFieldsRenderer class.
 */
final class ProfileFieldsRendererTest extends TestCase {

	/**
	 * Test that ProfileFieldsRenderer class exists.
	 */
	public function test_profile_fields_renderer_class_exists() {
		$this->assertTrue( class_exists( ProfileFieldsRenderer::class ) );
	}

	/**
	 * Test that ProfileFieldsRenderer can be instantiated.
	 */
	public function test_profile_fields_renderer_can_be_instantiated() {
		$service  = new UploadService();
		$renderer = new ProfileFieldsRenderer( $service );
		$this->assertInstanceOf( ProfileFieldsRenderer::class, $renderer );
	}

	/**
	 * Test that renderer has register_hooks method.
	 */
	public function test_renderer_has_register_hooks_method() {
		$service  = new UploadService();
		$renderer = new ProfileFieldsRenderer( $service );
		$this->assertTrue( method_exists( $renderer, 'register_hooks' ) );
	}

	/**
	 * Test that renderer has enqueue_scripts method.
	 */
	public function test_renderer_has_enqueue_scripts_method() {
		$service  = new UploadService();
		$renderer = new ProfileFieldsRenderer( $service );
		$this->assertTrue( method_exists( $renderer, 'enqueue_scripts' ) );
	}

	/**
	 * Test that renderer has render_upload_field method.
	 */
	public function test_renderer_has_render_upload_field_method() {
		$service  = new UploadService();
		$renderer = new ProfileFieldsRenderer( $service );
		$this->assertTrue( method_exists( $renderer, 'render_upload_field' ) );
	}

	/**
	 * Test that renderer has show_error_notice method.
	 */
	public function test_renderer_has_show_error_notice_method() {
		$service  = new UploadService();
		$renderer = new ProfileFieldsRenderer( $service );
		$this->assertTrue( method_exists( $renderer, 'show_error_notice' ) );
	}
}
