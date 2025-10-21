# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added - Pro Features
- **Automatic Avatar Cleanup**: Scheduled deletion of inactive avatars (Task 3.10)
  - WP-Cron integration for periodic cleanup tasks
  - Configurable criteria: avatar age and user inactivity periods
  - Dry-run mode for safe preview before deletion
  - Email notifications to users before deletion
  - Summary reports to administrators after cleanup
  - Configurable schedule: daily, weekly, or monthly
  - Settings page integration with full configuration options
  - CleanupService domain service with comprehensive unit tests
- **Avatar Moderation Panel**: Comprehensive moderation system (RF-P03)
  - Dedicated admin menu page for moderating avatar uploads
  - Status management: pending, approved, rejected
  - Bulk actions: approve or reject multiple avatars at once
  - Individual actions: approve or reject single avatars with one click
  - Filtering and search: find avatars by user, status, or role
  - Pagination: handle large moderation queues efficiently
  - Badge counter: menu badge shows pending avatar count
  - Previous avatar backup: automatically restore previous avatar on rejection
  - Smart display: only show approved avatars when moderation is enabled
- **Moderation History Tracking**:
  - Complete audit trail of all moderation decisions
  - Records action, moderator ID, timestamp, and optional comments
  - Per-user history accessible via API
  - GDPR-compliant data storage in user meta
- **Moderation Domain Services**:
  - `ModerationQueue`: Manages queue retrieval, filtering, and status tracking
  - `DecisionService`: Processes approve/reject decisions with rollback support
  - Extensibility hooks: `avatarsteward/avatar_approved`, `avatarsteward/avatar_rejected`
- **Upload Integration with Moderation**:
  - Automatic pending status when "Require Approval" is enabled
  - Previous avatar automatically stored for potential restoration
  - Seamless integration with existing upload workflow
- **Avatar Display Integration**:
  - AvatarHandler respects moderation status
  - Pending/rejected avatars are never displayed publicly
  - Approved avatars show immediately after moderation

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