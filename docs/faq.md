# FAQ - Avatar Steward

## General Questions

### What is Avatar Steward?

Avatar Steward is a WordPress plugin that allows uploading and managing user avatars locally, without relying on external services like Gravatar. It provides complete control over user profile images with advanced moderation, customization options, and privacy protection.

### Is it compatible with my theme?

Yes, Avatar Steward works with any WordPress theme that uses the standard `get_avatar()` function. This includes virtually all modern WordPress themes. The plugin seamlessly integrates with WordPress's native avatar system, so your theme doesn't need any modifications.

### Do I need Gravatar?

No, Avatar Steward completely replaces Gravatar with local avatars. Once activated, the plugin handles all avatar requests locally, eliminating external dependencies. This improves privacy, performance, and gives you full control over avatar management.

### What's the difference between Free and Pro versions?

**Free (MVP) Version includes:**
- Local avatar uploads
- Settings page with upload restrictions
- Role-based permissions
- WordPress Settings API integration
- Basic moderation controls

**Pro Version adds** (coming soon):
- Avatar library with pre-designed avatars
- Social media integrations (Twitter, Facebook, LinkedIn)
- Advanced moderation dashboard
- Multiple avatars per user
- Audit logs and reporting
- Priority support

### Is my data safe and private?

Yes! Avatar Steward is designed with privacy in mind:
- All avatars stored locally on your server
- No external requests to Gravatar or other services
- MIME type validation prevents malicious uploads
- File size and dimension restrictions
- Capability-based access control
- GPL-licensed with open source code

## Installation and Configuration

### How do I install the plugin?

**Standard WordPress installation:**
1. Download the plugin ZIP from your purchase source
2. Go to **Plugins > Add New > Upload Plugin**
3. Select the ZIP file and click **Install Now**
4. Click **Activate Plugin** after installation

**Docker development installation:**
```bash
git clone https://github.com/JPMarichal/avataria.git
cd avataria
cp .env.example .env
docker compose -f docker-compose.dev.yml up -d
```
Then access WordPress at `http://localhost:8080` (admin/admin)

### What are the system requirements?

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and plugin files
- **Permissions**: Write access to `wp-content/uploads/` directory

### How do I configure the avatar size and formats?

1. Go to **Settings > Avatar Steward** in WordPress admin
2. In the **Upload Restrictions** section:
   - **Max File Size**: Set between 0.1 MB and 10 MB (default: 2 MB)
   - **Allowed Formats**: Check JPEG, PNG, GIF, and/or WebP
   - **Max Dimensions**: Set width and height (default: 2048x2048px)
   - **Convert to WebP**: Enable for better compression
3. Click **Save Changes**

### How do I set up Docker for development?

1. **Install Docker Desktop** (Windows/Mac) or Docker Engine (Linux)
2. **Clone the repository**:
   ```bash
   git clone https://github.com/JPMarichal/avataria.git
   cd avataria
   ```
3. **Configure environment**:
   ```bash
   cp .env.example .env
   # Optionally edit .env to change ports
   ```
4. **Start containers**:
   ```bash
   docker compose -f docker-compose.dev.yml up -d
   ```
5. **Access WordPress**: `http://localhost:8080` (admin/admin)
6. **Stop environment**: `docker compose -f docker-compose.dev.yml down`

### Can I change the Docker ports?

Yes! Edit the `.env` file:
```bash
WORDPRESS_PORT=8090      # Change from 8080
PHPMYADMIN_PORT=8091     # Change from 8081
```
Then restart: `docker compose -f docker-compose.dev.yml up -d`

### What if port 8080 is already in use?

Edit `.env` and change `WORDPRESS_PORT` to an available port (e.g., 8090, 9000). Then restart the Docker environment.

## Usage and Features

### How do users upload avatars?

1. User logs into WordPress
2. Goes to **Users > Your Profile**
3. Finds the **Avatar** section
4. Clicks **"Choose file"** and selects an image
5. Clicks **"Update Profile"** to save
6. Avatar appears site-wide immediately (unless moderation is required)

### What happens if users don't upload an avatar?

The plugin will automatically generate an avatar with the user's initials (coming soon in MVP). The initials are extracted from the user's display name and shown with a consistent color. Alternatively, you can configure a custom default avatar in settings.

### How do I moderate avatars?

If you've enabled **Require Approval** in settings:

1. New avatar uploads enter a moderation queue
2. Go to **Avatar Steward > Moderation** (Pro feature)
3. Review each pending avatar
4. Click **Approve** or **Reject**
5. Approved avatars become visible immediately
6. Users are notified of rejections

**Note**: Full moderation dashboard is a Pro feature. The free version has the toggle but requires Pro for the full workflow.

### Can administrators upload avatars for users?

Yes! Administrators can:
1. Go to **Users** in WordPress admin
2. Click **Edit** on any user
3. Upload or remove avatars in the Avatar section
4. Click **Update User** to save

### What image formats are supported?

The plugin supports:
- **JPEG** (.jpg, .jpeg) - Best for photos
- **PNG** (.png) - Best for graphics with transparency
- **GIF** (.gif) - For simple images (not recommended for avatars)
- **WebP** (.webp) - Modern format with excellent compression

Administrators can enable/disable formats in **Settings > Avatar Steward**.

### What is WebP conversion and should I enable it?

WebP is a modern image format that provides superior compression compared to JPEG/PNG, typically reducing file sizes by 25-35% without quality loss.

**Enable WebP if:**
- You want better performance
- Your server has PHP GD or Imagick with WebP support
- You're targeting modern browsers

**Keep disabled if:**
- You need compatibility with older browsers
- You want to preserve original formats
- Your server doesn't support WebP

### How do I restrict uploads to specific roles?

1. Go to **Settings > Avatar Steward**
2. In **Roles & Permissions** section
3. Under **Allowed Roles**, check only the roles you want to allow
4. Common configurations:
   - **All roles**: Open community sites
   - **Subscriber and above**: Prevent spam accounts
   - **Author and above**: Editorial sites
   - **Administrator only**: Managed corporate sites
5. Click **Save Changes**

## Development and Testing

### How do I run tests?

```bash
# Install dependencies first
composer install

# Run PHPUnit tests
composer test

# Run with coverage report
vendor/bin/phpunit --coverage-html docs/reports/tests/coverage-html
```

### How do I run linting?

```bash
# PHP linting (WordPress Coding Standards)
composer lint

# Auto-fix PHP issues
vendor/bin/phpcbf

# JavaScript linting
npm run lint

# Auto-fix JS issues
npm run lint:fix
```

### What testing tools are included?

- **PHPUnit**: Unit and integration tests (24+ tests)
- **PHP_CodeSniffer**: WordPress Coding Standards enforcement
- **ESLint**: JavaScript code quality
- **Docker**: Isolated development environment
- **Composer scripts**: Easy command execution

### Where are test reports saved?

- **Linting reports**: `docs/reports/linting/`
- **Test results**: `docs/reports/tests/`
- **Coverage reports**: `docs/reports/tests/coverage-html/`
- **Security scans**: `docs/reports/security-report*.md`

### How do I contribute to development?

1. Fork the repository
2. Set up Docker environment
3. Create a feature branch
4. Make your changes
5. Run tests: `composer test`
6. Run linting: `composer lint`
7. Submit a pull request

## Pro Version

### What does the Pro version include?

**Pro features** (coming soon):
- **Avatar Library**: Curated collection of pre-designed avatars
- **Social Integrations**: Import from Twitter, Facebook, LinkedIn
- **Advanced Moderation**: Full moderation dashboard with bulk actions
- **Multi-Avatar Support**: Users can have multiple avatars
- **Audit Logs**: Export reports of avatar changes
- **Verification Badges**: Mark verified user avatars
- **Sectorial Templates**: Industry-specific avatar collections
- **Priority Support**: Email support with faster response times

### How do I activate the Pro license?

Once Pro is available:
1. Purchase Pro license
2. Receive license key via email
3. Go to **Settings > Avatar Steward > License**
4. Enter your license key
5. Click **Activate**
6. Pro features unlock immediately

### How long does Pro support last?

Pro version includes:
- 12 months of updates
- 12 months of email support
- License can be renewed annually
- Free version receives community support only

## Support

### Where do I get support?

**Free Version:**
- GitHub Issues: Bug reports and feature requests
- Documentation: Check `docs/` folder
- FAQ: This document
- Community forums

**Pro Version:**
- Email support: Priority response (coming soon)
- Documentation: Complete API reference
- Video tutorials
- Implementation assistance

### How long does support last?

- **Free version**: Community support (best effort)
- **Pro version**: 12 months of premium email support (coming soon)
- **Updates**: Both versions receive security and compatibility updates

### What should I include in a support request?

1. **WordPress version**: Check in **Dashboard > Updates**
2. **PHP version**: Check in **Tools > Site Health**
3. **Plugin version**: Check in **Plugins** list
4. **Error messages**: Copy exact error text
5. **Steps to reproduce**: What you did before the error
6. **Screenshots**: If relevant
7. **Browser/device**: If front-end issue

## Common Issues

### Avatars are not showing

**Troubleshooting steps:**

1. **Check plugin activation**:
   - Go to **Plugins**
   - Ensure Avatar Steward is **Active**

2. **Verify permissions**:
   ```bash
   # Linux/Mac
   chmod 755 wp-content/uploads
   
   # Or use FTP client to set permissions
   ```

3. **Clear cache**:
   - Clear any caching plugin cache
   - Clear browser cache (Ctrl+Shift+Del)
   - Clear CDN cache if using one

4. **Check theme compatibility**:
   - Temporarily switch to a default theme (Twenty Twenty-Four)
   - If avatars work, theme may need updating

5. **Check for conflicts**:
   - Deactivate other avatar plugins
   - Test with minimal plugins active

### Error uploading image

**Common upload errors and solutions:**

1. **"File too large"**:
   - Compress image using TinyPNG or similar
   - Ask admin to increase limit in settings
   - Maximum: 10 MB

2. **"Invalid format"**:
   - Convert to JPEG or PNG
   - Check allowed formats in settings
   - Ensure file isn't corrupted

3. **"Dimensions too large"**:
   - Resize image to smaller dimensions
   - Use image editor or online tool
   - Check max dimensions in settings

4. **"Permission denied"**:
   - Check folder permissions (should be 755 or 775)
   - Contact hosting provider
   - Verify web server can write to uploads directory

5. **"Upload failed"**:
   - Check PHP upload_max_filesize setting
   - Check PHP post_max_size setting
   - Verify disk space available
   - Try a different image

### Conflict with another plugin

**Common plugin conflicts:**

1. **Other avatar plugins**:
   - WP User Avatar
   - Simple Local Avatars
   - Gravatar alternatives
   - **Solution**: Deactivate conflicting plugin

2. **Security plugins**:
   - May block uploads
   - **Solution**: Whitelist Avatar Steward

3. **Cache plugins**:
   - May cache old avatars
   - **Solution**: Clear cache after avatar changes

4. **Image optimization plugins**:
   - May conflict with WebP conversion
   - **Solution**: Disable WebP in one plugin

**Testing for conflicts:**
1. Deactivate all other plugins
2. Test avatar upload
3. If works, reactivate plugins one by one
4. Identify which plugin causes conflict
5. Report compatibility issue

### Docker container won't start

**Troubleshooting Docker issues:**

1. **Port already in use**:
   ```bash
   # Edit .env
   WORDPRESS_PORT=8090
   PHPMYADMIN_PORT=8091
   
   # Restart
   docker compose -f docker-compose.dev.yml up -d
   ```

2. **Check logs**:
   ```bash
   docker compose -f docker-compose.dev.yml logs wordpress
   ```

3. **Remove and recreate**:
   ```bash
   docker compose -f docker-compose.dev.yml down -v
   docker compose -f docker-compose.dev.yml up -d
   ```

4. **Permission issues** (Linux):
   ```bash
   sudo chown -R $USER:$USER .
   ```

### Performance is slow

**Optimization tips:**

1. **Reduce max dimensions**:
   - Set to 1024x1024px instead of 2048x2048px
   - Smaller images load faster

2. **Enable WebP conversion**:
   - 25-35% smaller file sizes
   - Check **Convert to WebP** in settings

3. **Reduce max file size**:
   - Set to 1 MB instead of 2 MB
   - Encourages users to optimize

4. **Use CDN**:
   - Serve avatars from CDN for better performance
   - Configure via caching plugin

5. **Object caching**:
   - Enable Redis or Memcached
   - Reduces database queries

## Best Practices

### For Site Administrators

- **Set realistic limits**: Balance quality with storage and performance
- **Enable moderation**: Especially for public sites with open registration
- **Regular backups**: Include `wp-content/uploads/` in backup strategy
- **Monitor storage**: Check disk space regularly
- **Keep updated**: Update plugin for security and compatibility
- **Test changes**: Use Docker environment before deploying to production

### For Users

- **Optimize images**: Compress before uploading
- **Use square images**: Avatars typically display as circles or squares
- **Choose clear photos**: Face should be visible at small sizes (e.g., 64x64px)
- **Respect guidelines**: Follow your site's content policies
- **Test display**: Check how avatar looks in different contexts

### For Developers

- **Use hooks**: Customize via filters instead of editing core
- **Test thoroughly**: Run `composer test` before committing
- **Follow standards**: Run `composer lint` to check code
- **Document changes**: Update relevant docs
- **Version control**: Use Git for all changes
- **Docker for development**: Use provided Docker environment

## Additional Information

### Changelog

See `CHANGELOG.md` for complete version history and changes.

### License

Avatar Steward is licensed under GPL v2 or later. See `LICENSE.txt` for details.

### Privacy Policy

Avatar Steward does not collect or transmit any user data externally. All avatars and settings are stored locally on your WordPress installation.

### Credits

Avatar Steward is evolved from Simple Local Avatars with extensive refactoring and new features. See `docs/legal/origen-gpl.md` for GPL compliance documentation.

---

**Still have questions?** Check the full documentation in the `docs/` folder or create an issue on GitHub.