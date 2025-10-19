<?php
/**
 * Logger implementation for Avatar Steward.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Infrastructure;

/**
 * Logger class that integrates with WordPress debug logging.
 *
 * This logger respects WordPress debug settings (WP_DEBUG, WP_DEBUG_LOG)
 * and provides structured logging throughout the plugin.
 */
class Logger implements LoggerInterface {

	/**
	 * Log level constants.
	 */
	private const LEVEL_EMERGENCY = 'emergency';
	private const LEVEL_ALERT     = 'alert';
	private const LEVEL_CRITICAL  = 'critical';
	private const LEVEL_ERROR     = 'error';
	private const LEVEL_WARNING   = 'warning';
	private const LEVEL_NOTICE    = 'notice';
	private const LEVEL_INFO      = 'info';
	private const LEVEL_DEBUG     = 'debug';

	/**
	 * Valid log levels.
	 *
	 * @var array<string>
	 */
	private const VALID_LEVELS = array(
		self::LEVEL_EMERGENCY,
		self::LEVEL_ALERT,
		self::LEVEL_CRITICAL,
		self::LEVEL_ERROR,
		self::LEVEL_WARNING,
		self::LEVEL_NOTICE,
		self::LEVEL_INFO,
		self::LEVEL_DEBUG,
	);

	/**
	 * Minimum log level to record.
	 *
	 * @var string
	 */
	private string $min_level;

	/**
	 * Whether logging is enabled.
	 *
	 * @var bool
	 */
	private bool $enabled;

	/**
	 * Constructor.
	 *
	 * @param string $min_level Minimum log level (default: 'debug').
	 * @param bool   $enabled   Whether logging is enabled (auto-detected from WP_DEBUG if null).
	 */
	public function __construct( string $min_level = self::LEVEL_DEBUG, ?bool $enabled = null ) {
		$this->min_level = $min_level;

		// Auto-detect if logging should be enabled based on WordPress constants.
		if ( null === $enabled ) {
			$this->enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
		} else {
			$this->enabled = $enabled;
		}
	}

	/**
	 * System is unusable.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function emergency( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_EMERGENCY, $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function alert( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_ALERT, $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function critical( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_CRITICAL, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function error( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_ERROR, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function warning( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_WARNING, $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function notice( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_NOTICE, $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function info( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_INFO, $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function debug( string $message, array $context = array() ): void {
		$this->log( self::LEVEL_DEBUG, $message, $context );
	}

	/**
	 * Log with an arbitrary level.
	 *
	 * @param string               $level   Log level.
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void {
		if ( ! $this->enabled ) {
			return;
		}

		if ( ! $this->should_log( $level ) ) {
			return;
		}

		$formatted_message = $this->format_message( $level, $message, $context );

		// Use WordPress error_log if available and WP_DEBUG_LOG is enabled.
		if ( function_exists( 'error_log' ) ) {
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( $formatted_message );
			}
		}

		/**
		 * Fires when a log entry is recorded.
		 *
		 * @param string $level   Log level.
		 * @param string $message Log message.
		 * @param array  $context Additional context data.
		 *
		 * @since 1.0.0
		 */
		if ( function_exists( 'do_action' ) ) {
			do_action( 'avatarsteward_log', $level, $message, $context );
		}
	}

	/**
	 * Check if a message should be logged based on level.
	 *
	 * @param string $level Log level to check.
	 *
	 * @return bool True if should log.
	 */
	private function should_log( string $level ): bool {
		if ( ! in_array( $level, self::VALID_LEVELS, true ) ) {
			return false;
		}

		$level_priority     = array_search( $level, self::VALID_LEVELS, true );
		$min_level_priority = array_search( $this->min_level, self::VALID_LEVELS, true );

		// Lower index = higher priority. Log if level priority is higher or equal to min level.
		return false !== $level_priority && false !== $min_level_priority && $level_priority <= $min_level_priority;
	}

	/**
	 * Format a log message with level and context.
	 *
	 * @param string               $level   Log level.
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return string Formatted message.
	 */
	private function format_message( string $level, string $message, array $context ): string {
		$level_upper = strtoupper( $level );
		$formatted   = "[Avatar Steward] [{$level_upper}] {$message}";

		if ( ! empty( $context ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
			$context_json = json_encode( $context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			$formatted   .= ' | Context: ' . $context_json;
		}

		return $formatted;
	}

	/**
	 * Get the minimum log level.
	 *
	 * @return string Minimum log level.
	 */
	public function get_min_level(): string {
		return $this->min_level;
	}

	/**
	 * Check if logging is enabled.
	 *
	 * @return bool True if enabled.
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}

	/**
	 * Set the minimum log level.
	 *
	 * @param string $level Minimum log level.
	 *
	 * @return void
	 */
	public function set_min_level( string $level ): void {
		if ( in_array( $level, self::VALID_LEVELS, true ) ) {
			$this->min_level = $level;
		}
	}

	/**
	 * Enable or disable logging.
	 *
	 * @param bool $enabled Whether to enable logging.
	 *
	 * @return void
	 */
	public function set_enabled( bool $enabled ): void {
		$this->enabled = $enabled;
	}
}
