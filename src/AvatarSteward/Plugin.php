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
	 * License manager instance.
	 *
	 * @var LicenseManager|null
	 */
	private ?LicenseManager $license_manager = null;

	/**
	 * Library page instance.
	 *
	 * @var Admin\LibraryPage|null
	 */
	private ?Admin\LibraryPage $library_page = null;

	/**
	 * Visual identity REST controller instance.
	 *
	 * @var Infrastructure\REST\VisualIdentityController|null
	 */
	private ?Infrastructure\REST\VisualIdentityController $visual_identity_controller = null;

	/**
	 * Cleanup service instance.
	 *
	 * @var Domain\Cleanup\CleanupService|null
	 */
	private ?Domain\Cleanup\CleanupService $cleanup_service = null;
	 * Audit service instance.
	 *
	 * @var Domain\Audit\AuditService|null
	 */
	private ?Domain\Audit\AuditService $audit_service = null;

	/**
	 * Audit page instance.
	 *
	 * @var Admin\AuditPage|null
	 */
	private ?Admin\AuditPage $audit_page = null;

	/**
	 * Audit REST controller instance.
	 *
	 * @var Infrastructure\REST\AuditController|null
	 */
	private ?Infrastructure\REST\AuditController $audit_controller = null;

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		if ( function_exists( 'add_action' ) ) {
			add_action( 'plugins_loaded', array( $this, 'boot' ) );
			add_action( 'rest_api_init', array( $this, 'init_rest_api' ) );
			add_action( 'avatarsteward_cleanup_inactive_avatars', array( $this, 'run_cleanup' ) );
		}
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return self The singleton instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
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
	$this->init_library_page();
	$this->init_audit_service();
  $this->init_cleanup_service();

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

		// Initialize license manager if not already initialized.
		if ( null === $this->license_manager ) {
			$this->license_manager = new LicenseManager();
		}

		$this->settings_page = new Admin\SettingsPage( $this->license_manager );
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
	 * Get the license manager instance.
	 *
	 * @return LicenseManager|null License manager instance.
	 */
	public function get_license_manager(): ?LicenseManager {
		if ( null === $this->license_manager ) {
			$this->license_manager = new LicenseManager();
		}
		return $this->license_manager;
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
	 * Initialize the library page.
	 *
	 * @return void
	 */
	private function init_library_page(): void {
		if ( ! class_exists( Domain\Library\BadgeService::class ) ) {
			require_once __DIR__ . '/Domain/Library/BadgeService.php';
		}

		if ( ! class_exists( Domain\Library\SectoralTemplateService::class ) ) {
			require_once __DIR__ . '/Domain/Library/SectoralTemplateService.php';
		}

		if ( ! class_exists( Domain\Library\LibraryService::class ) ) {
			require_once __DIR__ . '/Domain/Library/LibraryService.php';
		}

		if ( ! class_exists( Admin\LibraryPage::class ) ) {
			require_once __DIR__ . '/Admin/LibraryPage.php';
		}

		if ( ! class_exists( Admin\LibraryRestController::class ) ) {
			require_once __DIR__ . '/Admin/LibraryRestController.php';
		}

		if ( ! class_exists( Domain\Uploads\UploadService::class ) ) {
			require_once __DIR__ . '/Domain/Uploads/UploadService.php';
		}

		$badge_service   = new Domain\Library\BadgeService();
		$library_service = new Domain\Library\LibraryService( null, $badge_service );
		$upload_service  = new Domain\Uploads\UploadService();

		$this->library_page = new Admin\LibraryPage( $library_service, $upload_service );
		$this->library_page->init();

		// Register REST API routes.
		add_action(
			'rest_api_init',
			function () use ( $library_service ) {
				$rest_controller = new Admin\LibraryRestController( $library_service );
				$rest_controller->register_routes();
			}
		);
	}

	/**
	 * Get the library page instance.
	 *
	 * @return Admin\LibraryPage|null Library page instance.
	 */
	public function get_library_page(): ?Admin\LibraryPage {
		return $this->library_page;
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

		// Register audit REST controller.
		if ( $this->audit_controller ) {
			$this->audit_controller->register_routes();
		}
	}

	/**
	 * Get the visual identity controller instance.
	 *
	 * @return Infrastructure\REST\VisualIdentityController|null Controller instance.
	 */
	public function get_visual_identity_controller(): ?Infrastructure\REST\VisualIdentityController {
		return $this->visual_identity_controller;
	}

	/**
	 * Initialize the cleanup service.
	 *
	 * @return void
	 */
	private function init_cleanup_service(): void {
		if ( ! class_exists( Domain\Cleanup\CleanupService::class ) ) {
			require_once __DIR__ . '/Domain/Cleanup/CleanupService.php';
		}

		$this->cleanup_service = new Domain\Cleanup\CleanupService();

		// Schedule or unschedule cleanup based on settings.
		if ( $this->settings_page ) {
			$settings = $this->settings_page->get_settings();

			if ( ! empty( $settings['cleanup_enabled'] ) ) {
				$schedule = isset( $settings['cleanup_schedule'] ) ? $settings['cleanup_schedule'] : 'weekly';
				$this->cleanup_service->schedule_cleanup( $schedule );
			} else {
				$this->cleanup_service->unschedule_cleanup();
			}
		}
	}

	/**
	 * Get the cleanup service instance.
	 *
	 * @return Domain\Cleanup\CleanupService|null Cleanup service instance.
	 */
	public function get_cleanup_service(): ?Domain\Cleanup\CleanupService {
		return $this->cleanup_service;
	}

	/**
	 * Run the cleanup task (triggered by WP-Cron).
	 *
	 * @return void
	 */
	public function run_cleanup(): void {
		if ( ! $this->cleanup_service ) {
			return;
		}

		if ( ! $this->settings_page ) {
			return;
		}

		$settings = $this->settings_page->get_settings();

		// Only run if cleanup is enabled.
		if ( empty( $settings['cleanup_enabled'] ) ) {
			return;
		}

		// Find inactive avatars.
		$criteria = array(
			'max_age_days'         => isset( $settings['cleanup_max_age_days'] ) ? (int) $settings['cleanup_max_age_days'] : 365,
			'exclude_active_users' => true,
			'user_inactivity_days' => isset( $settings['cleanup_user_inactivity_days'] ) ? (int) $settings['cleanup_user_inactivity_days'] : 180,
		);

		$inactive_avatars = $this->cleanup_service->find_inactive_avatars( $criteria );

		// Delete inactive avatars.
		$options = array(
			'dry_run'       => false,
			'notify_users'  => ! empty( $settings['cleanup_notify_users'] ),
			'notify_admins' => ! empty( $settings['cleanup_notify_admins'] ),
		);

		$this->cleanup_service->delete_inactive_avatars( $inactive_avatars, $options );
	 * Initialize the audit service.
	 *
	 * @return void
	 */
	private function init_audit_service(): void {
		if ( ! class_exists( Domain\Audit\AuditRepository::class ) ) {
			require_once __DIR__ . '/Domain/Audit/AuditRepository.php';
		}

		if ( ! class_exists( Domain\Audit\AuditService::class ) ) {
			require_once __DIR__ . '/Domain/Audit/AuditService.php';
		}

		if ( ! class_exists( Infrastructure\Logger::class ) ) {
			require_once __DIR__ . '/Infrastructure/LoggerInterface.php';
			require_once __DIR__ . '/Infrastructure/Logger.php';
		}

		// Initialize repository and service.
		$repository         = new Domain\Audit\AuditRepository();
		$logger             = new Infrastructure\Logger();
		$this->audit_service = new Domain\Audit\AuditService( $repository, $logger );

		// Create audit table on plugin activation.
		register_activation_hook( dirname( __DIR__, 2 ) . '/avatar-steward.php', array( $repository, 'create_table' ) );

		// Initialize audit page if in admin.
		if ( is_admin() ) {
			$this->init_audit_page();
		}

		// Initialize REST controller.
		if ( ! class_exists( Infrastructure\REST\AuditController::class ) ) {
			require_once __DIR__ . '/Infrastructure/REST/AuditController.php';
		}

		$this->audit_controller = new Infrastructure\REST\AuditController(
			$this->audit_service,
			$this->get_license_manager()
		);

		// Schedule daily log purge cron.
		$this->schedule_log_purge();
	}

	/**
	 * Initialize the audit page.
	 *
	 * @return void
	 */
	private function init_audit_page(): void {
		if ( ! class_exists( Admin\AuditPage::class ) ) {
			require_once __DIR__ . '/Admin/AuditPage.php';
		}

		$this->audit_page = new Admin\AuditPage(
			$this->audit_service,
			$this->get_license_manager()
		);
		$this->audit_page->init();
	}

	/**
	 * Schedule daily log purge cron job.
	 *
	 * @return void
	 */
	private function schedule_log_purge(): void {
		if ( ! wp_next_scheduled( 'avatarsteward_purge_audit_logs' ) ) {
			wp_schedule_event( time(), 'daily', 'avatarsteward_purge_audit_logs' );
		}

		add_action(
			'avatarsteward_purge_audit_logs',
			function () {
				if ( $this->audit_service ) {
					$retention_days = get_option( 'avatar_steward_audit_retention_days', 90 );
					$this->audit_service->purge_old_logs( $retention_days );
				}
			}
		);
	}

	/**
	 * Get the audit service instance.
	 *
	 * @return Domain\Audit\AuditService|null Audit service instance.
	 */
	public function get_audit_service(): ?Domain\Audit\AuditService {
		return $this->audit_service;
	}

	/**
	 * Get the audit page instance.
	 *
	 * @return Admin\AuditPage|null Audit page instance.
	 */
	public function get_audit_page(): ?Admin\AuditPage {
		return $this->audit_page;
	}
}
