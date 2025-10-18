<?php
/**
 * Tests for BandwidthOptimizer class.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AvatarSteward\Domain\LowBandwidth\BandwidthOptimizer;
use AvatarSteward\Domain\Initials\Generator;

/**
 * Test case for BandwidthOptimizer.
 */
final class BandwidthOptimizerTest extends TestCase {

	/**
	 * Generator instance.
	 *
	 * @var Generator
	 */
	private Generator $generator;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->generator = new Generator();
	}

	/**
	 * Test default configuration.
	 */
	public function test_default_configuration() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$this->assertFalse( $optimizer->is_enabled(), 'Should be disabled by default' );
		$this->assertEquals( 102400, $optimizer->get_threshold(), 'Default threshold should be 100KB (102400 bytes)' );
	}

	/**
	 * Test custom configuration.
	 */
	public function test_custom_configuration() {
		$config = array(
			'enabled'   => true,
			'threshold' => 204800, // 200KB.
		);

		$optimizer = new BandwidthOptimizer( $this->generator, $config );

		$this->assertTrue( $optimizer->is_enabled(), 'Should be enabled when configured' );
		$this->assertEquals( 204800, $optimizer->get_threshold(), 'Threshold should match configuration' );
	}

	/**
	 * Test enable method.
	 */
	public function test_enable() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$this->assertFalse( $optimizer->is_enabled(), 'Should start disabled' );

		$optimizer->enable();

		$this->assertTrue( $optimizer->is_enabled(), 'Should be enabled after calling enable()' );
	}

	/**
	 * Test disable method.
	 */
	public function test_disable() {
		$config = array( 'enabled' => true );
		$optimizer = new BandwidthOptimizer( $this->generator, $config );

		$this->assertTrue( $optimizer->is_enabled(), 'Should start enabled' );

		$optimizer->disable();

		$this->assertFalse( $optimizer->is_enabled(), 'Should be disabled after calling disable()' );
	}

	/**
	 * Test set_threshold method.
	 */
	public function test_set_threshold() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$optimizer->set_threshold( 51200 ); // 50KB.

		$this->assertEquals( 51200, $optimizer->get_threshold(), 'Threshold should be updated' );
	}

	/**
	 * Test set_threshold with negative value.
	 */
	public function test_set_threshold_negative_value() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$optimizer->set_threshold( -1000 );

		$this->assertEquals( 0, $optimizer->get_threshold(), 'Negative threshold should be clamped to 0' );
	}

	/**
	 * Test exceeds_threshold when disabled.
	 */
	public function test_exceeds_threshold_when_disabled() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		// Should always return false when disabled, regardless of file size.
		$this->assertFalse( $optimizer->exceeds_threshold( 1 ), 'Should return false when disabled' );
	}

	/**
	 * Test svg_to_data_uri conversion.
	 */
	public function test_svg_to_data_uri_conversion() {
		$optimizer = new BandwidthOptimizer( $this->generator );
		$svg       = '<svg xmlns="http://www.w3.org/2000/svg"><circle r="10"/></svg>';

		$data_uri = $optimizer->svg_to_data_uri( $svg );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $data_uri, 'Should start with data URI prefix' );
		$this->assertStringContainsString( 'svg', $data_uri, 'Should contain encoded SVG content' );
	}

	/**
	 * Test generate_svg_avatar with explicit name.
	 */
	public function test_generate_svg_avatar_with_name() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$data_uri = $optimizer->generate_svg_avatar( 1, 96, 'Test User' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $data_uri, 'Should return SVG data URI' );
		$this->assertStringContainsString( 'svg', $data_uri, 'Should contain SVG markup' );
	}

	/**
	 * Test generate_svg_avatar with default size.
	 */
	public function test_generate_svg_avatar_default_size() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$data_uri = $optimizer->generate_svg_avatar( 1, 0, 'Test User' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $data_uri, 'Should handle default size' );
	}

	/**
	 * Test get_performance_metrics structure.
	 */
	public function test_get_performance_metrics_structure() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$metrics = $optimizer->get_performance_metrics( 1, 96 );

		$this->assertIsArray( $metrics, 'Should return an array' );
		$this->assertArrayHasKey( 'original_size', $metrics );
		$this->assertArrayHasKey( 'svg_size', $metrics );
		$this->assertArrayHasKey( 'reduction', $metrics );
		$this->assertArrayHasKey( 'percentage', $metrics );
	}

	/**
	 * Test get_performance_metrics with invalid attachment.
	 */
	public function test_get_performance_metrics_invalid_attachment() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$metrics = $optimizer->get_performance_metrics( 99999, 96 );

		$this->assertEquals( 0, $metrics['original_size'], 'Original size should be 0 for invalid attachment' );
		$this->assertGreaterThan( 0, $metrics['svg_size'], 'SVG size should be calculated' );
	}

	/**
	 * Test SVG size is smaller than typical image.
	 */
	public function test_svg_size_advantage() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		// Generate SVG and measure size.
		$svg      = $this->generator->generate( 'Test User', 96 );
		$svg_size = strlen( $svg );

		// SVG avatars should be very small (typically < 1KB).
		$this->assertLessThan( 2048, $svg_size, 'SVG should be smaller than 2KB' );
	}

	/**
	 * Test data URI encoding preserves SVG content.
	 */
	public function test_data_uri_preserves_content() {
		$optimizer = new BandwidthOptimizer( $this->generator );
		$svg       = '<svg><text>AB</text></svg>';

		$data_uri = $optimizer->svg_to_data_uri( $svg );

		// Decode and verify content.
		$decoded = rawurldecode( str_replace( 'data:image/svg+xml,', '', $data_uri ) );
		$this->assertEquals( $svg, $decoded, 'Data URI should preserve original SVG content' );
	}

	/**
	 * Test multiple sizes generate different data URIs.
	 */
	public function test_multiple_sizes_generate_different_uris() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$uri_small  = $optimizer->generate_svg_avatar( 1, 48, 'Test' );
		$uri_medium = $optimizer->generate_svg_avatar( 1, 96, 'Test' );
		$uri_large  = $optimizer->generate_svg_avatar( 1, 256, 'Test' );

		$this->assertNotEquals( $uri_small, $uri_medium, 'Different sizes should generate different URIs' );
		$this->assertNotEquals( $uri_medium, $uri_large, 'Different sizes should generate different URIs' );
	}

	/**
	 * Test special characters in names are handled.
	 */
	public function test_special_characters_in_names() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$names = array(
			'José María',
			'François Müller',
			'<script>alert("xss")</script>',
			'User & Co.',
		);

		foreach ( $names as $name ) {
			$data_uri = $optimizer->generate_svg_avatar( 1, 96, $name );
			$this->assertStringStartsWith( 'data:image/svg+xml,', $data_uri, "Should handle name: $name" );
		}
	}

	/**
	 * Test empty name handling.
	 */
	public function test_empty_name_handling() {
		$optimizer = new BandwidthOptimizer( $this->generator );

		$data_uri = $optimizer->generate_svg_avatar( 1, 96, '' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $data_uri, 'Should handle empty name gracefully' );
	}
}
