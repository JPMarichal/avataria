# Third-Party Licenses and Assets

This document registers all third-party libraries, fonts, icons, images, and other resources included in Avatar Steward, along with their licenses and usage permissions.

## License Summary

**Avatar Steward** is licensed under GPL v2 or later (see `LICENSE.txt`).

This plugin is derived from **Simple Local Avatars** by 10up and other contributors, which is also licensed under GPL v2. See `docs/legal/origen-gpl.md` for detailed information about the GPL heritage and refactoring plan.

## PHP Dependencies

### Production Dependencies
Currently, Avatar Steward has no production PHP dependencies. All functionality is self-contained within the plugin.

### Development Dependencies
The following packages are used during development and testing but are **not included** in the final distribution package:

| Package | Version | License | Purpose |
|---------|---------|---------|---------|
| `phpunit/phpunit` | ^9.6 | BSD-3-Clause | Unit testing framework |
| `wp-coding-standards/wpcs` | ^3.0 | MIT | WordPress coding standards for phpcs |
| `dealerdirect/phpcodesniffer-composer-installer` | ^1.0 | MIT | Composer installer for phpcs standards |

## JavaScript Dependencies

### Production Dependencies
Currently, Avatar Steward includes no third-party JavaScript libraries in production.

### Development Dependencies
The following packages are used during development but are **not included** in the final distribution:

| Package | Version | License | Purpose |
|---------|---------|---------|---------|
| `eslint` | ^8.57.0 | MIT | JavaScript linting tool |

## Assets and Media

### Icons
- **Default Avatar Icons**: Custom-designed icons created specifically for Avatar Steward.
  - License: GPL v2 (part of the plugin)
  - No third-party icon libraries are currently included.

### Fonts
- **System Fonts**: The plugin uses system fonts available on the user's browser/OS.
  - No custom web fonts are embedded in the current version.

### Images
- **Demo/Example Images**: Any demo images will be created specifically for this project or sourced from public domain/CC0 resources.
  - License: Public Domain / CC0 / GPL v2
  - Sources will be documented here when added.

## WordPress Core Integration

Avatar Steward integrates with WordPress core APIs and follows WordPress Coding Standards. WordPress itself is licensed under GPL v2 or later.

## License Compatibility

All dependencies and assets used in Avatar Steward are compatible with the GPL v2 license:
- MIT License: Compatible with GPL (permissive license)
- BSD-3-Clause: Compatible with GPL (permissive license)
- Public Domain/CC0: Compatible with GPL (no restrictions)

## Future Additions

When adding new third-party resources to Avatar Steward:

1. **Verify License Compatibility**: Ensure the license is GPL-compatible (MIT, BSD, Apache 2.0, CC0, Public Domain, or GPL itself).
2. **Document Here**: Add an entry to the appropriate section above.
3. **Include License Text**: If required by the license, include the full license text in a `licenses/` subdirectory.
4. **Attribution**: Provide proper attribution in code comments and/or documentation.
5. **Security Check**: Run dependency security checks using the `gh-advisory-database` tool for supported ecosystems.

## Verification Process

Before each release:
- [ ] Review all dependencies listed in `composer.json` and `package.json`
- [ ] Verify that dev dependencies are excluded from the distribution package
- [ ] Check for any new assets (fonts, icons, images) added since last release
- [ ] Confirm all licenses are documented and compatible
- [ ] Update this file with any new additions

## Resources

- GNU GPL v2: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
- GPL Compatibility: https://www.gnu.org/licenses/license-list.html
- WordPress Plugin Guidelines: https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/

---

**Last Updated**: October 17, 2025  
**Reviewed By**: Development Team  
**Next Review**: Before each major release
