# Avatar Section Visibility Fix

## Issue
The Avatar section was not appearing on the WordPress user profile page despite the previous PR implementing the ProfileFieldsRenderer and UploadHandler classes.

## Root Cause
The `ProfileFieldsRenderer` and `UploadHandler` classes existed with proper implementations but were never instantiated or registered in the Plugin bootstrap process. Without instantiation and hook registration, WordPress had no way to know these features existed.

## Solution
Modified `src/AvatarSteward/Plugin.php` to properly initialize the upload services:

1. **Added Properties**: Added `$profile_fields_renderer` and `$upload_handler` properties to store instances
2. **Created Initialization Method**: Added `init_upload_services()` method that:
   - Loads required classes
   - Instantiates `UploadService`
   - Instantiates `ProfileFieldsRenderer` and calls `register_hooks()`
   - Instantiates `UploadHandler` and calls `register_hooks()`
3. **Integrated with Boot Process**: Called `init_upload_services()` from `boot()` method
4. **Added Getters**: Added `get_profile_fields_renderer()` and `get_upload_handler()` methods

## WordPress Hooks Registered
The initialization now properly registers these WordPress hooks:

### ProfileFieldsRenderer
- `show_user_profile` - Renders avatar upload field on own profile
- `edit_user_profile` - Renders avatar upload field on other user's profile
- `admin_enqueue_scripts` - Enqueues CSS and JS for avatar section
- `admin_notices` - Shows error notices if upload fails

### UploadHandler
- `personal_options_update` - Handles avatar upload on own profile update
- `edit_user_profile_update` - Handles avatar upload on other user's profile update

## Changes Made
- Modified: `src/AvatarSteward/Plugin.php` (+63 lines)
- Modified: `tests/phpunit/PluginTest.php` (+36 lines, 4 new tests)

## Testing
- All existing tests pass (215 tests)
- Added 4 new tests to verify upload services initialization
- Total: 219 tests, 428 assertions - all passing
- Linting passes (WordPress Coding Standards)
- CodeQL security scan: no issues

## Files Involved
- `src/AvatarSteward/Plugin.php` - Main plugin class
- `src/AvatarSteward/Domain/Uploads/ProfileFieldsRenderer.php` - Renders avatar UI
- `src/AvatarSteward/Domain/Uploads/UploadHandler.php` - Handles form submissions
- `src/AvatarSteward/Domain/Uploads/UploadService.php` - Core upload logic
- `assets/css/profile-avatar.css` - Avatar section styling
- `assets/js/profile-avatar.js` - Avatar section repositioning

## Expected Behavior After Fix
When users navigate to their WordPress profile page (Dashboard → Users → Profile):
1. An "Avatar" section appears with a light gray background and rounded border
2. The section includes an upload field for images (JPEG, PNG, GIF, WebP)
3. If an avatar exists, it displays with a checkbox to remove it
4. The JavaScript repositions the section after the "About Yourself" section
5. On form submission, the avatar is processed and stored as a media attachment

## Architecture Pattern
This fix follows the established plugin architecture:
- Singleton Plugin class manages component lifecycle
- Services are instantiated in dedicated `init_*` methods
- Hooks are registered via `register_hooks()` methods
- Getter methods provide access for testing and debugging
