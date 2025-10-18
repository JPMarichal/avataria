# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of Avatar Steward plugin
- Local avatar upload functionality
- Initials-based avatar generator
- Basic admin settings page with WordPress Settings API
- Settings page accessible at Settings > Avatar Steward
- Upload restrictions configuration (file size, formats, dimensions)
- Roles & permissions configuration (allowed roles, approval requirement)
- Input validation and sanitization for all settings
- Comprehensive PHPUnit tests for settings page
- Docker-based development environment
- Documentation structure aligned with CodeCanyon requirements

### Changed
- Refactored from Simple Local Avatars codebase with GPL compliance

### Fixed
- Namespace isolation to avoid conflicts with original plugin

## [0.1.0] - 2025-10-17

### Added
- Core plugin functionality for local avatars
- WordPress integration with `get_avatar()` override
- Basic user profile avatar upload
- Initial documentation and licensing