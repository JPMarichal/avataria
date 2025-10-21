# License Extraction Scripts

This directory contains automation scripts for extracting and documenting third-party licenses used in Avatar Steward.

## Scripts

### extract-php-licenses.sh

Extracts license information from Composer dependencies (PHP packages).

**Usage:**
```bash
# Extract production dependencies only
./scripts/licensing/extract-php-licenses.sh

# Include development dependencies
./scripts/licensing/extract-php-licenses.sh --dev

# Save to file
./scripts/licensing/extract-php-licenses.sh --output /tmp/php-licenses.md

# Show help
./scripts/licensing/extract-php-licenses.sh --help
```

**Requirements:**
- Composer installed
- `jq` recommended for better formatting (optional)

**Output:**
- Markdown table of dependencies with versions and licenses
- GPL compatibility notes
- Warnings for potentially incompatible licenses

### extract-js-licenses.sh

Extracts license information from NPM dependencies (JavaScript packages).

**Usage:**
```bash
# Extract production dependencies only
./scripts/licensing/extract-js-licenses.sh

# Include development dependencies
./scripts/licensing/extract-js-licenses.sh --dev

# Save to file
./scripts/licensing/extract-js-licenses.sh --output /tmp/js-licenses.md

# Show help
./scripts/licensing/extract-js-licenses.sh --help
```

**Requirements:**
- Node.js and NPM installed
- `license-checker` package (installed automatically if missing)
- `jq` recommended for better formatting (optional)

**Output:**
- Markdown table of dependencies with versions and licenses
- GPL compatibility notes
- Warnings for potentially incompatible licenses

## Integration with CI/CD

These scripts can be integrated into GitHub Actions or other CI/CD pipelines to automatically check licenses on dependency changes.

**Example GitHub Actions workflow:**

```yaml
name: License Audit

on:
  pull_request:
    paths:
      - 'composer.json'
      - 'composer.lock'
      - 'package.json'
      - 'package-lock.json'

jobs:
  audit-licenses:
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
        run: |
          chmod +x scripts/licensing/extract-php-licenses.sh
          ./scripts/licensing/extract-php-licenses.sh
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '16'
      
      - name: Install NPM dependencies
        run: npm ci --production
      
      - name: Check JavaScript licenses
        run: |
          chmod +x scripts/licensing/extract-js-licenses.sh
          ./scripts/licensing/extract-js-licenses.sh
```

## Manual Verification

Before each release, run both scripts and verify:

1. **No GPL-incompatible licenses** are present in production dependencies
2. **All licenses are documented** in `docs/licenses-pro.md`
3. **Attribution requirements** are met for any new dependencies
4. **Development dependencies** are excluded from the distribution package

**Quick check command:**
```bash
# Check PHP licenses
composer licenses --no-dev

# Check for incompatible PHP licenses
composer licenses --no-dev | grep -E "proprietary|AGPL|GPL-3"

# Check JavaScript licenses (requires license-checker)
npx license-checker --production

# Check for incompatible JavaScript licenses
npx license-checker --production --failOn 'GPL-3.0;AGPL-3.0;proprietary'
```

## License Compatibility Reference

### ✅ GPL-Compatible Licenses
- **MIT** - Permissive, allows sublicensing
- **BSD (2-Clause, 3-Clause)** - Permissive, allows sublicensing
- **Apache 2.0** - Permissive with patent grant
- **LGPL (2.1, 3.0)** - Library GPL, compatible
- **ISC** - Similar to MIT
- **Public Domain / CC0** - No restrictions
- **GPL v2+** - Same as plugin license

### ⚠️ Review Required
- **GPL v3** - Not compatible with GPL v2-only (but compatible with GPL v2+)
- **AGPL** - Network copyleft, may require review
- **Custom licenses** - Requires manual review

### ❌ Incompatible Licenses
- **Proprietary / All Rights Reserved** - Not compatible
- **CC BY-NC (Non-Commercial)** - Not compatible with commercial use
- **GPL v2-only** when we use v2+ - Check if this is an issue

## Troubleshooting

### "composer not found"
Install Composer: https://getcomposer.org/download/

### "npm not found"
Install Node.js: https://nodejs.org/

### "jq not found"
Install jq:
- Ubuntu/Debian: `sudo apt-get install jq`
- macOS: `brew install jq`
- Windows: Download from https://stedolan.github.io/jq/

Scripts will work without jq but with simpler formatting.

### "license-checker not found"
The JavaScript script will attempt to install it automatically. If this fails:
```bash
npm install -g license-checker
```

## Updating licenses-pro.md

After running these scripts and discovering changes:

1. Review the output for any new dependencies
2. Check that all licenses are GPL-compatible
3. Update `docs/licenses-pro.md` with any new information
4. Document any required attributions
5. Commit the updated documentation

## Related Documentation

- `docs/licenses-pro.md` - Comprehensive asset registry for Pro version
- `docs/licensing.md` - User-facing license information
- `docs/legal/origen-gpl.md` - GPL heritage documentation
- `documentacion/08_CodeCanyon_Checklist.md` - CodeCanyon requirements

## Support

For questions or issues with these scripts:
1. Check this README
2. Review script help: `./script-name.sh --help`
3. Open an issue in the repository

---

**Last Updated:** October 21, 2025  
**Maintainer:** Avatar Steward Development Team
