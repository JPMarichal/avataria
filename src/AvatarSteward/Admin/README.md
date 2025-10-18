# Admin Settings

This directory contains the administrative interface components for Avatar Steward.

## SettingsPage

The `SettingsPage` class implements the Avatar Steward settings page using WordPress Settings API.

### Location

The settings page is accessible from:
- **WordPress Admin**: Settings > Avatar Steward
- **Direct URL**: `/wp-admin/options-general.php?page=avatar-steward`

### Features

#### Upload Restrictions Section

Configure restrictions for avatar uploads:

- **Max File Size (MB)**: Maximum file size for avatar uploads (0.1 - 10 MB, default: 2 MB)
- **Allowed Formats**: Select which image formats are allowed (JPEG, PNG, GIF, WebP)
- **Max Width (pixels)**: Maximum width for avatar images (100 - 5000px, default: 2048px)
- **Max Height (pixels)**: Maximum height for avatar images (100 - 5000px, default: 2048px)
- **Convert to WebP**: Automatically convert uploaded images to WebP format for better compression

#### Roles & Permissions Section

Configure user access and moderation:

- **Allowed Roles**: Select which user roles can upload avatars (Administrator, Editor, Author, Contributor, Subscriber)
- **Require Approval**: When enabled, uploaded avatars will be held for moderation before being displayed

### Usage

#### Initializing the Settings Page

The settings page is automatically initialized when the plugin boots:

```php
use AvatarSteward\Plugin;

// The settings page is initialized automatically
$plugin = Plugin::instance();
$plugin->boot();

// Access the settings page instance
$settings_page = $plugin->get_settings_page();
```

#### Retrieving Settings

```php
use AvatarSteward\Admin\SettingsPage;

$settings_page = new SettingsPage();

// Get current settings (with defaults if not set)
$settings = $settings_page->get_settings();

// Access individual settings
$max_file_size = $settings['max_file_size']; // 2.0 (float)
$allowed_formats = $settings['allowed_formats']; // array('image/jpeg', 'image/png')
$max_width = $settings['max_width']; // 2048 (int)
$max_height = $settings['max_height']; // 2048 (int)
$convert_to_webp = $settings['convert_to_webp']; // false (bool)
$allowed_roles = $settings['allowed_roles']; // array('administrator', 'editor', ...)
$require_approval = $settings['require_approval']; // false (bool)
```

#### Default Settings

```php
$defaults = array(
    'max_file_size'    => 2.0,
    'allowed_formats'  => array('image/jpeg', 'image/png'),
    'max_width'        => 2048,
    'max_height'       => 2048,
    'convert_to_webp'  => false,
    'allowed_roles'    => array('administrator', 'editor', 'author', 'contributor', 'subscriber'),
    'require_approval' => false,
);
```

### Validation & Sanitization

All settings are validated and sanitized before saving:

- **max_file_size**: Clamped between 0.1 and 10.0 MB
- **allowed_formats**: Only valid MIME types (image/jpeg, image/png, image/gif, image/webp) are accepted
- **max_width**: Clamped between 100 and 5000 pixels
- **max_height**: Clamped between 100 and 5000 pixels
- **convert_to_webp**: Boolean value
- **allowed_roles**: Only valid WordPress roles are accepted
- **require_approval**: Boolean value

### Hooks

The settings page uses standard WordPress hooks:

- `admin_menu`: Registers the settings page in the admin menu
- `admin_init`: Registers settings, sections, and fields

### Capabilities

Users need the `manage_options` capability to access the settings page (typically administrators).

### Storage

Settings are stored in WordPress options table under the key: `avatar_steward_options`

### Testing

The SettingsPage class includes comprehensive PHPUnit tests covering:

- Class instantiation
- Default settings structure
- Validation of input bounds
- Sanitization of invalid values
- Boolean field handling
- Method existence verification

Run tests with:

```bash
composer test
```

### Code Standards

The code follows WordPress Coding Standards and can be validated with:

```bash
composer lint
```

### Example Filters

While the settings page provides a UI for configuration, developers can also filter settings programmatically:

```php
// Example: Modify max file size for specific conditions
add_filter('option_avatar_steward_options', function($options) {
    if (is_multisite()) {
        $options['max_file_size'] = 1.0; // Limit to 1MB on multisite
    }
    return $options;
});
```

## Future Enhancements

Planned features for future versions:

- Import/Export settings functionality
- Reset to defaults button with confirmation
- Settings validation before save with user feedback
- Contextual help sidebar with tips
- Settings search/filter functionality
- Settings change audit log
