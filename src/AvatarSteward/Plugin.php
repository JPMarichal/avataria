<?php
/**
 * Main Plugin class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward;

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
		// Initialize upload functionality.
		$this->init_upload_handlers();

		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_booted' );
		}
	}

	/**
	 * Initialize upload handlers.
	 *
	 * @return void
	 */
	private function init_upload_handlers(): void {
		if ( ! is_admin() ) {
			return;
		}

		$upload_service  = new Domain\Uploads\UploadService();
		$upload_handler  = new Domain\Uploads\UploadHandler( $upload_service );
		$fields_renderer = new Domain\Uploads\ProfileFieldsRenderer( $upload_service );

		$upload_handler->register_hooks();
		$fields_renderer->register_hooks();
	}
}
