# Test Reports

This directory stores the results of automated tests, including PHPUnit unit tests and integration tests.

## PHPUnit Tests

Run PHPUnit tests with:
```bash
composer test
```

Generate coverage report with:
```bash
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

Reports are saved with format: `phpunit-YYYYMMDD.txt`

## Test Structure

- **Unit Tests**: Located in `tests/phpunit/`
- **Bootstrap**: `tests/phpunit/bootstrap.php` loads dependencies and mocks WordPress functions
- **Configuration**: `phpunit.xml.dist` in the project root

## Coverage Requirements

- Aim for minimum 70% code coverage for core functionality
- Critical services (uploads, moderation, integrations) should have 90%+ coverage
- Coverage reports are generated in `coverage-html/` subdirectory

## Manual Testing Scenarios

Document manual test scenarios in this directory with format: `manual-YYYYMMDD-scenario.md`

Include:
- Test environment (Docker version, WordPress version, PHP version)
- Steps to reproduce
- Expected vs actual results
- Screenshots if applicable

## Reporting Format

Each report should include:
- Date and time of execution
- Branch name
- Environment details (WordPress 6.8.3, PHP 8.1, etc.)
- Number of tests run
- Pass/fail status
- Coverage percentage
- Any failures or warnings with details
