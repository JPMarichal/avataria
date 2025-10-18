# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added - MVP Core Features
- **Settings Page**: Comprehensive admin settings page at Settings > Avatar Steward
  - WordPress Settings API integration for native admin experience
  - Contextual help and documentation links
  - Settings validation and sanitization
- **Upload Restrictions Configuration**:
  - Max file size setting (0.1 - 10 MB, default: 2 MB)
  - Allowed formats selection (JPEG, PNG, GIF, WebP)
  - Max dimensions configuration (100 - 5000px, default: 2048px)
  - Optional WebP conversion toggle
- **Roles & Permissions Configuration**:
  - Multi-role selection for upload permissions
  - Moderation queue toggle (require approval)
  - Capability-based access controls
- **Development Environment**:
  - Docker Compose configuration for local development
  - WordPress 6.8.3 with PHP 8.1
  - MySQL 8.0 database
  - phpMyAdmin for database management
  - Hot-reload plugin mounting
- **Testing Infrastructure**:
  - PHPUnit test suite with 24+ tests
  - Comprehensive unit tests for settings validation
  - Integration test structure
  - Code coverage reporting
- **Quality Assurance Tools**:
  - PHP_CodeSniffer with WordPress Coding Standards
  - ESLint configuration for JavaScript
  - Automated linting via composer scripts
  - Pre-commit validation guidelines
- **Documentation Structure**:
  - User manual with installation and usage guides
  - FAQ section for common questions
  - Developer documentation for API usage
  - Security reports and compliance tracking
  - Legal documentation for GPL compliance

### Changed - From Simple Local Avatars
- Refactored namespace from `Simple_Local_Avatars` to `AvatarSteward\`
- Modernized codebase with PHP 7.4+ features (typed properties, return types)
- Replaced legacy options structure with WordPress Settings API
- Enhanced validation and sanitization for security
- Improved code organization with domain-driven structure

### Fixed
- Namespace isolation to prevent conflicts with Simple Local Avatars
- Input validation edge cases in settings sanitization
- Role validation against current WordPress roles

### Security
- MIME type validation for all uploaded files
- Nonce verification for form submissions
- Capability checks for admin functions
- Input sanitization using WordPress functions
- Output escaping for all user-displayed content

## [0.1.0] - 2025-10-17

### Added
- Core plugin functionality for local avatars
- WordPress integration with `get_avatar()` override
- Basic user profile avatar upload
- Initial documentation and licensing