# Package Structure Specification

This document defines the packaging structure for Avatar Steward, ensuring compliance with CodeCanyon requirements and WordPress plugin standards.

## Overview

Avatar Steward will be distributed as a clean, professional package containing:
- The WordPress plugin code
- Complete documentation
- Demo assets (screenshots, video)
- Example configurations and use cases

## Package Structure

The final distribution package will follow this structure:

```
avatar-steward/
├── plugin/                          # Main WordPress plugin (ready to install)
│   ├── avatar-steward.php          # Plugin bootstrap file
│   ├── LICENSE.txt                 # GPL v2 license
│   ├── README.txt                  # WordPress.org standard readme
│   ├── src/                        # Plugin source code
│   │   └── AvatarSteward/          # Namespaced PHP classes
│   │       ├── Core/               # Core initialization and service container
│   │       ├── Admin/              # Admin panel screens and UI
│   │       ├── Domain/             # Business logic (Uploads, Initials, etc.)
│   │       └── Infrastructure/     # Persistence and WordPress adapters
│   ├── assets/                     # Frontend/admin assets
│   │   ├── css/                    # Stylesheets
│   │   ├── js/                     # JavaScript files
│   │   └── images/                 # Icons and UI images
│   └── languages/                  # Translation files (.pot, .po, .mo)
│
├── assets/                          # Marketing and demo assets (NOT in plugin)
│   ├── screenshots/                # High-quality screenshots for CodeCanyon
│   │   ├── 01-dashboard.png       # Admin dashboard view
│   │   ├── 02-profile-upload.png  # User profile avatar upload
│   │   ├── 03-initials-generator.png  # Initials avatar example
│   │   ├── 04-moderation-panel.png    # Moderation interface (Pro)
│   │   └── 05-settings.png        # Settings page
│   ├── video/                      # Demo video
│   │   ├── demo.mp4               # Product demo video
│   │   └── demo-script.txt        # Video script and narration
│   └── banner/                     # Marketing materials
│       ├── codecanyon-preview.jpg # CodeCanyon preview image
│       └── feature-graphic.png    # Feature highlights graphic
│
├── docs/                            # Complete documentation
│   ├── README.md                   # Installation, configuration, usage guide
│   ├── CHANGELOG.md                # Version history
│   ├── user-manual.md             # Detailed user manual
│   ├── faq.md                     # Frequently Asked Questions
│   ├── examples.md                # Code examples and use cases
│   ├── support.md                 # Support policy and channels
│   ├── licensing.md               # Third-party licenses
│   ├── QUALITY_TOOLS.md           # Development tools guide
│   ├── PACKAGING.md               # This file
│   ├── legal/                     # Legal documentation
│   │   └── origen-gpl.md          # GPL heritage and refactoring log
│   └── api/                       # API documentation (future)
│       └── hooks.md               # WordPress hooks reference
│
└── examples/                        # Example configurations and snippets
    ├── docker-compose.demo.yml    # Demo environment setup
    ├── .env.example               # Environment variables template
    ├── custom-palette.php         # Example: Custom color palette for initials
    ├── role-restrictions.php      # Example: Restricting uploads by role
    └── migration-script.php       # Example: Migrating from Gravatar/other plugins
```

## Package Types

### 1. Free Version (WordPress.org)
**Package Name**: `avatar-steward-free.zip`

**Contents**:
- `plugin/` directory (core functionality only)
- Basic documentation (README.txt, CHANGELOG.md)
- GPL v2 license

**Features**:
- Local avatar uploads
- Initials generator
- Basic settings panel
- Gravatar replacement

**Distribution**: WordPress.org plugin repository

### 2. Pro Version (CodeCanyon)
**Package Name**: `avatar-steward-pro.zip`

**Contents**:
- `plugin/` directory (full functionality)
- `assets/` directory (screenshots, video, marketing materials)
- `docs/` directory (complete documentation)
- `examples/` directory (code examples, demo setup)
- Installation instructions (PDF)

**Additional Features**:
- Avatar moderation panel
- Avatar library with curated avatars
- Social media integrations (import from Twitter, Facebook, etc.)
- Advanced role-based restrictions
- Audit logs and export
- Priority support
- License key activation

**Distribution**: CodeCanyon marketplace

## Build Process

### Exclusions

The following files and directories are **excluded** from the final package:

#### Development Files
- `node_modules/` - NPM dependencies
- `vendor/` - Composer development dependencies (if using --no-dev, only dev deps excluded)
- `.git/` - Git repository metadata
- `.github/` - GitHub workflows and configs
- `.windsurf/` - IDE-specific files
- `tests/` - PHPUnit test files
- `phpunit.xml.dist` - PHPUnit configuration
- `phpcs.xml` - PHP CodeSniffer configuration
- `.eslintrc.json` - ESLint configuration

#### Temporary and Cache Files
- `*.log` - Log files
- `.DS_Store` - macOS metadata
- `Thumbs.db` - Windows thumbnails
- `.phpunit.result.cache` - PHPUnit cache
- `docs/reports/tests/coverage-html/` - Test coverage reports
- `docs/reports/linting/` - Linting reports

#### Environment-Specific
- `.env` - Environment variables (include `.env.example` only)
- `docker-compose.dev.yml` - Development Docker setup (include demo version)
- `docker-compose.override.yml` - Local overrides

#### Documentation Source Files
- `documentacion/` - Spanish planning documentation (internal use only)
- `simple-local-avatars/` - Original plugin reference (for development only)

### Build Commands

#### For Free Version:
```bash
# Clean build directory
rm -rf build/
mkdir -p build/avatar-steward-free

# Copy plugin files
cp -r plugin/* build/avatar-steward-free/
cp LICENSE.txt build/avatar-steward-free/
cp docs/README.md build/avatar-steward-free/README.txt
cp CHANGELOG.md build/avatar-steward-free/

# Create ZIP
cd build
zip -r avatar-steward-free.zip avatar-steward-free/
```

#### For Pro Version:
```bash
# Clean build directory
rm -rf build/
mkdir -p build/avatar-steward-pro

# Copy all distribution directories
cp -r plugin/ build/avatar-steward-pro/
cp -r assets/ build/avatar-steward-pro/
cp -r docs/ build/avatar-steward-pro/
cp -r examples/ build/avatar-steward-pro/

# Remove excluded files from docs
rm -rf build/avatar-steward-pro/docs/reports/

# Create ZIP
cd build
zip -r avatar-steward-pro.zip avatar-steward-pro/
```

## Quality Checklist

Before packaging, verify:

### Code Quality
- [ ] `composer lint` passes with zero errors
- [ ] `composer test` passes all tests
- [ ] `npm run lint` passes with zero errors
- [ ] No dev dependencies in final package
- [ ] All code follows WordPress Coding Standards
- [ ] Security scan (SAST) completed with no critical issues

### Documentation
- [ ] README.md is complete and accurate
- [ ] CHANGELOG.md is up-to-date
- [ ] All user-facing text is in English (per Restricción C-05)
- [ ] FAQ addresses common questions
- [ ] Support policy is clearly documented
- [ ] All third-party licenses are documented in licensing.md

### Assets
- [ ] At least 5 high-quality screenshots included
- [ ] Screenshots show key features and workflows
- [ ] Video demo is clear, professional, and under 3 minutes
- [ ] CodeCanyon preview image meets size requirements (590x300px)

### Testing
- [ ] Plugin installs cleanly on fresh WordPress
- [ ] Plugin activates without errors
- [ ] Demo environment (docker-compose.demo.yml) works
- [ ] Compatibility tested with WordPress 5.8+ and PHP 7.4+
- [ ] No conflicts with popular themes/plugins

### Legal
- [ ] LICENSE.txt is present and unmodified (GPL v2)
- [ ] Copyright notices are accurate
- [ ] GPL heritage documented in docs/legal/origen-gpl.md
- [ ] No proprietary or incompatible licenses

### CodeCanyon Specific (Pro Version)
- [ ] License activation system implemented
- [ ] Update mechanism configured
- [ ] Support channels clearly defined
- [ ] Refund policy compliant with Envato standards
- [ ] Item description and features list ready

## Package Validation

### Automated Checks
```bash
# Verify package contents
unzip -l avatar-steward-pro.zip

# Check for dev dependencies
unzip -l avatar-steward-pro.zip | grep -E "node_modules|vendor/phpunit"
# Should return nothing

# Verify plugin structure
unzip -l avatar-steward-pro.zip | grep "plugin/avatar-steward.php"
# Should find the main plugin file
```

### Manual Review
1. Extract package to clean environment
2. Install WordPress from scratch
3. Upload and activate plugin
4. Test core features:
   - Avatar upload
   - Initials generation
   - Settings save/load
   - (Pro) Moderation panel
   - (Pro) License activation
5. Check for PHP warnings/errors in debug mode
6. Verify assets load correctly
7. Test on different browsers

## Distribution Channels

### CodeCanyon Submission
1. Package the Pro version following structure above
2. Prepare item description (English)
3. Upload screenshots and demo video
4. Submit for review
5. Address any reviewer feedback

### WordPress.org Submission
1. Package the Free version
2. Prepare plugin description for WordPress.org
3. Create SVN repository account
4. Submit for review by Plugin Review Team
5. Address any feedback

## Version Management

- Use semantic versioning: `MAJOR.MINOR.PATCH`
- Tag releases in Git: `v1.0.0`, `v1.1.0`, etc.
- Update version in:
  - `plugin/avatar-steward.php` header
  - `README.txt` stable tag
  - `CHANGELOG.md` entry
  - `package.json` version
  - `composer.json` version (if applicable)

## Support and Updates

- **CodeCanyon customers**: Automatic updates via Envato Market plugin
- **WordPress.org users**: Updates via WordPress admin dashboard
- **GitHub users**: Download releases from GitHub Releases page

## References

- [CodeCanyon Requirements](https://help.author.envato.com/hc/en-us)
- [WordPress Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [GPL License](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
- [Semantic Versioning](https://semver.org/)

---

**Last Updated**: October 17, 2025  
**Document Owner**: Development Team  
**Next Review**: Before first public release
