<?php
/**
 * Debug específico para assets de perfil de Avatar Steward
 * 
 * Agrega este código al functions.php de tu tema activo en WordPress
 * o úsalo como plugin temporal para debuggear.
 */

add_action('admin_enqueue_scripts', function($hook) {
    if ('profile.php' !== $hook && 'user-edit.php' !== $hook) {
        return;
    }
    
    echo "<script>console.log('=== AVATAR STEWARD ASSET DEBUG ===');</script>";
    
    // Verificar constantes definidas
    $constants_debug = array(
        'AVATAR_STEWARD_VERSION' => defined('AVATAR_STEWARD_VERSION') ? AVATAR_STEWARD_VERSION : 'NOT DEFINED',
        'AVATAR_STEWARD_PLUGIN_FILE' => defined('AVATAR_STEWARD_PLUGIN_FILE') ? AVATAR_STEWARD_PLUGIN_FILE : 'NOT DEFINED',
        'AVATAR_STEWARD_PLUGIN_DIR' => defined('AVATAR_STEWARD_PLUGIN_DIR') ? AVATAR_STEWARD_PLUGIN_DIR : 'NOT DEFINED',
        'AVATAR_STEWARD_PLUGIN_URL' => defined('AVATAR_STEWARD_PLUGIN_URL') ? AVATAR_STEWARD_PLUGIN_URL : 'NOT DEFINED'
    );
    
    foreach ($constants_debug as $constant => $value) {
        echo "<script>console.log('$constant: $value');</script>";
    }
    
    // Verificar si la clase ProfileFieldsRenderer existe y está cargada
    if (class_exists('AvatarSteward\\Domain\\Uploads\\ProfileFieldsRenderer')) {
        echo "<script>console.log('ProfileFieldsRenderer class: LOADED');</script>";
    } else {
        echo "<script>console.log('ProfileFieldsRenderer class: NOT LOADED');</script>";
    }
    
    // Verificar archivos físicos
    if (defined('AVATAR_STEWARD_PLUGIN_DIR')) {
        $css_file = AVATAR_STEWARD_PLUGIN_DIR . 'assets/css/profile-avatar.css';
        $js_file = AVATAR_STEWARD_PLUGIN_DIR . 'assets/js/profile-avatar.js';
        
        $css_exists = file_exists($css_file) ? 'EXISTS' : 'MISSING';
        $js_exists = file_exists($js_file) ? 'EXISTS' : 'MISSING';
        
        echo "<script>console.log('CSS file ($css_file): $css_exists');</script>";
        echo "<script>console.log('JS file ($js_file): $js_exists');</script>";
        
        if (defined('AVATAR_STEWARD_PLUGIN_URL')) {
            $css_url = AVATAR_STEWARD_PLUGIN_URL . 'assets/css/profile-avatar.css';
            $js_url = AVATAR_STEWARD_PLUGIN_URL . 'assets/js/profile-avatar.js';
            
            echo "<script>console.log('Expected CSS URL: $css_url');</script>";
            echo "<script>console.log('Expected JS URL: $js_url');</script>";
        }
    }
    
    echo "<script>console.log('=== END ASSET DEBUG ===');</script>";
}, 5); // Prioridad alta para ejecutar antes que otros scripts