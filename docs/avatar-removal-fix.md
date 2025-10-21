# Avatar Removal Fix Documentation

## Issue Summary

When a user removed their avatar by checking "Remove current avatar" and updating their profile, two problems occurred:
1. The avatar display showed a broken image instead of falling back to the default (initials-based) avatar
2. The attachment file remained in the Media Library, causing unnecessary storage usage

## Root Causes

1. **Broken Image**: When an avatar attachment was deleted manually from the Media Library after being set as a user's avatar, the user meta still referenced the non-existent attachment ID, causing `wp_get_attachment_image_url()` to return false and display a broken image.

2. **Orphaned Attachments**: The `delete_avatar()` method only removed the user meta reference but did not delete the actual attachment file from WordPress Media Library.

## Solution Implemented

### 1. New Setting: Delete Attachment on Remove

Added a new configurable setting in the Avatar Steward settings page:

- **Location**: Settings > Avatar Steward > Roles & Permissions
- **Field**: "Delete Attachment on Remove" 
- **Type**: Checkbox (enabled/disabled)
- **Default**: Disabled (to maintain backward compatibility)
- **Purpose**: When enabled, automatically deletes the avatar attachment from Media Library when a user removes their avatar

### 2. Enhanced UploadService

Updated `UploadService::delete_avatar()` method:

**New signature:**
```php
public function delete_avatar( int $user_id, bool $delete_attachment = false ): bool
```

**New features:**
- Accepts optional `$delete_attachment` parameter
- When `$delete_attachment` is true, calls `wp_delete_attachment()` to permanently remove the file
- Checks if attachment is used by other users before deletion (via new `is_attachment_used_by_others()` method)
- Only deletes the attachment if it's not shared with other users
- Comprehensive logging of all operations

**New private method:**
```php
private function is_attachment_used_by_others( int $attachment_id, int $exclude_user_id ): bool
```

This method queries the database to check if any other users are using the same attachment as their avatar.

### 3. Enhanced UploadHandler

Updated `UploadHandler::handle_profile_update()` to:
- Read the `delete_attachment_on_remove` setting from options
- Pass the setting value to `delete_avatar()` method when user removes their avatar

### 4. Enhanced AvatarHandler

Updated `AvatarHandler::get_local_avatar_url()` to:
- Verify that the attachment still exists using `get_post()`
- If attachment doesn't exist, clean up the orphaned user meta
- Return null to trigger fallback to initials avatar

This ensures that if an attachment is deleted (manually or automatically), the broken image is replaced with a proper fallback.

### 5. Improved Fallback Logic

The existing fallback logic in `AvatarHandler::filter_avatar_data()` now works properly:
- When `get_local_avatar_url()` returns null, it attempts to generate an initials-based avatar
- The initials generator creates an SVG avatar with the user's initials
- This provides a clean, professional appearance instead of a broken image

## Code Changes

### Modified Files

1. `src/AvatarSteward/Admin/SettingsPage.php`
   - Added `delete_attachment_on_remove` field registration
   - Added `render_delete_attachment_on_remove_field()` method
   - Updated default settings array
   - Updated sanitization to handle the new setting

2. `src/AvatarSteward/Domain/Uploads/UploadService.php`
   - Enhanced `delete_avatar()` with optional attachment deletion
   - Added `is_attachment_used_by_others()` helper method
   - Added comprehensive logging

3. `src/AvatarSteward/Domain/Uploads/UploadHandler.php`
   - Updated to read and use the `delete_attachment_on_remove` setting

4. `src/AvatarSteward/Core/AvatarHandler.php`
   - Added attachment existence verification
   - Added automatic cleanup of orphaned user meta

5. `README.md`
   - Documented the new "Delete Attachment on Remove" setting
   - Updated default settings documentation

### Test Coverage

Added new tests in:

1. `tests/phpunit/Admin/SettingsPageTest.php`
   - Test for new setting in default settings structure
   - Test for default value (false)
   - Test for sanitization of boolean value

2. `tests/phpunit/Domain/Uploads/UploadServiceTest.php`
   - Test for `delete_avatar()` with `delete_attachment` parameter set to false
   - Test for `delete_avatar()` with `delete_attachment` parameter set to true

All 222 tests pass successfully.

## Usage

### For Users

1. Navigate to **Settings > Avatar Steward**
2. Scroll to the "Roles & Permissions" section
3. Enable "Delete Attachment on Remove" if you want attachments to be automatically deleted
4. Click "Save Settings"

When a user removes their avatar:
- The user meta is always removed (avatar association is deleted)
- If the setting is enabled AND the attachment is not used by other users, the file is deleted from Media Library
- The avatar display automatically falls back to showing the user's initials

### For Developers

The setting can be accessed programmatically:

```php
$options = get_option( 'avatar_steward_options', array() );
$delete_on_remove = ! empty( $options['delete_attachment_on_remove'] );
```

## Backward Compatibility

- The default value for `delete_attachment_on_remove` is `false`, maintaining existing behavior
- Existing installations will not automatically delete attachments unless the setting is explicitly enabled
- All existing tests continue to pass without modification

## Security Considerations

- The attachment is only deleted if not used by other users (prevents accidental data loss)
- Proper capability checks ensure only authorized users can modify settings
- Nonce verification ensures CSRF protection for profile updates

## Performance Impact

- Minimal: One additional database query when removing an avatar (to check for shared usage)
- The query is optimized using WordPress's `get_users()` with `number => 1` limit
- No impact on avatar display performance

## Future Enhancements

Possible future improvements could include:
- Batch cleanup of orphaned attachments
- Admin notice when orphaned attachments are detected
- Statistics on storage saved by automatic deletion
