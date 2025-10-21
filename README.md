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

### Core MVP Features

#### Avatar Upload Management
- **User Profile Upload**: Users can upload custom avatars directly from their WordPress profile page
- **Multiple Format Support**: Accepts JPEG, PNG, GIF, and WebP image formats
- **File Validation**: Comprehensive validation for file type, size, dimensions, and security
- **WordPress Media Integration**: Uploaded avatars are stored in the WordPress media library
- **Admin Controls**: Administrators can upload avatars for any user
- **Easy Removal**: Users can remove their avatar and revert to default at any time
- **Error Handling**: Clear, user-friendly error messages for invalid uploads

#### Avatar Moderation (Pro Feature)
- **Moderation Queue**: Optional approval workflow for uploaded avatars
- **Status Management**: Track avatars as pending, approved, or rejected
- **Bulk Actions**: Approve or reject multiple avatars simultaneously
- **History Tracking**: Complete audit trail of moderation decisions
- **Previous Avatar Backup**: Automatically restore previous avatar on rejection
- **Smart Display**: Only show approved avatars when moderation is enabled
- **Admin Panel**: Dedicated moderation interface with filtering and search
- **Badge Counter**: Menu badge shows pending avatar count

#### Advanced Settings Page
- **Upload Restrictions**: Configure max file size (0.1-10 MB), allowed formats, and maximum dimensions (100-5000px)
- **Format Options**: Select which image formats are allowed (JPEG, PNG, GIF, WebP)
- **WebP Conversion**: Optional automatic conversion to WebP format for better compression
- **Role-Based Access**: Control which user roles can upload avatars
- **Moderation Queue**: Optional approval requirement for new avatar uploads
- **WordPress Settings API**: Fully integrated with WordPress native settings system

#### Security Features
- MIME type detection to prevent invalid file uploads
- File size limits (2MB default, configurable via settings)
- Dimension restrictions (2048x2048px default, configurable via settings)
- Nonce verification for secure form submissions
- Permission checks to ensure users can only edit their own profiles
- Input sanitization and validation for all settings
- Capability checks for admin-only functions

### Migration Tools
- **Simple Local Avatars Migration**: Import existing avatars from Simple Local Avatars plugin
- **WP User Avatar Migration**: Import existing avatars from WP User Avatar plugin
- **Gravatar Import**: Download and save Gravatars locally for all users
- **Batch Processing**: Migrate all users at once with detailed results
- **Safe Migration**: Skip users who already have avatars, no data loss
- **Statistics Dashboard**: View migration status and available avatars

### Avatar Library (Pro Feature)
- **Centralized Library**: Manage a collection of avatars available for all users
- **Metadata Support**: Organize avatars by author, license, sector, and tags
- **Profile Integration**: Users can select avatars from the library on their profile page
- **Search and Filter**: Find avatars quickly with search and filter options
- **Sectoral Templates**: Import avatar templates organized by industry sectors
- **REST API**: Full REST API support for programmatic access and integrations
- **Bulk Import**: Upload multiple avatars at once with sectoral categorization
- **Performance Optimized**: Transient caching for fast library queries

For detailed information, see the [Avatar Library Documentation](docs/avatar-library.md).

### Social Media Integrations (Pro Feature)
- **Twitter / X Integration**: Users can import their Twitter profile picture as their avatar
- **Facebook Integration**: Users can import their Facebook profile picture as their avatar
- **OAuth 2.0 Security**: Secure authentication with industry-standard OAuth protocols
- **PKCE Support**: Enhanced security for Twitter integration using Proof Key for Code Exchange
- **Easy Connect/Disconnect**: Simple one-click connection and disconnection of social accounts
- **Privacy First**: Tokens stored securely, no automatic syncing, user controls all imports
- **Extensible Architecture**: Strategy Pattern allows easy addition of new social providers

For detailed setup instructions, see the [Social Integrations Setup Guide](docs/social-integrations.md).

### Visual Identity API (Pro Feature)
- **REST API Endpoints**: Programmatic access to color palettes and visual styles
- **Versioned API (v1)**: Stable, versioned API for long-term compatibility
- **Public Access**: Read-only endpoints available without authentication
- **Caching Support**: Built-in response caching for optimal performance
- **Color Palettes**: Access to avatar initials colors, brand colors, and status colors
- **Style Configuration**: Retrieve avatar dimensions, typography, and layout settings
- **Developer-Friendly**: Comprehensive documentation with JavaScript, CSS, and PHP examples
- **Extensible**: Add custom palettes and styles via WordPress filters

For detailed API documentation and usage examples, see the [Visual Identity API Documentation](docs/api/visual-identity.md).

## System Requirements

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and assets
- **Permissions**: Write access to `wp-content/uploads/` for uploading avatars

## Installation

### Pro License Activation

Avatar Steward Pro requires a valid license key to unlock advanced features.

**To activate your license:**

1. After installing and activating the plugin, go to **Settings > Avatar Steward License**
2. Enter your license key in the format: `XXXX-XXXX-XXXX-XXXX`
3. Click **"Activate License"**
4. Verify the status shows as **"Active"** with a green badge
5. Pro features are now available throughout the plugin

**License Management:**
- View activation details including date, domain, and masked license key
- Deactivate license if moving to a different site
- Each license is tied to one production domain

For detailed licensing documentation, see [docs/licensing-system.md](docs/licensing-system.md).

### Option 1: WordPress Admin Installation
1. Download the plugin ZIP file from your purchase location.
2. Go to your WordPress admin panel > **Plugins > Add New**.
3. Click **"Upload Plugin"** and select the ZIP file.
4. Click **"Install Now"** and wait for installation to complete.
5. Click **"Activate Plugin"** to enable Avatar Steward.
6. Navigate to **Settings > Avatar Steward** to configure the plugin.

### Option 2: Docker Development Environment

Perfect for testing, development, or evaluating the plugin before production deployment.

#### Prerequisites
- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
- **Git** (optional, for cloning the repository)
- **Terminal/Command Prompt** access

#### Quick Start

1. **Clone the repository** (or download and extract):
   ```bash
   git clone https://github.com/JPMarichal/avataria.git
   cd avataria
   ```

2. **Configure environment**:
   ```bash
   cp .env.example .env
   ```
   
   The default configuration includes:
   - WordPress: `http://localhost:8080`
   - phpMyAdmin: `http://localhost:8081`
   - Default admin credentials: `admin` / `admin`
   
   You can customize ports and credentials by editing `.env`:
   ```bash
   WORDPRESS_PORT=8080
   PHPMYADMIN_PORT=8081
   MYSQL_ROOT_PASSWORD=root_pass123
   ```

3. **Install dependencies** (optional, for development):
   ```bash
   composer install
   npm install
   ```

4. **Start the environment**:
   ```bash
   docker compose -f docker-compose.dev.yml up -d
   ```
   
   This command will:
   - Pull WordPress 6.8.3 with PHP 8.1
   - Set up MySQL 8.0 database
   - Install phpMyAdmin for database management
   - Mount the plugin source code at `/wp-content/plugins/avatar-steward`

5. **Access WordPress**:
   - Open your browser to `http://localhost:8080`
   - Login with username: `admin`, password: `admin`
   - The Avatar Steward plugin will be available in **Plugins** menu

6. **Activate the plugin**:
   - Go to **Plugins** in WordPress admin
   - Find **Avatar Steward** and click **Activate**
   - Navigate to **Settings > Avatar Steward** to configure

#### Stopping the Environment

```bash
# Stop containers (data persists)
docker compose -f docker-compose.dev.yml stop

# Stop and remove containers (data persists in volumes)
docker compose -f docker-compose.dev.yml down

# Remove everything including data volumes
docker compose -f docker-compose.dev.yml down -v
```

#### Troubleshooting Docker Installation

**Port conflicts**: If ports 8080 or 8081 are already in use:
```bash
# Edit .env and change ports
WORDPRESS_PORT=8090
PHPMYADMIN_PORT=8091
```

**Permission issues on Linux**:
```bash
sudo chown -R $USER:$USER .
```

**View logs**:
```bash
docker compose -f docker-compose.dev.yml logs -f wordpress
```

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

### Demo Environment for Reviewers

A reproducible demo environment is available for testing the plugin:

**Start the demo:**
```bash
docker compose -f docker-compose.demo.yml up -d
```

**Access:**
- WordPress: http://localhost:8080
- PHPMyAdmin: http://localhost:8081

For complete setup instructions and testing guide, see [Demo Environment Documentation](docs/demo/README.md).

### Creating Plugin Package

To create a clean distribution package for WordPress.org or CodeCanyon:

**Validate before packaging:**
```bash
# Check CodeCanyon requirements
./scripts/validate-codecanyon.sh
```

**Generate package:**
```bash
# Basic package (includes dev dependencies)
./scripts/package-plugin.sh

# Production package (no dev dependencies)
./scripts/package-plugin.sh --no-dev

# Pro version package
./scripts/package-plugin.sh --no-dev --pro --version 1.0.0
```

**PowerShell (Windows):**
```powershell
.\scripts\package-plugin.ps1 -NoDev -Pro -Version "1.0.0"
```

The packaging script:
- Excludes development files using `.distignore`
- Optionally installs production dependencies only
- Includes essential user documentation
- Creates a properly structured ZIP file

## Configuration

After activating the plugin, configure Avatar Steward to match your site's requirements:

1. Go to **Settings > Avatar Steward** in the admin panel.
2. Configure upload restrictions:
   - **Max File Size**: Set the maximum file size for avatar uploads (0.1 - 10 MB, default: 2 MB)
   - **Allowed Formats**: Select which image formats are allowed (JPEG, PNG, GIF, WebP)
   - **Max Dimensions**: Set maximum width and height for avatars (100 - 5000px, default: 2048px)
   - **Convert to WebP**: Enable automatic conversion to WebP format for better compression
3. Configure performance optimization:
   - **Low Bandwidth Mode**: Automatically use SVG avatars when images exceed size threshold
   - **File Size Threshold**: Set the threshold in KB (default: 100 KB) for switching to SVG
4. Configure roles & permissions:
   - **Allowed Roles**: Select which user roles can upload avatars
   - **Require Approval**: Enable moderation queue for new avatar uploads
5. Configure social integrations (Pro version):
   - **Twitter Client ID & Secret**: Enter credentials from Twitter Developer Portal
   - **Facebook App ID & Secret**: Enter credentials from Facebook for Developers
   - See [Social Integrations Setup Guide](docs/social-integrations.md) for detailed instructions
6. For Pro version: Enter your license key in the "License" tab.

## Using Social Media Integrations

Users can import their profile pictures from connected social media accounts:

### For Users

1. Go to your **Profile** page in WordPress admin
2. Scroll to the **Social Avatar Import** section
3. Click **Connect** next to Twitter or Facebook
4. Authorize the connection on the social platform
5. Once connected, click **Import Avatar** to use your social profile picture
6. You can disconnect at any time using the **Disconnect** button

### For Administrators

1. Configure API credentials in **Settings > Avatar Steward > Social Integrations**
2. Create apps on [Twitter Developer Portal](https://developer.twitter.com/) and [Facebook for Developers](https://developers.facebook.com/)
3. Enter your Client ID/Secret and App ID/Secret in the settings
4. Ensure callback URLs match your site's profile URL

For complete setup instructions, see the [Social Integrations Setup Guide](docs/social-integrations.md).

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

## Avatar Moderation

If you've enabled "Require Approval" in the plugin settings, uploaded avatars will be held in a moderation queue for administrator review.

### Accessing the Moderation Panel

1. Go to **Avatar Moderation** in the admin menu (appears as a top-level menu item)
2. The menu badge shows the number of pending avatars awaiting review
3. Requires the `moderate_comments` capability (typically Editors and Administrators)

### Moderation Interface

The moderation panel provides several features:

#### Status Tabs
- **Pending**: Avatars awaiting approval (default view)
- **Approved**: Previously approved avatars
- **Rejected**: Previously rejected avatars

Each tab displays a count of avatars in that status.

#### Filtering and Search
- **Search**: Find avatars by username, email, or display name
- **Status Filter**: Switch between pending, approved, and rejected avatars
- **Role Filter**: Filter avatars by user role

#### Avatar Table Columns
- **Avatar**: Thumbnail preview (64x64px)
- **User**: Display name and email address
- **Role**: User's primary role
- **Uploaded**: Time since upload (e.g., "2 hours ago")
- **Status**: Current moderation status (color-coded badge)
- **Actions**: Approve or Reject buttons (for pending avatars)

#### Individual Actions
For pending avatars, you can:
- **Approve**: Makes the avatar visible immediately. Previous avatar backup is cleared.
- **Reject**: Removes the avatar and restores the previous avatar (if any). The rejected file is permanently deleted.

#### Bulk Actions
Select multiple avatars using checkboxes and apply actions in bulk:
1. Check the avatars you want to moderate
2. Select "Approve" or "Reject" from the dropdown
3. Click "Apply"
4. Review the success message showing how many were processed

### Moderation Workflow

1. **User uploads avatar**: If "Require Approval" is enabled, the avatar status is set to "pending"
2. **Avatar is hidden**: Users with pending avatars see their previous avatar (or default) until approved
3. **Moderator reviews**: Administrator accesses the moderation panel
4. **Decision made**:
   - **Approved**: Avatar becomes visible to all users
   - **Rejected**: Avatar is removed, previous avatar restored
5. **History tracked**: All moderation actions are logged with moderator ID, timestamp, and optional comments

### Moderation History

Each avatar maintains a history of moderation decisions:
- Action performed (approved/rejected)
- Moderator who made the decision
- Timestamp of the action
- Optional moderator comment

This history can be used for audit purposes and GDPR compliance.

### Integration with Avatar Display

Avatars are only shown publicly when:
- Moderation is disabled (default), OR
- Moderation is enabled AND status is "approved"

Pending or rejected avatars are never displayed, ensuring content quality and safety.

## Basic Usage

#### Convert to WebP
- **Type**: Checkbox (enabled/disabled)
- **Default**: Disabled
- **Purpose**: Automatically convert uploaded avatars to WebP format for better compression and performance

### Roles & Permissions

Control who can upload avatars and whether moderation is required:

#### Allowed Roles
- **Options**: Administrator, Editor, Author, Contributor, Subscriber
- **Default**: All roles enabled
- **Purpose**: Restrict avatar uploads to specific user roles
- **Note**: Users without allowed roles won't see the upload interface

#### Require Approval
- **Type**: Checkbox (enabled/disabled)
- **Default**: Disabled
- **Purpose**: Enable moderation queue where avatars must be approved before display
- **Note**: When enabled, new uploads are held for review by administrators

#### Delete Attachment on Remove
- **Type**: Checkbox (enabled/disabled)
- **Default**: Disabled
- **Purpose**: Automatically delete avatar attachment from Media Library when user removes their avatar
- **Note**: When enabled, the attachment file is permanently deleted only if it's not used by other users. When disabled, the attachment remains in the Media Library after avatar removal.

### Saving Settings

1. Configure your desired options
2. Click **"Save Changes"** at the bottom of the page
3. Settings are validated automatically - invalid values will show error messages
4. A success message confirms when settings are saved

### Programmatic Access

Developers can retrieve settings programmatically:

```php
$settings_page = \AvatarSteward\Plugin::instance()->get_settings_page();
$settings = $settings_page->get_settings();

// Access specific settings
$max_file_size = $settings['max_file_size'];      // e.g., 2.0 (MB)
$allowed_formats = $settings['allowed_formats'];   // e.g., ['image/jpeg', 'image/png']
$max_width = $settings['max_width'];               // e.g., 2048 (pixels)
$require_approval = $settings['require_approval']; // e.g., false
```

## Basic Usage

### For End Users

#### Uploading Your Avatar

1. Log in to your WordPress site
2. Go to **Users > Your Profile** (or click your name in the admin bar)
3. Scroll to the **Avatar** section
4. Click **"Choose file"** to select an image from your computer
5. Ensure your image meets the requirements:
   - File size within configured limit (default: 2 MB)
   - Format matches allowed types (default: JPEG, PNG)
   - Dimensions within limits (default: 2048x2048px)
6. Click **"Update Profile"** to save your changes
7. Your new avatar will appear throughout the site

#### Removing Your Avatar

1. Go to **Users > Your Profile**
2. In the **Avatar** section, click **"Remove Avatar"**
3. Confirm the action when prompted
4. Click **"Update Profile"**
5. You'll revert to the default avatar (initials or site default)

### For Administrators

#### Managing User Avatars

1. Go to **Users** in WordPress admin
2. Click **Edit** on any user
3. Scroll to the **Avatar** section
4. Upload or remove avatars for that user
5. Click **"Update User"** to save

#### Moderating Avatars (When Approval Required)

When **Require Approval** is enabled in settings:

1. Go to **Avatar Steward > Moderation** (Pro feature)
2. Review pending avatar uploads
3. Approve or reject each avatar
4. Approved avatars become visible immediately
5. Rejected avatars prompt users to upload a different image

### For Developers

#### Settings API Access

Retrieve all settings:

```php
$plugin = \AvatarSteward\Plugin::instance();
$settings_page = $plugin->get_settings_page();
$settings = $settings_page->get_settings();
```

Access individual settings:

### Low-Bandwidth Mode
- Automatically serves lightweight SVG avatars when images exceed a size threshold.
- Reduces bandwidth usage by up to 99% for large avatar images.
- Configurable threshold (default: 100 KB).
- Overhead < 1ms per avatar, well under 50ms requirement.
- See `docs/performance.md` for detailed metrics and benchmarks.

### Avatar Library (Pro Version)
- Access a curated collection of pre-designed avatars.
- Assign default avatars to new users.

#### Default Values

All settings have sensible defaults:

```php
array(
    'max_file_size'                => 2.0,                     // 2 MB
    'allowed_formats'              => ['image/jpeg', 'image/png'],
    'max_width'                    => 2048,                    // pixels
    'max_height'                   => 2048,                    // pixels
    'convert_to_webp'              => false,
    'allowed_roles'                => ['administrator', 'editor', 'author', 'contributor', 'subscriber'],
    'require_approval'             => false,
    'delete_attachment_on_remove'  => false,
)
```

#### Hooks and Filters

Avatar Steward provides hooks for customization:

```php
// Filter settings before save
add_filter('avatarsteward_sanitize_settings', function($settings) {
    // Customize settings validation
    return $settings;
});

// Action when plugin boots
add_action('avatarsteward_booted', function() {
    // Run code after plugin initialization
});

// Social integrations hooks
add_action('avatarsteward_social_connected', function($user_id, $provider_name) {
    // Fired when user connects a social account
}, 10, 2);

add_action('avatarsteward_avatar_imported', function($user_id, $provider, $attachment_id) {
    // Fired when avatar is imported from social platform
}, 10, 3);

// Register custom social providers
add_action('avatarsteward_register_providers', function($integration_service) {
    $integration_service->register_provider(new CustomProvider());
});
```

#### Visual Identity REST API

Access color palettes and visual styles programmatically:

```javascript
// Fetch complete visual identity
fetch('/wp-json/avatar-steward/v1/visual-identity')
  .then(response => response.json())
  .then(data => {
    console.log('Palettes:', data.palettes);
    console.log('Styles:', data.styles);
  });

// Get avatar color palette
fetch('/wp-json/avatar-steward/v1/visual-identity/palettes/avatar_initials')
  .then(response => response.json())
  .then(palette => {
    palette.colors.forEach(color => {
      console.log('Color:', color);
    });
  });

// Get avatar styles
fetch('/wp-json/avatar-steward/v1/visual-identity/styles/avatar')
  .then(response => response.json())
  .then(styles => {
    console.log('Border radius:', styles.properties.border_radius);
    console.log('Font family:', styles.properties.font_family);
  });
```

**Extend with custom palettes and styles:**

```php
// Add custom color palette
add_filter('avatar_steward_palettes', function($palettes) {
    $palettes['custom_brand'] = array(
        'name'        => 'Custom Brand Colors',
        'description' => 'My brand color palette',
        'colors'      => array('#ff6b6b', '#4ecdc4', '#45b7d1'),
        'usage'       => 'custom_branding',
    );
    return $palettes;
});

// Add custom styles
add_filter('avatar_steward_styles', function($styles) {
    $styles['custom_theme'] = array(
        'name'        => 'Custom Theme Styles',
        'description' => 'Additional styling options',
        'properties'  => array(
            'border_width' => '2px',
            'shadow'       => '0 2px 4px rgba(0,0,0,0.1)',
        ),
    );
    return $styles;
});
```

For complete API documentation, see [Visual Identity API Documentation](docs/api/visual-identity.md).

## Additional Documentation

- **[Visual Identity API](docs/api/visual-identity.md)** - REST API documentation for accessing color palettes and visual styles
- **[Social Integrations Setup Guide](docs/social-integrations.md)** - Complete guide for setting up Twitter and Facebook integrations
- **[Social Integrations API](docs/api/integrations.md)** - Developer documentation for extending social integrations
- **[Migration Guide](docs/migracion/migration-guide.md)** - Guide for migrating from other avatar plugins
- **[Performance Documentation](docs/performance.md)** - Performance metrics and optimization details

## Support

For technical support, refer to the documentation in `docs/` or contact through the channels specified in `docs/support.md`.

## License

This plugin is licensed under GPL v2 or later. Refer to `LICENSE.txt` for more details.

## Changelog

Refer to `CHANGELOG.md` for version history and changes.
