# Avatar Migration Guide

This guide explains how to migrate existing avatars from other plugins or services to Avatar Steward.

## Overview

Avatar Steward includes a built-in migration tool that allows you to import avatars from:

- **Simple Local Avatars** - Migrate existing avatar associations
- **WP User Avatar** - Migrate existing avatar associations
- **Gravatar** - Download and import Gravatars for all users

## Accessing the Migration Tool

1. Log in to WordPress admin
2. Navigate to **Tools > Avatar Migration**
3. Review the current statistics before starting
4. Select a migration source
5. Click "Start Migration"

## Before You Start

### Important Safety Steps

1. **Backup Your Database** - Always create a complete database backup before migration
2. **Test on Staging** - Run the migration on a staging site first to verify results
3. **Review Statistics** - Check the statistics table to see how many avatars are available for migration
4. **Disable Old Plugins** - Consider deactivating the old avatar plugin after successful migration

### System Requirements

- WordPress admin access with `manage_options` capability
- PHP 7.4 or higher
- For Gravatar import: Outbound HTTP/HTTPS access to gravatar.com
- Sufficient disk space for downloaded avatars

## Migration Sources

### Simple Local Avatars

**What it does:**
- Migrates avatar associations from the Simple Local Avatars plugin
- Converts the `simple_local_avatar` user meta to `avatar_steward_avatar`
- Avatar files remain in their current location in the media library
- No files are downloaded or moved

**When to use:**
- When migrating from Simple Local Avatars plugin
- When you want to preserve existing uploaded avatars

**Migration process:**
1. Scans all users for `simple_local_avatar` meta
2. Copies attachment ID to `avatar_steward_avatar` meta
3. Adds `avatar_steward_migrated_from` meta with value `simple_local_avatars`
4. Skips users who already have Avatar Steward avatars

### WP User Avatar

**What it does:**
- Migrates avatar associations from the WP User Avatar plugin
- Converts the `wp_user_avatar` user meta to `avatar_steward_avatar`
- Avatar files remain in their current location in the media library
- No files are downloaded or moved

**When to use:**
- When migrating from WP User Avatar plugin
- When you want to preserve existing uploaded avatars

**Migration process:**
1. Scans all users for `wp_user_avatar` meta
2. Copies attachment ID to `avatar_steward_avatar` meta
3. Adds `avatar_steward_migrated_from` meta with value `wp_user_avatar`
4. Skips users who already have Avatar Steward avatars

### Gravatar

**What it does:**
- Downloads Gravatars from gravatar.com for all users
- Saves them as local WordPress attachments
- Links them to users as Avatar Steward avatars
- Only downloads for users who have a Gravatar set

**When to use:**
- When you want to make all Gravatars local
- When you want to reduce external dependencies
- When you want to improve privacy by not loading images from gravatar.com

**Migration process:**
1. Scans all users and generates Gravatar hash from email
2. Attempts to download avatar from gravatar.com (512x512 size)
3. If Gravatar exists (not 404), saves it to WordPress uploads directory
4. Creates WordPress attachment with proper metadata
5. Links attachment to user as Avatar Steward avatar
6. Adds `avatar_steward_migrated_from` meta with value `gravatar`
7. Skips users who already have Avatar Steward avatars
8. Skips users who don't have a Gravatar

**Performance considerations:**
- May take several minutes for sites with many users
- Network-intensive (downloads images from gravatar.com)
- Disk space required: ~20-50 KB per avatar
- Recommended for sites with < 1000 users

## Understanding Migration Results

After running a migration, you'll see a results summary:

### For Simple Local Avatars and WP User Avatar

```
Migration complete! Migrated: X, Skipped: Y, Total users: Z
```

- **Migrated**: Number of avatars successfully migrated
- **Skipped**: Users without source avatars or already having Avatar Steward avatars
- **Total users**: Total number of users on the site

### For Gravatar

```
Migration complete! Imported: X, Skipped: Y, Failed: Z, Total users: W
```

- **Imported**: Number of Gravatars successfully downloaded and saved
- **Skipped**: Users without Gravatars or already having Avatar Steward avatars
- **Failed**: Download failures (network errors, invalid images, etc.)
- **Total users**: Total number of users on the site

## Current Statistics

The migration page shows statistics including:

- **Total Users**: Total number of users on your site
- **Users with Avatar Steward avatars**: Users who currently have avatars managed by Avatar Steward
- **Migrated from [Source]**: Number of avatars migrated from each source
- **Available [Source] (not yet migrated)**: Number of avatars available for migration

## What Gets Migrated

✅ **Migrated:**
- User avatar associations
- Avatar files in media library (for plugin migrations)
- Metadata indicating migration source

❌ **Not Migrated:**
- Old plugin settings (reconfigure in Avatar Steward)
- Historical logs from old plugins
- Plugin-specific custom fields
- Avatar moderation status from old plugins

## After Migration

### Verification Steps

1. **Check Avatar Display** - Visit user profiles and verify avatars appear correctly
2. **Test Avatar Upload** - Upload a new avatar to confirm functionality
3. **Review Statistics** - Ensure migration counts match expectations
4. **Check Media Library** - Verify avatar attachments are present

### Cleanup (Optional)

After successful migration and verification:

1. Keep the old plugin deactivated for 1-2 weeks
2. Monitor for any issues
3. If everything works correctly, you can delete the old plugin
4. Old user meta (e.g., `simple_local_avatar`) can be left in place or cleaned up manually

### Rollback

If migration fails or produces unexpected results:

1. Restore from your database backup
2. Or manually delete `avatar_steward_avatar` and `avatar_steward_migrated_from` user meta
3. Re-enable the old avatar plugin if necessary

## Troubleshooting

### No avatars were migrated

**Possible causes:**
- Users don't have avatars in the source plugin
- All users already have Avatar Steward avatars
- Source plugin uses different meta keys

**Solution:**
- Check the statistics to see if source avatars are detected
- Verify the old plugin is properly configured
- Manually inspect user meta in the database

### Gravatar downloads are failing

**Possible causes:**
- Network connectivity issues
- Firewall blocking outbound HTTPS
- Rate limiting from gravatar.com
- Server timeout settings too low

**Solution:**
- Check server outbound connectivity
- Review firewall rules
- Try migrating in smaller batches (requires custom code)
- Increase PHP `max_execution_time` setting

### Migration takes too long

**Possible causes:**
- Large number of users
- Slow network for Gravatar downloads
- Server performance constraints

**Solution:**
- Run migration during low-traffic periods
- Use WP-CLI for large migrations (custom script required)
- Consider staging approach: migrate in batches

### Avatars don't display after migration

**Possible causes:**
- WordPress not using Avatar Steward hook
- Theme or plugin overriding avatar display
- Incorrect attachment IDs migrated

**Solution:**
- Clear all caches (WordPress, browser, CDN)
- Check that Avatar Steward plugin is active
- Verify attachment IDs exist in media library
- Test with default WordPress theme

## Advanced Usage

### Re-running Migrations

Migrations automatically skip users who already have Avatar Steward avatars. This means you can:

- Re-run migrations safely if they fail partway through
- Run multiple migrations sequentially (e.g., first Simple Local Avatars, then Gravatar for users without)

### Migration Priority

If you have multiple avatar sources, follow this order:

1. **First**: Migrate from existing plugins (Simple Local Avatars or WP User Avatar)
2. **Second**: Import Gravatars for remaining users

This ensures you preserve uploaded avatars first before falling back to Gravatar.

## Support

If you encounter issues during migration:

1. Check this documentation for troubleshooting steps
2. Review WordPress debug logs for error messages
3. Verify system requirements are met
4. Contact support with migration results and error messages

## Technical Details

### User Meta Keys

- `avatar_steward_avatar` - Stores the attachment ID of the avatar
- `avatar_steward_migrated_from` - Stores the migration source identifier

### Migration Sources

- `simple_local_avatars` - Migrated from Simple Local Avatars
- `wp_user_avatar` - Migrated from WP User Avatar
- `gravatar` - Imported from Gravatar

### File Storage

All avatars are stored in the WordPress uploads directory following standard WordPress media practices:
- Path: `wp-content/uploads/YYYY/MM/avatar-{user-id}-{hash}.jpg`
- Integrated with WordPress media library
- Standard WordPress image sizes generated automatically

## Best Practices

1. **Always backup** before any migration
2. **Test on staging** before production
3. **Run during maintenance window** to avoid user confusion
4. **Monitor results** and verify avatar display
5. **Keep old plugin inactive** for a grace period before deletion
6. **Document your migration** for future reference
