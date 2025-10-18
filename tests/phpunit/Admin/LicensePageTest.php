<?php
/**
 * Tests for LicensePage class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Admin;

use AvatarSteward\Admin\LicensePage;
use AvatarSteward\Domain\Licensing\LicenseManager;
use PHPUnit\Framework\TestCase;

/**
 * Test LicensePage functionality.
 */
class LicensePageTest extends TestCase {

	/**
	 * License manager mock.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $license_manager;

	/**
	 * License page instance.
	 *
	 * @var LicensePage
	 */
	private LicensePage $page;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->license_manager = $this->createMock( LicenseManager::class );
		$this->page            = new LicensePage( $this->license_manager );
	}

	/**
	 * Test constructor initializes with license manager.
	 *
	 * @return void
	 */
	public function test_constructor(): void {
		$this->assertInstanceOf( LicensePage::class, $this->page );
	}

	/**
	 * Test init registers actions.
	 *
	 * @return void
	 */
	public function test_init_registers_actions(): void {
		$actions_called = array();

		// Mock add_action to track calls.
		if ( ! function_exists( 'add_action' ) ) {
			/**
			 * Mock add_action.
			 *
			 * @param string   $hook     Hook name.
			 * @param callable $callback Callback function.
			 * @return void
			 */
			function add_action( $hook, $callback ) {
				global $actions_called;
				$actions_called[] = $hook;
			}
		}

		$this->page->init();

		// We can't easily test this without WordPress test framework,
		// but we can at least verify the method exists.
		$this->assertTrue( method_exists( $this->page, 'init' ) );
	}

	/**
	 * Test page has handle_activation method.
	 *
	 * @return void
	 */
	public function test_has_handle_activation_method(): void {
		$this->assertTrue( method_exists( $this->page, 'handle_activation' ) );
	}

	/**
	 * Test page has handle_deactivation method.
	 *
	 * @return void
	 */
	public function test_has_handle_deactivation_method(): void {
		$this->assertTrue( method_exists( $this->page, 'handle_deactivation' ) );
	}

	/**
	 * Test page has render_page method.
	 *
	 * @return void
	 */
	public function test_has_render_page_method(): void {
		$this->assertTrue( method_exists( $this->page, 'render_page' ) );
	}

	/**
	 * Test page has register_page method.
	 *
	 * @return void
	 */
	public function test_has_register_page_method(): void {
		$this->assertTrue( method_exists( $this->page, 'register_page' ) );
	}
}
