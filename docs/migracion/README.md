# Avatar Migration Documentation

This directory contains documentation for migrating avatars to Avatar Steward from other plugins and services.

## Available Documentation

- **[migration-guide.md](migration-guide.md)** - Complete guide for migrating avatars from Gravatar, Simple Local Avatars, and WP User Avatar

## Quick Start

1. **Backup your database** before starting any migration
2. Navigate to **Tools > Avatar Migration** in WordPress admin
3. Review current statistics
4. Select migration source
5. Click "Start Migration"
6. Verify results

## Migration Sources Supported

- **Simple Local Avatars** - Migrate existing uploaded avatars
- **WP User Avatar** - Migrate existing uploaded avatars  
- **Gravatar** - Download and import Gravatars

## Safety

⚠️ **Always test on a staging site first!**

Migrations are designed to be safe and non-destructive:
- Existing avatars remain in media library
- Original plugin data is not deleted
- Users with Avatar Steward avatars are skipped
- Can be re-run safely if migration fails

## Support

For migration issues:
1. Check the [migration-guide.md](migration-guide.md) troubleshooting section
2. Review WordPress debug logs
3. Contact support with migration results

## Technical Details

See [migration-guide.md](migration-guide.md) for:
- User meta keys used
- Migration process details
- File storage information
- Advanced usage scenarios
