# GPL Origin Record for Avatar Steward

## 1. Summary

Avatar Steward reuses code from the "Simple Local Avatars" plugin published on WordPress.org to build an enhanced avatar management solution. This document records the origin, legal obligations, and comprehensive transformation plan that ensures a legitimate and differentiated derivative work.

## 2. Original Source

- **Base project:** [Simple Local Avatars](https://wordpress.org/plugins/simple-local-avatars/)
- **Reference repository:** https://github.com/10up/simple-local-avatars (version 2.7.8 as reference)
- **License:** GNU General Public License (GPL) version 2 or later
- **Copyright:** 10up (and other listed contributors)
- **Last reference check:** 2025-10-16

## 3. GPL Obligations Assumed

- Maintain `LICENSE.txt` with complete GPL text and references to the original author.
- Preserve copyright notices within headers of inherited files.
- Explicitly declare that Avatar Steward is a derivative work of Simple Local Avatars.
- Redistribute the code (free or paid) only under GPL, guaranteeing the same rights to third parties.
- Document significant changes and provide access to complete source code of distributed versions.
- Make source code publicly available through GitHub repository.

## 4. Namespace and Prefix Refactor Strategy

To avoid conflicts with Simple Local Avatars and ensure clear differentiation, Avatar Steward implements a comprehensive namespace refactor:

### 4.1 PHP Namespaces

| Legacy Namespace/Class | New Namespace/Class | Status |
| --- | --- | --- |
| `Simple_Local_Avatars` (main class) | `AvatarSteward\Plugin` | Completed |
| Global functions `sla_*` | `AvatarSteward\Upload\*` services | Planned |
| No namespace (procedural) | `AvatarSteward\Core\*` | Planned |

**Strategy:**
- All new PHP code uses PSR-4 autoloading under `AvatarSteward\` namespace
- Legacy references removed or encapsulated in compatibility wrappers
- Plugin bootstrap in `src/avatar-steward.php` (not `simple-local-avatars.php`)

### 4.2 WordPress Hooks and Filters

| Legacy Hook | New Hook | Purpose |
| --- | --- | --- |
| `simple_local_avatars_*` | `avatarsteward/*` (with subnamespaces) | Plugin actions/filters |
| `simple_local_avatar` (meta key) | `avatarsteward_local_avatar` | User meta storage |
| `sla_*` capabilities | `avatar_steward_*` capabilities | Permission system |

**Strategy:**
- Use forward-slash separated namespaces: `avatarsteward/upload/can_delete`
- Provide migration hooks for backwards compatibility during transition
- Document all public hooks in `docs/hooks.md`

### 4.3 JavaScript and CSS Prefixes

| Legacy Prefix | New Prefix | Usage |
| --- | --- | --- |
| `sla-` | `avapro-` | CSS classes |
| `simpleLocalAvatars` | `avatarSteward` | JS objects |
| `simple-local-avatars` | `avatar-steward` | Script/style handles |

**Strategy:**
- BEM-style CSS with `.avapro-` prefix for all selectors
- JavaScript namespaced under `window.avatarSteward` object
- All asset handles use `avatar-steward-` prefix

### 4.4 Database and Meta Keys

| Legacy Key | New Key | Migration Strategy |
| --- | --- | --- |
| `simple_local_avatar` | `avatarsteward_local_avatar` | Migration script provided |
| `simple_local_avatar_rating` | `avatarsteward_avatar_rating` | Copy on first access |
| `simple_local_avatar_media_id` | `avatarsteward_media_id` | One-time migration |

**Strategy:**
- Provide migration script in `examples/migration-script.php`
- Support reading legacy keys during transition period
- Document migration path in user documentation

## 5. Inherited Components and Transformation Actions

| Inherited Component (ref.) | Refactor Action Planned | Status |
| --- | --- | --- |
| `simple-local-avatars.php` (bootstrap) | Rewrite main loader under `AvatarSteward\Core` namespace; remove global dependencies. | Completed (bootstrap) |
| Upload and assignment functions (`sla_functions.php`) | Encapsulate in service classes (`AvatarSteward\Upload\LocalAvatarService`), add extended validations (privacy and performance). | Planned |
| Admin hooks (metabox, options) | Migrate to modular controllers with renewed UI and minimal reactivity (no external dependencies). | Planned |
| Reused texts and strings | Review translations, replace branding, include references to new documentation. | Planned |
| Capability system | Update prefixes to `avatar_steward_*` and document roles/capabilities in `docs/`. | Planned |
| Avatar generation (initials) | Create new `AvatarSteward\Generator\InitialsGenerator` with advanced features. | Planned |
| REST API endpoints | Register under `/wp-json/avatar-steward/v1/*` namespace. | Planned |

> **Note:** Update the "Status" column and detail additional files as development progresses.

## 6. Key Differentiators Planned

- Advanced initials-based avatar generator with color customization and contrast rules.
- Local curated avatar library with license control.
- Moderation panel with audit trail and decision logging.
- Optional social network integrations for importing avatars under user consent.
- Compliance pipeline (CodeCanyon, asset licenses, automated tests, reproducible demo).

## 7. Evidence and Tracking

- Register refactor milestones and GPL verification in `docs/reports/codecanyon-compliance.md`.
- Maintain commit history showing structural transformations from original code.
- Attach relevant diffs (before/after) when replacing inherited components.
- Document key rewrite phases and new features in `CHANGELOG.md`.
- Record execution environment (e.g., `docker-compose.dev.yml`, `.env`) and migration scripts used during transformations.
- **2025-10-16:** Verified that `simple-local-avatars/simple-local-avatars.php` and `includes/class-simple-local-avatars.php` maintain original GPL headers provided by 10up without modifications.
- **2025-10-17:** Updated `LICENSE.txt` with complete GPL v2 text and enhanced namespace refactor documentation.

## 8. Conflict Avoidance Checklist

To ensure Avatar Steward can coexist with Simple Local Avatars (if needed) and avoid naming conflicts:

- [x] Use distinct PHP namespace (`AvatarSteward\` vs no namespace)
- [x] Use distinct plugin file name (`avatar-steward.php` vs `simple-local-avatars.php`)
- [x] Use distinct text domain (`avatar-steward` vs `simple-local-avatars`)
- [x] Use distinct hook prefixes (`avatarsteward/*` vs `simple_local_avatars_*`)
- [x] Use distinct capability prefixes (`avatar_steward_*` vs `sla_*`)
- [x] Use distinct CSS prefixes (`.avapro-` vs `.sla-`)
- [x] Use distinct JS namespaces (`avatarSteward` vs `simpleLocalAvatars`)
- [ ] Use distinct option keys in wp_options table
- [ ] Use distinct user meta keys (with migration script)
- [ ] Use distinct REST API namespace (`/avatar-steward/v1/` vs `/simple-local-avatars/v1/`)
- [ ] Use distinct WP-CLI commands (if implemented)
- [ ] Use distinct database table names (if custom tables added)

## 9. Contact and Review

- **Legal/Technical Responsible:** Avatar Steward Lead Developer
- **Last Review:** 2025-10-17
- **Next Review:** At completion of Phase 1 (MVP)

Keeping this file updated is part of the Definition of Done for each phase related to GPL legacy refactoring.
