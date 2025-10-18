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
	 * Avatar handler instance.
	 *
	 * @var AvatarHandler|null
	 */
	private ?AvatarHandler $avatar_handler = null;

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
		$this->init_avatar_handler();

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
	 * Get the settings page instance.
	 *
	 * @return Admin\SettingsPage|null Settings page instance.
	 */
	public function get_settings_page(): ?Admin\SettingsPage {
		return $this->settings_page;
	}

	/**
	 * Initialize the avatar handler.
	 *
	 * @return void
	 */
	private function init_avatar_handler(): void {
		if ( ! class_exists( AvatarHandler::class ) ) {
			require_once __DIR__ . '/Core/AvatarHandler.php';
		}

		$this->avatar_handler = new AvatarHandler();

		// Set up bandwidth optimizer if low-bandwidth mode is enabled.
		$settings = $this->settings_page ? $this->settings_page->get_settings() : array();

		if ( ! empty( $settings['low_bandwidth_mode'] ) ) {
			if ( ! class_exists( Generator::class ) ) {
				require_once __DIR__ . '/Domain/Initials/Generator.php';
			}
			if ( ! class_exists( BandwidthOptimizer::class ) ) {
				require_once __DIR__ . '/Domain/LowBandwidth/BandwidthOptimizer.php';
			}

			$generator = new Generator();
			$threshold = isset( $settings['bandwidth_threshold'] ) ? (int) $settings['bandwidth_threshold'] : 100;

			$optimizer = new BandwidthOptimizer(
				$generator,
				array(
					'enabled'   => true,
					'threshold' => $threshold * 1024, // Convert KB to bytes.
				)
			);

			$this->avatar_handler->set_optimizer( $optimizer );
		}

		$this->avatar_handler->init();
	}

	/**
	 * Get the avatar handler instance.
	 *
	 * @return AvatarHandler|null Avatar handler instance.
	 */
	public function get_avatar_handler(): ?AvatarHandler {
		return $this->avatar_handler;
	}
}
