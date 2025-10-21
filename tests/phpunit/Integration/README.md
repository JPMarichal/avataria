# Integration Tests Documentation

## Overview

This directory contains integration tests for Avatar Steward Pro features. Integration tests validate complete workflows across multiple components, ensuring that Pro features work correctly end-to-end.

## Test Structure

```
tests/phpunit/integration/
├── helpers/
│   └── IntegrationTestCase.php         # Base test case with setup/teardown helpers
├── fixtures/
│   └── TestFixtures.php                # Test data and sample configurations
├── ProActivationIntegrationTest.php     # License activation workflows
├── ModerationWorkflowIntegrationTest.php # Moderation queue and approval workflows
├── SocialImportIntegrationTest.php      # Social media import workflows
├── LibraryWorkflowIntegrationTest.php   # Avatar library management workflows
└── AuditLoggingIntegrationTest.php      # Audit logging and export workflows
```

## Running Integration Tests

All integration tests run as part of the standard test suite:

```bash
# Run all tests (unit + integration)
composer test

# Run only integration tests
vendor/bin/phpunit tests/phpunit/Integration/

# Run specific integration test
vendor/bin/phpunit tests/phpunit/Integration/ProActivationIntegrationTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html tests/phpunit/Integration/
```

**Note**: Some integration tests may initially fail or error because they test features that are still being developed or call methods with specific signatures. These tests serve as:
1. **Documentation** of expected workflows and feature behavior
2. **Contracts** that define how Pro features should work end-to-end
3. **Future verification** to ensure implementations match specifications

As features are completed, tests will be updated to match actual implementations.

## Test Scenarios

### 1. Pro Activation Workflow (`ProActivationIntegrationTest`)

Tests the complete license activation and management workflow:

- **License Activation**: Validates license key format and activates Pro features
- **License Deactivation**: Removes license and disables Pro features
- **Invalid License Rejection**: Handles invalid or malformed license keys
- **Pro Feature Access Control**: Ensures features are gated behind license status
- **License Reactivation**: Allows changing license keys

**Example Flow**:
1. User enters valid license key
2. License key is validated
3. Pro status changes to "active"
4. Pro features become available
5. License info is stored in database

### 2. Moderation Workflow (`ModerationWorkflowIntegrationTest`)

Tests complete avatar moderation from submission to approval/rejection:

- **Submission to Approval**: Avatar enters queue → moderator approves → user notified
- **Submission to Rejection**: Avatar enters queue → moderator rejects → previous avatar restored
- **Bulk Approval**: Multiple avatars approved simultaneously
- **Previous Avatar Restoration**: Rejected avatars revert to previous approved version
- **License Requirement**: Moderation requires active Pro license

**Example Flow**:
1. User uploads avatar with moderation enabled
2. Avatar enters pending queue
3. Moderator reviews avatar in admin panel
4. Moderator approves or rejects with reason
5. User receives notification
6. Approved avatar becomes active, or previous avatar is restored

### 3. Social Import Workflow (`SocialImportIntegrationTest`)

Tests social media avatar import functionality:

- **Twitter Import**: Complete OAuth flow and profile picture import
- **Facebook Import**: OAuth authorization and avatar download
- **Import with Moderation**: Imported avatars enter moderation queue if enabled
- **Account Disconnection**: Users can disconnect social accounts
- **Provider Registration**: All configured providers are available
- **License Requirement**: Social imports require Pro license

**Example Flow** (Twitter):
1. User clicks "Import from Twitter"
2. User redirected to Twitter OAuth
3. User authorizes application
4. System fetches profile picture
5. Avatar uploaded to WordPress media
6. Avatar enters moderation queue (if enabled)
7. User notified of import status

### 4. Library Workflow (`LibraryWorkflowIntegrationTest`)

Tests avatar library management and user selection:

- **Add to Library**: Upload avatars with metadata (author, license, sector, tags)
- **Bulk Import**: Import multiple sectoral avatars simultaneously
- **User Selection**: Users select pre-approved avatars from library
- **Filtering by Sector**: Browse avatars by industry (corporate, healthcare, tech, etc.)
- **Search by Tags**: Find avatars using keyword search
- **Library with Moderation**: Library avatars may bypass moderation
- **Pagination**: Navigate large library collections

**Example Flow**:
1. Admin uploads avatars to library with sector/tags
2. User browses library by sector (e.g., "corporate")
3. User previews available avatars
4. User selects avatar
5. Avatar is assigned to user's profile
6. User avatar updated immediately (or enters moderation)

### 5. Audit Logging Workflow (`AuditLoggingIntegrationTest`)

Tests comprehensive audit logging for all avatar operations:

- **Upload Logging**: Tracks when avatars are uploaded
- **Moderation Logging**: Records approval/rejection decisions and moderators
- **Social Import Logging**: Logs which provider was used
- **Library Selection Logging**: Tracks library avatar assignments
- **Complete Audit Trail**: Full history of user's avatar changes
- **Filtering**: Filter logs by action type, date range, user
- **Export**: Export audit logs to CSV for compliance
- **Sensitive Data Handling**: Ensures no passwords/tokens in logs
- **Log Retention**: Automatic cleanup of old logs

**Example Flow**:
1. User performs avatar operation (upload, import, select)
2. Logger records action with timestamp and metadata
3. Admin views audit log in dashboard
4. Admin filters by user or date range
5. Admin exports logs to CSV for compliance reporting

## Test Data and Fixtures

The `TestFixtures` class provides sample data for all test scenarios:

### Sample Users
- Subscriber, Editor, Administrator roles
- With realistic names and email addresses

### Sample Library Avatars
- Corporate sector (professional, business attire)
- Technology sector (casual, developer style)
- Healthcare sector (medical professionals)
- Education sector (academic professionals)

### Sample Social Configurations
- Twitter/X: Client ID, Client Secret, OAuth settings
- Facebook: App ID, App Secret, permissions

### Sample Moderation Queue
- Pending avatars (awaiting review)
- Approved avatars (with moderator info)
- Rejected avatars (with rejection reasons)

### Sample Audit Logs
- Upload, approval, rejection, social import, library selection events
- Complete metadata for each action

### Sample License Keys
- Valid license key
- Invalid license key
- Expired license key

### Sample Settings
- Basic configuration (default limits)
- Restrictive configuration (tight security)
- Permissive configuration (relaxed rules)

## Using Test Fixtures

```php
use AvatarSteward\Tests\Integration\Fixtures\TestFixtures;

// Get sample users
$users = TestFixtures::get_sample_users();
$admin_user = $users[2]; // Administrator

// Get sample library avatars
$avatars = TestFixtures::get_sample_library_avatars();
$corporate_avatar = $avatars[0];

// Get sample social configs
$configs = TestFixtures::get_sample_social_configs();
$twitter_config = $configs['twitter'];

// Get sample license keys
$licenses = TestFixtures::get_sample_license_keys();
$valid_key = $licenses['valid'];
```

## Base Integration Test Case

The `IntegrationTestCase` base class provides common utilities:

### Setup/Teardown
- Automatic cleanup before and after each test
- Resets options and user meta

### Helper Methods
- `create_test_user($role)` - Create mock user with role
- `create_test_avatar($user_id)` - Create mock avatar attachment
- `activate_pro_license()` - Activate Pro for testing
- `deactivate_pro_license()` - Deactivate Pro
- `enable_moderation()` - Enable moderation mode
- `disable_moderation()` - Disable moderation mode

### Usage

```php
use AvatarSteward\Tests\Integration\Helpers\IntegrationTestCase;

class MyIntegrationTest extends IntegrationTestCase {
    
    protected function setUp(): void {
        parent::setUp(); // Important!
        $this->activate_pro_license();
    }
    
    public function test_my_workflow(): void {
        $user_id = $this->create_test_user('subscriber');
        $avatar_id = $this->create_test_avatar($user_id);
        
        // Test workflow...
    }
}
```

## Docker Staging Environment

For manual integration testing, use the Docker staging environment:

```bash
# Start Docker environment
docker compose -f docker-compose.dev.yml up -d

# Access WordPress
open http://localhost:8080

# Access PHPMyAdmin
open http://localhost:8081

# View logs
docker compose -f docker-compose.dev.yml logs -f wordpress
```

### Manual Test Scenarios

1. **Pro Activation**:
   - Navigate to Settings → Avatar Steward → License
   - Enter license key: `AVATAR-STEWARD-PRO-12345-VALID`
   - Click "Activate License"
   - Verify Pro features appear in menu

2. **Moderation Workflow**:
   - Enable moderation in Settings
   - Upload avatar as subscriber user
   - Login as admin
   - Navigate to Moderation panel
   - Approve or reject avatar
   - Verify user notification

3. **Social Import**:
   - Configure Twitter/Facebook credentials in Settings
   - Navigate to Profile
   - Click "Import from Twitter"
   - Complete OAuth flow
   - Verify avatar imported

4. **Library Selection**:
   - Upload avatars to library as admin
   - Tag with sector and keywords
   - Login as regular user
   - Browse library by sector
   - Select and assign avatar

5. **Audit Logging**:
   - Perform various avatar operations
   - Navigate to Audit Log panel
   - Filter by user and date
   - Export logs to CSV
   - Verify all actions logged

## CI/CD Integration

Integration tests run automatically on:
- Every pull request
- Every commit to main branch
- Scheduled nightly builds

### GitHub Actions Workflow

```yaml
- name: Run integration tests
  run: composer test
  
- name: Upload coverage
  if: success()
  uses: codecov/codecov-action@v3
```

## Best Practices

1. **Clean State**: Always start with clean state using `setUp()` and `tearDown()`
2. **Independent Tests**: Each test should be runnable independently
3. **Realistic Data**: Use fixtures that represent real-world scenarios
4. **Complete Workflows**: Test entire user journeys, not just single methods
5. **Error Cases**: Test both success and failure paths
6. **Pro Gating**: Verify features are properly gated behind Pro license
7. **Documentation**: Comment complex workflows for future maintainers

## Troubleshooting

### Tests Fail with "License not active"
- Ensure `activate_pro_license()` is called in `setUp()`
- Check license manager is properly instantiated

### Tests Fail with "Moderation not enabled"
- Call `enable_moderation()` after activating Pro license
- Verify option is set correctly

### Mock Functions Not Working
- Ensure `bootstrap.php` is loaded
- Check that WordPress functions are properly mocked

### Database State Issues
- Verify `tearDown()` is cleaning up properly
- Check for global state pollution
- Use isolated transactions if needed

## Contributing

When adding new Pro features:

1. Create integration test file in `tests/phpunit/integration/`
2. Extend `IntegrationTestCase` base class
3. Add fixtures to `TestFixtures` if needed
4. Document test scenarios in this README
5. Ensure tests are independent and reproducible
6. Add manual test scenarios for Docker environment

## Coverage Goals

- Integration tests: 80%+ of Pro feature workflows
- Critical paths: 100% coverage (license activation, moderation, social import)
- All test scenarios documented with example data
- Manual test scenarios verified in staging environment
