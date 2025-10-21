# Role-Based Restrictions and Avatar Expiration

## Overview

Avatar Steward Pro includes advanced role-based restrictions that allow administrators to configure different upload permissions, file size limits, and format restrictions for each user role. Additionally, automatic avatar expiration can be configured to remove avatars after a specified period.

## Features

### 1. Role-Based Upload Permissions

Control which user roles are allowed to upload avatars. This is useful for restricting avatar uploads to specific user types (e.g., only subscribers and authors).

**Location:** Settings > Avatar Steward > Roles & Permissions > Allowed Roles

**Default:** All roles (administrator, editor, author, contributor, subscriber) are allowed by default.

### 2. Role-Based File Size Limits (Pro)

Set different maximum file size limits for each user role. For example, administrators might be allowed to upload 5MB files while subscribers are limited to 1MB.

**Location:** Settings > Avatar Steward > Pro Features > Role-Based File Size Limits

**Default:** If not specified, the global max file size setting (2MB) applies.

**Configuration Example:**
- Administrator: 5.0 MB
- Editor: 3.0 MB
- Author: 2.0 MB
- Contributor: 1.5 MB
- Subscriber: 1.0 MB

### 3. Role-Based Format Restrictions (Pro)

Control which image formats each user role can upload. For example, administrators might be able to upload all formats (JPEG, PNG, GIF, WebP) while subscribers are limited to JPEG only.

**Location:** Settings > Avatar Steward > Pro Features > Role-Based Format Restrictions

**Default:** If not specified, the global allowed formats setting applies (JPEG and PNG).

**Configuration Example:**
- Administrator: JPEG, PNG, GIF, WebP
- Editor: JPEG, PNG, WebP
- Author: JPEG, PNG
- Contributor: JPEG, PNG
- Subscriber: JPEG only

### 4. Avatar Expiration (Pro)

Automatically remove avatars after a specified number of days. This is useful for maintaining data hygiene and ensuring avatars don't become outdated.

**Location:** Settings > Avatar Steward > Pro Features > Enable Avatar Expiration

**Settings:**
- **Enable Avatar Expiration:** Toggle to enable/disable automatic expiration
- **Avatar Expiration Days:** Number of days (1-3650) after which avatars will be removed

**Default:** Disabled (365 days if enabled)

## Technical Implementation

### UploadService Methods

#### `can_user_upload(int $user_id): bool`

Checks if a user's role is allowed to upload avatars based on the configured allowed roles.

```php
$upload_service = new UploadService();
if ($upload_service->can_user_upload($user_id)) {
    // User can upload
}
```

#### `validate_file(array $file, ?int $user_id = null): array`

Validates an uploaded file. When a user ID is provided, role-based restrictions are applied.

```php
$upload_service = new UploadService();
$result = $upload_service->validate_file($_FILES['avatar'], $user_id);
if ($result['success']) {
    // File is valid
} else {
    // Show error: $result['message']
}
```

#### `process_upload(array $file, int $user_id): array`

Processes and stores an uploaded avatar. Automatically checks user permissions and applies role-based restrictions.

```php
$upload_service = new UploadService();
$result = $upload_service->process_upload($_FILES['avatar'], $user_id);
if ($result['success']) {
    $attachment_id = $result['attachment_id'];
    // Avatar uploaded successfully
}
```

### Settings API

All role-based restriction settings are stored in the `avatar_steward_options` option using the WordPress Settings API.

**Option Structure:**
```php
array(
    'allowed_roles' => array('administrator', 'editor', 'author'),
    'role_file_size_limits' => array(
        'administrator' => 5.0,
        'subscriber' => 1.0,
    ),
    'role_format_restrictions' => array(
        'administrator' => array('image/jpeg', 'image/png', 'image/gif', 'image/webp'),
        'subscriber' => array('image/jpeg'),
    ),
    'avatar_expiration_enabled' => true,
    'avatar_expiration_days' => 365,
)
```

### Security and Validation

All settings are validated and sanitized through the `SettingsPage::sanitize_settings()` method:

1. **Role Validation:** Only valid WordPress roles are accepted
2. **Size Limits:** File sizes are clamped to 0.1-10 MB
3. **Format Validation:** Only valid image MIME types are accepted
4. **Expiration Days:** Values are clamped to 1-3650 days

### Pro License Check

Pro features (role-based file size limits, format restrictions, and expiration) are only available when a valid Pro license is active. The settings are:

1. Only displayed in the admin UI when `LicenseManager::is_pro_active()` returns true
2. Only sanitized and saved when Pro is active
3. Ignored during upload validation if Pro is not active

## Use Cases

### Use Case 1: Restricting Avatar Uploads by Role

**Scenario:** A membership site wants only paying subscribers to have custom avatars.

**Configuration:**
1. Navigate to Settings > Avatar Steward > Roles & Permissions
2. Uncheck all roles except "subscriber"
3. Save settings

**Result:** Only users with the subscriber role can upload avatars. Other users will see an error message: "You do not have permission to upload avatars."

### Use Case 2: Different Size Limits by Role

**Scenario:** A community site wants to allow administrators to upload high-quality avatars while limiting regular users to smaller files to save storage space.

**Configuration:**
1. Activate Avatar Steward Pro license
2. Navigate to Settings > Avatar Steward > Pro Features
3. Set role-based file size limits:
   - Administrator: 5.0 MB
   - Subscriber: 1.0 MB
4. Save settings

**Result:** When a subscriber tries to upload a 2MB file, they'll see an error: "File size exceeds the maximum allowed size of 1.00 MB."

### Use Case 3: Format Restrictions for Security

**Scenario:** A corporate site wants to limit regular users to JPEG only to prevent potential security issues with other formats.

**Configuration:**
1. Activate Avatar Steward Pro license
2. Navigate to Settings > Avatar Steward > Pro Features
3. Set role-based format restrictions:
   - Administrator: All formats
   - Editor: JPEG, PNG, WebP
   - Subscriber: JPEG only
4. Save settings

**Result:** When a subscriber tries to upload a PNG file, they'll see an error: "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed."

### Use Case 4: Automatic Avatar Expiration

**Scenario:** A job board wants to ensure user avatars are kept current and automatically remove avatars older than 6 months.

**Configuration:**
1. Activate Avatar Steward Pro license
2. Navigate to Settings > Avatar Steward > Pro Features
3. Check "Enable Avatar Expiration"
4. Set "Avatar Expiration Days" to 180 (6 months)
5. Save settings

**Result:** Avatars older than 180 days will be automatically removed. Users will need to re-upload their avatars to maintain a custom avatar.

## Best Practices

1. **Start with Default Settings:** Begin with allowing all roles and adjust based on your needs
2. **Test Restrictions:** Test upload restrictions with test user accounts before applying to production
3. **Communicate Changes:** Inform users about upload restrictions and requirements
4. **Set Reasonable Limits:** Balance storage concerns with user experience
5. **Monitor Storage:** Keep track of avatar storage usage to adjust limits as needed
6. **Regular Audits:** Use expiration settings to maintain data hygiene

## Troubleshooting

### Users Can't Upload Avatars

**Check:**
1. Is the user's role in the "Allowed Roles" list?
2. Is the file size within the role's limit?
3. Is the file format allowed for the role?

### Pro Features Not Visible

**Check:**
1. Is Avatar Steward Pro license activated?
2. Navigate to Settings > Avatar Steward > License to verify activation status

### Restrictions Not Being Applied

**Check:**
1. Settings have been saved
2. Clear any caching plugins
3. Test with a fresh user session

## Filters and Hooks

### Filter: `avatar_steward_can_use_pro_feature`

Allows programmatic control over Pro feature availability.

```php
add_filter('avatar_steward_can_use_pro_feature', function($can_use, $feature_name) {
    // Custom logic
    return $can_use;
}, 10, 2);
```

## Database Schema

Role-based restrictions are stored in the WordPress options table:

- **Option Name:** `avatar_steward_options`
- **Autoload:** No (loaded only when needed)
- **Format:** Serialized PHP array

## Performance Considerations

1. **User Role Checks:** Performed once per upload request
2. **Settings Cache:** WordPress options are automatically cached
3. **Minimal Database Queries:** No additional queries for restriction checks

## Migration Notes

When upgrading from Avatar Steward Free to Pro:
- Existing settings are preserved
- New Pro fields are added with default values
- No data migration is required

## Security Considerations

1. **Capability Checks:** All settings require `manage_options` capability
2. **Input Validation:** All inputs are sanitized before storage
3. **MIME Type Detection:** Uses PHP's `finfo` for accurate file type detection
4. **Role Validation:** Only valid WordPress roles are accepted

## Future Enhancements

Planned features for future versions:
- Per-role image dimension limits
- Custom error messages per role
- Scheduled expiration with notifications
- Avatar approval workflows per role
- Usage statistics per role
