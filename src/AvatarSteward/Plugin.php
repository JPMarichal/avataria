<?php
/**
 * Main Plugin class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward;

use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Initials\Generator;
use AvatarSteward\Domain\LowBandwidth\BandwidthOptimizer;

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
	 * Profile fields renderer instance.
	 *
	 * @var Domain\Uploads\ProfileFieldsRenderer|null
	 */
	private ?Domain\Uploads\ProfileFieldsRenderer $profile_fields_renderer = null;

	/**
	 * Upload handler instance.
	 *
	 * @var Domain\Uploads\UploadHandler|null
	 */
	private ?Domain\Uploads\UploadHandler $upload_handler = null;

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
		$this->init_upload_services();

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

	/**
	 * Initialize upload services.
	 *
	 * @return void
	 */
	private function init_upload_services(): void {
		if ( ! class_exists( Domain\Uploads\UploadService::class ) ) {
			require_once __DIR__ . '/Domain/Uploads/UploadService.php';
		}

		if ( ! class_exists( Domain\Uploads\ProfileFieldsRenderer::class ) ) {
			require_once __DIR__ . '/Domain/Uploads/ProfileFieldsRenderer.php';
		}

		if ( ! class_exists( Domain\Uploads\UploadHandler::class ) ) {
			require_once __DIR__ . '/Domain/Uploads/UploadHandler.php';
		}

		// Create upload service instance.
		$upload_service = new Domain\Uploads\UploadService();

		// Create and register profile fields renderer.
		$this->profile_fields_renderer = new Domain\Uploads\ProfileFieldsRenderer( $upload_service );
		$this->profile_fields_renderer->register_hooks();

		// Create and register upload handler.
		$this->upload_handler = new Domain\Uploads\UploadHandler( $upload_service );
		$this->upload_handler->register_hooks();
	}

	/**
	 * Get the profile fields renderer instance.
	 *
	 * @return Domain\Uploads\ProfileFieldsRenderer|null Profile fields renderer instance.
	 */
	public function get_profile_fields_renderer(): ?Domain\Uploads\ProfileFieldsRenderer {
		return $this->profile_fields_renderer;
	}

	/**
	 * Get the upload handler instance.
	 *
	 * @return Domain\Uploads\UploadHandler|null Upload handler instance.
	 */
	public function get_upload_handler(): ?Domain\Uploads\UploadHandler {
		return $this->upload_handler;
	}
}
