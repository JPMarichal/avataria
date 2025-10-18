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

## System Requirements

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and assets
- **Permissions**: Write access to `wp-content/uploads/` for uploading avatars

## Installation

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

## Configuration

After activating the plugin, configure Avatar Steward to match your site's requirements:

### Accessing Settings

1. In WordPress admin, go to **Settings > Avatar Steward**
2. The settings page has two main sections: **Upload Restrictions** and **Roles & Permissions**

### Upload Restrictions

Configure how users can upload avatars:

#### Max File Size
- **Range**: 0.1 MB to 10 MB
- **Default**: 2.0 MB
- **Purpose**: Limits the maximum file size for uploaded avatars to prevent storage issues

#### Allowed Formats
- **Options**: JPEG, PNG, GIF, WebP
- **Default**: JPEG and PNG
- **Purpose**: Select which image formats users can upload
- **Note**: All formats are validated by MIME type for security

#### Max Dimensions
- **Max Width**: 100 to 5000 pixels (default: 2048px)
- **Max Height**: 100 to 5000 pixels (default: 2048px)
- **Purpose**: Prevents excessively large images that could impact performance

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

```php
$max_file_size = $settings['max_file_size'];      // float (MB)
$allowed_formats = $settings['allowed_formats'];   // array of MIME types
$max_width = $settings['max_width'];               // int (pixels)
$max_height = $settings['max_height'];             // int (pixels)
$convert_to_webp = $settings['convert_to_webp'];   // bool
$allowed_roles = $settings['allowed_roles'];       // array of role slugs
$require_approval = $settings['require_approval']; // bool
```

#### Default Values

All settings have sensible defaults:

```php
array(
    'max_file_size'    => 2.0,                     // 2 MB
    'allowed_formats'  => ['image/jpeg', 'image/png'],
    'max_width'        => 2048,                    // pixels
    'max_height'       => 2048,                    // pixels
    'convert_to_webp'  => false,
    'allowed_roles'    => ['administrator', 'editor', 'author', 'contributor', 'subscriber'],
    'require_approval' => false,
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
```

## Support

For technical support, refer to the documentation in `docs/` or contact through the channels specified in `docs/support.md`.

## License

This plugin is licensed under GPL v2 or later. Refer to `LICENSE.txt` for more details.

## Changelog

Refer to `CHANGELOG.md` for version history and changes.
