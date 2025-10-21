# Avatar Steward Pro - Third-Party Licenses and Assets Registry

This document provides a comprehensive registry of all third-party assets, libraries, fonts, icons, and images included in the Avatar Steward Pro package. It ensures GPL compatibility and CodeCanyon compliance by documenting licenses, origins, attribution requirements, and distribution terms.

## Document Purpose

- **Track all assets:** Complete inventory of third-party resources
- **Verify compatibility:** Ensure all licenses are compatible with GPL v2 or later
- **Attribution compliance:** Document required attributions and how they are provided
- **CodeCanyon requirements:** Meet marketplace quality and legal standards
- **Distribution clarity:** Clarify what can be distributed and under what terms

## License Summary

**Avatar Steward** (Free and Pro versions) is licensed under **GPL v2 or later** (see `LICENSE.txt`).

This plugin is derived from **Simple Local Avatars** by 10up and other contributors, also licensed under GPL v2. See `docs/legal/origen-gpl.md` for detailed information about the GPL heritage and transformation plan.

---

## 1. PHP Dependencies

### 1.1 Production Dependencies

**Current Status:** Avatar Steward has **no production PHP dependencies**. All functionality is self-contained within the plugin to minimize conflicts and ensure maximum compatibility.

| Package | Version | License | Purpose | Included in Distribution |
|---------|---------|---------|---------|--------------------------|
| _(none)_ | - | - | - | - |

**Note:** For future Pro features, any production dependencies added must be documented here with their licenses verified for GPL compatibility.

### 1.2 Development Dependencies

The following packages are used during development and testing but are **NOT included** in the final distribution package:

| Package | Version | License | Purpose | GPL Compatible |
|---------|---------|---------|---------|----------------|
| `phpunit/phpunit` | ^9.6 | BSD-3-Clause | Unit testing framework | ✅ Yes |
| `wp-coding-standards/wpcs` | ^3.2 | MIT | WordPress coding standards for phpcs | ✅ Yes |
| `dealerdirect/phpcodesniffer-composer-installer` | ^1.0 | MIT | Composer installer for phpcs standards | ✅ Yes |
| `phpcompatibility/php-compatibility` | ^9.3 | LGPL-3.0-or-later | PHP compatibility checks | ✅ Yes |

**Verification Command:**
```bash
composer show --installed | grep -v "^vendor"
```

**Package Source:** https://packagist.org/

**Attribution Required:** No (not included in distribution)

---

## 2. JavaScript Dependencies

### 2.1 Production Dependencies

**Current Status:** Avatar Steward includes **no third-party JavaScript libraries** in production. All JavaScript is custom-written for this plugin.

| Library | Version | License | Purpose | Included in Distribution |
|---------|---------|---------|---------|--------------------------|
| _(none)_ | - | - | - | - |

**Existing JavaScript Files:**
- `assets/js/avatar-steward.js` - Core plugin functionality (custom, GPL v2)
- `assets/js/profile-avatar.js` - Profile page avatar section repositioning (custom, GPL v2)

### 2.2 Development Dependencies

The following packages are used during development but are **NOT included** in the final distribution:

| Package | Version | License | Purpose | GPL Compatible |
|---------|---------|---------|---------|----------------|
| `eslint` | ^8.57.0 | MIT | JavaScript linting tool | ✅ Yes |

**Verification Command:**
```bash
npm list --depth=0 --prod
```

**Package Source:** https://www.npmjs.com/

**Attribution Required:** No (not included in distribution)

---

## 3. CSS Frameworks and Libraries

### 3.1 Production CSS

**Current Status:** Avatar Steward uses **custom CSS only**. No third-party CSS frameworks or libraries are included.

| Framework/Library | Version | License | Purpose | Included in Distribution |
|-------------------|---------|---------|---------|--------------------------|
| _(none)_ | - | - | - | - |

**Existing CSS Files:**
- `assets/css/profile-avatar.css` - Profile page styling (custom, GPL v2)

**Note:** The plugin relies on WordPress core admin styles for consistent UI integration.

---

## 4. Fonts

### 4.1 Fonts Included in Plugin

**Current Status:** Avatar Steward uses **system fonts only**. No custom web fonts are embedded in the current version.

| Font Name | Version | License | Source | Purpose | Attribution Required |
|-----------|---------|---------|--------|---------|----------------------|
| _(none)_ | - | - | - | - | - |

### 4.2 Fonts for Avatar Generation

**Current Status:** The initials avatar generator uses browser-rendered text with system fonts.

**Default Font Stack:**
```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
```

**License:** System fonts (no license restrictions)

### 4.3 Future Font Considerations (Pro Version)

If custom fonts are added for Pro features:
- Use only fonts with open licenses (OFL, Apache 2.0, MIT, or GPL-compatible)
- Recommended sources:
  - Google Fonts (SIL Open Font License)
  - Font Squirrel (commercial-use filter)
  - Adobe Fonts (verify license compatibility)
- **Action Required:** Document each font with its license file

---

## 5. Icons and Images

### 5.1 Icons Included in Plugin

**Current Status:** Avatar Steward uses **WordPress Dashicons** (part of WordPress core) and custom-designed icons.

| Icon Set/File | Version | License | Source | Purpose | Attribution Required |
|---------------|---------|---------|--------|---------|----------------------|
| WordPress Dashicons | Core | GPL v2 | WordPress Core | Admin UI icons | No (WordPress dependency) |

**Custom Icons:**
- Default avatar placeholder icons: Custom-designed, GPL v2 (part of the plugin)
- Admin interface icons: WordPress Dashicons

### 5.2 Images Included in Plugin

**Current Status:** No static images are bundled with the plugin. All avatars are user-uploaded or dynamically generated.

| Image File | License | Source | Purpose | Attribution Required |
|------------|---------|--------|---------|----------------------|
| _(none)_ | - | - | - | - |

### 5.3 Avatar Library Images (Pro Version - Planned)

**Status:** Not yet implemented

**Requirements for Future Implementation:**
- Use only CC0 (Public Domain) or GPL-compatible images
- Document each image source in this file
- Include required attributions in plugin documentation
- Store license copies in `licenses/` subdirectory

**Recommended Sources:**
- **Unsplash** (https://unsplash.com/) - Unsplash License (free for commercial use)
- **Pexels** (https://www.pexels.com/) - Pexels License (free for commercial use)
- **Pixabay** (https://pixabay.com/) - Pixabay License (CC0-like)
- **This Person Does Not Exist** (https://thispersondoesnotexist.com/) - AI-generated, no copyright
- **UI Faces** (https://www.uifaces.co/) - Free for mockups (verify for Pro distribution)

**Future Table Template:**

| Image File | License | Source | URL | Attribution Text | Included In |
|------------|---------|--------|-----|------------------|-------------|
| example.jpg | CC0 | Unsplash | https://... | (not required for CC0) | Avatar Library |

---

## 6. Marketing Assets

### 6.1 Screenshots

**Status:** Documented, pending capture during MVP testing phase

**License:** GPL v2 (original work, part of documentation)

**Location:** `assets/screenshots/`

**Content:**
- Plugin interface screenshots: Original work, captured from WordPress admin
- Avatar test images: Only CC0/Public Domain images used

**Screenshot Sources:**
When screenshots include sample avatars, document source images here:

| Screenshot File | Avatar/Image Used | Source | License | URL | Attribution |
|-----------------|-------------------|--------|---------|-----|-------------|
| _(pending)_ | _(pending)_ | _(pending)_ | CC0 | _(pending)_ | _(pending)_ |

**Note:** This table will be updated as screenshots are captured during the MVP testing phase.

### 6.2 Demo Video

**Status:** Script complete, video production pending

**License:** GPL v2 (original content)

**Location:** `assets/demo-script.md`

**Components:**
- Script: Original content, GPL v2
- Interface recordings: GPL v2 (plugin interface)
- Background music: Must be royalty-free or CC0
- Voiceover: Original work, GPL v2
- Stock footage (if any): Must be CC0 or properly licensed

**Music/Audio Sources for Future Production:**

| Audio File | Type | License | Source | URL | Attribution Required |
|------------|------|---------|--------|-----|----------------------|
| _(pending)_ | Music | CC0 or Royalty-Free | _(pending)_ | _(pending)_ | _(pending)_ |

**Recommended Audio Sources:**
- **YouTube Audio Library** - Royalty-free music
- **Free Music Archive** (https://freemusicarchive.org/) - Various licenses
- **Incompetech** (https://incompetech.com/) - CC BY 3.0
- **Purple Planet** (https://www.purple-planet.com/) - Royalty-free

---

## 7. WordPress Core Integration

Avatar Steward integrates with WordPress core APIs and follows WordPress Coding Standards.

- **WordPress Core:** GPL v2 or later
- **Dependencies:** WordPress >= 5.8, PHP >= 7.4
- **APIs Used:**
  - `get_avatar()` and `pre_get_avatar_data` filter
  - `wp_handle_upload()` for file uploads
  - Settings API for options pages
  - User meta API for avatar storage
  - WordPress admin styles (Dashicons)

**License Compatibility:** ✅ GPL v2 or later

---

## 8. License Compatibility Matrix

All dependencies and assets used in Avatar Steward are compatible with the GPL v2 license:

| License Type | GPL Compatible | Permissive | Can Sublicense | Common Uses |
|--------------|----------------|------------|----------------|-------------|
| GPL v2+ | ✅ Yes | No | No | WordPress Core, this plugin |
| MIT | ✅ Yes | Yes | Yes | Development tools, libraries |
| BSD-3-Clause | ✅ Yes | Yes | Yes | PHPUnit, testing tools |
| LGPL-3.0 | ✅ Yes | No | No | PHP Compatibility checker |
| Public Domain/CC0 | ✅ Yes | Yes | Yes | Stock photos, audio |
| Apache 2.0 | ✅ Yes | Yes | Yes | Some libraries and fonts |
| SIL OFL 1.1 | ✅ Yes | Yes | No | Open source fonts |

**Incompatible Licenses (Never Use):**
- Proprietary/Commercial-only licenses
- Non-commercial only licenses
- Licenses with GPL-incompatible restrictions
- Creative Commons NC (Non-Commercial) licenses

---

## 9. Attribution Requirements

### 9.1 Required Attributions

**Current Status:** No third-party resources requiring attribution are currently included in the distribution package.

**Format for Future Attributions:**

When adding resources that require attribution:

1. **In Code Comments:**
   ```php
   /**
    * Font: [Font Name] by [Author]
    * License: [License Type]
    * Source: [URL]
    */
   ```

2. **In Documentation:** Add entry to this file and `docs/licensing.md`

3. **In User-Facing Credits:** Add to `README.md` or About page if required by license

### 9.2 Distribution Credits

**Avatar Steward Credits:**
- Based on Simple Local Avatars by 10up (GPL v2) - See `docs/legal/origen-gpl.md`
- Development tools: PHPUnit, WordPress Coding Standards, ESLint
- All credits maintained in `README.md` and plugin headers

---

## 10. Pro Version Asset Planning

### 10.1 Avatar Library (Tarea 3.4)

**Planned Features:**
- Curated collection of avatar images
- Categorized by style/theme
- Professional and verified seals

**License Requirements:**
- Use only CC0 or GPL-compatible images
- Document source for each image
- Include license files for sets
- Verify commercial use permissions

**Asset Registry Template:**
```
Avatar Set: [Name]
Source: [Website/Artist]
License: [CC0/GPL/etc.]
Image Count: [Number]
Categories: [List]
Attribution: [Required text or "None"]
Verification Date: [YYYY-MM-DD]
License File: licenses/avatar-library/[filename]
```

### 10.2 Social Integration Icons (Tarea 3.3)

**Planned Features:**
- Import from Facebook, Twitter, Google, etc.
- Social platform icons/logos

**License Requirements:**
- Use official brand assets or icon libraries
- Respect brand guidelines
- Document trademark usage compliance
- Consider icon font libraries (Font Awesome, Material Icons)

**Recommended Icon Sources:**
- **Font Awesome Free** (SIL OFL 1.1) - https://fontawesome.com/
- **Material Icons** (Apache 2.0) - https://material.io/icons/
- **Simple Icons** (CC0 1.0) - https://simpleicons.org/
- Official brand press kits (verify terms)

### 10.3 Admin UI Enhancements

**Planned Features:**
- Enhanced admin interface
- Custom styling for moderation panel
- Visual identity API

**Asset Needs:**
- Custom icons for moderation actions
- UI illustrations
- Admin dashboard widgets

**License Strategy:**
- Design custom icons (GPL v2)
- Use WordPress Dashicons where possible
- License any purchased icon sets properly

---

## 11. Automated License Extraction

### 11.1 PHP Dependencies

**Command to extract licenses from Composer:**
```bash
# List all installed packages with licenses
composer licenses

# Generate detailed license report
composer show --all | grep -E "^(name|license)" | sed 'N;s/\n/ /'

# Export to JSON for automation
composer show --format=json | jq '.installed[] | {name, version, license}'
```

**Automation Script (for CI/CD):**
```bash
#!/bin/bash
# scripts/extract-php-licenses.sh

echo "# PHP Dependency Licenses" > /tmp/php-licenses.md
echo "" >> /tmp/php-licenses.md
composer licenses --format=json | jq -r '.[] | "## \(.name)\n- Version: \(.version)\n- License: \(.license | join(", "))\n"' >> /tmp/php-licenses.md
```

### 11.2 JavaScript Dependencies

**Command to extract licenses from NPM:**
```bash
# List all production dependencies with licenses
npm list --prod --depth=0

# Use license-checker for detailed report
npx license-checker --production --json

# Export to CSV
npx license-checker --production --csv > licenses-npm.csv
```

**Automation Script:**
```bash
#!/bin/bash
# scripts/extract-js-licenses.sh

echo "# JavaScript Dependency Licenses" > /tmp/js-licenses.md
echo "" >> /tmp/js-licenses.md
npx license-checker --production --json | jq -r 'to_entries[] | "## \(.key)\n- License: \(.value.licenses)\n- Repository: \(.value.repository)\n"' >> /tmp/js-licenses.md
```

### 11.3 CI/CD Integration

**GitHub Actions Workflow (Suggested):**
```yaml
# .github/workflows/license-audit.yml
name: License Audit

on:
  pull_request:
    paths:
      - 'composer.json'
      - 'composer.lock'
      - 'package.json'
      - 'package-lock.json'

jobs:
  audit:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      
      - name: Install Composer dependencies
        run: composer install --no-dev
      
      - name: Check PHP licenses
        run: composer licenses --no-dev
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '16'
      
      - name: Install NPM dependencies
        run: npm ci --production
      
      - name: Check NPM licenses
        run: npx license-checker --production --failOn 'GPL-3.0;AGPL-3.0;proprietary'
```

---

## 12. Verification Checklist

Before each release, complete the following verification:

### 12.1 Pre-Release Checklist

- [ ] **Dependencies Review**
  - [ ] Run `composer show --installed` and verify all licenses
  - [ ] Run `npm list --prod` and verify all licenses
  - [ ] Check for any new dependencies since last release

- [ ] **Assets Review**
  - [ ] Verify all fonts have documented licenses
  - [ ] Verify all icons have documented licenses
  - [ ] Verify all images have documented sources
  - [ ] Check screenshot content for third-party assets

- [ ] **Documentation Update**
  - [ ] Update this file with any new assets
  - [ ] Update `docs/licensing.md` if needed
  - [ ] Verify attribution text is current
  - [ ] Check license file copies are included

- [ ] **Distribution Package**
  - [ ] Confirm dev dependencies are excluded
  - [ ] Verify only necessary vendor files included
  - [ ] Check no unintended files in package
  - [ ] Test package installation

- [ ] **GPL Compliance**
  - [ ] Verify `LICENSE.txt` is current
  - [ ] Check GPL headers in all PHP files
  - [ ] Confirm source code availability plan
  - [ ] Verify origin documentation is current

- [ ] **CodeCanyon Compliance**
  - [ ] All licenses documented
  - [ ] Attribution requirements met
  - [ ] No incompatible licenses
  - [ ] Quality standards met

### 12.2 Automated Checks

**Run before tagging release:**
```bash
# Check PHP licenses
composer licenses --no-dev

# Check for GPL-incompatible licenses
composer licenses --no-dev | grep -E 'GPL-3.0|AGPL|proprietary'

# Check JavaScript licenses (if any production deps)
npx license-checker --production --summary

# Verify no dev files in package
grep -r "vendor.*test" avatar-steward-*.zip
```

---

## 13. Security Considerations

### 13.1 Dependency Security

**Regular security audits:**
```bash
# PHP security check (requires security checker)
composer audit

# NPM security check
npm audit

# GitHub security scanning (integrated in repo)
# Check: Security > Dependabot alerts
```

**Policy:**
- Review security alerts within 48 hours
- Update vulnerable dependencies promptly
- Document any accepted risks with justification

### 13.2 Asset Security

- Verify image sources for malware/exploits
- Sanitize uploaded user avatars
- Validate all third-party assets before inclusion
- Use trusted sources only (official repos, verified publishers)

---

## 14. Distribution Strategy

### 14.1 What's Included in Distribution

**GPL Source Package (Free Version):**
- All source code (PHP, JS, CSS)
- GPL license text
- Documentation
- No third-party binaries
- No development dependencies

**Pro Package (CodeCanyon):**
- All free version content
- Pro features source code
- Avatar library images (with licenses)
- Social integration code
- Pro documentation
- License activation system
- Support entitlement

### 14.2 What's NOT Included

**Excluded from Distribution:**
- Development dependencies (`vendor/` dev packages)
- Node modules (`node_modules/`)
- Build tools and configs (for packaged version)
- Internal documentation (`documentacion/`)
- Test files (when packaged)
- Git metadata (`.git/`)

### 14.3 License Key System (Pro)

**Implementation Notes:**
- License key validation: Custom implementation (GPL v2)
- No obfuscation (GPL requirement)
- Source code included (GPL requirement)
- Users can modify/redistribute (GPL requirement)

**Compliance:**
- License keys manage support/updates, not usage rights
- Full source code provided regardless of activation
- No functionality disabled if GPL obligations met

---

## 15. Future Additions Workflow

When adding new third-party resources to Avatar Steward:

### Step-by-Step Process

1. **Identify Asset Need**
   - Document why the asset is needed
   - Consider custom creation vs. third-party

2. **Source Selection**
   - Search GPL-compatible sources first
   - Verify license compatibility
   - Check attribution requirements
   - Review quality and maintenance status

3. **License Verification**
   - Download and review license text
   - Verify commercial use is permitted
   - Check sublicensing rights
   - Confirm GPL compatibility

4. **Documentation**
   - Add entry to this file (`licenses-pro.md`)
   - Update `docs/licensing.md` if user-facing
   - Copy license file to `licenses/` subdirectory
   - Add attribution to code comments

5. **Security Check**
   - Run security scan on new dependency
   - Use `gh-advisory-database` tool for supported ecosystems
   - Review package source code if possible
   - Check package reputation/download stats

6. **Integration**
   - Add to `composer.json` or `package.json`
   - Test in development environment
   - Update build/packaging scripts
   - Document usage in code

7. **Review**
   - Code review with license focus
   - Verify attribution placement
   - Check package size impact
   - Confirm GPL compliance

---

## 16. Contact and Resources

### Internal Documentation
- `docs/licensing.md` - User-facing license information
- `docs/legal/origen-gpl.md` - GPL heritage and refactoring
- `documentacion/08_CodeCanyon_Checklist.md` - CodeCanyon requirements
- `assets/README.md` - Marketing assets documentation

### External Resources
- **GNU GPL v2:** https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
- **GPL Compatibility:** https://www.gnu.org/licenses/license-list.html
- **WordPress Plugin Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- **CodeCanyon Requirements:** https://help.author.envato.com/hc/en-us/articles/204677370
- **Envato License Help:** https://help.author.envato.com/hc/en-us

### Tools
- **Composer Licenses:** `composer licenses`
- **NPM License Checker:** https://www.npmjs.com/package/license-checker
- **FOSSA (License Compliance):** https://fossa.com/
- **License Compatibility Tool:** https://joinup.ec.europa.eu/collection/eupl/solution/joinup-licensing-assistant/jla-compatibility-checker

### Reporting Issues
If you discover:
- Unlicensed asset in the codebase
- GPL-incompatible license
- Missing attribution
- Incorrect license information

**Action:** Open an issue in the repository with:
- Asset/dependency name
- License concern details
- Proposed resolution
- Priority level

---

## 17. Version History

### Version 1.0 - October 21, 2025
- Initial comprehensive asset registry created
- Documented current state (no third-party production assets)
- Listed development dependencies
- Created framework for Pro version assets
- Added automation suggestions
- Established verification checklist
- Documented attribution requirements

### Future Updates
- Document actual Pro assets as they are added
- Update automation scripts based on usage
- Refine checklist based on release experience
- Add examples of actual attributions

---

## 18. Compliance Statement

As of the date of this document:

✅ **All production assets** included in Avatar Steward are either:
- Custom-created and licensed under GPL v2 or later, OR
- Part of WordPress core (GPL v2 or later), OR
- Have documented GPL-compatible licenses

✅ **All attribution requirements** are met or documented for future compliance

✅ **No GPL-incompatible licenses** are included in the distribution package

✅ **Development dependencies** are properly excluded from distribution

✅ **Source code** is fully available under GPL v2 or later

This document will be reviewed and updated before each major release and before submission to CodeCanyon.

---

**Document Maintainer:** Avatar Steward Development Team  
**Last Review Date:** October 21, 2025  
**Next Review Due:** Before version 1.0.0 release  
**Contact:** See repository issues for questions or updates
