<?php
/**
 * Main Plugin class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward;

use AvatarSteward\Core\AvatarHandler;

/**
 * Plugin singleton class.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Settings page instance.
	 *
	 * @var Admin\SettingsPage|null
	 */
	private ?Admin\SettingsPage $settings_page = null;

	/**
	 * Migration page instance.
	 *
	 * @var Admin\MigrationPage|null
	 */
	private ?Admin\MigrationPage $migration_page = null;

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'plugins_loaded', array( $this, 'boot' ) );
		}
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return self The singleton instance.
	 */
	public static function instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the plugin.
	 *
	 * @return void
	 */
	public function boot(): void {
		$this->init_settings_page();
		$this->init_migration_page();

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_booted' );
		}
	}

	/**
	 * Initialize the settings page.
	 *
	 * @return void
	 */
	private function init_settings_page(): void {
		if ( ! class_exists( Admin\SettingsPage::class ) ) {
			require_once __DIR__ . '/Admin/SettingsPage.php';
		}

		$this->settings_page = new Admin\SettingsPage();
		$this->settings_page->init();
	}

	/**
	 * Initialize the migration page.
	 *
	 * @return void
	 */
	private function init_migration_page(): void {
		if ( ! class_exists( Admin\MigrationPage::class ) ) {
			require_once __DIR__ . '/Admin/MigrationPage.php';
		}

		if ( ! class_exists( Domain\Migration\MigrationService::class ) ) {
			require_once __DIR__ . '/Domain/Migration/MigrationService.php';
		}

		$migration_service    = new Domain\Migration\MigrationService();
		$this->migration_page = new Admin\MigrationPage( $migration_service );
		$this->migration_page->init();
	}

	/**
	 * Get the settings page instance.
	 *
	 * @return Admin\SettingsPage|null Settings page instance.
	 */
	public function get_settings_page(): ?Admin\SettingsPage {
		return $this->settings_page;
	}

	/**
	 * Get the migration page instance.
	 *
	 * @return Admin\MigrationPage|null Migration page instance.
	 */
	public function get_migration_page(): ?Admin\MigrationPage {
		return $this->migration_page;
	}
}
