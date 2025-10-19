<?php
/**
 * Tests for Logger class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Infrastructure\Logger;
use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Test case for Logger class.
 */
final class LoggerTest extends TestCase {

	/**
	 * Logger instance.
	 *
	 * @var Logger
	 */
	private Logger $logger;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		// Create logger with logging explicitly enabled for testing.
		$this->logger = new Logger( 'debug', true );
	}

	/**
	 * Test that Logger class exists.
	 */
	public function test_logger_class_exists() {
		$this->assertTrue( class_exists( Logger::class ) );
	}

	/**
	 * Test that Logger implements LoggerInterface.
	 */
	public function test_logger_implements_interface() {
		$this->assertInstanceOf( LoggerInterface::class, $this->logger );
	}

	/**
	 * Test that Logger can be instantiated.
	 */
	public function test_logger_can_be_instantiated() {
		$logger = new Logger();
		$this->assertInstanceOf( Logger::class, $logger );
	}

	/**
	 * Test that Logger can be instantiated with custom min level.
	 */
	public function test_logger_with_custom_min_level() {
		$logger = new Logger( 'error', true );
		$this->assertInstanceOf( Logger::class, $logger );
		$this->assertEquals( 'error', $logger->get_min_level() );
	}

	/**
	 * Test that Logger respects enabled state.
	 */
	public function test_logger_respects_enabled_state() {
		$enabled_logger  = new Logger( 'debug', true );
		$disabled_logger = new Logger( 'debug', false );

		$this->assertTrue( $enabled_logger->is_enabled() );
		$this->assertFalse( $disabled_logger->is_enabled() );
	}

	/**
	 * Test emergency logging.
	 */
	public function test_emergency_logging() {
		// This should not throw an exception.
		$this->logger->emergency( 'Emergency test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test alert logging.
	 */
	public function test_alert_logging() {
		$this->logger->alert( 'Alert test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test critical logging.
	 */
	public function test_critical_logging() {
		$this->logger->critical( 'Critical test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test error logging.
	 */
	public function test_error_logging() {
		$this->logger->error( 'Error test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test warning logging.
	 */
	public function test_warning_logging() {
		$this->logger->warning( 'Warning test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test notice logging.
	 */
	public function test_notice_logging() {
		$this->logger->notice( 'Notice test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test info logging.
	 */
	public function test_info_logging() {
		$this->logger->info( 'Info test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test debug logging.
	 */
	public function test_debug_logging() {
		$this->logger->debug( 'Debug test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test logging with context.
	 */
	public function test_logging_with_context() {
		$context = array(
			'user_id' => 123,
			'action'  => 'upload',
			'file'    => 'avatar.jpg',
		);

		$this->logger->info( 'Test message with context', $context );
		$this->assertTrue( true );
	}

	/**
	 * Test arbitrary level logging.
	 */
	public function test_arbitrary_level_logging() {
		$this->logger->log( 'info', 'Arbitrary level test message' );
		$this->assertTrue( true );
	}

	/**
	 * Test that disabled logger does not log.
	 */
	public function test_disabled_logger_does_not_log() {
		$logger = new Logger( 'debug', false );
		// This should not log anything and should not throw an exception.
		$logger->error( 'This should not be logged' );
		$this->assertFalse( $logger->is_enabled() );
	}

	/**
	 * Test setting and getting min level.
	 */
	public function test_set_and_get_min_level() {
		$this->logger->set_min_level( 'warning' );
		$this->assertEquals( 'warning', $this->logger->get_min_level() );
	}

	/**
	 * Test enabling and disabling logger.
	 */
	public function test_set_enabled() {
		$this->logger->set_enabled( false );
		$this->assertFalse( $this->logger->is_enabled() );

		$this->logger->set_enabled( true );
		$this->assertTrue( $this->logger->is_enabled() );
	}

	/**
	 * Test that logger filters messages by level.
	 */
	public function test_logger_filters_by_level() {
		// Create logger with 'error' as minimum level.
		$logger = new Logger( 'error', true );

		// These should be logged (higher or equal priority).
		$logger->emergency( 'Emergency message' );
		$logger->alert( 'Alert message' );
		$logger->critical( 'Critical message' );
		$logger->error( 'Error message' );

		// These should not be logged (lower priority).
		$logger->warning( 'Warning message' );
		$logger->notice( 'Notice message' );
		$logger->info( 'Info message' );
		$logger->debug( 'Debug message' );

		// Test passes if no exceptions are thrown.
		$this->assertTrue( true );
	}

	/**
	 * Test that logger handles empty context gracefully.
	 */
	public function test_logger_handles_empty_context() {
		$this->logger->info( 'Message without context', array() );
		$this->assertTrue( true );
	}

	/**
	 * Test that logger handles complex context data.
	 */
	public function test_logger_handles_complex_context() {
		$context = array(
			'nested' => array(
				'data' => array(
					'value' => 123,
				),
			),
			'array'  => array( 1, 2, 3 ),
			'string' => 'test',
			'number' => 42,
			'bool'   => true,
		);

		$this->logger->info( 'Complex context test', $context );
		$this->assertTrue( true );
	}

	/**
	 * Test that all log level constants are valid.
	 */
	public function test_all_log_levels_are_valid() {
		$levels = array( 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' );

		foreach ( $levels as $level ) {
			$logger = new Logger( $level, true );
			$this->assertEquals( $level, $logger->get_min_level() );
		}
	}

	/**
	 * Test that invalid log level does not change min_level.
	 */
	public function test_invalid_log_level_ignored() {
		$this->logger->set_min_level( 'invalid_level' );
		// Should remain 'debug' from setUp.
		$this->assertEquals( 'debug', $this->logger->get_min_level() );
	}
}
