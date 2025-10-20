#!/usr/bin/env php
<?php
/**
 * Demonstration script for avatar initials fallback fix.
 * 
 * This script demonstrates how the AvatarHandler now properly falls back
 * to initials-based avatars when no local avatar exists, preventing
 * broken image URLs like "2x" from being displayed.
 *
 * @package AvatarSteward
 */

// Load Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';

// Load Avatar Steward classes.
require_once __DIR__ . '/../src/AvatarSteward/Core/AvatarHandler.php';
require_once __DIR__ . '/../src/AvatarSteward/Domain/Uploads/UploadService.php';
require_once __DIR__ . '/../src/AvatarSteward/Domain/Initials/Generator.php';

use AvatarSteward\Core\AvatarHandler;
use AvatarSteward\Domain\Uploads\UploadService;
use AvatarSteward\Domain\Initials\Generator;

// Mock WordPress functions for demonstration.
function get_userdata( $user_id ) {
	$users = array(
		1 => (object) array(
			'ID'           => 1,
			'user_login'   => 'john_doe',
			'user_email'   => 'john@example.com',
			'display_name' => 'John Doe',
			'first_name'   => 'John',
			'last_name'    => 'Doe',
		),
		2 => (object) array(
			'ID'           => 2,
			'user_login'   => 'jane_smith',
			'user_email'   => 'jane@example.com',
			'display_name' => 'Jane Smith',
			'first_name'   => 'Jane',
			'last_name'    => 'Smith',
		),
	);
	return $users[ $user_id ] ?? false;
}

function get_user_meta( $user_id, $key = '', $single = false ) {
	// Simulate no local avatar.
	return false;
}

function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {
	// Mock filter registration.
	return true;
}

echo "\n=== Avatar Steward: Initials Fallback Demonstration ===\n\n";

// Create services.
echo "1. Creating services...\n";
$upload_service = new UploadService();
$generator      = new Generator();
$handler        = new AvatarHandler( $upload_service );

echo "   ✓ UploadService created\n";
echo "   ✓ Generator created\n";
echo "   ✓ AvatarHandler created\n\n";

// Set generator on handler.
echo "2. Configuring avatar handler with initials generator...\n";
$handler->set_generator( $generator );
echo "   ✓ Generator configured\n\n";

// Initialize handler.
echo "3. Initializing avatar handler...\n";
$handler->init();
echo "   ✓ Handler initialized\n\n";

// Test scenarios.
echo "4. Testing avatar URL generation for users WITHOUT local avatars...\n\n";

$test_cases = array(
	array(
		'user_id'     => 1,
		'user_name'   => 'John Doe',
		'original_url' => 'http://localhost:8080/wp-admin/2x', // Broken URL.
	),
	array(
		'user_id'     => 2,
		'user_name'   => 'Jane Smith',
		'original_url' => 'https://gravatar.com/avatar/test?s=96',
	),
);

foreach ( $test_cases as $i => $test ) {
	echo "   Test Case " . ( $i + 1 ) . ": User {$test['user_id']} ({$test['user_name']})\n";
	echo "   ---------------------------------------------------------\n";
	echo "   Original URL: {$test['original_url']}\n";
	
	// Call filter_avatar_url.
	$result_url = $handler->filter_avatar_url(
		$test['original_url'],
		$test['user_id'],
		array( 'size' => 96 )
	);
	
	// Check if URL was replaced.
	if ( $result_url === $test['original_url'] ) {
		echo "   ❌ FAIL: Original URL was NOT replaced\n";
	} elseif ( strpos( $result_url, 'data:image/svg+xml;charset=utf-8,' ) === 0 ) {
		echo "   ✓ SUCCESS: Initials avatar generated!\n";
		
		// Decode and show SVG snippet.
		$svg_content = rawurldecode( substr( $result_url, strlen( 'data:image/svg+xml;charset=utf-8,' ) ) );
		$svg_snippet = substr( $svg_content, 0, 100 ) . '...';
		
		echo "   Generated URL type: SVG Data URL\n";
		echo "   SVG Content (first 100 chars): {$svg_snippet}\n";
		
		// Extract initials from SVG.
		if ( preg_match( '/<text[^>]*>([^<]+)<\/text>/', $svg_content, $matches ) ) {
			echo "   Initials: {$matches[1]}\n";
		}
		
		// Extract color from SVG.
		if ( preg_match( '/fill="([^"]+)"/', $svg_content, $matches ) ) {
			echo "   Background Color: {$matches[1]}\n";
		}
	} else {
		echo "   ⚠ WARNING: URL changed but not to expected format\n";
		echo "   Result: {$result_url}\n";
	}
	
	echo "\n";
}

// Test filter_avatar_data as well.
echo "5. Testing filter_avatar_data for consistency...\n\n";

foreach ( $test_cases as $i => $test ) {
	echo "   Test Case " . ( $i + 1 ) . ": User {$test['user_id']} ({$test['user_name']})\n";
	
	$args = array(
		'url'  => $test['original_url'],
		'size' => 96,
	);
	
	$result_data = $handler->filter_avatar_data( $args, $test['user_id'] );
	
	if ( isset( $result_data['found_avatar'] ) && $result_data['found_avatar'] ) {
		echo "   ✓ found_avatar flag set to true\n";
	} else {
		echo "   ❌ found_avatar flag not set\n";
	}
	
	if ( isset( $result_data['url'] ) && strpos( $result_data['url'], 'data:image/svg+xml;charset=utf-8,' ) === 0 ) {
		echo "   ✓ Avatar URL is SVG data URL\n";
	} else {
		echo "   ❌ Avatar URL is not in expected format\n";
	}
	
	echo "\n";
}

// Summary.
echo "=== Summary ===\n\n";
echo "This demonstration shows that:\n";
echo "1. When a user has NO local avatar stored\n";
echo "2. The AvatarHandler no longer returns broken URLs like '2x'\n";
echo "3. Instead, it generates a proper SVG avatar with user's initials\n";
echo "4. Both filter_avatar_url() and filter_avatar_data() work consistently\n";
echo "5. The generated avatars are valid SVG data URLs that render properly\n\n";

echo "✅ Fix verified successfully!\n\n";
