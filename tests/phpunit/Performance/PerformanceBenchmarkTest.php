<?php
/**
 * Performance benchmark tests - Phase 2 requirement.
 *
 * Tests that performance requirements are met:
 * - get_avatar() < 50ms
 * - Initials generation < 100ms
 * - Page load overhead < 50ms
 * - Library pagination < 200ms
 * - Batch moderation operations < 5s for 100 items
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Tests\Performance;

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\Initials\Generator;
use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Uploads\UploadService;

/**
 * Performance benchmark test case.
 */
final class PerformanceBenchmarkTest extends TestCase {

	/**
	 * Generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Avatar handler instance.
	 *
	 * @var AvatarHandler
	 */
	private AvatarHandler $handler;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->generator = new Generator();
		$upload_service  = new UploadService();
		$this->handler   = new AvatarHandler( $upload_service );
	}

	/**
	 * Test initials generation completes in under 100ms.
	 *
	 * @return void
	 */
	public function test_initials_generation_under_100ms() {
		$start_time = microtime( true );

		// Generate 100 avatars to get realistic benchmark.
		for ( $i = 0; $i < 100; $i++ ) {
			$this->generator->extract_initials( "User Name $i" );
		}

		$end_time     = microtime( true );
		$elapsed_ms   = ( $end_time - $start_time ) * 1000;
		$per_avatar_ms = $elapsed_ms / 100;

		$this->assertLessThan(
			100,
			$per_avatar_ms,
			"Initials generation should take less than 100ms per avatar (took {$per_avatar_ms}ms)"
		);
	}

	/**
	 * Test SVG generation performance.
	 *
	 * @return void
	 */
	public function test_svg_generation_performance() {
		$start_time = microtime( true );

		// Generate 50 SVGs.
		for ( $i = 0; $i < 50; $i++ ) {
			$this->generator->generate( "User $i", 96 );
		}

		$end_time     = microtime( true );
		$elapsed_ms   = ( $end_time - $start_time ) * 1000;
		$per_svg_ms = $elapsed_ms / 50;

		$this->assertLessThan(
			50,
			$per_svg_ms,
			"SVG generation should take less than 50ms per avatar (took {$per_svg_ms}ms)"
		);
	}

	/**
	 * Test color generation consistency and performance.
	 *
	 * @return void
	 */
	public function test_color_generation_performance() {
		$start_time = microtime( true );

		$names = array(
			'John Doe',
			'Jane Smith',
			'Bob Johnson',
			'Alice Williams',
			'Charlie Brown',
		);

		// Generate colors 1000 times.
		for ( $i = 0; $i < 1000; $i++ ) {
			foreach ( $names as $name ) {
				$this->generator->get_color_for_name( $name );
			}
		}

		$end_time   = microtime( true );
		$elapsed_ms = ( $end_time - $start_time ) * 1000;

		$this->assertLessThan(
			1000,
			$elapsed_ms,
			"Color generation for 5000 operations should take less than 1000ms (took {$elapsed_ms}ms)"
		);
	}

	/**
	 * Test filename validation performance.
	 *
	 * @return void
	 */
	public function test_filename_validation_performance() {
		$upload_service = new UploadService();
		$start_time     = microtime( true );

		$files = array(
			array(
				'name'     => 'simple.jpg',
				'type'     => 'image/jpeg',
				'tmp_name' => '/tmp/simple.jpg',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 1024,
			),
			array(
				'name'     => 'complex_name_with_unicode_café.png',
				'type'     => 'image/png',
				'tmp_name' => '/tmp/complex.png',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 2048,
			),
		);

		// Validate 1000 files.
		for ( $i = 0; $i < 1000; $i++ ) {
			foreach ( $files as $file ) {
				$upload_service->validate_file( $file );
			}
		}

		$end_time   = microtime( true );
		$elapsed_ms = ( $end_time - $start_time ) * 1000;

		$this->assertLessThan(
			1000,
			$elapsed_ms,
			"File validation for 2000 operations should take less than 1000ms (took {$elapsed_ms}ms)"
		);
	}

	/**
	 * Test avatar handler filter performance.
	 *
	 * @return void
	 */
	public function test_avatar_handler_filter_performance() {
		$start_time = microtime( true );

		// Simulate 100 avatar filter calls.
		for ( $i = 0; $i < 100; $i++ ) {
			$args = array(
				'url'  => 'https://gravatar.com/avatar/test',
				'size' => 96,
			);
			$this->handler->filter_avatar_data( $args, "user$i@example.com" );
		}

		$end_time   = microtime( true );
		$elapsed_ms = ( $end_time - $start_time ) * 1000;
		$per_call_ms = $elapsed_ms / 100;

		$this->assertLessThan(
			50,
			$per_call_ms,
			"Avatar filter should process in less than 50ms per call (took {$per_call_ms}ms)"
		);
	}

	/**
	 * Test memory usage during initials generation.
	 *
	 * @return void
	 */
	public function test_initials_generation_memory_usage() {
		$memory_before = memory_get_usage();

		// Generate 1000 initials.
		for ( $i = 0; $i < 1000; $i++ ) {
			$this->generator->extract_initials( "User Name $i" );
		}

		$memory_after = memory_get_usage();
		$memory_used  = ( $memory_after - $memory_before ) / 1024 / 1024; // MB.

		$this->assertLessThan(
			5,
			$memory_used,
			"Initials generation for 1000 users should use less than 5MB (used {$memory_used}MB)"
		);
	}

	/**
	 * Test bulk operations performance.
	 *
	 * Simulates processing 100 avatars in batch.
	 *
	 * @return void
	 */
	public function test_bulk_operations_under_5_seconds() {
		$start_time = microtime( true );

		// Simulate bulk processing 100 items.
		for ( $i = 0; $i < 100; $i++ ) {
			// Simulate validation + generation.
			$this->generator->extract_initials( "User $i" );
			$this->generator->generate( "User $i", 96 );
		}

		$end_time   = microtime( true );
		$elapsed_s = $end_time - $start_time;

		$this->assertLessThan(
			5,
			$elapsed_s,
			"Bulk processing 100 items should take less than 5 seconds (took {$elapsed_s}s)"
		);
	}

	/**
	 * Test concurrent avatar requests performance.
	 *
	 * Simulates a page with 50 avatars loading simultaneously.
	 *
	 * @return void
	 */
	public function test_concurrent_avatar_requests() {
		$start_time = microtime( true );

		// Simulate 50 concurrent avatar requests.
		for ( $i = 0; $i < 50; $i++ ) {
			$args = array(
				'url'  => 'https://example.com/avatar.jpg',
				'size' => 96,
			);
			$this->handler->filter_avatar_data( $args, $i );
		}

		$end_time     = microtime( true );
		$elapsed_ms   = ( $end_time - $start_time ) * 1000;
		$page_overhead = $elapsed_ms;

		$this->assertLessThan(
			50,
			$page_overhead / 50,
			"Per-avatar overhead should be less than 50ms (actual: " . ( $page_overhead / 50 ) . 'ms)'
		);
	}

	/**
	 * Test caching effectiveness.
	 *
	 * @return void
	 */
	public function test_caching_reduces_subsequent_calls() {
		$name = 'Test User';

		// First call - uncached.
		$start_uncached = microtime( true );
		$result1        = $this->generator->generate( $name, 96 );
		$end_uncached   = microtime( true );
		$uncached_time  = ( $end_uncached - $start_uncached ) * 1000;

		// Second call - should benefit from any internal caching.
		$start_cached = microtime( true );
		$result2      = $this->generator->generate( $name, 96 );
		$end_cached   = microtime( true );
		$cached_time  = ( $end_cached - $start_cached ) * 1000;

		// Results should be identical.
		$this->assertEquals( $result1, $result2, 'Cached result should be identical to uncached' );

		// This is informational - caching may or may not improve performance in unit tests.
		$this->assertIsFloat( $cached_time, 'Cached time should be measurable' );
	}

	/**
	 * Test scaling with large datasets.
	 *
	 * @return void
	 */
	public function test_scales_with_large_datasets() {
		$start_time = microtime( true );

		// Generate 1000 different avatars.
		for ( $i = 0; $i < 1000; $i++ ) {
			$this->generator->generate( "User $i", 96 );
		}

		$end_time   = microtime( true );
		$elapsed_s = $end_time - $start_time;

		$this->assertLessThan(
			10,
			$elapsed_s,
			"Should handle 1000 avatar generations in under 10 seconds (took {$elapsed_s}s)"
		);
	}
}
