# Visual Identity API Documentation

This document describes the REST API endpoints for accessing Avatar Steward's visual identity system, including color palettes and style configurations.

## Table of Contents

- [Overview](#overview)
- [Authentication](#authentication)
- [Base URL](#base-url)
- [Caching](#caching)
- [Endpoints](#endpoints)
  - [Get Complete Visual Identity](#get-complete-visual-identity)
  - [Get All Palettes](#get-all-palettes)
  - [Get Specific Palette](#get-specific-palette)
  - [Get All Styles](#get-all-styles)
  - [Get Specific Style](#get-specific-style)
  - [Clear Cache](#clear-cache)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Code Examples](#code-examples)
  - [JavaScript Examples](#javascript-examples)
  - [CSS Examples](#css-examples)
  - [PHP Examples](#php-examples)
- [Extending the API](#extending-the-api)

## Overview

The Visual Identity API provides programmatic access to Avatar Steward's color palettes and visual styles. This allows plugins, themes, and integrations to maintain consistent styling across all avatar-related UI elements.

**API Version:** v1  
**Status:** Public (read-only)  
**Format:** JSON

## Authentication

All read endpoints are **public** and require no authentication. The cache clearing endpoint requires administrator privileges.

## Base URL

```
/wp-json/avatar-steward/v1/visual-identity
```

Replace with your WordPress site URL:
```
https://example.com/wp-json/avatar-steward/v1/visual-identity
```

## Caching

All responses include appropriate cache headers:
- **Cache-Control:** `public, max-age=3600` (1 hour)
- **Expires:** Set to 1 hour from request time

Cached data is stored in WordPress transients and can be cleared via the API or when plugin settings change.

## Endpoints

### Get Complete Visual Identity

Retrieve all palettes and styles in a single request.

**Endpoint:** `GET /wp-json/avatar-steward/v1/visual-identity`

**Response:**
```json
{
  "version": "1.0.0",
  "palettes": {
    "avatar_initials": {
      "name": "Avatar Initials Palette",
      "description": "Default color palette used for generating initial-based avatars",
      "colors": [
        "#1abc9c",
        "#2ecc71",
        "#3498db",
        "#9b59b6",
        "#34495e",
        "#16a085",
        "#27ae60",
        "#2980b9",
        "#8e44ad",
        "#2c3e50",
        "#f39c12",
        "#e74c3c",
        "#c0392b",
        "#d35400",
        "#7f8c8d"
      ],
      "usage": "initials_avatars"
    },
    "primary": {
      "name": "Primary Brand Colors",
      "description": "Main brand colors for UI elements",
      "colors": [
        "#0073aa",
        "#00a0d2",
        "#007cba",
        "#005177"
      ],
      "usage": "ui_elements"
    },
    "status": {
      "name": "Status Colors",
      "description": "Colors for indicating status and states",
      "colors": {
        "success": "#46b450",
        "warning": "#f0b849",
        "error": "#dc3232",
        "info": "#00a0d2",
        "neutral": "#dcdcde"
      },
      "usage": "status_indicators"
    }
  },
  "styles": {
    "avatar": {
      "name": "Avatar Styles",
      "description": "Default styling for avatars",
      "properties": {
        "border_radius": "50%",
        "min_size": 32,
        "max_size": 512,
        "default_size": 96,
        "font_family": "Arial, Helvetica, sans-serif",
        "text_color": "#ffffff"
      }
    },
    "typography": {
      "name": "Typography",
      "description": "Font settings for initials and labels",
      "properties": {
        "font_family": "Arial, Helvetica, sans-serif",
        "font_weight": "normal",
        "text_transform": "uppercase"
      }
    },
    "layout": {
      "name": "Layout Styles",
      "description": "Spacing and layout properties",
      "properties": {
        "spacing_small": "8px",
        "spacing_medium": "16px",
        "spacing_large": "24px"
      }
    }
  }
}
```

---

### Get All Palettes

Retrieve all available color palettes.

**Endpoint:** `GET /wp-json/avatar-steward/v1/visual-identity/palettes`

**Response:**
```json
{
  "avatar_initials": {
    "name": "Avatar Initials Palette",
    "description": "Default color palette used for generating initial-based avatars",
    "colors": ["#1abc9c", "#2ecc71", ...],
    "usage": "initials_avatars"
  },
  "primary": { ... },
  "status": { ... }
}
```

---

### Get Specific Palette

Retrieve a single color palette by its key.

**Endpoint:** `GET /wp-json/avatar-steward/v1/visual-identity/palettes/{palette_key}`

**Parameters:**
- `palette_key` (string, required): The palette identifier (e.g., `avatar_initials`, `primary`, `status`)

**Example Request:**
```
GET /wp-json/avatar-steward/v1/visual-identity/palettes/avatar_initials
```

**Success Response (200):**
```json
{
  "name": "Avatar Initials Palette",
  "description": "Default color palette used for generating initial-based avatars",
  "colors": [
    "#1abc9c",
    "#2ecc71",
    "#3498db",
    ...
  ],
  "usage": "initials_avatars"
}
```

**Error Response (404):**
```json
{
  "code": "avatar_steward_palette_not_found",
  "message": "Palette not found.",
  "data": {
    "status": 404
  }
}
```

---

### Get All Styles

Retrieve all visual style configurations.

**Endpoint:** `GET /wp-json/avatar-steward/v1/visual-identity/styles`

**Response:**
```json
{
  "avatar": {
    "name": "Avatar Styles",
    "description": "Default styling for avatars",
    "properties": {
      "border_radius": "50%",
      "min_size": 32,
      "max_size": 512,
      ...
    }
  },
  "typography": { ... },
  "layout": { ... }
}
```

---

### Get Specific Style

Retrieve a single style configuration by its key.

**Endpoint:** `GET /wp-json/avatar-steward/v1/visual-identity/styles/{style_key}`

**Parameters:**
- `style_key` (string, required): The style identifier (e.g., `avatar`, `typography`, `layout`)

**Example Request:**
```
GET /wp-json/avatar-steward/v1/visual-identity/styles/avatar
```

**Success Response (200):**
```json
{
  "name": "Avatar Styles",
  "description": "Default styling for avatars",
  "properties": {
    "border_radius": "50%",
    "min_size": 32,
    "max_size": 512,
    "default_size": 96,
    "font_family": "Arial, Helvetica, sans-serif",
    "text_color": "#ffffff"
  }
}
```

**Error Response (404):**
```json
{
  "code": "avatar_steward_style_not_found",
  "message": "Style not found.",
  "data": {
    "status": 404
  }
}
```

---

### Clear Cache

Clear all cached visual identity data. Requires administrator privileges.

**Endpoint:** `DELETE /wp-json/avatar-steward/v1/visual-identity/cache`

**Authentication:** Required (WordPress administrator)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cache cleared successfully."
}
```

**Error Response (403):**
```json
{
  "code": "rest_forbidden",
  "message": "Sorry, you are not allowed to do that.",
  "data": {
    "status": 403
  }
}
```

---

## Response Format

All successful responses return JSON with appropriate HTTP status codes:
- **200 OK:** Request successful
- **404 Not Found:** Resource not found
- **403 Forbidden:** Insufficient permissions
- **500 Internal Server Error:** Server error

## Error Handling

Errors follow WordPress REST API conventions:

```json
{
  "code": "error_code",
  "message": "Human-readable error message",
  "data": {
    "status": 404
  }
}
```

## Code Examples

### JavaScript Examples

#### Fetch Complete Visual Identity

```javascript
// Using Fetch API
fetch('/wp-json/avatar-steward/v1/visual-identity')
  .then(response => response.json())
  .then(data => {
    console.log('Visual Identity:', data);
    console.log('Avatar Colors:', data.palettes.avatar_initials.colors);
  })
  .catch(error => console.error('Error:', error));

// Using async/await
async function getVisualIdentity() {
  try {
    const response = await fetch('/wp-json/avatar-steward/v1/visual-identity');
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching visual identity:', error);
  }
}
```

#### Get Specific Palette

```javascript
async function getAvatarPalette() {
  const response = await fetch('/wp-json/avatar-steward/v1/visual-identity/palettes/avatar_initials');
  const palette = await response.json();
  
  // Use the colors
  palette.colors.forEach((color, index) => {
    console.log(`Color ${index}: ${color}`);
  });
  
  return palette;
}
```

#### Apply Styles Dynamically

```javascript
async function applyAvatarStyles() {
  const response = await fetch('/wp-json/avatar-steward/v1/visual-identity/styles/avatar');
  const styles = await response.json();
  
  // Apply to avatar elements
  document.querySelectorAll('.avatar').forEach(avatar => {
    avatar.style.borderRadius = styles.properties.border_radius;
    avatar.style.fontFamily = styles.properties.font_family;
  });
}
```

#### Using jQuery

```javascript
jQuery(document).ready(function($) {
  $.ajax({
    url: '/wp-json/avatar-steward/v1/visual-identity/palettes',
    method: 'GET',
    success: function(palettes) {
      console.log('Palettes:', palettes);
      
      // Generate color swatches
      $.each(palettes.avatar_initials.colors, function(index, color) {
        $('<div>')
          .css('background-color', color)
          .addClass('color-swatch')
          .appendTo('#color-palette');
      });
    },
    error: function(xhr, status, error) {
      console.error('Error:', error);
    }
  });
});
```

---

### CSS Examples

#### Generate CSS Variables

```javascript
// Fetch visual identity and create CSS custom properties
async function generateCSSVariables() {
  const response = await fetch('/wp-json/avatar-steward/v1/visual-identity');
  const data = await response.json();
  
  let cssVars = ':root {\n';
  
  // Add palette colors
  data.palettes.avatar_initials.colors.forEach((color, index) => {
    cssVars += `  --avatar-color-${index + 1}: ${color};\n`;
  });
  
  // Add status colors
  Object.entries(data.palettes.status.colors).forEach(([key, color]) => {
    cssVars += `  --status-${key}: ${color};\n`;
  });
  
  // Add avatar styles
  cssVars += `  --avatar-border-radius: ${data.styles.avatar.properties.border_radius};\n`;
  cssVars += `  --avatar-font-family: ${data.styles.avatar.properties.font_family};\n`;
  
  cssVars += '}\n';
  
  // Inject into page
  const styleElement = document.createElement('style');
  styleElement.textContent = cssVars;
  document.head.appendChild(styleElement);
}

// Call on page load
generateCSSVariables();
```

**Generated CSS Output:**
```css
:root {
  --avatar-color-1: #1abc9c;
  --avatar-color-2: #2ecc71;
  --avatar-color-3: #3498db;
  /* ... more colors ... */
  --status-success: #46b450;
  --status-warning: #f0b849;
  --status-error: #dc3232;
  --avatar-border-radius: 50%;
  --avatar-font-family: Arial, Helvetica, sans-serif;
}
```

#### Using CSS Variables

```css
/* Avatar styling using API-generated variables */
.custom-avatar {
  border-radius: var(--avatar-border-radius);
  font-family: var(--avatar-font-family);
  background-color: var(--avatar-color-1);
}

.status-badge.success {
  background-color: var(--status-success);
}

.status-badge.warning {
  background-color: var(--status-warning);
}
```

---

### PHP Examples

#### Using WordPress HTTP API

```php
<?php
// Get complete visual identity
function get_avatar_steward_visual_identity() {
    $response = wp_remote_get( home_url( '/wp-json/avatar-steward/v1/visual-identity' ) );
    
    if ( is_wp_error( $response ) ) {
        return null;
    }
    
    $body = wp_remote_retrieve_body( $response );
    return json_decode( $body, true );
}

// Usage
$visual_identity = get_avatar_steward_visual_identity();
if ( $visual_identity ) {
    $colors = $visual_identity['palettes']['avatar_initials']['colors'];
    foreach ( $colors as $color ) {
        echo '<div style="background-color: ' . esc_attr( $color ) . '"></div>';
    }
}
```

#### Get Specific Palette

```php
<?php
function get_avatar_palette( $palette_key ) {
    $url = sprintf(
        '%s/wp-json/avatar-steward/v1/visual-identity/palettes/%s',
        home_url(),
        $palette_key
    );
    
    $response = wp_remote_get( $url );
    
    if ( is_wp_error( $response ) ) {
        return null;
    }
    
    return json_decode( wp_remote_retrieve_body( $response ), true );
}

// Usage
$initials_palette = get_avatar_palette( 'avatar_initials' );
```

#### Direct Service Access (Within Plugin)

```php
<?php
use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;

// When inside the plugin, you can use the service directly
$service = new VisualIdentityService();

// Get palettes
$palettes = $service->get_palettes();

// Get specific palette
$avatar_palette = $service->get_palette( 'avatar_initials' );

// Get styles
$styles = $service->get_styles();

// Get specific style
$avatar_style = $service->get_style( 'avatar' );

// Clear cache
$service->clear_cache();
```

#### WordPress Theme Integration

```php
<?php
// In your theme's functions.php
function my_theme_enqueue_avatar_styles() {
    // Get visual identity data
    $response = wp_remote_get( home_url( '/wp-json/avatar-steward/v1/visual-identity' ) );
    
    if ( ! is_wp_error( $response ) ) {
        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        
        // Generate inline CSS
        $custom_css = ':root {';
        foreach ( $data['palettes']['avatar_initials']['colors'] as $index => $color ) {
            $custom_css .= sprintf( '--avatar-color-%d: %s;', $index + 1, $color );
        }
        $custom_css .= '}';
        
        wp_add_inline_style( 'my-theme-style', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_avatar_styles' );
```

---

## Extending the API

### Add Custom Palettes

You can add custom palettes using WordPress filters:

```php
<?php
add_filter( 'avatar_steward_palettes', function( $palettes ) {
    $palettes['custom_brand'] = array(
        'name'        => __( 'Custom Brand Colors', 'my-plugin' ),
        'description' => __( 'Custom colors for my brand', 'my-plugin' ),
        'colors'      => array(
            '#ff6b6b',
            '#4ecdc4',
            '#45b7d1',
            '#96ceb4',
        ),
        'usage'       => 'custom_branding',
    );
    
    return $palettes;
} );
```

### Add Custom Styles

```php
<?php
add_filter( 'avatar_steward_styles', function( $styles ) {
    $styles['custom_theme'] = array(
        'name'        => __( 'Custom Theme Styles', 'my-plugin' ),
        'description' => __( 'Additional styling options', 'my-plugin' ),
        'properties'  => array(
            'border_width'  => '2px',
            'border_color'  => '#000000',
            'shadow'        => '0 2px 4px rgba(0,0,0,0.1)',
        ),
    );
    
    return $styles;
} );
```

### Clear Cache Programmatically

```php
<?php
use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;

// Clear cache when settings change
add_action( 'update_option_avatar_steward_settings', function() {
    $service = new VisualIdentityService();
    $service->clear_cache();
} );
```

---

## Best Practices

1. **Cache responses** in your application to minimize API calls
2. **Use appropriate endpoints** - fetch only the data you need
3. **Handle errors gracefully** - API may be unavailable or return errors
4. **Respect cache headers** - honor the Cache-Control directives
5. **Test with different WordPress configurations** - multisite, different themes, etc.
6. **Don't hardcode URLs** - use `home_url()` or similar to build API URLs
7. **Follow WordPress REST API conventions** when extending

---

## Support

For issues or questions about the Visual Identity API:
- Check the [main plugin documentation](../README.md)
- Review [integration examples](./integrations.md)
- Submit issues on the plugin's support forum or repository

---

**Version:** 1.0.0  
**Last Updated:** October 2025  
**API Stability:** Stable
