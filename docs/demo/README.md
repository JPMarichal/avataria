# Avatar Steward - Demo Environment

This directory contains documentation and configuration for setting up a reproducible demo environment for Avatar Steward plugin reviewers and testers.

## Quick Start

### Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine + Docker Compose (Linux)
- At least 2GB of free RAM
- Ports 8080 and 8081 available

### Starting the Demo

1. **Clone or download the repository:**
   ```bash
   git clone https://github.com/your-org/avatar-steward.git
   cd avatar-steward
   ```

2. **Start the demo environment:**
   ```bash
   docker compose -f docker-compose.demo.yml up -d
   ```

3. **Wait for WordPress to initialize** (approximately 30-60 seconds):
   ```bash
   docker compose -f docker-compose.demo.yml logs -f wordpress
   ```
   Press Ctrl+C when you see "Apache/X.X.X (Unix) configured -- resuming normal operations"

4. **Access WordPress:**
   - WordPress: http://localhost:8080
   - PHPMyAdmin: http://localhost:8081 (optional, for database inspection)

5. **Complete WordPress installation:**
   - Navigate to http://localhost:8080
   - Follow the WordPress installation wizard
   - Create admin account (use any credentials you prefer for testing)

6. **Activate Avatar Steward plugin:**
   - Log in to WordPress admin (http://localhost:8080/wp-admin)
   - Go to Plugins → Installed Plugins
   - Find "Avatar Steward" and click "Activate"

## Testing the Plugin

### Basic Features to Test

1. **Upload Local Avatar:**
   - Go to Users → Your Profile
   - Scroll to "Avatar" section
   - Upload a profile picture
   - Save changes
   - View your profile to confirm avatar appears

2. **Initials Generator (Fallback):**
   - Create a new user without uploading an avatar
   - Check that user's avatar shows their initials
   - Test with different names to see color variations

3. **Avatar Display:**
   - Add a comment to a post to see avatar in comments
   - Check author archives to see avatar
   - View user list to see avatars

4. **Admin Settings:**
   - Go to Settings → Avatar Steward
   - Explore available options (size limits, fallback options, etc.)
   - Adjust settings and verify changes take effect

### Pro Features (if testing Pro version)

5. **Avatar Moderation:**
   - Go to Avatar Steward → Moderation
   - Upload avatars for different users
   - Test approve/reject workflow

6. **Avatar Library:**
   - Go to Avatar Steward → Library
   - Test selecting avatars from predefined options
   - Test category filtering

7. **Social Integration:**
   - Test importing avatars from social profiles
   - Verify caching and performance

## Stopping the Demo

```bash
# Stop containers but keep data
docker compose -f docker-compose.demo.yml stop

# Stop and remove containers (keeps volumes)
docker compose -f docker-compose.demo.yml down

# Remove everything including data (full cleanup)
docker compose -f docker-compose.demo.yml down -v
```

## Troubleshooting

### Port Already in Use

If port 8080 or 8081 is already in use, you can modify the ports in `docker-compose.demo.yml`:

```yaml
services:
  wordpress:
    ports:
      - "8090:80"  # Change 8080 to 8090
```

### Database Connection Errors

If you see database connection errors:

1. Wait longer - MySQL can take 30-60 seconds to initialize
2. Check database container status:
   ```bash
   docker compose -f docker-compose.demo.yml ps
   ```
3. View database logs:
   ```bash
   docker compose -f docker-compose.demo.yml logs demo_db
   ```

### Plugin Not Appearing

If Avatar Steward doesn't appear in the plugins list:

1. Verify the plugin files are mounted correctly:
   ```bash
   docker compose -f docker-compose.demo.yml exec wordpress ls -la /var/www/html/wp-content/plugins/avatar-steward/
   ```
2. Check file permissions
3. Restart containers:
   ```bash
   docker compose -f docker-compose.demo.yml restart
   ```

### Reset Demo Environment

To start fresh:

```bash
docker compose -f docker-compose.demo.yml down -v
docker compose -f docker-compose.demo.yml up -d
```

## Environment Details

### WordPress Configuration

- **WordPress Version:** 6.8.3
- **PHP Version:** 8.1
- **Debug Mode:** Enabled
- **Table Prefix:** `avs_`

### Database Configuration

- **Database:** avatarsteward_demo
- **User:** demo_user
- **Password:** demo_pass_2024
- **Root Password:** demo_root_pass_2024

**Note:** These are demo credentials only. Never use these in production!

### Default Ports

- **WordPress:** 8080
- **PHPMyAdmin:** 8081
- **MySQL:** 3306 (internal only)

## For Reviewers

### Review Checklist

- [ ] Plugin activates without errors
- [ ] Avatar upload works correctly
- [ ] File type validation works (try uploading non-image files)
- [ ] File size limits are enforced
- [ ] Initials generator provides fallback avatars
- [ ] Settings page is accessible and functional
- [ ] No PHP errors in WordPress debug log
- [ ] No JavaScript console errors
- [ ] Avatars display correctly in all contexts (comments, author archives, etc.)
- [ ] Plugin deactivates cleanly
- [ ] Plugin files follow WordPress coding standards

### Accessing Logs

**WordPress debug log:**
```bash
docker compose -f docker-compose.demo.yml exec wordpress tail -f /var/www/html/wp-content/debug.log
```

**Apache error log:**
```bash
docker compose -f docker-compose.demo.yml logs -f wordpress
```

**Database queries (via PHPMyAdmin):**
- Visit http://localhost:8081
- Login with demo credentials
- Use SQL tab to run queries

## Additional Resources

- [WordPress Codex - Plugin API](https://codex.wordpress.org/Plugin_API)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Avatar Steward Documentation](../../README.md)
- [CodeCanyon Quality Standards](../../documentacion/08_CodeCanyon_Checklist.md)

## Support

For issues specific to this demo environment, please check:

1. Docker is running and has sufficient resources
2. All prerequisites are installed
3. Ports are available
4. Logs for specific error messages

For plugin-related issues, refer to the main [README.md](../../README.md) or [FAQ](../faq.md).
