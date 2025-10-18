# User Manual - Avatar Steward

## Introduction

Avatar Steward is a WordPress plugin that allows managing user avatars locally, without depending on external services like Gravatar. This provides better privacy, performance, and control over user profile images on your WordPress site.

**Key Benefits:**
- **Privacy**: No external requests to Gravatar or other services
- **Performance**: Faster page loads without external dependencies
- **Control**: Complete control over avatar uploads, formats, and moderation
- **Flexibility**: Customizable settings for different site requirements

## System Requirements

Before installing Avatar Steward, ensure your system meets these requirements:

- **WordPress**: Version 5.8 or higher
- **PHP**: Version 7.4 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Database**: MySQL 5.6+ or MariaDB 10.0+
- **Disk Space**: Minimum 50 MB for avatars and plugin files
- **Permissions**: Write access to `wp-content/uploads/` directory

## Installation and Activation

### Standard WordPress Installation

1. **Download** the plugin ZIP file from your purchase source
2. **Navigate** to WordPress admin panel
3. Go to **Plugins > Add New**
4. Click **"Upload Plugin"** button at the top
5. Click **"Choose File"** and select the Avatar Steward ZIP
6. Click **"Install Now"** and wait for upload to complete
7. Click **"Activate Plugin"** when installation finishes
8. Look for the success message confirming activation

### Docker Development Installation

For developers or testing environments:

1. **Prerequisites**:
   ```bash
   # Ensure Docker is installed
   docker --version
   
   # Clone the repository
   git clone https://github.com/JPMarichal/avataria.git
   cd avataria
   ```

2. **Configure environment**:
   ```bash
   # Copy environment template
   cp .env.example .env
   
   # Optionally edit .env to customize ports
   nano .env
   ```

3. **Start containers**:
   ```bash
   # Install dependencies (optional for development)
   composer install
   npm install
   
   # Start Docker environment
   docker compose -f docker-compose.dev.yml up -d
   ```

4. **Access WordPress**:
   - URL: `http://localhost:8080`
   - Username: `admin`
   - Password: `admin`

5. **Activate plugin**:
   - Go to **Plugins** in WordPress admin
   - Find **Avatar Steward** and click **Activate**

## Basic Configuration

### Accessing Settings

1. Log in to WordPress admin
2. Navigate to **Settings > Avatar Steward**
3. You'll see two main configuration sections

### Upload Restrictions

Control how users can upload avatar images:

#### Maximum File Size

- **Purpose**: Limits avatar file sizes to prevent storage issues
- **Range**: 0.1 MB to 10 MB
- **Default**: 2.0 MB
- **Recommendation**: 
  - Small sites: 1-2 MB
  - Large sites with good hosting: 3-5 MB
  - Sites with limited storage: 0.5-1 MB

#### Allowed Image Formats

- **Purpose**: Controls which image types users can upload
- **Options**: JPEG, PNG, GIF, WebP
- **Default**: JPEG and PNG
- **Recommendations**:
  - **JPEG**: Best for photos, good compression
  - **PNG**: Best for graphics with transparency
  - **GIF**: For simple animations (not recommended for avatars)
  - **WebP**: Modern format with excellent compression (requires PHP 7.4+)

#### Maximum Dimensions

- **Purpose**: Prevents oversized images that impact performance
- **Range**: 100 to 5000 pixels
- **Default**: 2048 x 2048 pixels
- **Recommendations**:
  - Most sites: 1024x1024px or 2048x2048px
  - High-DPI displays: 2048x2048px
  - Performance-focused: 512x512px or 1024x1024px

#### Convert to WebP

- **Purpose**: Automatically convert uploads to WebP for better compression
- **Default**: Disabled
- **When to enable**: 
  - Modern sites targeting recent browsers
  - Performance optimization is priority
  - You have PHP GD or Imagick with WebP support
- **When to disable**:
  - Compatibility with older browsers is required
  - You need to preserve original image formats

### Roles & Permissions

Control who can upload avatars and whether moderation is needed:

#### Allowed Roles

- **Purpose**: Restrict avatar uploads to specific user roles
- **Options**: Administrator, Editor, Author, Contributor, Subscriber
- **Default**: All roles enabled
- **Common Configurations**:
  - **Open**: All roles (default) - suitable for community sites
  - **Moderate**: Subscriber and above - prevents spam accounts
  - **Restricted**: Author and above - for editorial sites
  - **Admin Only**: Administrator only - for managed corporate sites

#### Require Approval

- **Purpose**: Enable moderation queue for new avatars
- **Default**: Disabled
- **When to enable**:
  - Community sites with public registration
  - Sites requiring content moderation
  - Educational institutions with student users
  - Any site concerned about inappropriate content
- **When to disable**:
  - Small, trusted user base
  - Private/internal sites
  - Sites with trusted user registration process

### Saving Your Configuration

1. Review your settings in both sections
2. Scroll to the bottom of the page
3. Click **"Save Changes"**
4. Wait for the success message
5. Settings are now active for all users

**Important**: Settings are validated automatically. If you enter invalid values (e.g., file size over 10 MB), you'll see an error message and settings won't be saved.

## Usage for Users

### Uploading Your Avatar

1. **Access your profile**:
   - Click your name in the admin bar
   - Or go to **Users > Your Profile**

2. **Locate the Avatar section**:
   - Scroll down to find the "Avatar" or "Profile Picture" section
   - You'll see your current avatar (or default placeholder)

3. **Choose your image**:
   - Click **"Choose file"** or **"Browse"**
   - Select an image from your computer
   - Ensure it meets the site's requirements:
     - File size within limit (shown on the page)
     - Correct format (JPEG, PNG, etc.)
     - Reasonable dimensions

4. **Upload**:
   - Click **"Update Profile"** at the bottom
   - Wait for the page to reload
   - Your new avatar appears immediately (unless moderation is required)

### Error Messages

If upload fails, you'll see one of these messages:

- **"File too large"**: Reduce image size or compress it
- **"Invalid format"**: Convert to JPEG or PNG
- **"Dimensions too large"**: Resize image to smaller dimensions
- **"Upload failed"**: Check file isn't corrupted, try again

### Removing Your Avatar

1. Go to **Users > Your Profile**
2. Find the Avatar section
3. Click **"Remove Avatar"** button
4. Confirm when prompted
5. Click **"Update Profile"**
6. You'll revert to the default avatar (initials or site default)

### Where Your Avatar Appears

Once uploaded, your avatar appears in:
- Comment sections
- Author bylines on posts
- User lists in admin
- Admin toolbar
- BuddyPress/bbPress profiles (if installed)
- Any theme location using `get_avatar()`

## Moderation (Administrators)

### Managing User Avatars

As an administrator, you can upload avatars for any user:

1. Go to **Users** in WordPress admin
2. Hover over a user and click **Edit**
3. Scroll to the **Avatar** section
4. Upload or remove avatars as needed
5. Click **"Update User"** to save

### Moderating Avatars (When Approval Required)

When **Require Approval** is enabled in settings:

1. Go to **Avatar Steward > Moderation** (Pro feature)
2. View pending avatar uploads
3. For each avatar:
   - Review the image
   - Check against site policies
   - Click **Approve** or **Reject**
4. Approved avatars become visible immediately
5. Rejected avatars notify the user to upload a different image

**Note**: Moderation features are available in the Pro version.

## Initials Generator (Coming Soon)

If users don't upload custom avatars, Avatar Steward can automatically generate avatars with their initials:

- Extracts initials from user's display name
- Assigns consistent colors based on user ID
- Provides professional appearance without uploads
- Customizable colors and styles in settings (Pro feature)

## Pro Version Features (Coming Soon)

### Avatar Library
- Access a curated collection of pre-designed avatars
- Assign default avatars to new users
- Sectorial templates for different industries

### Social Integrations
- Import avatars from Twitter, Facebook, LinkedIn
- OAuth authentication flow
- One-click social avatar import

### Advanced Moderation
- Bulk approve/reject avatars
- Automated content scanning
- Audit logs and export reports
- Email notifications for new uploads

### Multi-Avatar Support
- Users can upload multiple avatars
- Switch between avatars easily
- Context-specific avatars (work vs. casual)

## Troubleshooting

### Avatars Not Showing

1. **Check plugin activation**: Ensure Avatar Steward is active in **Plugins**
2. **Verify permissions**: Ensure `wp-content/uploads/` is writable
3. **Clear cache**: Clear any caching plugins or CDN cache
4. **Check theme compatibility**: Ensure theme uses `get_avatar()` function

### Upload Errors

1. **"Permission denied"**:
   - Check folder permissions: `wp-content/uploads/` should be writable (755 or 775)
   - Contact your hosting provider if needed

2. **"File too large"**:
   - Compress your image using tools like TinyPNG
   - Or ask administrator to increase the limit

3. **"Invalid format"**:
   - Convert image to JPEG or PNG
   - Check if file is corrupted

### Performance Issues

1. **Slow page loads**:
   - Reduce max dimensions in settings
   - Enable WebP conversion
   - Use CDN for avatar delivery

2. **Storage issues**:
   - Reduce max file size limit
   - Enable WebP conversion (typically 25-35% smaller)
   - Periodically clean old/unused avatars

### Docker Environment Issues

1. **Port conflicts**:
   ```bash
   # Edit .env file
   WORDPRESS_PORT=8090
   PHPMYADMIN_PORT=8091
   ```

2. **Container won't start**:
   ```bash
   # Check logs
   docker compose -f docker-compose.dev.yml logs wordpress
   
   # Restart containers
   docker compose -f docker-compose.dev.yml restart
   ```

3. **Permission issues** (Linux):
   ```bash
   sudo chown -R $USER:$USER .
   ```

## Best Practices

### For Site Administrators

1. **Set realistic limits**: Balance quality with storage/performance
2. **Enable moderation**: For public sites with open registration
3. **Regular backups**: Include `wp-content/uploads/` in backups
4. **Monitor storage**: Check disk space regularly
5. **Update settings**: Adjust as your site grows

### For Users

1. **Optimize images**: Compress before uploading
2. **Use square images**: Avatars display best as squares
3. **Choose clear photos**: Ensure face is visible at small sizes
4. **Respect guidelines**: Follow site's content policies
5. **Test display**: Check how avatar looks in different contexts

## Getting Help

### Documentation

- **Installation Guide**: See Installation section above
- **Settings Reference**: See Configuration section above
- **FAQ**: Check `docs/faq.md` for common questions
- **Developer Docs**: See `docs/` directory for API reference

### Support Channels

- **GitHub Issues**: For bugs and feature requests
- **Documentation**: Check `docs/` folder in plugin directory
- **WordPress Forums**: Community support (free version)
- **Email Support**: Premium support (Pro version)

### Before Asking for Help

1. Check this manual for relevant section
2. Review the FAQ document
3. Verify system requirements
4. Test with default WordPress theme
5. Disable other plugins temporarily
6. Check browser console for errors

## Additional Resources

- **WordPress Codex**: Avatar and user profile documentation
- **Plugin Repository**: Updates and version history
- **Developer Hooks**: See `docs/developer-guide.md`
- **Security Best Practices**: See `docs/security-report.md`