<?php
/**
 * Test script to verify all services are connected
 */

// Load WordPress
require_once __DIR__ . '/wp-config.php';

// Test plugin instance
if (class_exists('AvatarSteward\\Plugin')) {
    $plugin = AvatarSteward\Plugin::instance();
    
    echo "=== AVATAR STEWARD SERVICES TEST ===\n";
    
    // Test upload services
    $renderer = $plugin->get_profile_fields_renderer();
    echo "ProfileFieldsRenderer: " . ($renderer ? "✅ CONNECTED" : "❌ NOT CONNECTED") . "\n";
    
    $upload_handler = $plugin->get_upload_handler();
    echo "UploadHandler: " . ($upload_handler ? "✅ CONNECTED" : "❌ NOT CONNECTED") . "\n";
    
    // Test avatar handler
    $avatar_handler = $plugin->get_avatar_handler();
    echo "AvatarHandler: " . ($avatar_handler ? "✅ CONNECTED" : "❌ NOT CONNECTED") . "\n";
    
    // Test initials generator
    $generator = $plugin->get_initials_generator();
    echo "Initials Generator: " . ($generator ? "✅ CONNECTED" : "❌ NOT CONNECTED") . "\n";
    
    // Test bandwidth optimizer
    $optimizer = $plugin->get_bandwidth_optimizer();
    echo "Bandwidth Optimizer: " . ($optimizer ? "✅ CONNECTED" : "❌ NOT CONNECTED") . "\n";
    
    // Test avatar generation for current user
    if ($generator && $avatar_handler) {
        try {
            $test_svg = $generator->generate('Test User', 96);
            echo "Initials Generation: " . ($test_svg ? "✅ WORKING" : "❌ FAILED") . "\n";
        } catch (Exception $e) {
            echo "Initials Generation: ❌ ERROR - " . $e->getMessage() . "\n";
        }
    }
    
    echo "=== END TEST ===\n";
    
} else {
    echo "❌ Plugin class not found\n";
}