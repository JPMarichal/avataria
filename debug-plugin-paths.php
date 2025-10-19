<?php
/**
 * Debug Avatar Steward Plugin Paths
 * 
 * Coloca este archivo en la raíz de tu instalación de WordPress
 * y ábrelo desde el navegador para verificar las rutas del plugin.
 */

// Simular entorno WordPress básico
define('WP_DEBUG', true);
define('ABSPATH', __DIR__ . '/');

// Simular funciones de WordPress necesarias
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return trailingslashit(dirname($file));
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        $plugin_dir = dirname($file);
        $plugin_name = basename($plugin_dir);
        return "http://localhost:8080/wp-content/plugins/$plugin_name/";
    }
}

if (!function_exists('trailingslashit')) {
    function trailingslashit($string) {
        return rtrim($string, '/\\') . '/';
    }
}

echo "<h1>Debug Avatar Steward Plugin Paths</h1>";

// Simular la ruta del plugin
$plugin_file = __DIR__ . '/wp-content/plugins/avatar-steward/avatar-steward.php';

echo "<h2>Plugin File Path:</h2>";
echo "<code>$plugin_file</code><br>";

if (file_exists($plugin_file)) {
    echo "<span style='color: green;'>✓ Plugin file exists</span><br>";
    
    // Simular las constantes del plugin
    $plugin_dir = plugin_dir_path($plugin_file);
    $plugin_url = plugin_dir_url($plugin_file);
    
    echo "<h2>Plugin Directory:</h2>";
    echo "<code>$plugin_dir</code><br>";
    
    echo "<h2>Plugin URL:</h2>";
    echo "<code>$plugin_url</code><br>";
    
    // Verificar archivos de assets
    $css_file = $plugin_dir . 'assets/css/profile-avatar.css';
    $js_file = $plugin_dir . 'assets/js/profile-avatar.js';
    
    echo "<h2>Asset Files:</h2>";
    echo "CSS: <code>$css_file</code> ";
    echo file_exists($css_file) ? "<span style='color: green;'>✓ Exists</span>" : "<span style='color: red;'>✗ Missing</span>";
    echo "<br>";
    
    echo "JS: <code>$js_file</code> ";
    echo file_exists($js_file) ? "<span style='color: green;'>✓ Exists</span>" : "<span style='color: red;'>✗ Missing</span>";
    echo "<br>";
    
    // URLs que deberían generarse
    $css_url = $plugin_url . 'assets/css/profile-avatar.css';
    $js_url = $plugin_url . 'assets/js/profile-avatar.js';
    
    echo "<h2>Expected URLs:</h2>";
    echo "CSS: <a href='$css_url' target='_blank'>$css_url</a><br>";
    echo "JS: <a href='$js_url' target='_blank'>$js_url</a><br>";
    
} else {
    echo "<span style='color: red;'>✗ Plugin file does not exist</span><br>";
    
    // Listar lo que hay en wp-content/plugins/
    $plugins_dir = __DIR__ . '/wp-content/plugins/';
    if (is_dir($plugins_dir)) {
        echo "<h2>Available plugins:</h2>";
        $dirs = scandir($plugins_dir);
        foreach ($dirs as $dir) {
            if ($dir != '.' && $dir != '..' && is_dir($plugins_dir . $dir)) {
                echo "- $dir<br>";
            }
        }
    }
}

echo "<h2>Current Working Directory:</h2>";
echo "<code>" . __DIR__ . "</code><br>";

echo "<h2>PHP Include Path:</h2>";
echo "<code>" . get_include_path() . "</code><br>";