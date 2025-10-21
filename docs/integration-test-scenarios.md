# Integration Test Scenarios - Avatar Steward Pro Features

## Document Purpose

This document provides a comprehensive overview of integration test scenarios for Avatar Steward Pro features. Each scenario represents a complete end-to-end workflow that users will experience.

## Test Execution Summary

**Date**: October 21, 2025  
**Environment**: PHPUnit 9.6.29, PHP 8.3.6  
**Test Suite**: Integration Tests  
**Location**: `tests/phpunit/Integration/`

### Test Statistics

- **Total Tests**: 41
- **Test Files**: 5
- **Helper Classes**: 2 (IntegrationTestCase, TestFixtures)
- **Coverage Areas**: Pro Activation, Moderation, Social Import, Library, Audit Logging

## Test Scenarios by Feature

### 1. Pro License Activation (6 tests)

**File**: `ProActivationIntegrationTest.php`

| Scenario | Description | Status |
|----------|-------------|--------|
| License Activation Workflow | Complete activation flow from key entry to Pro feature unlock | ⚠️ Pending implementation |
| License Deactivation Workflow | Remove license and verify Pro features disabled | ⚠️ Pending implementation |
| Invalid License Rejection | Reject malformed or invalid license keys | ✅ Working |
| Pro Feature Access Control | Gate features behind active license status | ⚠️ Pending implementation |
| License Reactivation | Change license keys without data loss | ⚠️ Pending implementation |

**Key Workflows Tested**:
1. User enters valid license key
2. System validates key format and authenticity
3. License data stored in database
4. Pro status updated to "active"
5. Pro features become available in UI
6. Admin can view license info and expiration
7. License can be deactivated cleanly

---

### 2. Avatar Moderation (6 tests)

**File**: `ModerationWorkflowIntegrationTest.php`

| Scenario | Description | Status |
|----------|-------------|--------|
| Submission to Approval | Avatar enters queue, moderator approves, user notified | ⚠️ Pending `add_to_queue()` method |
| Submission to Rejection | Avatar enters queue, moderator rejects, previous restored | ⚠️ Pending `add_to_queue()` method |
| Bulk Approval | Multiple avatars approved simultaneously | ⚠️ Pending `add_to_queue()` method |
| Previous Avatar Restoration | Rejected avatars revert to previous approved version | ⚠️ Pending method |
| License Requirement | Moderation requires active Pro license | ⚠️ Pending method |
| Date Filtering | Filter pending avatars by submission date | ⚠️ Pending method |

**Key Workflows Tested**:
1. User uploads avatar with moderation enabled
2. Avatar enters pending queue with metadata
3. Moderator views queue in admin panel
4. Moderator approves or rejects with reason
5. Approved avatar becomes active
6. Rejected avatar restores previous version
7. User receives notification of decision
8. Decision history tracked for audit

---

### 3. Social Media Import (9 tests)

**File**: `SocialImportIntegrationTest.php`

| Scenario | Description | Status |
|----------|-------------|--------|
| Twitter Import Workflow | Complete OAuth flow and profile picture import | ⚠️ Pending `is_enabled()` method |
| Facebook Import Workflow | OAuth authorization and avatar download | ⚠️ Pending `is_enabled()` method |
| Import with Moderation | Imported avatars enter moderation queue if enabled | ⚠️ Pending method |
| Account Disconnection | Users can disconnect social accounts | ✅ Working |
| Provider Registration | All configured providers available | ✅ Working |
| License Requirement | Social imports require Pro license | ✅ Working |
| OAuth Error Handling | Handle invalid OAuth callbacks gracefully | ⚠️ Signature mismatch |
| Provider Configurations | PKCE for Twitter, standard OAuth for Facebook | ✅ Working |

**Key Workflows Tested** (Twitter):
1. User clicks "Import from Twitter"
2. System redirects to Twitter OAuth page
3. User authorizes application
4. OAuth callback with authorization code
5. System exchanges code for access token
6. System fetches profile picture URL
7. Image downloaded and uploaded to WordPress
8. Avatar assigned to user (or enters moderation)
9. Connection status saved for future imports

---

### 4. Avatar Library (9 tests)

**File**: `LibraryWorkflowIntegrationTest.php`

| Scenario | Description | Status |
|----------|-------------|--------|
| Add to Library | Upload avatars with metadata (author, license, sector, tags) | ⚠️ Method signature mismatch |
| Bulk Import | Import multiple sectoral avatars simultaneously | ⚠️ Method signature mismatch |
| User Selection | Users select pre-approved avatars from library | ✅ Working |
| Sector Filtering | Browse avatars by industry sector | ⚠️ Pending implementation |
| Tag Search | Find avatars using keyword search | ⚠️ Pending implementation |
| Library with Moderation | Library avatars may bypass moderation | ⚠️ Pending implementation |
| Remove from Library | Admin removes avatars from library | ✅ Working |
| License Requirement | Library requires Pro license | ✅ Working |
| Pagination | Navigate large library collections | ⚠️ Pending implementation |

**Key Workflows Tested**:
1. Admin uploads avatar to library
2. Admin tags with sector, tags, author, license
3. Avatar appears in centralized library
4. Users browse library by sector filter
5. Users search library by tags
6. User previews avatar before selection
7. User selects avatar
8. Avatar assigned to user profile
9. Same library avatar can be used by multiple users

---

### 5. Audit Logging (11 tests)

**File**: `AuditLoggingIntegrationTest.php`

| Scenario | Description | Status |
|----------|-------------|--------|
| Upload Logging | Track when avatars are uploaded | ✅ Working |
| Moderation Logging | Record approval/rejection decisions | ✅ Working |
| Social Import Logging | Log which provider was used | ✅ Working |
| Library Selection Logging | Track library avatar assignments | ✅ Working |
| Deletion Logging | Log avatar removals | ✅ Working |
| Complete Audit Trail | Full history of user's avatar changes | ✅ Working |
| Action Filtering | Filter logs by action type | ✅ Working |
| Date Filtering | Filter logs by date range | ✅ Working |
| Export Logs | Export audit logs to CSV | ✅ Working |
| Sensitive Data Handling | No passwords/tokens in logs | ✅ Working |
| Log Retention | Automatic cleanup of old logs | ✅ Working |

**Key Workflows Tested**:
1. User performs avatar operation (upload, import, select)
2. Logger records action with timestamp, user, metadata
3. Admin views audit log in dashboard
4. Admin filters by user, action, or date range
5. Admin exports filtered logs to CSV
6. Exported CSV includes all required compliance fields
7. Sensitive data properly redacted
8. Old logs cleaned up per retention policy

---

## Test Data Fixtures

**File**: `tests/phpunit/Integration/Fixtures/TestFixtures.php`

Provides realistic sample data for all scenarios:

### Sample Users (3)
- Subscriber: john_doe, jane_smith
- Administrator: admin_user

### Sample Library Avatars (5)
- Corporate: Male/female business professionals
- Technology: Casual developer styles
- Healthcare: Medical professionals
- Education: Academic professionals

### Sample Social Configs
- Twitter: Client ID, Client Secret, OAuth 2.0 + PKCE
- Facebook: App ID, App Secret, OAuth 2.0

### Sample Moderation Queue (4)
- 2 pending avatars
- 1 approved avatar (with moderator info)
- 1 rejected avatar (with rejection reason)

### Sample Audit Logs (5)
- Upload, approval, rejection, social import, library selection events

### Sample License Keys (3)
- Valid Pro license
- Invalid license key
- Expired license key

### Sample Settings (3)
- Basic configuration (default limits)
- Restrictive configuration (tight security)
- Permissive configuration (relaxed rules)

---

## Integration Test Helper

**File**: `tests/phpunit/Integration/Helpers/IntegrationTestCase.php`

Base class providing:

### Setup/Teardown
- Automatic cleanup before/after each test
- Resets WordPress options and user meta

### Helper Methods
- `create_test_user($role)` - Create mock users
- `create_test_avatar($user_id)` - Create mock avatars
- `activate_pro_license()` - Enable Pro for testing
- `deactivate_pro_license()` - Disable Pro features
- `enable_moderation()` - Turn on moderation mode
- `disable_moderation()` - Turn off moderation mode

---

## Expected Test Results

### Initial State (Current)
Some tests will fail initially because:
1. Methods may not exist yet or have different signatures
2. Features are still in development
3. Tests document expected behavior before implementation

### As Features Complete
Tests will be updated to match actual implementations while maintaining the documented workflows.

### Success Criteria
- All integration tests pass
- Pro features work end-to-end as documented
- Complete audit trail for compliance
- All workflows tested in staging environment

---

## Manual Testing Scenarios

For comprehensive testing, also perform manual scenarios in Docker staging:

1. **Pro Activation**: Activate license and verify all Pro features appear
2. **Moderation Queue**: Submit, approve, reject avatars; verify notifications
3. **Twitter Import**: Complete OAuth flow and import profile picture
4. **Facebook Import**: Complete OAuth flow and import profile picture
5. **Library Management**: Upload, organize, filter, and select library avatars
6. **Audit Log**: Perform operations and verify complete audit trail
7. **Bulk Operations**: Approve 10 avatars simultaneously
8. **Error Handling**: Test invalid inputs and edge cases

See `docs/integration-testing-staging.md` for detailed manual test procedures.

---

## Next Steps

### For Developers
1. Review test scenarios to understand expected workflows
2. Implement features to match test specifications
3. Update test method calls to match actual implementations
4. Verify all tests pass before release

### For QA
1. Run automated integration tests: `composer test`
2. Perform manual scenarios in staging environment
3. Document any deviations from expected behavior
4. Verify edge cases and error handling

### For Compliance
1. Review audit logging coverage
2. Verify all required actions are logged
3. Test log export and retention
4. Ensure GDPR compliance for user data

---

## Maintenance

### Adding New Scenarios
1. Create test method in appropriate test file
2. Use fixtures from `TestFixtures.php`
3. Extend `IntegrationTestCase` for setup/teardown
4. Document scenario in this file
5. Add corresponding manual test to staging guide

### Updating Scenarios
1. Update test method to match implementation
2. Update this documentation
3. Verify staging guide matches test
4. Re-run full test suite

---

## References

- **Integration Tests**: `tests/phpunit/Integration/`
- **Test Documentation**: `tests/phpunit/Integration/README.md`
- **Staging Environment**: `docs/integration-testing-staging.md`
- **Test Reports**: `docs/reports/tests/`
- **PHPUnit Configuration**: `phpunit.xml.dist`

---

**Document Version**: 1.0  
**Last Updated**: October 21, 2025  
**Author**: Avatar Steward Development Team
