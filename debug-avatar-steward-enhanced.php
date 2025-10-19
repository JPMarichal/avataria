<?php
/**
 * Enhanced Debug script for Avatar Steward
 * 
 * Add this to your WordPress functions.php temporarily to debug the plugin
 */

add_action('admin_footer', function() {
    $screen = get_current_screen();
    if ($screen && ($screen->id === 'profile' || $screen->id === 'user-edit')) {
        ?>
        <script>
        console.log('=== AVATAR STEWARD ENHANCED DEBUG ===');
        console.log('Screen ID:', '<?php echo $screen->id; ?>');
        console.log('Avatar section exists:', !!document.getElementById('avatar-steward-section'));
        console.log('Profile form exists:', !!document.querySelector('.user-edit-php, .profile-php'));
        console.log('Form tables count:', document.querySelectorAll('.form-table').length);
        
        // Check if CSS is loaded
        const cssLoaded = Array.from(document.styleSheets).some(sheet => {
            try {
                return sheet.href && sheet.href.includes('profile-avatar.css');
            } catch (e) {
                return false;
            }
        });
        console.log('Profile CSS loaded:', cssLoaded);
        
        // Enhanced JS loading check
        const scripts = Array.from(document.querySelectorAll('script[src]'));
        const jsLoaded = scripts.some(script => script.src.includes('profile-avatar.js'));
        console.log('Profile JS loaded:', jsLoaded);
        
        // Debug: List all loaded scripts for troubleshooting
        console.log('All profile-related scripts:', 
            scripts.filter(s => s.src.includes('profile') || s.src.includes('avatar')).map(s => s.src)
        );
        
        // Check for JavaScript errors
        window.addEventListener('error', function(e) {
            if (e.filename && e.filename.includes('profile-avatar.js')) {
                console.error('Avatar Steward JS Error:', e.message, 'at line', e.lineno);
            }
        });
        
        // Wait a moment and check if Avatar Steward JS executed
        setTimeout(() => {
            const avatarSection = document.getElementById('avatar-steward-section');
            if (avatarSection) {
                const formTables = document.querySelectorAll('.form-table');
                let position = -1;
                
                // Find which position the avatar section is in
                Array.from(document.querySelectorAll('.form-table, #avatar-steward-section')).forEach((el, index) => {
                    if (el.id === 'avatar-steward-section') {
                        position = index;
                    }
                });
                
                console.log('Avatar section position among form elements:', position);
                console.log('Expected: should be 1 or 2 (after first form-table)');
                
                if (position > 2) {
                    console.warn('⚠️ Avatar section not repositioned - JS may not be executing');
                } else {
                    console.log('✅ Avatar section appears to be correctly positioned');
                }
            }
        }, 1000);
        
        console.log('=== END ENHANCED DEBUG ===');
        </script>
        <?php
    }
});

add_action('wp_loaded', function() {
    if (class_exists('AvatarSteward\\Plugin')) {
        $plugin = AvatarSteward\Plugin::instance();
        error_log('Avatar Steward: Plugin instance exists');
        
        $renderer = $plugin->get_profile_fields_renderer();
        if ($renderer) {
            error_log('Avatar Steward: ProfileFieldsRenderer exists');
            
            // Check if we're on the profile page and assets should be enqueued
            if (is_admin() && (strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false || strpos($_SERVER['REQUEST_URI'], 'user-edit.php') !== false)) {
                error_log('Avatar Steward: On profile page, assets should be enqueued');
            }
        } else {
            error_log('Avatar Steward: ProfileFieldsRenderer is NULL');
        }
    } else {
        error_log('Avatar Steward: Plugin class not found');
    }
});

// Debug enqueue scripts
add_action('wp_enqueue_scripts', function() {
    error_log('Avatar Steward Debug: wp_enqueue_scripts hook fired');
});

add_action('admin_enqueue_scripts', function($hook) {
    error_log('Avatar Steward Debug: admin_enqueue_scripts hook fired for: ' . $hook);
});