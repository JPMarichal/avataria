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
use AvatarSteward\Domain\Licensing\LicenseManager;

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
	 * Integration service instance.
	 *
	 * @var Domain\Integrations\IntegrationService|null
	 */
	private ?Domain\Integrations\IntegrationService $integration_service = null;

	/**
	 * Visual identity REST controller instance.
	 *
	 * @var Infrastructure\REST\VisualIdentityController|null
	 */
	private ?Infrastructure\REST\VisualIdentityController $visual_identity_controller = null;

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'plugins_loaded', array( $this, 'boot' ) );
			add_action( 'rest_api_init', array( $this, 'init_rest_api' ) );
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
		$this->init_avatar_handler();
		$this->init_settings_page();
		$this->init_migration_page();
		$this->init_integration_service();

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_booted' );
		}
	}

	/**
	 * Initialize the avatar handler.
	 *
	 * @return void
	 */
	private function init_avatar_handler(): void {
		if ( ! class_exists( Core\AvatarHandler::class ) ) {
			require_once __DIR__ . '/Core/AvatarHandler.php';
		}

		$this->avatar_handler = new Core\AvatarHandler();
		$this->avatar_handler->init();
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
	 * Initialize the moderation system.
	 *
	 * @return void
	 */
	private function init_moderation(): void {
		if ( ! class_exists( Domain\Moderation\ModerationQueue::class ) ) {
			require_once __DIR__ . '/Domain/Moderation/ModerationQueue.php';
		}

		if ( ! class_exists( Domain\Moderation\DecisionService::class ) ) {
			require_once __DIR__ . '/Domain/Moderation/DecisionService.php';
		}

		if ( ! class_exists( Admin\ModerationPage::class ) ) {
			require_once __DIR__ . '/Admin/ModerationPage.php';
		}

		// Initialize moderation queue.
		$this->moderation_queue = new Domain\Moderation\ModerationQueue();

		// Set moderation queue in avatar handler if available.
		if ( $this->avatar_handler ) {
			$this->avatar_handler->set_moderation_queue( $this->moderation_queue );
		}

		// Initialize decision service.
		$decision_service = new Domain\Moderation\DecisionService( $this->moderation_queue );

		// Initialize moderation page.
		$this->moderation_page = new Admin\ModerationPage( $this->moderation_queue, $decision_service );
		$this->moderation_page->init();
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
	 * Initialize the integration service.
	 *
	 * @return void
	 */
	private function init_integration_service(): void {
		if ( ! class_exists( Domain\Integrations\IntegrationService::class ) ) {
			require_once __DIR__ . '/Domain/Integrations/SocialProviderInterface.php';
			require_once __DIR__ . '/Domain/Integrations/AbstractSocialProvider.php';
			require_once __DIR__ . '/Domain/Integrations/TwitterProvider.php';
			require_once __DIR__ . '/Domain/Integrations/FacebookProvider.php';
			require_once __DIR__ . '/Domain/Integrations/IntegrationService.php';
		}

		$this->integration_service = new Domain\Integrations\IntegrationService();
		$this->integration_service->init();
	}

	/**
	 * Get the integration service instance.
	 *
	 * @return Domain\Integrations\IntegrationService|null Integration service instance.
	 */
	public function get_integration_service(): ?Domain\Integrations\IntegrationService {
		return $this->integration_service;
	}

	/**
	 * Initialize REST API endpoints.
	 *
	 * @return void
	 */
	public function init_rest_api(): void {
		if ( ! class_exists( Infrastructure\REST\VisualIdentityController::class ) ) {
			require_once __DIR__ . '/Domain/VisualIdentity/VisualIdentityService.php';
			require_once __DIR__ . '/Infrastructure/REST/VisualIdentityController.php';
		}

		$this->visual_identity_controller = new Infrastructure\REST\VisualIdentityController();
		$this->visual_identity_controller->register_routes();
	}

	/**
	 * Get the visual identity controller instance.
	 *
	 * @return Infrastructure\REST\VisualIdentityController|null Controller instance.
	 */
	public function get_visual_identity_controller(): ?Infrastructure\REST\VisualIdentityController {
		return $this->visual_identity_controller;
	}
}
