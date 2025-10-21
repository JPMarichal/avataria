# Test Reports

This directory stores the results of automated tests, including PHPUnit unit tests, integration tests, security tests, performance benchmarks, and regression tests.

## Quick Start

### Running Tests

Run all tests:
```bash
composer test
```

Run specific test categories:
```bash
# Regression tests
vendor/bin/phpunit tests/phpunit/Regression/

# Security tests
vendor/bin/phpunit tests/phpunit/Security/

# Performance tests
vendor/bin/phpunit tests/phpunit/Performance/

# Integration tests
vendor/bin/phpunit tests/phpunit/Integration/
```

### Generate Coverage Report

```bash
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

**Note:** Requires Xdebug to be installed and enabled.

## Test Structure

### Test Categories

#### 1. **Unit Tests** (`tests/phpunit/Domain/`, `tests/phpunit/Admin/`, etc.)
- Test individual classes and methods in isolation
- Mock WordPress functions and dependencies
- Fast execution, no database required

#### 2. **Integration Tests** (`tests/phpunit/Integration/`)
- Test complete workflows end-to-end
- Validate interactions between components
- Examples: Library workflow, moderation workflow, social import

#### 3. **Regression Tests** (`tests/phpunit/Regression/`)
- Validate documented bug fixes remain fixed
- Reference fixes documented in `docs/fixes/`
- Prevent reintroduction of known bugs

#### 4. **Security Tests** (`tests/phpunit/Security/`)
- Validate protection against common vulnerabilities
- Tests for: SQL injection, XSS, CSRF, file upload attacks, path traversal
- Comprehensive input sanitization validation

#### 5. **Performance Tests** (`tests/phpunit/Performance/`)
- Benchmark critical operations
- Validate performance requirements (e.g., < 50ms, < 100ms)
- Monitor memory usage and scalability

### Test Configuration

- **Configuration File**: `phpunit.xml.dist` in project root
- **Bootstrap**: `tests/phpunit/bootstrap.php` loads dependencies
- **Coverage Output**: `docs/reports/tests/coverage-html/`
- **JUnit Report**: `docs/reports/tests/junit.xml`

## Test Suite Statistics

**Current Status:**
- **Total Tests:** 473
- **Test Files:** 42
- **Test Categories:** 5 (Unit, Integration, Regression, Security, Performance)
- **Coverage Target:** ≥80% for critical modules

**Recent Addition (2025-10-21):**
- Added 42 new tests across 4 new categories
- Comprehensive edge case coverage
- Security and performance validation
- Full documentation in `phase2-3-coverage.md`

## Coverage Requirements

### Minimum Coverage Targets

- **Core Functionality:** 80% minimum
- **Critical Services:** 90% minimum
  - Upload Service
  - Avatar Handler
  - Initials Generator
  - Settings Page
- **Security-Critical Code:** 95% minimum
  - File validation
  - Input sanitization
  - Permission checks

### Coverage Reports

Coverage reports are generated in:
- **HTML Format:** `coverage-html/` subdirectory
- **Text Format:** Included in test output
- **JUnit Format:** `junit.xml` for CI integration

## Test Documentation

### Comprehensive Documentation

- **Test Coverage Report:** `phase2-3-coverage.md`
  - Complete breakdown of Phase 2 & 3 test coverage
  - Status of all acceptance criteria
  - Recommendations for improvements

- **Edge Cases Documentation:** `../../testing/edge-cases-comprehensive.md`
  - 100+ documented edge cases
  - Test locations and status
  - Organized by feature area

- **Test Results:** Historical test results stored with format `phpunit-YYYYMMDD.txt`

## Manual Testing Scenarios

Document manual test scenarios in this directory with format: `manual-YYYYMMDD-scenario.md`

### Required Information

Include in each manual test document:
- Test environment (Docker version, WordPress version, PHP version)
- Steps to reproduce
- Expected vs actual results
- Screenshots if applicable
- Tester name and date

## Reporting Format

Each automated test report should include:
- **Date and Time:** When tests were executed
- **Branch Name:** Git branch being tested
- **Environment Details:** WordPress version, PHP version, server details
- **Test Statistics:**
  - Number of tests run
  - Passed / Failed / Skipped
  - Errors and warnings
- **Coverage Percentage:** Overall and per-module
- **Failures:** Detailed information about any failures
- **Performance:** Execution time
- **Memory Usage:** Peak memory consumption

## CI/CD Integration

### GitHub Actions

Tests are automatically run on:
- Every pull request
- Every push to main branch
- Nightly builds (optional)

### Test Artifacts

CI generates and stores:
- JUnit XML reports
- HTML coverage reports
- Performance benchmark results
- Test execution logs

## Troubleshooting

### Common Issues

**"Xdebug not enabled" warning:**
- Install Xdebug: `pecl install xdebug`
- Configure in php.ini: `zend_extension=xdebug.so`
- Set mode: `xdebug.mode=coverage`

**"WordPress functions not available":**
- These are expected for unit tests
- Use integration tests for WordPress-dependent functionality
- Or set up WordPress test environment with WP_UnitTestCase

**Tests timing out:**
- Increase timeout in phpunit.xml.dist
- Check for infinite loops in tested code
- Consider splitting large test cases

## Best Practices

### Writing Tests

1. **Arrange-Act-Assert pattern:** Structure tests clearly
2. **One assertion per test:** Keep tests focused
3. **Descriptive names:** Test method names should describe what's being tested
4. **Test isolation:** Each test should be independent
5. **Mock external dependencies:** Don't rely on real APIs or database

### Test Maintenance

1. **Update tests with code changes:** Keep tests in sync
2. **Fix flaky tests immediately:** Don't ignore intermittent failures
3. **Review test coverage regularly:** Identify gaps
4. **Document complex test scenarios:** Add comments for clarity
5. **Keep tests fast:** Aim for < 1 second per test

## Additional Resources

- **PHPUnit Documentation:** https://phpunit.de/documentation.html
- **WordPress Test Documentation:** https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/
- **Avatar Steward Test Coverage:** See `phase2-3-coverage.md` in this directory

---

**Last Updated:** 2025-10-21  
**Maintained By:** Avatar Steward Development Team
