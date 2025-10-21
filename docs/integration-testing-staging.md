# Docker Staging Environment for Integration Testing

This guide explains how to set up and use the Docker staging environment for manual integration testing of Avatar Steward Pro features.

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/JPMarichal/avataria.git
cd avataria

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker containers
docker compose -f docker-compose.dev.yml up -d

# 4. Wait for WordPress to initialize (30-60 seconds)
docker compose -f docker-compose.dev.yml logs -f wordpress

# 5. Access WordPress
open http://localhost:8080

# 6. Initial Setup
# - Complete WordPress installation wizard
# - Login with admin credentials
# - Navigate to Plugins → Activate "Avatar Steward"
```

## Environment Configuration

Edit `.env` file to customize:

```bash
# WordPress Configuration
WORDPRESS_PORT=8080
WORDPRESS_DB_NAME=avatarsteward
WORDPRESS_DB_USER=avatarsteward
WORDPRESS_DB_PASSWORD=avatarsteward_password
WORDPRESS_TABLE_PREFIX=wp_

# MySQL Configuration  
MYSQL_DATABASE=avatarsteward
MYSQL_USER=avatarsteward
MYSQL_PASSWORD=avatarsteward_password
MYSQL_ROOT_PASSWORD=root_password

# PHPMyAdmin
PHPMYADMIN_PORT=8081
```

## Services

### WordPress (Port 8080)
- URL: http://localhost:8080
- Admin URL: http://localhost:8080/wp-admin
- Plugin automatically mounted from project directory
- Changes to plugin files reflected immediately

### PHPMyAdmin (Port 8081)
- URL: http://localhost:8081
- Username: `avatarsteward` (or `root`)
- Password: `avatarsteward_password` (or `root_password`)
- Database: `avatarsteward`

### MySQL (Port 3306)
- Host: `localhost` (from host machine) or `db` (from containers)
- Database: `avatarsteward`
- User: `avatarsteward`
- Password: `avatarsteward_password`

## Manual Testing Scenarios

### Scenario 1: Pro License Activation

**Objective**: Test the complete Pro license activation workflow.

**Steps**:
1. Access WordPress admin: http://localhost:8080/wp-admin
2. Navigate to **Settings → Avatar Steward → License**
3. Enter test license key: `AVATAR-STEWARD-PRO-12345-VALID`
4. Click **Activate License**
5. Verify success message appears
6. Verify Pro menu items appear (Moderation, Library, etc.)
7. Navigate to **Settings → Avatar Steward → Pro Features**
8. Verify Pro badge/indicator is shown

**Expected Results**:
- ✅ License activates successfully
- ✅ Success message displayed
- ✅ Pro menu items appear
- ✅ Pro features become accessible
- ✅ License info stored in database

**Rollback**:
- Click **Deactivate License**
- Verify Pro features are hidden

---

### Scenario 2: Avatar Moderation Workflow

**Objective**: Test complete avatar submission, approval, and rejection workflow.

**Prerequisites**: Pro license activated

**Steps**:

**Part A: Enable Moderation**
1. Navigate to **Settings → Avatar Steward**
2. Check **Enable Moderation Queue**
3. Click **Save Changes**

**Part B: Submit Avatar for Moderation**
1. Create test user (subscriber role) via **Users → Add New**
   - Username: `testuser1`
   - Email: `testuser1@example.com`
   - Role: Subscriber
2. Login as `testuser1` (use private browser window)
3. Navigate to **Profile → Edit Profile**
4. Upload avatar image (any JPG/PNG file)
5. Click **Update Profile**
6. Verify message: "Avatar submitted for moderation"

**Part C: Approve Avatar**
1. Login as admin
2. Navigate to **Avatar Steward → Moderation Queue**
3. Verify `testuser1` appears in pending list
4. Click **Approve** for testuser1's avatar
5. Verify avatar moves to approved list
6. View testuser1's profile/comments
7. Verify avatar is now displayed

**Part D: Reject Avatar**
1. Login as `testuser1` again
2. Upload a different avatar
3. Submit for moderation
4. Login as admin
5. Navigate to **Moderation Queue**
6. Click **Reject** for testuser1's new avatar
7. Enter rejection reason: "Low quality image"
8. Click **Submit**
9. Verify previous avatar is restored for testuser1

**Expected Results**:
- ✅ Moderation queue shows pending avatars
- ✅ Admin can approve/reject avatars
- ✅ Approved avatars become visible
- ✅ Rejected avatars restore previous version
- ✅ Rejection reasons are stored
- ✅ Users receive appropriate notifications

**Test Data**:
- Sample images available in `tests/phpunit/integration/fixtures/images/` (create if needed)

---

### Scenario 3: Social Media Import (Twitter/X)

**Objective**: Test Twitter profile picture import.

**Prerequisites**: 
- Pro license activated
- Twitter Developer account with API credentials

**Setup Twitter App**:
1. Visit https://developer.twitter.com/en/portal/dashboard
2. Create new app or use existing
3. Note: Client ID, Client Secret
4. Set callback URL: `http://localhost:8080/wp-admin/profile.php`

**Steps**:

**Part A: Configure Twitter Integration**
1. Navigate to **Settings → Avatar Steward → Social Integrations**
2. Find **Twitter / X** section
3. Enter **Client ID**: (from Twitter developer portal)
4. Enter **Client Secret**: (from Twitter developer portal)
5. Check **Enable Twitter Integration**
6. Click **Save Changes**

**Part B: Import Avatar**
1. Login as regular user
2. Navigate to **Profile → Edit Profile**
3. Find **Import from Social Media** section
4. Click **Import from Twitter**
5. Redirected to Twitter OAuth page
6. Click **Authorize App**
7. Redirected back to WordPress
8. Verify avatar imported
9. If moderation enabled, check moderation queue

**Expected Results**:
- ✅ OAuth flow completes successfully
- ✅ Twitter profile picture downloaded
- ✅ Avatar uploaded to WordPress media library
- ✅ Avatar assigned to user (or enters moderation)
- ✅ Connection status shows "Connected"
- ✅ User can disconnect Twitter account

**Alternative**: Test with Facebook following similar steps.

---

### Scenario 4: Avatar Library Management

**Objective**: Test avatar library upload, organization, and user selection.

**Prerequisites**: Pro license activated

**Steps**:

**Part A: Upload Avatars to Library**
1. Login as admin
2. Navigate to **Avatar Steward → Avatar Library**
3. Click **Add New Avatar**
4. Upload avatar image
5. Fill metadata:
   - **Author**: Avatar Steward
   - **License**: GPL-2.0
   - **Sector**: Corporate
   - **Tags**: professional, business, male
6. Click **Add to Library**
7. Repeat for 5-10 different avatars with various sectors

**Part B: Organize Library**
1. View library list
2. Filter by sector: **Corporate**
3. Verify only corporate avatars shown
4. Search by tag: **professional**
5. Verify search results
6. Test pagination (if > 10 avatars)

**Part C: User Selects from Library**
1. Login as regular user
2. Navigate to **Profile → Edit Profile**
3. Find **Avatar Library** section
4. Browse available avatars
5. Filter by sector if desired
6. Click **Select** on chosen avatar
7. Verify avatar preview updates
8. Click **Update Profile**
9. Verify avatar assigned

**Expected Results**:
- ✅ Admins can upload avatars with metadata
- ✅ Library shows all uploaded avatars
- ✅ Filtering by sector works
- ✅ Search by tags works
- ✅ Users can browse and select avatars
- ✅ Selected avatars assigned immediately (or enter moderation)
- ✅ Library avatars reusable by multiple users

**Test Data**:
- Download sample avatar pack from: `/tests/phpunit/integration/fixtures/sample-avatars/`
- Or use placeholder images from https://placeholder.com/

---

### Scenario 5: Audit Logging and Export

**Objective**: Test comprehensive audit logging of all avatar operations.

**Prerequisites**: Pro license activated

**Steps**:

**Part A: Generate Audit Trail**
1. Perform various operations:
   - Upload avatar as user A
   - Approve avatar as admin
   - Import from Twitter as user B
   - Select library avatar as user C
   - Reject avatar as admin
   - Delete avatar as user D

**Part B: View Audit Log**
1. Login as admin
2. Navigate to **Avatar Steward → Audit Log**
3. Verify all operations appear in log
4. Check each entry has:
   - Timestamp
   - User
   - Action type
   - Details/metadata

**Part C: Filter Logs**
1. Filter by **Action Type**: Approval
2. Verify only approvals shown
3. Filter by **User**: user B
4. Verify only user B's actions shown
5. Filter by **Date Range**: Last 24 hours
6. Verify only recent actions shown

**Part D: Export Logs**
1. Click **Export to CSV**
2. Choose date range: Last 7 days
3. Click **Export**
4. Verify CSV downloads
5. Open CSV in spreadsheet
6. Verify all columns present:
   - Timestamp, User ID, Username, Action, Details

**Expected Results**:
- ✅ All avatar operations logged
- ✅ Log entries include complete metadata
- ✅ Filtering works correctly
- ✅ Export generates valid CSV
- ✅ No sensitive data (passwords, tokens) in logs
- ✅ Logs readable and useful for compliance

---

## Common Testing Workflows

### Complete User Journey
1. New user registers
2. User uploads avatar → enters moderation queue
3. Admin reviews and approves
4. User changes avatar via social import
5. User changes avatar via library selection
6. Admin reviews audit log
7. Admin exports compliance report

### Bulk Operations
1. Create 10 test users
2. Each uploads avatar
3. All enter moderation queue
4. Admin approves all in bulk
5. Verify all avatars activated
6. Check audit log for bulk approval

### Error Handling
1. Try to upload oversized file → verify error message
2. Try to activate invalid license → verify rejection
3. Try to import without OAuth setup → verify error
4. Try to access Pro features without license → verify blocked

## Debugging

### View Plugin Logs
```bash
# WordPress debug log
docker compose -f docker-compose.dev.yml exec wordpress tail -f /var/www/html/wp-content/debug.log

# Apache error log
docker compose -f docker-compose.dev.yml logs -f wordpress
```

### Access Database
```bash
# Via PHPMyAdmin
open http://localhost:8081

# Via MySQL CLI
docker compose -f docker-compose.dev.yml exec db mysql -u avatarsteward -pavatarsteward_password avatarsteward
```

### View Plugin Files
```bash
# Files are mounted from host, edit directly:
# /home/runner/work/avataria/avataria/src/
# Changes reflect immediately in Docker
```

### Reset Environment
```bash
# Stop and remove containers
docker compose -f docker-compose.dev.yml down

# Remove volumes (clears database)
docker compose -f docker-compose.dev.yml down -v

# Start fresh
docker compose -f docker-compose.dev.yml up -d
```

## Test Data Fixtures

Sample data available in `tests/phpunit/integration/fixtures/`:

- **sample-users.sql**: Test users with various roles
- **sample-avatars.sql**: Pre-populated library avatars
- **sample-licenses.txt**: Valid/invalid license keys
- **sample-social-configs.json**: Social API configurations

### Load Fixtures

```bash
# Load test users
docker compose -f docker-compose.dev.yml exec -T db mysql -u avatarsteward -pavatarsteward_password avatarsteward < tests/phpunit/integration/fixtures/sample-users.sql

# Load library avatars
docker compose -f docker-compose.dev.yml exec -T db mysql -u avatarsteward -pavatarsteward_password avatarsteward < tests/phpunit/integration/fixtures/sample-avatars.sql
```

## Performance Testing

Test plugin performance under load:

```bash
# Install Apache Bench
apt-get install apache2-utils

# Test avatar upload endpoint
ab -n 100 -c 10 http://localhost:8080/wp-admin/admin-ajax.php?action=avatar_upload

# Test moderation queue
ab -n 100 -c 10 http://localhost:8080/wp-admin/admin.php?page=avatar-steward-moderation
```

## Security Testing

### Test File Upload Security
1. Try uploading PHP file disguised as image
2. Try uploading oversized file
3. Try uploading file with dangerous MIME type
4. Verify all rejected with appropriate errors

### Test Access Control
1. Try accessing admin features as subscriber
2. Try moderating avatars without permissions
3. Try accessing other users' avatars
4. Verify all blocked appropriately

### Test SQL Injection
1. Use special characters in search/filter
2. Try SQL injection in form fields
3. Verify inputs properly sanitized

## CI/CD Integration

This staging environment is used for:
- Manual QA before releases
- Integration test validation
- Demo recordings for marketing
- Training new developers

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Port already in use | Change `WORDPRESS_PORT` in `.env` |
| Database connection error | Wait 60 seconds for MySQL to initialize |
| Plugin not found | Check volume mounts in `docker-compose.dev.yml` |
| Permission errors | Run `chmod -R 755 wp-content/uploads` |
| Changes not reflected | Clear browser cache or use incognito |

## Maintenance

```bash
# Update WordPress version
# Edit docker-compose.dev.yml and change wordpress:6.8.3-php8.1

# Backup database
docker compose -f docker-compose.dev.yml exec db mysqldump -u root -proot_password avatarsteward > backup.sql

# Restore database
docker compose -f docker-compose.dev.yml exec -T db mysql -u root -proot_password avatarsteward < backup.sql
```

## Next Steps

After testing in staging:
1. Document any issues found
2. Create bug reports with screenshots
3. Update test documentation
4. Prepare for production release
