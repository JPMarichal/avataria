<?php
/**
 * Tests for SectoralTemplateService class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Domain\Library;

use AvatarSteward\Domain\Library\SectoralTemplateService;
use PHPUnit\Framework\TestCase;

/**
 * Test case for SectoralTemplateService.
 */
class SectoralTemplateServiceTest extends TestCase {

	/**
	 * Template service instance.
	 *
	 * @var SectoralTemplateService
	 */
	private SectoralTemplateService $template_service;

	/**
	 * Set up test case.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->template_service = new SectoralTemplateService();
	}

	/**
	 * Test that template service can be instantiated.
	 */
	public function test_template_service_instantiation(): void {
		$this->assertInstanceOf( SectoralTemplateService::class, $this->template_service );
	}

	/**
	 * Test get_templates returns all templates.
	 */
	public function test_get_templates(): void {
		$templates = $this->template_service->get_templates();

		$this->assertIsArray( $templates );
		$this->assertNotEmpty( $templates );
		$this->assertArrayHasKey( 'elearning', $templates );
		$this->assertArrayHasKey( 'ecommerce', $templates );
		$this->assertArrayHasKey( 'forum', $templates );
		$this->assertArrayHasKey( 'membership', $templates );
		$this->assertArrayHasKey( 'corporate', $templates );
		$this->assertArrayHasKey( 'healthcare', $templates );
	}

	/**
	 * Test get_template with valid sector.
	 */
	public function test_get_template_valid(): void {
		$template = $this->template_service->get_template( 'elearning' );

		$this->assertIsArray( $template );
		$this->assertArrayHasKey( 'name', $template );
		$this->assertArrayHasKey( 'description', $template );
		$this->assertArrayHasKey( 'tags', $template );
		$this->assertArrayHasKey( 'settings', $template );
		$this->assertSame( 'eLearning', $template['name'] );
	}

	/**
	 * Test get_template with invalid sector.
	 */
	public function test_get_template_invalid(): void {
		$template = $this->template_service->get_template( 'invalid' );

		$this->assertNull( $template );
	}

	/**
	 * Test template_exists with valid sector.
	 */
	public function test_template_exists_valid(): void {
		$this->assertTrue( $this->template_service->template_exists( 'elearning' ) );
		$this->assertTrue( $this->template_service->template_exists( 'forum' ) );
	}

	/**
	 * Test template_exists with invalid sector.
	 */
	public function test_template_exists_invalid(): void {
		$this->assertFalse( $this->template_service->template_exists( 'invalid' ) );
	}

	/**
	 * Test get_template_preview with valid sector.
	 */
	public function test_get_template_preview(): void {
		$preview = $this->template_service->get_template_preview( 'ecommerce' );

		$this->assertIsArray( $preview );
		$this->assertArrayHasKey( 'sector', $preview );
		$this->assertArrayHasKey( 'name', $preview );
		$this->assertArrayHasKey( 'description', $preview );
		$this->assertArrayHasKey( 'tags', $preview );
		$this->assertArrayHasKey( 'settings', $preview );
		$this->assertSame( 'ecommerce', $preview['sector'] );
		$this->assertSame( 'eCommerce', $preview['name'] );
	}

	/**
	 * Test get_template_preview with invalid sector.
	 */
	public function test_get_template_preview_invalid(): void {
		$preview = $this->template_service->get_template_preview( 'invalid' );

		$this->assertNull( $preview );
	}

	/**
	 * Test template structure.
	 */
	public function test_template_structure(): void {
		$templates = $this->template_service->get_templates();

		foreach ( $templates as $sector => $template ) {
			$this->assertArrayHasKey( 'name', $template );
			$this->assertArrayHasKey( 'description', $template );
			$this->assertArrayHasKey( 'tags', $template );
			$this->assertArrayHasKey( 'settings', $template );

			$this->assertIsString( $template['name'] );
			$this->assertIsString( $template['description'] );
			$this->assertIsArray( $template['tags'] );
			$this->assertIsArray( $template['settings'] );

			// Check settings structure.
			$this->assertArrayHasKey( 'require_approval', $template['settings'] );
			$this->assertArrayHasKey( 'allowed_roles', $template['settings'] );
			$this->assertArrayHasKey( 'max_file_size', $template['settings'] );

			$this->assertIsBool( $template['settings']['require_approval'] );
			$this->assertIsArray( $template['settings']['allowed_roles'] );
			$this->assertIsFloat( $template['settings']['max_file_size'] );
		}
	}

	/**
	 * Test all templates have unique names.
	 */
	public function test_templates_unique_names(): void {
		$templates = $this->template_service->get_templates();
		$names     = array();

		foreach ( $templates as $template ) {
			$this->assertNotContains( $template['name'], $names );
			$names[] = $template['name'];
		}

		$this->assertCount( count( $templates ), $names );
	}
}
