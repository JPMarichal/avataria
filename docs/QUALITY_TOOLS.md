# Quality Tools Quick Reference

This document provides a quick reference for using the quality tools configured for Avatar Steward.

## Prerequisites

Before running any quality tools, ensure dependencies are installed:

```bash
composer install
npm install
```

## PHP Linting (phpcs)

### Check code standards
```bash
composer lint
```

### Auto-fix issues (when possible)
```bash
vendor/bin/phpcbf
```

### Configuration
- File: `phpcs.xml`
- Standards: WordPress-Core, WordPress-Docs, WordPress-Extra
- Checks: `src/` directory
- Excludes: `vendor/`, `node_modules/`, `tests/`, `simple-local-avatars/`

## Unit Testing (PHPUnit)

### Run all tests
```bash
composer test
```

### Run tests with coverage report
```bash
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

### Run specific test file
```bash
vendor/bin/phpunit tests/phpunit/PluginTest.php
```

### Configuration
- File: `phpunit.xml.dist`
- Bootstrap: `tests/phpunit/bootstrap.php`
- Test directory: `tests/phpunit/`

## JavaScript Linting (ESLint)

### Check JavaScript code
```bash
npm run lint
```

### Auto-fix issues (when possible)
```bash
npm run lint:fix
```

### Configuration
- File: `.eslintrc.json`
- Checks: `assets/js/**/*.js`
- Standards: ES6+, tab indentation, single quotes

## CI/CD Integration

All three commands should be run before committing:

```bash
# Quick check
composer lint && composer test && npm run lint
```

## Reports

Generated reports are saved in:
- **Linting**: `docs/reports/linting/`
- **Tests**: `docs/reports/tests/`
- **Coverage**: `docs/reports/tests/coverage-html/`

## Common Issues

### phpcs: "No files to process"
- Check that `phpcs.xml` exists in the project root
- Verify `src/` directory contains PHP files

### PHPUnit: "Class not found"
- Run `composer dump-autoload` to regenerate autoloader
- Check that `composer.json` includes autoload configuration

### ESLint: "Cannot find module"
- Run `npm install` to install dependencies
- Verify `.eslintrc.json` exists in project root

## Standards Documentation

- **WordPress Coding Standards**: https://developer.wordpress.org/coding-standards/
- **PHPUnit**: https://phpunit.de/documentation.html
- **ESLint**: https://eslint.org/docs/latest/

## Customization

### Exclude files from phpcs
Edit `phpcs.xml` and add:
```xml
<exclude-pattern>*/path/to/exclude/*</exclude-pattern>
```

### Add custom phpcs rules
Edit `phpcs.xml` and add rule configuration:
```xml
<rule ref="RuleName">
    <exclude name="RuleName.Specific.Sniff"/>
</rule>
```

### Customize ESLint rules
Edit `.eslintrc.json` and modify the `rules` section:
```json
{
  "rules": {
    "rule-name": "off"
  }
}
```
