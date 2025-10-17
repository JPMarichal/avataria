# Avatar Steward

Avatar Steward is an advanced WordPress plugin that allows managing user avatars locally, with moderation options, avatar library, and social integrations. It is an evolution of the Simple Local Avatars plugin, designed for professional and high-traffic environments.

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
2. Configure basic options:
   - Maximum avatar size (default: 512x512 px)
   - Allowed formats (JPG, PNG, GIF)
   - Restrictions by user role
3. For Pro version: Enter your license key in the "License" tab.
4. Optional: Configure social integrations in the "Social" tab.

## Basic Usage

### Avatar Upload
- Users can upload avatars from their profile (Users > Your Profile).
- Administrators can moderate avatars in **Avatar Steward > Moderation**.

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
