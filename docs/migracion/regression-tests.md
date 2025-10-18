# Regression Tests for Avatar Migration

This document outlines the regression tests for the Avatar Steward migration functionality.

## Test Environment Setup

### Prerequisites
- WordPress 5.8 or higher
- PHP 7.4 or higher
- Avatar Steward plugin installed and activated
- Test user accounts with various avatar configurations

### Test Data Setup

Create the following test scenarios:

1. **Users with Simple Local Avatars**
   - User: `test_simple_1` with `simple_local_avatar` meta set
   - User: `test_simple_2` with `simple_local_avatar` meta set
   - User: `test_simple_3` without any avatar meta

2. **Users with WP User Avatar**
   - User: `test_wpuser_1` with `wp_user_avatar` meta set
   - User: `test_wpuser_2` with `wp_user_avatar` meta set
   - User: `test_wpuser_3` without any avatar meta

3. **Users with Gravatars**
   - User: `test_gravatar_1@example.com` with active Gravatar
   - User: `test_gravatar_2@example.com` with active Gravatar
   - User: `test_nogravatar@example.com` without Gravatar

4. **Mixed Scenarios**
   - User: `test_already_migrated` with `avatar_steward_avatar` meta already set
   - User: `test_no_avatar` without any avatar configuration

## Automated Tests (PHPUnit)

Location: `tests/phpunit/Domain/Migration/MigrationServiceTest.php`

### Test Coverage

✅ **Unit Tests** (9 tests, all passing)
- Class instantiation
- Method return structures
- Error handling without WordPress functions
- Force parameter handling
- Integer count validation

### Running Automated Tests

```bash
# Run all tests
composer test

# Run only migration tests
vendor/bin/phpunit tests/phpunit/Domain/Migration/

# Run with coverage report
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

## Manual Regression Tests

### Test Suite 1: Simple Local Avatars Migration

**Test Case 1.1: Basic Migration**
- **Precondition**: Users with `simple_local_avatar` meta exist
- **Steps**:
  1. Navigate to Tools > Avatar Migration
  2. Select "Simple Local Avatars" from dropdown
  3. Click "Start Migration"
- **Expected Result**:
  - Success message displays
  - "Migrated" count matches users with `simple_local_avatar` meta
  - Statistics updated to show migrated users
  - Users have `avatar_steward_avatar` meta set
  - Users have `avatar_steward_migrated_from` = `simple_local_avatars`
  - Original `simple_local_avatar` meta unchanged

**Test Case 1.2: Skip Already Migrated**
- **Precondition**: User already has `avatar_steward_avatar` meta
- **Steps**: Run Simple Local Avatars migration
- **Expected Result**:
  - User skipped (appears in "Skipped" count)
  - No duplicate avatars created
  - Original data unchanged

**Test Case 1.3: Skip Users Without Source Data**
- **Precondition**: User without `simple_local_avatar` meta
- **Steps**: Run Simple Local Avatars migration
- **Expected Result**:
  - User skipped (appears in "Skipped" count)
  - No changes to user meta

### Test Suite 2: WP User Avatar Migration

**Test Case 2.1: Basic Migration**
- **Precondition**: Users with `wp_user_avatar` meta exist
- **Steps**:
  1. Navigate to Tools > Avatar Migration
  2. Select "WP User Avatar" from dropdown
  3. Click "Start Migration"
- **Expected Result**:
  - Success message displays
  - "Migrated" count matches users with `wp_user_avatar` meta
  - Statistics updated to show migrated users
  - Users have `avatar_steward_avatar` meta set
  - Users have `avatar_steward_migrated_from` = `wp_user_avatar`
  - Original `wp_user_avatar` meta unchanged

**Test Case 2.2: Avatar Display After Migration**
- **Precondition**: Migration completed successfully
- **Steps**:
  1. Navigate to Users list
  2. View user profile
  3. Check avatar display on frontend
- **Expected Result**:
  - Avatar displays in admin users list
  - Avatar displays on user profile page
  - Avatar displays on frontend (comments, author pages, etc.)
  - No broken images

### Test Suite 3: Gravatar Import

**Test Case 3.1: Successful Import**
- **Precondition**: User with active Gravatar, no local avatar
- **Steps**:
  1. Navigate to Tools > Avatar Migration
  2. Select "Gravatar (download and import)"
  3. Click "Start Migration"
- **Expected Result**:
  - Success message displays
  - "Imported" count includes user
  - Avatar downloaded to uploads directory
  - WordPress attachment created
  - User has `avatar_steward_avatar` meta with attachment ID
  - User has `avatar_steward_migrated_from` = `gravatar`
  - Avatar displays correctly

**Test Case 3.2: Skip User Without Gravatar**
- **Precondition**: User without Gravatar (404 response)
- **Steps**: Run Gravatar import
- **Expected Result**:
  - User appears in "Skipped" count
  - No avatar downloaded
  - No user meta created

**Test Case 3.3: Handle Network Failures**
- **Precondition**: Network connectivity issues or timeout
- **Steps**: Run Gravatar import during simulated network issue
- **Expected Result**:
  - Failed users appear in "Failed" count
  - Process continues for other users
  - No partial/corrupt avatars created

**Test Case 3.4: Skip Already Migrated Users**
- **Precondition**: User has `avatar_steward_avatar` meta
- **Steps**: Run Gravatar import
- **Expected Result**:
  - User skipped (even if they have Gravatar)
  - Existing avatar preserved

### Test Suite 4: Statistics and Reporting

**Test Case 4.1: Accurate Statistics Display**
- **Precondition**: Mixed user scenarios
- **Steps**: Navigate to Tools > Avatar Migration
- **Expected Result**:
  - "Total Users" matches actual count
  - "Users with Avatar Steward avatars" accurate
  - "Migrated from [Source]" counts accurate
  - "Available [Source]" counts accurate

**Test Case 4.2: Statistics Update After Migration**
- **Precondition**: Before any migration
- **Steps**:
  1. Note statistics before migration
  2. Run migration
  3. Review statistics after
- **Expected Result**:
  - Statistics reflect migration results
  - "Available [Source]" decreases by migrated count
  - "Users with Avatar Steward avatars" increases

### Test Suite 5: Data Integrity

**Test Case 5.1: No Data Loss**
- **Precondition**: Users with existing avatars
- **Steps**: Run any migration
- **Expected Result**:
  - Original plugin data (meta) intact
  - Media library attachments unchanged
  - No orphaned attachments

**Test Case 5.2: Idempotent Migrations**
- **Precondition**: Migration already run once
- **Steps**: Run same migration again
- **Expected Result**:
  - All users skipped
  - No duplicate data created
  - No errors reported

**Test Case 5.3: Sequential Migrations**
- **Precondition**: Different avatar sources available
- **Steps**:
  1. Run Simple Local Avatars migration
  2. Run WP User Avatar migration
  3. Run Gravatar import
- **Expected Result**:
  - Each migration processes only relevant users
  - No conflicts between migrations
  - Priority respected (existing avatars not overwritten)

### Test Suite 6: Security and Permissions

**Test Case 6.1: Admin Access Required**
- **Precondition**: Logged in as non-admin user
- **Steps**: Attempt to access Tools > Avatar Migration
- **Expected Result**:
  - Menu item not visible, OR
  - Access denied error if URL accessed directly

**Test Case 6.2: Nonce Verification**
- **Precondition**: Migration form loaded
- **Steps**: Submit form with invalid/missing nonce
- **Expected Result**:
  - Request rejected
  - Error message displayed
  - No migration executed

**Test Case 6.3: CSRF Protection**
- **Precondition**: Valid admin session
- **Steps**: Submit migration request from external site
- **Expected Result**:
  - Request rejected due to nonce verification
  - No migration executed

### Test Suite 7: Performance

**Test Case 7.1: Large User Base**
- **Precondition**: Site with 100+ users
- **Steps**: Run Gravatar import
- **Expected Result**:
  - Process completes without timeout
  - Memory usage within acceptable limits
  - Server remains responsive

**Test Case 7.2: Concurrent Requests**
- **Precondition**: Multiple admin users
- **Steps**: Two admins attempt migration simultaneously
- **Expected Result**:
  - No duplicate migrations
  - No race conditions
  - One completes successfully

## Regression Test Checklist

Run these tests before each release:

- [ ] All automated tests pass (PHPUnit)
- [ ] Simple Local Avatars migration works
- [ ] WP User Avatar migration works
- [ ] Gravatar import works
- [ ] Already migrated users are skipped
- [ ] Statistics are accurate
- [ ] Avatar display works after migration
- [ ] No data loss occurs
- [ ] Migrations are idempotent
- [ ] Security checks pass
- [ ] Performance is acceptable
- [ ] Error handling works correctly
- [ ] Documentation matches functionality

## Test Results Documentation

Document test results in the following format:

```
Test Date: YYYY-MM-DD
Tester: [Name]
Environment: [Staging/Production]
WordPress Version: X.X.X
PHP Version: X.X.X
Avatar Steward Version: X.X.X

Test Results:
- Test Suite 1: [PASS/FAIL] - [Notes]
- Test Suite 2: [PASS/FAIL] - [Notes]
- Test Suite 3: [PASS/FAIL] - [Notes]
- Test Suite 4: [PASS/FAIL] - [Notes]
- Test Suite 5: [PASS/FAIL] - [Notes]
- Test Suite 6: [PASS/FAIL] - [Notes]
- Test Suite 7: [PASS/FAIL] - [Notes]

Overall Result: [PASS/FAIL]
```

Store results in: `docs/reports/tests/migration-regression-YYYYMMDD.md`

## Automated Test Results

Current automated test results:

```
PHPUnit 9.6.29
Tests: 116, Assertions: 236
OK (116 tests, 236 assertions)
```

Migration-specific tests:
- `tests/phpunit/Domain/Migration/MigrationServiceTest.php`: 9 tests, 21 assertions

## Known Issues and Limitations

Document any known issues:

1. **Large Gravatar Imports**
   - May timeout on sites with 1000+ users
   - Workaround: Use WP-CLI or increase timeout

2. **Network Dependencies**
   - Gravatar import requires outbound HTTPS
   - May fail behind restrictive firewalls

3. **Memory Usage**
   - Processing many large images may consume memory
   - Configure PHP memory_limit appropriately

## Future Test Enhancements

Planned improvements:

1. Integration tests with WordPress test suite
2. Automated UI testing with Playwright/Selenium
3. Load testing for large user bases
4. Continuous integration with GitHub Actions
5. Code coverage reports for migration code

## Support and Debugging

If regression tests fail:

1. Review WordPress debug.log
2. Check PHP error logs
3. Verify test preconditions
4. Compare with documented expected results
5. Check for plugin conflicts
6. Document unexpected behavior
7. Report issues with test results attached
