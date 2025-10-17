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
		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_booted' );
		}
	}
}
