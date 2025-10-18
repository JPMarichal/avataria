# Avatar Steward

Avatar Steward is an advanced WordPress plugin that allows managing user avatars locally, with moderation options, avatar library, and social integrations. It is an evolution of the Simple Local Avatars plugin, designed for professional and high-traffic environments.

## System Requirements

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and assets
- **Permissions**: Write access to `wp-content/uploads/` for uploading avatars

## Features

### Avatar Upload Management
- **User Profile Upload**: Users can upload custom avatars directly from their WordPress profile page
- **Multiple Format Support**: Accepts JPEG, PNG, GIF, and WebP image formats
- **File Validation**: Comprehensive validation for file type, size, dimensions, and security
- **WordPress Media Integration**: Uploaded avatars are stored in the WordPress media library
- **Admin Controls**: Administrators can upload avatars for any user
- **Easy Removal**: Users can remove their avatar and revert to default at any time
- **Error Handling**: Clear, user-friendly error messages for invalid uploads

### Security Features
- MIME type detection to prevent invalid file uploads
- File size limits (2MB default, configurable)
- Dimension restrictions (2000x2000px max, configurable)
- Nonce verification for secure form submissions
- Permission checks to ensure users can only edit their own profiles

### Migration Tools
- **Simple Local Avatars Migration**: Import existing avatars from Simple Local Avatars plugin
- **WP User Avatar Migration**: Import existing avatars from WP User Avatar plugin
- **Gravatar Import**: Download and save Gravatars locally for all users
- **Batch Processing**: Migrate all users at once with detailed results
- **Safe Migration**: Skip users who already have avatars, no data loss
- **Statistics Dashboard**: View migration status and available avatars

## System Requirements

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and assets
- **Permissions**: Write access to `wp-content/uploads/` for uploading avatars

## Installation

### Option 1: Manual Installation
1. Download the plugin ZIP file from CodeCanyon.
2. Go to your WordPress admin panel > Plugins > Add New.
3. Click "Upload Plugin" and select the ZIP file.
4. Activate the plugin after installation.

### Option 2: Installation with Docker (for Development/Demo)
If you want to test the plugin in a local environment:

1. Ensure Docker Desktop is installed.
2. Clone the repository and navigate to the project root.
3. Copy `.env.example` to `.env` and adjust variables if necessary.
4. Install dependencies:
   ```bash
   composer install
   npm install
   ```
5. Start the environment:
   ```bash
   docker compose -f docker-compose.dev.yml up -d
   ```
6. Access WordPress at `http://localhost:8080` (username: admin, password: admin).

## Development & Quality Tools

This project uses several quality assurance tools to maintain code standards and reliability.

### PHP Code Standards (phpcs)

We follow WordPress Coding Standards for PHP code.

**Run linting:**
```bash
composer lint
```

**Auto-fix issues (when possible):**
```bash
vendor/bin/phpcbf
```

Configuration is in `phpcs.xml`. The linter checks:
- WordPress-Core coding standards
- WordPress documentation standards
- PHP 7.4+ compatibility
- Proper text domain usage (`avatar-steward`)
- Namespace prefixing (`AvatarSteward\` or `avatar_steward_`)

### PHP Unit Tests (PHPUnit)

Unit tests ensure code functionality and prevent regressions.

**Run tests:**
```bash
composer test
```

**Run tests with coverage report:**
```bash
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

Configuration is in `phpunit.xml.dist`. Tests are located in `tests/phpunit/`.

### JavaScript Linting (ESLint)

JavaScript code follows ESLint best practices.

**Run linting:**
```bash
npm run lint
```

**Auto-fix issues (when possible):**
```bash
npm run lint:fix
```

Configuration is in `.eslintrc.json`. The linter enforces:
- ES6+ syntax
- Tab indentation
- Single quotes
- Semicolons
- No trailing spaces

### Test Reports

Test results and linting reports are saved in:
- `docs/reports/linting/` - PHP linting reports
- `docs/reports/tests/` - Test execution reports and coverage

### Pre-commit Checklist

Before committing code, ensure:
1. ✅ `composer lint` passes without errors
2. ✅ `composer test` passes all tests
3. ✅ `npm run lint` passes without errors (if JS changes)
4. ✅ Code is properly documented
5. ✅ New features have corresponding tests

## Configuration

After activating the plugin:

1. Go to **Settings > Avatar Steward** in the admin panel.
2. Configure upload restrictions:
   - **Max File Size**: Set the maximum file size for avatar uploads (0.1 - 10 MB, default: 2 MB)
   - **Allowed Formats**: Select which image formats are allowed (JPEG, PNG, GIF, WebP)
   - **Max Dimensions**: Set maximum width and height for avatars (100 - 5000px, default: 2048px)
   - **Convert to WebP**: Enable automatic conversion to WebP format for better compression
3. Configure roles & permissions:
   - **Allowed Roles**: Select which user roles can upload avatars
   - **Require Approval**: Enable moderation queue for new avatar uploads
4. For Pro version: Enter your license key in the "License" tab.
5. Optional: Configure social integrations in the "Social" tab.

## Migrating from Other Plugins

If you're switching from another avatar plugin or want to import Gravatars:

1. **Backup your database** before starting any migration
2. Go to **Tools > Avatar Migration** in the admin panel
3. Review the statistics showing available avatars for migration
4. Select a migration source:
   - **Simple Local Avatars**: Migrate existing avatar associations (no file downloads)
   - **WP User Avatar**: Migrate existing avatar associations (no file downloads)
   - **Gravatar**: Download and save all user Gravatars locally (may take time)
5. Click "Start Migration" and review the results
6. Verify avatars display correctly on user profiles and throughout the site

For detailed migration instructions, see [docs/migracion/migration-guide.md](docs/migracion/migration-guide.md)

## Basic Usage

### Avatar Upload
- Users can upload avatars from their profile (Users > Your Profile).
- File size and format restrictions are enforced based on settings.
- Administrators can moderate avatars in **Avatar Steward > Moderation**.

### Settings API
- Settings are stored using WordPress Options API
- All inputs are validated and sanitized before saving
- Default values are provided for all settings
- Settings can be retrieved programmatically:

```php
$settings_page = \AvatarSteward\Plugin::instance()->get_settings_page();
$settings = $settings_page->get_settings();
$max_file_size = $settings['max_file_size']; // 2.0 MB
```

### Initials Generator
- If no avatar is uploaded, the plugin automatically generates an avatar with the user's initials.
- Customize colors and styles in settings.

### Avatar Library (Pro Version)
- Access a curated collection of pre-designed avatars.
- Assign default avatars to new users.

### Moderation
- Review and approve/reject uploaded avatars.
- Export audit reports.

## Support

For technical support, refer to the documentation in `docs/` or contact through the channels specified in `docs/support.md`.

## License

This plugin is licensed under GPL v2 or later. Refer to `LICENSE.txt` for more details.

## Changelog

Refer to `CHANGELOG.md` for version history and changes.
