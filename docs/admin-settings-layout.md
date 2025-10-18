# Avatar Steward Settings Page - Visual Representation

## Page Location
```
WordPress Admin Dashboard
└── Settings
    └── Avatar Steward  ← Click here
```

## Page Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│                                                                       │
│  Avatar Steward Settings                                             │
│                                                                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Upload Restrictions                                                 │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  Configure restrictions for avatar uploads.                          │
│                                                                       │
│  Max File Size (MB)                                                  │
│  [ 2.0 ]                                                             │
│  Maximum file size for avatar uploads in megabytes. Default: 2 MB.  │
│                                                                       │
│  Allowed Formats                                                     │
│  ☑ JPEG                                                              │
│  ☑ PNG                                                               │
│  ☐ GIF                                                               │
│  ☐ WebP                                                              │
│  Select which image formats are allowed for avatar uploads.          │
│                                                                       │
│  Max Width (pixels)                                                  │
│  [ 2048 ]                                                            │
│  Maximum width for avatar images in pixels. Default: 2048px.        │
│                                                                       │
│  Max Height (pixels)                                                 │
│  [ 2048 ]                                                            │
│  Maximum height for avatar images in pixels. Default: 2048px.       │
│                                                                       │
│  Convert to WebP                                                     │
│  ☐ Automatically convert uploaded images to WebP format             │
│  WebP format provides better compression and smaller file sizes.     │
│                                                                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  Roles & Permissions                                                 │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │
│  Configure which user roles can upload avatars and whether           │
│  approval is required.                                               │
│                                                                       │
│  Allowed Roles                                                       │
│  ☑ Administrator                                                     │
│  ☑ Editor                                                            │
│  ☑ Author                                                            │
│  ☑ Contributor                                                       │
│  ☑ Subscriber                                                        │
│  Select which user roles are allowed to upload avatars.              │
│                                                                       │
│  Require Approval                                                    │
│  ☐ Require approval before avatars are published                    │
│  When enabled, uploaded avatars will be held for moderation          │
│  before being displayed.                                             │
│                                                                       │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  [ Save Settings ]                                                   │
│                                                                       │
└─────────────────────────────────────────────────────────────────────┘
```

## Field Types and Validation

### Number Fields
- **Max File Size**: `<input type="number" min="0.1" max="10" step="0.1">`
- **Max Width**: `<input type="number" min="100" max="5000" step="1">`
- **Max Height**: `<input type="number" min="100" max="5000" step="1">`

### Checkbox Fields
- **Allowed Formats**: Multiple checkboxes (JPEG, PNG, GIF, WebP)
- **Allowed Roles**: Multiple checkboxes (all WordPress roles)
- **Convert to WebP**: Single checkbox
- **Require Approval**: Single checkbox

## Form Behavior

### On Save
1. Form submits to `options.php` (WordPress Settings API)
2. `sanitize_settings()` method validates and sanitizes all inputs
3. Settings saved to `avatar_steward_options` in wp_options table
4. Success message displayed: "Settings saved."
5. Page reloads with updated values

### Validation Rules

```php
// File Size: Clamped to 0.1 - 10 MB
if ($input < 0.1) return 0.1;
if ($input > 10.0) return 10.0;

// Dimensions: Clamped to 100 - 5000 pixels
if ($width < 100) return 100;
if ($width > 5000) return 5000;

// Formats: Only valid MIME types accepted
$valid = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$sanitized = array_intersect($input, $valid);

// Roles: Only valid WordPress roles accepted
$valid_roles = array_keys(wp_roles()->roles);
$sanitized = array_intersect($input, $valid_roles);

// Booleans: Cast to true/false
$sanitized = !empty($input);
```

## Access Control

**Required Capability**: `manage_options`
- Typically only Administrators have this capability
- Other roles cannot access the settings page
- Direct URL access is blocked by capability check

## Data Storage

**Option Name**: `avatar_steward_options`

**Storage Format**:
```php
array(
    'max_file_size'    => 2.0,              // float
    'allowed_formats'  => ['image/jpeg', 'image/png'],  // array
    'max_width'        => 2048,             // int
    'max_height'       => 2048,             // int
    'convert_to_webp'  => false,            // bool
    'allowed_roles'    => [                 // array
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber'
    ],
    'require_approval' => false,            // bool
)
```

## Integration with WordPress

### Hooks Used
- `admin_menu`: Register settings page
- `admin_init`: Register settings, sections, and fields

### WordPress Functions Used
- `add_options_page()`: Add page to Settings menu
- `register_setting()`: Register option group
- `add_settings_section()`: Add section to page
- `add_settings_field()`: Add field to section
- `settings_fields()`: Output nonce and option group
- `do_settings_sections()`: Render sections and fields
- `submit_button()`: Output save button
- `get_option()`: Retrieve settings
- `wp_parse_args()`: Merge with defaults

### Security Features
- Nonce verification (automatic via WordPress Settings API)
- Capability checks (`manage_options`)
- Output escaping (`esc_html`, `esc_attr`)
- Input sanitization (custom `sanitize_settings()`)
- SQL injection prevention (WordPress handles option storage)

## Example Usage in Code

### Get Settings
```php
$plugin = \AvatarSteward\Plugin::instance();
$settings_page = $plugin->get_settings_page();
$settings = $settings_page->get_settings();

// Check max file size
if ($file_size > $settings['max_file_size'] * 1024 * 1024) {
    // File too large
}

// Check allowed format
if (!in_array($mime_type, $settings['allowed_formats'])) {
    // Format not allowed
}

// Check if user role is allowed
if (!in_array($user_role, $settings['allowed_roles'])) {
    // User cannot upload
}
```

### Filter Settings
```php
// Developers can filter settings
add_filter('option_avatar_steward_options', function($options) {
    // Modify settings programmatically
    $options['max_file_size'] = 5.0;
    return $options;
});
```

## Accessibility Features

- Semantic HTML structure
- Proper label associations
- Descriptive help text for each field
- Logical tab order
- Screen reader friendly
- WordPress admin color schemes supported

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- IE11: Not supported (WordPress 5.9+ requirement)

## Responsive Design

The settings page uses WordPress admin styles and is responsive:
- Mobile: Stacked layout, full-width fields
- Tablet: Optimized spacing
- Desktop: Standard WordPress admin layout
