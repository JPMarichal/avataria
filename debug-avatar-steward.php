<?php
/**
 * Debug script for Avatar Steward
 * 
 * Add this to your WordPress functions.php temporarily to debug the plugin
 */

add_action('admin_footer', function() {
    $screen = get_current_screen();
    if ($screen && ($screen->id === 'profile' || $screen->id === 'user-edit')) {
        ?>
        <script>
        console.log('=== AVATAR STEWARD DEBUG ===');
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
        
        // Check if JS is loaded
        const scripts = Array.from(document.querySelectorAll('script[src]'));
        const jsLoaded = scripts.some(script => script.src.includes('profile-avatar.js'));
        console.log('Profile JS loaded:', jsLoaded);
        
        console.log('=== END DEBUG ===');
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
        } else {
            error_log('Avatar Steward: ProfileFieldsRenderer is NULL');
        }
    } else {
        error_log('Avatar Steward: Plugin class not found');
    }
});