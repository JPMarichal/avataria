# Copilot Instructions for Avatar Steward

This document provides guidance for GitHub Copilot when working with the Avatar Steward WordPress plugin codebase.

## Project Overview

Avatar Steward is an advanced WordPress plugin for managing user avatars locally. It evolved from Simple Local Avatars and is designed for professional, high-traffic environments. The plugin includes moderation features, avatar library, and social integrations.

**Key Requirements:**
- WordPress ≥ 5.8
- PHP ≥ 7.4
- GPL-2.0-or-later license

## Repository Structure

- `src/` - All new PHP code using `AvatarSteward\` namespace
- `simple-local-avatars/` - Legacy code (READ-ONLY reference)
- `tests/phpunit/` - PHPUnit tests mirroring namespace structure
- `assets/` - JavaScript, CSS, and static assets
- `docs/` - Documentation and reports
- `documentacion/` - Spanish-language project documentation

## Development Commands

### Linting
```bash
# PHP linting (WordPress Coding Standards)
composer lint

# Auto-fix PHP code style issues
vendor/bin/phpcbf

# JavaScript linting
npm run lint
npm run lint:fix
```

### Testing
```bash
# Run PHPUnit tests
composer test

# Run tests with coverage
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

### Setup
```bash
# Install dependencies
composer install
npm install

# Docker environment (for development)
docker compose -f docker-compose.dev.yml up -d
```

## Coding Guidelines

**Full guidelines:** See `.github/instructions/coding.instructions.md`

Key points:
1. **Develop only in `src/`** - All new PHP code must use the `AvatarSteward\` namespace
2. **Follow SOLID principles** - Respect modular service architecture (Uploads, Generator, Moderation, Integrations, Analytics)
3. **Use English** - All user-facing strings, comments, and config defaults in English
4. **WordPress compatibility** - Use WordPress APIs (`add_action`, `WP_REST_Controller`, etc.) and proper escaping
5. **Avoid global state** - Use dependency injection and service patterns
6. **Document GPL origin** - Track adapted legacy code in `docs/legal/origen-gpl.md`
7. **Update documentation** - Keep README.md and relevant docs current
8. **Follow WordPress Coding Standards** - Enforced via `phpcs --standard=WordPress`

## Testing Guidelines

**Full guidelines:** See `.github/instructions/testing.instructions.md`

Key points:
1. **Automated tests first** - Add/update PHPUnit tests for every new service in `src/`
2. **Test location** - Unit tests in `tests/phpunit/` mirror namespace structure
3. **Integration tests** - Test WordPress hooks and REST endpoints
4. **CI expectations** - All checks must pass before merge
5. **Report results** - Export to `docs/reports/linting/` and `docs/reports/tests/`
6. **Security scans** - Log SAST/DAST results in `docs/reports/security-scan.md`

## Architecture Principles

- **SOLID, KISS, DRY** - Follow established design patterns
- **Domain organization** - Code organized by functional domains
- **Service-based** - Use factories and dependency containers
- **WordPress hooks** - Expose hooks and filters for extensibility

## Quality Standards

- All PHP code must pass `composer lint`
- All tests must pass `composer test`
- JavaScript must pass `npm run lint`
- Maintain test coverage for new features
- No security vulnerabilities in new code

## Documentation

When making changes, update relevant documentation:
- `README.md` - User-facing documentation (English)
- `CHANGELOG.md` - Version history and changes
- `docs/` - Technical documentation and reports
- `documentacion/` - Project planning and architecture docs

## Important Notes

- The text domain is `avatar-steward`
- Namespace prefix is `AvatarSteward\` for PHP classes
- Function prefix is `avatar_steward_` for global functions
- Target environments are professional WordPress sites with moderation needs
- CodeCanyon compliance is required (see `documentacion/08_CodeCanyon_Checklist.md`)

## Getting Help

- Review existing code in `src/` for patterns
- Check architecture docs in `documentacion/06_Guia_de_Desarrollo.md`
- See WordPress Codex for API reference
- Follow Definition of Done in `documentacion/11_Definition_of_Done.md`
