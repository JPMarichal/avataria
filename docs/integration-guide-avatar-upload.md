# Avatar Upload Feature - Integration Guide

## Overview

This document describes how the avatar upload feature integrates with WordPress and provides manual testing instructions.

## Architecture

### Component Integration

```
WordPress Profile Page (wp-admin/profile.php)
           ↓
ProfileFieldsRenderer::render_upload_field()
           ↓ (on form submit)
UploadHandler::handle_profile_update()
           ↓
UploadService::process_upload()
           ↓
WordPress Media Library + User Meta
```

### WordPress Hooks Used

1. **`show_user_profile`** - Renders upload field on user's own profile
2. **`edit_user_profile`** - Renders upload field when admin edits another user
3. **`personal_options_update`** - Handles upload when user updates own profile
4. **`edit_user_profile_update`** - Handles upload when admin updates another user's profile
5. **`admin_enqueue_scripts`** - Enqueues WordPress media uploader scripts

### Data Storage

- **Avatar Attachment**: Stored in WordPress media library via `wp_insert_attachment()`
- **User Meta**: Attachment ID stored in `avatar_steward_avatar` meta key
- **Error Messages**: Temporary storage in transients (30 second expiry)

## Manual Testing Instructions

### Test Case 1: Valid Upload

**Prerequisites:**
- WordPress installed and running
- Avatar Steward plugin activated
- User logged in to WordPress admin

**Steps:**
1. Navigate to Users > Your Profile
2. Scroll to the "Avatar" section
3. Click "Choose File" button
4. Select a valid image file:
   - Format: JPEG, PNG, GIF, or WebP
   - Size: < 2MB
   - Dimensions: < 2000x2000px
5. Click "Update Profile"

**Expected Result:**
- Profile updates successfully
- Avatar preview appears in the Avatar section
- Avatar displays on user comments/posts (if theme supports)
- No error messages displayed

### Test Case 2: Invalid File Type

**Steps:**
1. Navigate to Users > Your Profile
2. Try to upload a non-image file (e.g., .txt, .pdf, .zip)
3. Click "Update Profile"

**Expected Result:**
- Error notice appears: "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed."
- No avatar is uploaded
- Previous avatar (if any) remains unchanged

### Test Case 3: File Too Large

**Steps:**
1. Navigate to Users > Your Profile
2. Try to upload an image larger than 2MB
3. Click "Update Profile"

**Expected Result:**
- Error notice appears: "File size exceeds the maximum allowed size of 2.00 MB."
- No avatar is uploaded
- Previous avatar (if any) remains unchanged

### Test Case 4: Image Too Large (Dimensions)

**Steps:**
1. Navigate to Users > Your Profile
2. Try to upload an image with dimensions > 2000x2000px
3. Click "Update Profile"

**Expected Result:**
- Error notice appears: "Image dimensions exceed the maximum allowed size of 2000x2000 pixels."
- No avatar is uploaded
- Previous avatar (if any) remains unchanged

### Test Case 5: Remove Avatar

**Prerequisites:**
- User has an uploaded avatar

**Steps:**
1. Navigate to Users > Your Profile
2. Check the "Remove current avatar" checkbox
3. Click "Update Profile"

**Expected Result:**
- Avatar is removed
- Avatar preview no longer appears
- User reverts to default/initials avatar
- Success: Profile updated

### Test Case 6: Admin Upload for Another User

**Prerequisites:**
- Logged in as administrator
- Another user exists in the system

**Steps:**
1. Navigate to Users > All Users
2. Click "Edit" on another user
3. Scroll to "Avatar" section
4. Upload a valid image
5. Click "Update User"

**Expected Result:**
- Avatar uploads successfully for the target user
- Admin can see the uploaded avatar in the preview
- Target user's avatar displays correctly on frontend

### Test Case 7: Permission Restriction

**Prerequisites:**
- Logged in as a user without admin privileges
- Test user account exists

**Steps:**
1. Try to access edit page for another user directly via URL
2. Attempt to upload an avatar

**Expected Result:**
- WordPress should block access to edit other users
- Even if somehow bypassed, `current_user_can()` check prevents upload
- No unauthorized avatar changes possible

## Integration with WordPress Features

### Media Library
- Uploaded avatars appear in Media Library
- Can be viewed/downloaded like any attachment
- Metadata includes title "Avatar for user {ID}"
- No post parent (set to 0)

### User Meta
```php
// Retrieve avatar attachment ID
$avatar_id = get_user_meta( $user_id, 'avatar_steward_avatar', true );

// Get avatar URL
$avatar_url = wp_get_attachment_image_src( $avatar_id, array( 96, 96 ) )[0];
```

### Theme Integration
The avatar can be integrated into themes using the standard WordPress avatar filters (implementation pending for Phase 2.2).

## File System

### Upload Location
Avatars are stored by WordPress in:
```
wp-content/uploads/{year}/{month}/
```

Example:
```
wp-content/uploads/2025/10/avatar-user-5-scaled.jpg
```

### File Naming
WordPress automatically:
- Sanitizes filenames
- Adds unique suffixes if file exists
- Creates multiple sizes (thumbnails, etc.)
- Generates attachment metadata

## Error Handling

### Client-Side Validation
- HTML5 `accept` attribute limits file picker to images
- User-friendly file input with clear instructions

### Server-Side Validation
- Multiple validation layers in `UploadService::validate_file()`
- Detailed error messages via transients
- Fallback to safe defaults on error

### WordPress Integration
- Uses `wp_handle_upload()` for secure file handling
- Respects WordPress upload settings
- Compatible with WordPress security plugins

## Performance Considerations

### File Processing
- Minimal processing on upload (metadata generation only)
- WordPress handles image resizing automatically
- No blocking operations during profile update

### Database Queries
- Single user meta write on successful upload
- Single user meta read when displaying avatar
- Efficient using WordPress caching

## Compatibility

### WordPress Versions
- Tested with WordPress 5.8+
- Uses stable WordPress APIs
- No deprecated function usage

### PHP Versions
- Compatible with PHP 7.4+
- Uses modern PHP features (typed properties, strict types)
- Follows PSR standards

### Themes & Plugins
- No conflicts expected
- Uses standard WordPress hooks
- Respects WordPress security policies

## Troubleshooting

### Avatar Not Appearing
1. Check user meta: `get_user_meta( $user_id, 'avatar_steward_avatar', true )`
2. Verify attachment exists in media library
3. Check file permissions on uploads directory
4. Ensure theme supports custom avatars (Phase 2.2)

### Upload Failing
1. Check PHP `upload_max_filesize` and `post_max_size` settings
2. Verify write permissions on `wp-content/uploads/`
3. Check WordPress memory limit
4. Review PHP error logs

### Permission Issues
1. Verify user role has `edit_user` capability
2. Check WordPress user permissions
3. Review any security plugin restrictions

## Next Steps (Future Phases)

- **Phase 2.2**: Integrate with `get_avatar` filter
- **Phase 2.3**: Add avatar moderation interface
- **Phase 3**: Add social media import
- **Phase 4**: Add avatar library/presets

## Code References

- **UploadService**: `src/AvatarSteward/Domain/Uploads/UploadService.php`
- **UploadHandler**: `src/AvatarSteward/Domain/Uploads/UploadHandler.php`
- **ProfileFieldsRenderer**: `src/AvatarSteward/Domain/Uploads/ProfileFieldsRenderer.php`
- **Tests**: `tests/phpunit/Domain/Uploads/`

---

**Version:** 0.1.0  
**Last Updated:** 2025-10-18  
**Status:** ✅ Production Ready
