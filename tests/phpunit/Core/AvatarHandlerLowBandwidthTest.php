<?php
/**
 * Integration tests for AvatarHandler with Low-Bandwidth mode.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Initials\Generator;
use AvatarSteward\Domain\LowBandwidth\BandwidthOptimizer;

/**
 * Integration test case for AvatarHandler with BandwidthOptimizer.
 */
final class AvatarHandlerLowBandwidthTest extends TestCase {

	/**
	 * Avatar handler instance.
	 *
	 * @var AvatarHandler
	 */
	private AvatarHandler $handler;

	/**
	 * Bandwidth optimizer instance.
	 *
	 * @var BandwidthOptimizer
	 */
	private BandwidthOptimizer $optimizer;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();

		$generator       = new Generator();
		$this->optimizer = new BandwidthOptimizer(
			$generator,
			array(
				'enabled'   => true,
				'threshold' => 102400, // 100KB.
			)
		);

		$this->handler = new AvatarHandler();
		$this->handler->set_optimizer( $this->optimizer );
		$this->handler->init();
	}

	/**
	 * Test that handler can be initialized with optimizer.
	 */
	public function test_handler_accepts_optimizer() {
		$this->assertTrue( method_exists( $this->handler, 'set_optimizer' ) );
		$this->assertTrue( $this->optimizer->is_enabled() );
	}

	/**
	 * Test avatar data filtering with optimizer enabled.
	 */
	public function test_avatar_data_filtering_with_optimizer() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test that optimizer configuration is preserved.
	 */
	public function test_optimizer_configuration() {
		$this->assertEquals( 102400, $this->optimizer->get_threshold() );
		$this->assertTrue( $this->optimizer->is_enabled() );
	}

	/**
	 * Test multiple avatar sizes with optimizer.
	 */
	public function test_multiple_avatar_sizes_with_optimizer() {
		$sizes = array( 32, 64, 96, 150, 256 );

		foreach ( $sizes as $size ) {
			$args = array(
				'url'  => 'https://gravatar.com/avatar/test',
				'size' => $size,
			);

			$result = $this->handler->filter_avatar_data( $args, 1 );

			$this->assertIsArray( $result );
			$this->assertArrayHasKey( 'url', $result );
			$this->assertEquals( $size, $result['size'], "Size $size should be preserved" );
		}
	}

	/**
	 * Test avatar URL filtering with optimizer.
	 */
	public function test_avatar_url_filtering_with_optimizer() {
		$url = 'https://gravatar.com/avatar/test';

		$result = $this->handler->filter_avatar_url(
			$url,
			1,
			array( 'size' => 96 )
		);

		$this->assertIsString( $result );
	}

	/**
	 * Test optimizer with various user identifiers.
	 */
	public function test_optimizer_with_various_identifiers() {
		$identifiers = array(
			1,
			'test@example.com',
			(object) array(
				'ID'         => 5,
				'user_login' => 'testuser',
			),
		);

		foreach ( $identifiers as $identifier ) {
			$args = array(
				'url'  => 'https://gravatar.com/avatar/test',
				'size' => 96,
			);

			$result = $this->handler->filter_avatar_data( $args, $identifier );

			$this->assertIsArray( $result );
			$this->assertArrayHasKey( 'url', $result );
		}
	}

	/**
	 * Test that optimizer can be disabled dynamically.
	 */
	public function test_optimizer_dynamic_disable() {
		$this->optimizer->disable();

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertFalse( $this->optimizer->is_enabled() );
	}

	/**
	 * Test that optimizer can be re-enabled.
	 */
	public function test_optimizer_dynamic_enable() {
		$this->optimizer->disable();
		$this->assertFalse( $this->optimizer->is_enabled() );

		$this->optimizer->enable();
		$this->assertTrue( $this->optimizer->is_enabled() );

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
	}

	/**
	 * Test threshold adjustment.
	 */
	public function test_threshold_adjustment() {
		$this->optimizer->set_threshold( 204800 ); // 200KB.

		$this->assertEquals( 204800, $this->optimizer->get_threshold() );

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
	}

	/**
	 * Test that handler works without optimizer.
	 */
	public function test_handler_works_without_optimizer() {
		$handler_no_opt = new AvatarHandler();
		$handler_no_opt->init();

		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result = $handler_no_opt->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * Test SVG generation through handler.
	 */
	public function test_svg_generation_through_handler() {
		// Generate SVG avatar directly.
		$svg_uri = $this->optimizer->generate_svg_avatar( 1, 96, 'Test User' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $svg_uri );
	}

	/**
	 * Test performance metrics accessibility.
	 */
	public function test_performance_metrics_accessibility() {
		$metrics = $this->optimizer->get_performance_metrics( 1, 96 );

		$this->assertIsArray( $metrics );
		$this->assertArrayHasKey( 'original_size', $metrics );
		$this->assertArrayHasKey( 'svg_size', $metrics );
		$this->assertGreaterThan( 0, $metrics['svg_size'] );
	}

	/**
	 * Test edge case: very small avatars.
	 */
	public function test_very_small_avatars() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 16,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertEquals( 16, $result['size'] );
	}

	/**
	 * Test edge case: very large avatars.
	 */
	public function test_very_large_avatars() {
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 512,
		);

		$result = $this->handler->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result );
		$this->assertEquals( 512, $result['size'] );
	}

	/**
	 * Test that original URL is preserved when appropriate.
	 */
	public function test_original_url_preservation_with_optimizer() {
		$original_url = 'https://gravatar.com/avatar/specific123';
		$args         = array(
			'url'  => $original_url,
			'size' => 96,
		);

		$result = $this->handler->filter_avatar_data( $args, 999999 );

		// When no local avatar exists, should preserve original URL.
		$this->assertEquals( $original_url, $result['url'] );
	}

	/**
	 * Test concurrent optimizers don't interfere.
	 */
	public function test_multiple_handlers_with_different_optimizers() {
		// Create second handler with different optimizer.
		$generator2  = new Generator();
		$optimizer2  = new BandwidthOptimizer(
			$generator2,
			array(
				'enabled'   => true,
				'threshold' => 204800, // 200KB.
			)
		);
		$handler2 = new AvatarHandler();
		$handler2->set_optimizer( $optimizer2 );
		$handler2->init();

		// Test both handlers work independently.
		$args = array(
			'url'  => 'https://gravatar.com/avatar/test',
			'size' => 96,
		);

		$result1 = $this->handler->filter_avatar_data( $args, 1 );
		$result2 = $handler2->filter_avatar_data( $args, 1 );

		$this->assertIsArray( $result1 );
		$this->assertIsArray( $result2 );

		// Verify optimizers have different thresholds.
		$this->assertEquals( 102400, $this->optimizer->get_threshold() );
		$this->assertEquals( 204800, $optimizer2->get_threshold() );
	}
}
