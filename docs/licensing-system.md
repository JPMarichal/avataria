# Avatar Steward Pro - Licensing System Documentation

## Overview

The Avatar Steward Pro licensing system provides a simple yet secure mechanism for activating and managing Pro licenses. This system follows WordPress best practices and is designed for CodeCanyon distribution.

## Architecture

The licensing system consists of two main components:

### 1. LicenseManager Service

**Location:** `src/AvatarSteward/Domain/Licensing/LicenseManager.php`

The `LicenseManager` class handles all license-related operations:

- **License Activation**: Validates and stores license keys
- **License Deactivation**: Removes license data and sets status to inactive
- **Status Checking**: Determines if a Pro license is currently active
- **Feature Gating**: Controls access to Pro features based on license status
- **Data Storage**: Uses WordPress options API for persistence

### 2. LicensePage Admin UI

**Location:** `src/AvatarSteward/Admin/LicensePage.php`

The `LicensePage` class provides the administrative interface:

- Settings page under **Settings > Avatar Steward License**
- License activation form with validation
- License status display with visual badges
- Secure deactivation workflow
- List of Pro features

## License Key Format

License keys follow a standardized format:

```
XXXX-XXXX-XXXX-XXXX
```

- Four groups of four alphanumeric characters
- Case-insensitive
- Groups separated by hyphens

**Example:** `ABCD-1234-EFGH-5678`

## Usage

### Checking License Status

```php
$plugin = \AvatarSteward\Plugin::instance();
$license_manager = $plugin->get_license_manager();

if ( $license_manager->is_pro_active() ) {
    // Pro features available
}
```

### Feature Gating

```php
$license_manager = \AvatarSteward\Plugin::instance()->get_license_manager();

if ( $license_manager->can_use_pro_feature( 'moderation' ) ) {
    // Allow access to moderation panel
}
```

### Getting License Information

```php
$license_manager = \AvatarSteward\Plugin::instance()->get_license_manager();
$info = $license_manager->get_license_info();

// Returns array with:
// - status: 'active', 'inactive', 'expired', or 'invalid'
// - key: Masked license key (if active)
// - activated_at: Activation timestamp (if active)
// - domain: Site URL where license is activated
```

## License Status Constants

The `LicenseManager` class defines the following status constants:

- `LicenseManager::STATUS_ACTIVE` - License is active and valid
- `LicenseManager::STATUS_INACTIVE` - No license activated
- `LicenseManager::STATUS_EXPIRED` - License has expired (future use)
- `LicenseManager::STATUS_INVALID` - License key is invalid

## Data Storage

License data is stored in WordPress options:

- `avatar_steward_license` - Contains license data (key, activation date, domain, user ID)
- `avatar_steward_license_status` - Current license status

## Security Features

### Input Validation

- License keys are sanitized using `sanitize_text_field()`
- Format validation ensures only valid patterns are accepted
- Empty keys are rejected with clear error messages

### Nonce Protection

All form submissions are protected with WordPress nonces:
- `avatar_steward_activate_license` - Activation form
- `avatar_steward_deactivate_license` - Deactivation form

### Capability Checks

All admin actions require `manage_options` capability:
- Only administrators can activate/deactivate licenses
- Unauthorized users receive `wp_die()` error

### Key Masking

License keys are masked in the UI for security:
- Only last 4 characters are shown
- Format: `****-****-****-XXXX`

### CSRF Protection

- `check_admin_referer()` validates form submissions
- `wp_safe_redirect()` prevents open redirects

## Admin Interface

### Accessing the License Page

Navigate to **WordPress Admin > Settings > Avatar Steward License**

### License Activation Flow

1. Enter license key in format `XXXX-XXXX-XXXX-XXXX`
2. Click "Activate License"
3. System validates format and stores data
4. Success/error message displayed
5. License status updated to "Active"

### License Deactivation Flow

1. Click "Deactivate License" button
2. Confirm action in JavaScript alert
3. License data removed from database
4. Status updated to "Inactive"
5. Pro features disabled

### Status Display

The license page displays:

- **Active Status**: Green badge with activation details
- **Inactive Status**: Gray badge with activation form
- **License Key**: Masked format (last 4 digits visible)
- **Activation Date**: Timestamp of when license was activated
- **Domain**: Site URL where license is active
- **Pro Features List**: Overview of features unlocked with Pro license

## Extending the System

### Adding Custom Validation

You can extend license validation using WordPress filters:

```php
add_filter( 'avatar_steward_can_use_pro_feature', function( $can_use, $feature_name ) {
    // Add custom validation logic here
    return $can_use;
}, 10, 2 );
```

### Future Enhancements

The current implementation is designed for easy extension:

1. **Remote Validation**: Add API calls to verify licenses against remote server
2. **Expiration Dates**: Implement time-based license expiration
3. **Site Limits**: Restrict licenses to specific domains
4. **Upgrade Paths**: Support license upgrades and renewals
5. **License Tiers**: Different feature sets per license type

## Testing

### Unit Tests

Comprehensive test coverage is provided:

**LicenseManagerTest** (17 tests):
- License activation with valid/invalid formats
- Deactivation workflow
- Status checking
- Feature gating
- Data persistence
- Key masking

**LicensePageTest** (5 tests):
- Page initialization
- Handler methods existence
- Admin UI components

### Running Tests

```bash
composer test
```

## Integration with Plugin

The licensing system is automatically initialized when the plugin boots:

```php
// src/AvatarSteward/Plugin.php
public function boot(): void {
    $this->init_license_manager();  // Initialize license manager
    $this->init_settings_page();
    $this->init_migration_page();
    $this->init_license_page();     // Initialize license admin page
    
    // ...
}
```

## Pro Features

Once a license is activated, the following Pro features become available:

1. **Avatar Library** - Predefined avatars for users to choose from
2. **Social Media Integration** - Import avatars from Twitter, Facebook
3. **Moderation Panel** - Approve/reject user-uploaded avatars
4. **Multiple Avatars** - Users can upload up to 5 avatars
5. **Advanced Restrictions** - File size and dimension limits by role
6. **Role-based Permissions** - Control who can upload avatars
7. **Auto-deletion** - Remove inactive avatars after 6 months
8. **Audit Logs** - Export access/modification logs for compliance

## API Reference

### LicenseManager Methods

#### `activate( string $license_key ): array`

Activates a license key.

**Parameters:**
- `$license_key` - License key in format XXXX-XXXX-XXXX-XXXX

**Returns:**
```php
array(
    'success' => bool,
    'message' => string,
    'status'  => string|null
)
```

#### `deactivate(): array`

Deactivates the current license.

**Returns:**
```php
array(
    'success' => bool,
    'message' => string
)
```

#### `is_pro_active(): bool`

Checks if a Pro license is currently active.

**Returns:** `true` if license is active, `false` otherwise

#### `get_license_status(): string`

Gets the current license status.

**Returns:** One of the status constants (active, inactive, expired, invalid)

#### `can_use_pro_feature( string $feature_name ): bool`

Checks if a specific Pro feature is available.

**Parameters:**
- `$feature_name` - Name of the feature to check

**Returns:** `true` if feature is available, `false` otherwise

#### `get_license_info(): array`

Gets detailed license information for display.

**Returns:**
```php
array(
    'status'       => string,
    'key'          => string|null,  // Masked
    'activated_at' => string|null,
    'domain'       => string|null
)
```

## Troubleshooting

### License Won't Activate

**Symptoms:** Error message "Invalid license key format"

**Solution:**
- Ensure license key matches format: `XXXX-XXXX-XXXX-XXXX`
- Check for typos or extra spaces
- Verify all characters are alphanumeric

### Pro Features Not Working

**Symptoms:** Pro features disabled despite active license

**Solution:**
1. Check license status in admin: Settings > Avatar Steward License
2. Verify status shows as "Active" with green badge
3. Deactivate and reactivate license if needed
4. Clear any caching plugins

### License Data Lost

**Symptoms:** License status reset to inactive after update

**Solution:**
- License data stored in WordPress options should persist
- Check database for `avatar_steward_license` and `avatar_steward_license_status` options
- If missing, reactivate license

## Best Practices

1. **Single Site Licenses**: Each license should be used on one production site
2. **Staging Sites**: Document policy for using licenses on staging/development sites
3. **Backup License Key**: Keep license key in safe location for future reference
4. **Regular Validation**: Consider implementing periodic license validation
5. **Support Documentation**: Provide clear instructions with purchase

## Compliance

### GPL Compatibility

The licensing system is designed to be GPL-compatible:
- No DRM or encryption that restricts code modification
- License keys control feature availability, not code execution
- Source code remains open and redistributable
- Users maintain full GPL freedoms

### CodeCanyon Requirements

The implementation meets CodeCanyon standards:
- Clear licensing terms and activation process
- User-friendly admin interface
- Secure data handling
- Professional error messages
- Comprehensive documentation

## Support

For issues or questions regarding the licensing system:

1. Check this documentation first
2. Review test files for usage examples
3. Examine source code comments
4. Contact plugin support with license key

---

**Version:** 1.0.0  
**Last Updated:** October 2025  
**Compatibility:** WordPress 5.8+, PHP 7.4+
