<?php
/**
 * Logger Interface.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Infrastructure;

/**
 * Logger interface following PSR-3 style for consistent logging across the plugin.
 */
interface LoggerInterface {

	/**
	 * System is unusable.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function emergency( string $message, array $context = array() ): void;

	/**
	 * Action must be taken immediately.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function alert( string $message, array $context = array() ): void;

	/**
	 * Critical conditions.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function critical( string $message, array $context = array() ): void;

	/**
	 * Runtime errors that do not require immediate action.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function error( string $message, array $context = array() ): void;

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function warning( string $message, array $context = array() ): void;

	/**
	 * Normal but significant events.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function notice( string $message, array $context = array() ): void;

	/**
	 * Interesting events.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function info( string $message, array $context = array() ): void;

	/**
	 * Detailed debug information.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function debug( string $message, array $context = array() ): void;

	/**
	 * Log with an arbitrary level.
	 *
	 * @param string               $level   Log level.
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Additional context data.
	 *
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void;
}
