<?php
/**
 * Example demonstrating logger usage in Avatar Steward.
 *
 * This file shows practical examples of how to use the logging system
 * throughout the plugin for debugging and monitoring.
 *
 * @package AvatarSteward
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
 * phpcs:disable Generic.CodeAnalysis.EmptyStatement
 * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter
 * phpcs:disable Universal.Files.SeparateFunctionsFromOO
 */

// This is an example file for documentation purposes only.
// Do not include in production code.

use AvatarSteward\Infrastructure\Logger;
use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Example 1: Basic Logger Setup
 *
 * Create a logger and use it for basic logging.
 */
function example_basic_logger_usage() {
	// Create a logger instance.
	$logger = new Logger();

	// Log messages at different levels.
	$logger->debug( 'This is debug information for developers' );
	$logger->info( 'User logged in successfully' );
	$logger->notice( 'Password will expire in 7 days' );
	$logger->warning( 'Disk space is running low' );
	$logger->error( 'Failed to connect to database' );
	$logger->critical( 'Application database is down' );
	$logger->alert( 'Website is under DDoS attack' );
	$logger->emergency( 'System is completely unusable' );
}

/**
 * Example 2: Logging with Context
 *
 * Add structured data to log messages for better debugging.
 */
function example_logging_with_context() {
	$logger = new Logger();

	// Log with context array.
	$logger->info(
		'User avatar uploaded',
		array(
			'user_id'     => 123,
			'username'    => 'john_doe',
			'file_name'   => 'avatar.jpg',
			'file_size'   => 204800,
			'mime_type'   => 'image/jpeg',
			'upload_time' => time(),
			'ip_address'  => '192.168.1.1',
			'user_agent'  => 'Mozilla/5.0',
		)
	);

	// Log an error with detailed context.
	$logger->error(
		'Avatar upload validation failed',
		array(
			'user_id'       => 123,
			'error_code'    => 'FILE_TOO_LARGE',
			'file_size'     => 5242880,
			'max_file_size' => 2097152,
			'file_name'     => 'large_photo.jpg',
		)
	);
}

/**
 * Example 3: Service with Logger Injection
 *
 * Show how to inject logger into services following SOLID principles.
 */
class ExampleAvatarService {

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface|null $logger Optional logger instance.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Process an avatar operation.
	 *
	 * @param int $user_id User ID.
	 * @return bool Success status.
	 */
	public function process_avatar( int $user_id ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Starting avatar processing', array( 'user_id' => $user_id ) );
		}

		try {
			// Simulate some processing.
			$result = $this->do_processing( $user_id );

			if ( $result ) {
				if ( $this->logger ) {
					$this->logger->info(
						'Avatar processing completed successfully',
						array(
							'user_id' => $user_id,
							'time_ms' => 150,
						)
					);
				}
				return true;
			} else {
				if ( $this->logger ) {
					$this->logger->warning(
						'Avatar processing completed with warnings',
						array( 'user_id' => $user_id )
					);
				}
				return false;
			}
		} catch ( \Exception $e ) {
			if ( $this->logger ) {
				$this->logger->error(
					'Avatar processing failed',
					array(
						'user_id'   => $user_id,
						'exception' => get_class( $e ),
						'message'   => $e->getMessage(),
						'file'      => $e->getFile(),
						'line'      => $e->getLine(),
					)
				);
			}
			return false;
		}
	}

	/**
	 * Simulate processing.
	 *
	 * @param int $user_id User ID.
	 * @return bool Result.
	 */
	private function do_processing( int $user_id ): bool {
		// Simulate processing logic.
		return true;
	}
}

/**
 * Example 4: Configurable Log Levels
 *
 * Control which messages are logged based on environment.
 */
function example_configurable_log_levels() {
	// Development: Log everything.
	$dev_logger = new Logger( 'debug', true );
	$dev_logger->debug( 'This will be logged' );

	// Production: Only log errors and above.
	$prod_logger = new Logger( 'error', true );
	$prod_logger->info( 'This will NOT be logged' );
	$prod_logger->error( 'This WILL be logged' );

	// Staging: Log warnings and above.
	$staging_logger = new Logger( 'warning', true );
	$staging_logger->info( 'This will NOT be logged' );
	$staging_logger->warning( 'This WILL be logged' );
}

/**
 * Example 5: Custom Log Handler via Hook
 *
 * Add custom functionality to log events.
 */
function example_custom_log_handler() {
	// Set up a custom handler for critical errors.
	add_action(
		'avatarsteward_log',
		function ( $level, $message, $context ) {
			// Send critical errors to external monitoring service.
			if ( in_array( $level, array( 'critical', 'emergency', 'alert' ), true ) ) {
				// Example: Send to error tracking service.
				// send_to_sentry($level, $message, $context);.
			}

			// Store all errors in database for admin dashboard.
			if ( 'error' === $level ) {
				// Example: Store in custom table.
				// store_error_log($message, $context);.
			}

			// Send email notification for critical issues.
			if ( 'critical' === $level ) {
				wp_mail(
					get_option( 'admin_email' ),
					'[Avatar Steward] Critical Error',
					sprintf( '%s | %s', $message, wp_json_encode( $context ) )
				);
			}
		},
		10,
		3
	);

	// Now when you log, the custom handler will be triggered.
	$logger = new Logger();
	$logger->critical( 'Database connection lost', array( 'timestamp' => time() ) );
}

/**
 * Example 6: Debugging File Upload Flow
 *
 * Track the complete flow of an avatar upload.
 *
 * @param array $file    The uploaded file data.
 * @param int   $user_id The user ID.
 * @return bool Success status.
 */
function example_debug_upload_flow( array $file, int $user_id ) {
	$logger = new Logger();

	$logger->debug(
		'Upload flow started',
		array(
			'user_id'   => $user_id,
			'file_name' => $file['name'],
			'file_size' => $file['size'],
		)
	);

	// Validation step.
	$logger->debug( 'Validating file', array( 'step' => 'validation' ) );

	if ( $file['size'] > 2097152 ) {
		$logger->warning(
			'File size exceeds limit',
			array(
				'file_size'     => $file['size'],
				'max_file_size' => 2097152,
			)
		);
		return false;
	}

	$logger->debug( 'File validation passed', array( 'step' => 'validation' ) );

	// Upload step.
	$logger->debug( 'Starting WordPress upload', array( 'step' => 'upload' ) );

	// Simulate upload.
	$attachment_id = 789;

	$logger->info(
		'File uploaded successfully',
		array(
			'step'          => 'upload',
			'attachment_id' => $attachment_id,
		)
	);

	// Metadata generation.
	$logger->debug( 'Generating attachment metadata', array( 'step' => 'metadata' ) );

	$logger->info(
		'Upload flow completed',
		array(
			'user_id'       => $user_id,
			'attachment_id' => $attachment_id,
			'total_time_ms' => 234,
		)
	);

	return true;
}

/**
 * Example 7: Performance Monitoring
 *
 * Use logging to track performance metrics.
 */
function example_performance_monitoring() {
	$logger = new Logger();

	$start_time = microtime( true );

	// Simulate some operation.
	sleep( 1 );

	$end_time     = microtime( true );
	$duration_ms  = ( $end_time - $start_time ) * 1000;
	$memory_usage = memory_get_peak_usage( true ) / 1024 / 1024; // MB.

	if ( $duration_ms > 500 ) {
		$logger->warning(
			'Operation exceeded performance threshold',
			array(
				'operation'    => 'generate_avatar',
				'duration_ms'  => $duration_ms,
				'threshold_ms' => 500,
				'memory_mb'    => $memory_usage,
			)
		);
	} else {
		$logger->info(
			'Operation completed within performance budget',
			array(
				'operation'   => 'generate_avatar',
				'duration_ms' => $duration_ms,
				'memory_mb'   => $memory_usage,
			)
		);
	}
}

/**
 * Example 8: Migration Tracking
 *
 * Log detailed information during data migration.
 */
function example_migration_tracking() {
	$logger = new Logger();

	$logger->info( 'Starting migration from Gravatar' );

	$batch_size = 100;
	$total      = 1000;
	$migrated   = 0;
	$failed     = 0;

	for ( $i = 0; $i < $total; $i += $batch_size ) {
		$logger->debug(
			'Processing migration batch',
			array(
				'batch_start' => $i,
				'batch_end'   => min( $i + $batch_size, $total ),
			)
		);

		// Simulate batch processing.
		$batch_migrated = 95;
		$batch_failed   = 5;

		$migrated += $batch_migrated;
		$failed   += $batch_failed;

		if ( $batch_failed > 0 ) {
			$logger->warning(
				'Some users failed to migrate in batch',
				array(
					'batch_start' => $i,
					'failed'      => $batch_failed,
				)
			);
		}
	}

	$logger->info(
		'Migration completed',
		array(
			'total'        => $total,
			'migrated'     => $migrated,
			'failed'       => $failed,
			'success_rate' => ( $migrated / $total ) * 100,
		)
	);
}

/**
 * Example 9: Environment-Based Logger Configuration
 *
 * Configure logger based on WordPress environment.
 */
function example_environment_based_configuration(): Logger {
	// Detect environment.
	$is_development = defined( 'WP_DEBUG' ) && WP_DEBUG;
	$is_production  = defined( 'WP_ENV' ) && 'production' === WP_ENV;

	if ( $is_production ) {
		// Production: Only errors and above, explicitly enabled.
		return new Logger( 'error', true );
	} elseif ( $is_development ) {
		// Development: Everything, auto-detect from WP_DEBUG.
		return new Logger( 'debug' );
	} else {
		// Staging: Warnings and above.
		return new Logger( 'warning', true );
	}
}

/**
 * Example 10: Testing with Logger
 *
 * How to test code that uses logger.
 */
function example_testing_with_logger() {
	// Test 1: Service without logger (null logger).
	$service_without_logger = new ExampleAvatarService( null );
	$result                 = $service_without_logger->process_avatar( 123 );
	// No logs will be generated, but service still works.

	// Test 2: Service with enabled logger for test assertions.
	$test_logger         = new Logger( 'debug', true );
	$service_with_logger = new ExampleAvatarService( $test_logger );
	$result              = $service_with_logger->process_avatar( 123 );
	// Logs will be generated for debugging test failures.

	// Test 3: Capture logs via hook for assertions.
	$captured_logs = array();
	add_action(
		'avatarsteward_log',
		function ( $level, $message, $context ) use ( &$captured_logs ) {
			$captured_logs[] = compact( 'level', 'message', 'context' );
		},
		10,
		3
	);

	$service = new ExampleAvatarService( $test_logger );
	$service->process_avatar( 456 );

	// Assert logs contain expected entries.
	// assert(count($captured_logs) > 0);.
	// assert($captured_logs[0]['level'] === 'info');.
}
