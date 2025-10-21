# Project Structure and Documentation Guide

**Plugin:** Avatar Steward  
**Version:** 0.1.0  
**Last Updated:** October 20, 2025

This document provides a comprehensive guide to the project structure, documentation organization, and navigation.

---

## Directory Structure

```
avataria/                              # Plugin root (GitHub repository)
│
├── avatar-steward.php                 # Main plugin entry point
├── README.md                          # Main documentation (English)
├── CHANGELOG.md                       # Version history
├── LICENSE.txt                        # GPL 2.0 license
├── composer.json                      # PHP dependencies
├── phpunit.xml.dist                   # PHPUnit configuration
├── phpcs.xml                          # PHP CodeSniffer configuration
├── .gitignore                         # Git ignore rules
│
├── src/                               # Source code (PHP)
│   └── AvatarSteward/                 # Main namespace
│       ├── Plugin.php                 # Plugin orchestrator
│       ├── Admin/                     # Admin pages
│       ├── Core/                      # Avatar system core
│       ├── Domain/                    # Business logic
│       │   ├── Initials/              # Initials generator
│       │   ├── LowBandwidth/          # Bandwidth optimizer
│       │   ├── Migration/             # Migration tools
│       │   └── Uploads/               # Upload management
│       └── Infrastructure/            # Cross-cutting services
│
├── tests/                             # Test suite
│   └── phpunit/                       # PHPUnit tests
│       ├── bootstrap.php              # Test bootstrap
│       ├── Admin/                     # Admin tests
│       ├── Core/                      # Core tests
│       └── Domain/                    # Domain tests
│
├── assets/                            # Static assets
│   ├── css/                           # Stylesheets
│   ├── js/                            # JavaScript
│   ├── screenshots/                   # Plugin screenshots
│   └── README.md                      # Assets documentation
│
├── docs/                              # Technical documentation (English)
│   ├── admin-settings-layout.md       # Settings UI documentation
│   ├── demo-script.md                 # Video demo script
│   ├── examples.md                    # Code examples
│   ├── faq.md                         # Frequently asked questions
│   ├── implementation-avatar-override.md  # Technical implementation
│   ├── integration-guide-avatar-upload.md # Integration guide
│   ├── licensing.md                   # License information
│   ├── optimization-guide.md          # Asset optimization
│   ├── PACKAGING.md                   # Package preparation
│   ├── performance.md                 # Performance documentation
│   ├── QUALITY_TOOLS.md               # Quality tooling guide
│   ├── support.md                     # Support information
│   ├── user-manual.md                 # End-user manual
│   │
│   ├── development/                   # Developer documentation
│   │   └── logging.md                 # Logging documentation
│   │
│   ├── fixes/                         # Bug fix documentation
│   │   └── avatar-section-visibility-fix.md
│   │
│   ├── legal/                         # Legal documentation
│   │   └── origen-gpl.md              # GPL origin documentation
│   │
│   ├── migracion/                     # Migration documentation
│   │
│   ├── project-documentation/         # Project management docs
│   │   ├── CONGRUENCIA.md             # Project congruence report
│   │   ├── ESTRUCTURA.md              # Structure documentation
│   │   └── INSTRUCCIONES-INSTALACION.md  # Installation instructions
│   │
│   ├── reports/                       # Reports and checklists
│   │   ├── phase-2-completion.md      # Phase 2 completion report
│   │   ├── wordpress-org-checklist.md # WordPress.org checklist
│   │   ├── linting/                   # Linting reports
│   │   └── tests/                     # Test reports
│   │
│   └── testing/                       # Testing documentation
│
├── documentacion/                     # Project documentation (Spanish)
│   ├── 01_Requerimiento_Producto.md   # Product requirements
│   ├── 02_Estrategia_de_Negocio.md    # Business strategy
│   ├── 03_Estrategia_de_Marketing.md  # Marketing strategy
│   ├── 04_Plan_de_Trabajo.md          # Work plan
│   ├── 05_Stack_Tecnologico.md        # Tech stack
│   ├── 06_Guia_de_Desarrollo.md       # Development guide
│   ├── 07_Metodologia_de_Desarrollo.md # Development methodology
│   ├── 08_CodeCanyon_Checklist.md     # CodeCanyon requirements
│   ├── 09_Roadmap.md                  # Product roadmap
│   ├── 10_Checklist_PreDesarrollo.md  # Pre-development checklist
│   ├── 11_Definition_of_Done.md       # Definition of Done
│   ├── 12_Mockup_Admin_UI.md          # UI mockups
│   ├── 13_Arquitectura.md             # Architecture document
│   ├── backlog-mvp.md                 # MVP backlog
│   └── mvp-spec.json                  # MVP specifications
│
├── docker/                            # Docker configuration
│   └── config/                        # Docker config files
│       ├── README.md                  # Docker config documentation
│       ├── wp-config.php              # WordPress config for Docker
│       └── theme-functions.php        # Theme functions for testing
│
├── docker-compose.dev.yml             # Docker development environment
├── .env.example                       # Environment variables template
│
├── design/                            # Design assets and mockups
├── examples/                          # Usage examples
├── scripts/                           # Utility scripts
└── wp-content/                        # WordPress content (Docker only)
    ├── themes/                        # WordPress themes
    ├── uploads/                       # User uploads
    └── languages/                     # Translation files
```

---

## Documentation Organization

### By Audience

#### For End Users
- **Primary:** `README.md` - Installation, features, basic usage
- **Manual:** `docs/user-manual.md` - Detailed usage guide
- **Help:** `docs/faq.md` - Common questions and answers
- **Support:** `docs/support.md` - Getting help

#### For Developers
- **Getting Started:** `README.md` - Development setup
- **Architecture:** `documentacion/13_Arquitectura.md` - System design
- **Development Guide:** `documentacion/06_Guia_de_Desarrollo.md` - Coding guidelines
- **API Documentation:** PHPDoc comments in source code
- **Testing:** `.github/instructions/testing.instructions.md` - Testing guidelines
- **Code Examples:** `docs/examples.md` - Usage examples

#### For Contributors
- **Methodology:** `documentacion/07_Metodologia_de_Desarrollo.md` - Development process
- **Coding Guidelines:** `.github/instructions/coding.instructions.md`
- **Definition of Done:** `documentacion/11_Definition_of_Done.md`
- **Pull Requests:** Follow GitHub flow

#### For Project Management
- **Work Plan:** `documentacion/04_Plan_de_Trabajo.md` - Phase breakdown
- **Roadmap:** `documentacion/09_Roadmap.md` - Future plans
- **Backlog:** `documentacion/backlog-mvp.md` - Feature backlog
- **Reports:** `docs/reports/` - Progress and completion reports
- **Checklists:** `documentacion/08_CodeCanyon_Checklist.md`, `docs/reports/wordpress-org-checklist.md`

#### For Business
- **Product Requirements:** `documentacion/01_Requerimiento_Producto.md`
- **Business Strategy:** `documentacion/02_Estrategia_de_Negocio.md`
- **Marketing Strategy:** `documentacion/03_Estrategia_de_Marketing.md`
- **Licensing:** `docs/licensing.md`, `docs/legal/origen-gpl.md`

---

## Key Documents by Purpose

### Installation & Setup
1. `README.md` - Main installation guide
2. `docs/project-documentation/INSTRUCCIONES-INSTALACION.md` - Detailed installation
3. `.env.example` - Environment configuration template
4. `docker-compose.dev.yml` - Docker setup

### Development
1. `.github/instructions/coding.instructions.md` - Coding standards
2. `.github/instructions/testing.instructions.md` - Testing guidelines
3. `documentacion/06_Guia_de_Desarrollo.md` - Development guide
4. `phpcs.xml` - Code style configuration
5. `phpunit.xml.dist` - Test configuration

### Features & Implementation
1. `docs/implementation-avatar-override.md` - Avatar system
2. `docs/integration-guide-avatar-upload.md` - Upload system
3. `docs/performance.md` - Performance features
4. `documentacion/backlog-mvp.md` - Feature details

### Testing & Quality
1. `docs/QUALITY_TOOLS.md` - Quality tooling
2. `docs/reports/tests/` - Test reports
3. `docs/testing/` - Testing documentation
4. `documentacion/11_Definition_of_Done.md` - Quality criteria

### Publication
1. `docs/reports/wordpress-org-checklist.md` - WordPress.org submission
2. `documentacion/08_CodeCanyon_Checklist.md` - CodeCanyon requirements
3. `docs/PACKAGING.md` - Package preparation
4. `docs/demo-script.md` - Demo video script

### Progress & Status
1. `docs/reports/phase-2-completion.md` - Current phase status
2. `docs/project-documentation/CONGRUENCIA.md` - Project congruence
3. `CHANGELOG.md` - Version history
4. `documentacion/04_Plan_de_Trabajo.md` - Task completion

---

## Documentation Standards

### Language
- **Technical Documentation:** English (`docs/`, `README.md`, code comments)
- **Project Documentation:** Spanish (`documentacion/`)
- **User-Facing Text:** English (plugin UI, strings)

### Naming Conventions
- **Documentation Files:** kebab-case (e.g., `user-manual.md`)
- **Project Docs:** Numbered for sequence (e.g., `01_Requerimiento_Producto.md`)
- **Reports:** Descriptive names (e.g., `phase-2-completion.md`)

### File Organization
- **By Audience:** Separate docs for users, developers, management
- **By Topic:** Group related documentation together
- **By Phase:** Project management docs organized by phase
- **By Purpose:** Technical vs. business documentation

### Update Protocol
1. Update documentation when:
   - Features are added or changed
   - Code structure changes
   - Requirements evolve
   - Bugs are fixed
   - Processes change

2. Always update:
   - `CHANGELOG.md` - For version changes
   - `README.md` - For user-facing changes
   - Relevant technical docs - For implementation changes
   - Test documentation - For test changes

---

## Quick Reference

### I want to...

**Install the plugin**
→ `README.md` → Installation section

**Understand the project**
→ `documentacion/01_Requerimiento_Producto.md`

**Set up development environment**
→ `README.md` → Docker Development Environment

**Contribute code**
→ `.github/instructions/coding.instructions.md`

**Run tests**
→ `.github/instructions/testing.instructions.md`
→ `composer test`

**Understand architecture**
→ `documentacion/13_Arquitectura.md`
→ `docs/project-documentation/ESTRUCTURA.md`

**Check project status**
→ `docs/reports/phase-2-completion.md`

**Prepare for publication**
→ `docs/reports/wordpress-org-checklist.md`

**See what's next**
→ `documentacion/09_Roadmap.md`

**Get help**
→ `docs/faq.md`
→ `docs/support.md`

---

## Recent Changes

### October 20, 2025
- ✅ Reorganized root-level documentation to `docs/project-documentation/`
- ✅ Removed test/debug PHP files from root
- ✅ Moved Docker config files to `docker/config/`
- ✅ Moved asset documentation to `docs/`
- ✅ Fixed test compatibility issues (PHPUnit 8.5)
- ✅ Created Phase 2 completion report
- ✅ Created WordPress.org publication checklist
- ✅ Updated `.gitignore` to exclude test files
- ✅ All 219 tests passing

---

## Maintenance

### Documentation Review Schedule
- **Weekly:** Update progress in work plan
- **Sprint/Phase Completion:** Update completion reports
- **Feature Addition:** Update relevant technical docs
- **Release:** Update CHANGELOG.md, README.md

### Quality Checks
- **Before Commit:** Run `composer test`
- **Before PR:** Run `composer lint` (when configured)
- **Before Release:** Review all user-facing documentation

---

## Contact & Support

For questions about documentation organization or project structure:
- Review this guide first
- Check relevant documentation section
- Create issue on GitHub if documentation is unclear
- Contribute improvements via pull request

---

**Last Updated:** October 20, 2025  
**Version:** 0.1.0  
**Status:** Active Development - Phase 2 Complete
