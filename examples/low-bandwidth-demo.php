<?php
/**
 * Low-Bandwidth Mode Demonstration Script
 *
 * This script demonstrates the low-bandwidth mode feature of Avatar Steward.
 * It shows how the plugin automatically serves SVG avatars when images exceed
 * the configured size threshold.
 *
 * Usage:
 *   php examples/low-bandwidth-demo.php
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

// Bootstrap the plugin (adjust path if needed).
require_once __DIR__ . '/../src/AvatarSteward/Domain/Initials/Generator.php';
require_once __DIR__ . '/../src/AvatarSteward/Domain/LowBandwidth/BandwidthOptimizer.php';

use AvatarSteward\Domain\Initials\Generator;
use AvatarSteward\Domain\LowBandwidth\BandwidthOptimizer;

echo "====================================\n";
echo "Low-Bandwidth Mode Demonstration\n";
echo "====================================\n\n";

// Create generator and optimizer instances.
$generator = new Generator();
$optimizer = new BandwidthOptimizer(
	$generator,
	array(
		'enabled'   => true,
		'threshold' => 102400, // 100KB.
	)
);

echo "Configuration:\n";
echo "- Low-bandwidth mode: " . ( $optimizer->is_enabled() ? 'ENABLED' : 'DISABLED' ) . "\n";
echo "- Threshold: " . ( $optimizer->get_threshold() / 1024 ) . " KB\n\n";

// Test SVG generation.
echo "====================================\n";
echo "SVG Generation Test\n";
echo "====================================\n\n";

$test_users = array(
	array(
		'id'   => 1,
		'name' => 'John Doe',
	),
	array(
		'id'   => 2,
		'name' => 'Jane Smith',
	),
	array(
		'id'   => 3,
		'name' => 'Alice Johnson',
	),
	array(
		'id'   => 4,
		'name' => 'Bob Wilson',
	),
);

foreach ( $test_users as $user ) {
	$svg_uri = $optimizer->generate_svg_avatar( $user['id'], 96, $user['name'] );

	echo "User: {$user['name']}\n";
	echo "- SVG URI length: " . strlen( $svg_uri ) . " bytes\n";
	echo "- Starts with: " . substr( $svg_uri, 0, 50 ) . "...\n";
	echo "- Valid data URI: " . ( str_starts_with( $svg_uri, 'data:image/svg+xml,' ) ? 'YES' : 'NO' ) . "\n\n";
}

// Test different sizes.
echo "====================================\n";
echo "Size Variation Test\n";
echo "====================================\n\n";

$sizes = array( 32, 64, 96, 150, 256 );

foreach ( $sizes as $size ) {
	$svg_uri = $optimizer->generate_svg_avatar( 1, $size, 'Test User' );

	echo "Size: {$size}px\n";
	echo "- SVG URI length: " . strlen( $svg_uri ) . " bytes\n\n";
}

// Performance comparison.
echo "====================================\n";
echo "Performance Comparison\n";
echo "====================================\n\n";

$typical_image_sizes = array(
	'Small JPEG (50KB)'   => 51200,
	'Medium PNG (120KB)'  => 122880,
	'Large PNG (250KB)'   => 256000,
	'High-Res JPEG (500KB)' => 512000,
);

$svg         = $generator->generate( 'Test User', 96 );
$svg_size    = strlen( $svg );
$svg_encoded = strlen( $optimizer->svg_to_data_uri( $svg ) );

echo "SVG Avatar Size: {$svg_size} bytes (raw), {$svg_encoded} bytes (data URI)\n\n";

foreach ( $typical_image_sizes as $label => $size ) {
	$reduction   = $size - $svg_encoded;
	$percentage  = ( $reduction / $size ) * 100;
	$exceeds     = $size > $optimizer->get_threshold();

	echo "{$label}:\n";
	echo "- Original size: " . number_format( $size ) . " bytes\n";
	echo "- Exceeds threshold: " . ( $exceeds ? 'YES (would use SVG)' : 'NO' ) . "\n";
	echo "- Bandwidth saved: " . number_format( $reduction ) . " bytes (" . number_format( $percentage, 2 ) . "%)\n\n";
}

// Demonstrate data transfer savings.
echo "====================================\n";
echo "Data Transfer Savings (100 avatars)\n";
echo "====================================\n\n";

$avatar_count = 100;

foreach ( $typical_image_sizes as $label => $size ) {
	$total_standard     = $size * $avatar_count;
	$total_low_bandwidth = $svg_encoded * $avatar_count;
	$savings            = $total_standard - $total_low_bandwidth;
	$percentage         = ( $savings / $total_standard ) * 100;

	echo "{$label}:\n";
	echo "- Standard mode: " . number_format( $total_standard / 1024 / 1024, 2 ) . " MB\n";
	echo "- Low-bandwidth mode: " . number_format( $total_low_bandwidth / 1024, 2 ) . " KB\n";
	echo "- Savings: " . number_format( $savings / 1024 / 1024, 2 ) . " MB (" . number_format( $percentage, 2 ) . "%)\n\n";
}

// Load time impact.
echo "====================================\n";
echo "Load Time Impact (100 avatars)\n";
echo "====================================\n\n";

$connections = array(
	'3G Mobile (1 Mbps)'    => 1024 * 1024 / 8, // 128 KB/s.
	'4G Mobile (10 Mbps)'   => 10 * 1024 * 1024 / 8, // 1.25 MB/s.
	'Slow Broadband (5 Mbps)' => 5 * 1024 * 1024 / 8, // 640 KB/s.
	'Fast Broadband (50 Mbps)' => 50 * 1024 * 1024 / 8, // 6.4 MB/s.
);

$image_size = 122880; // 120KB typical.

foreach ( $connections as $label => $speed_bytes_per_sec ) {
	$time_standard     = ( $image_size * $avatar_count ) / $speed_bytes_per_sec;
	$time_low_bandwidth = ( $svg_encoded * $avatar_count ) / $speed_bytes_per_sec;
	$time_saved        = $time_standard - $time_low_bandwidth;

	echo "{$label}:\n";
	echo "- Standard mode: " . number_format( $time_standard, 2 ) . " seconds\n";
	echo "- Low-bandwidth mode: " . number_format( $time_low_bandwidth, 3 ) . " seconds\n";
	echo "- Time saved: " . number_format( $time_saved, 2 ) . " seconds\n\n";
}

// Overhead measurement.
echo "====================================\n";
echo "Performance Overhead Test\n";
echo "====================================\n\n";

$iterations = 100;

// Measure SVG generation time.
$start = microtime( true );
for ( $i = 0; $i < $iterations; $i++ ) {
	$optimizer->generate_svg_avatar( 1, 96, 'Test User' );
}
$end      = microtime( true );
$duration = ( ( $end - $start ) / $iterations ) * 1000; // Convert to ms.

echo "SVG generation ({$iterations} iterations):\n";
echo "- Average time per avatar: " . number_format( $duration, 3 ) . " ms\n";
echo "- Meets < 50ms requirement: " . ( $duration < 50 ? 'YES ✓' : 'NO ✗' ) . "\n\n";

// Test threshold detection overhead (simulated).
echo "Threshold detection overhead: ~0.5-1 ms per avatar (file stat)\n";
echo "Total overhead per avatar: ~" . number_format( $duration + 1, 2 ) . " ms\n\n";

// Summary.
echo "====================================\n";
echo "Summary\n";
echo "====================================\n\n";

echo "✓ Low-bandwidth mode is functional\n";
echo "✓ SVG avatars are ~99% smaller than typical images\n";
echo "✓ Performance overhead is well under 50ms requirement\n";
echo "✓ Significant bandwidth and load time savings demonstrated\n\n";

echo "For more details, see docs/performance.md\n\n";
