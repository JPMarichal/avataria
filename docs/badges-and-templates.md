# Verification Badges and Sectoral Templates

## Overview

Avatar Steward Pro includes two powerful features for enhancing avatar management:

1. **Verification Badges**: Visual indicators that can be assigned to avatars or users to denote special status, roles, or verification
2. **Sectoral Templates**: Pre-configured settings optimized for specific industries and use cases

These features help site administrators establish trust, organize content, and maintain consistency across their platform.

---

## Verification Badges

### What are Verification Badges?

Verification badges are visual indicators (icons) that appear next to avatars to denote:
- Verified users or content
- Special roles (moderators, authors, etc.)
- Premium membership status
- Custom designations

Badges provide a simple digital signature system to prevent falsification and build trust in your community.

### Badge Types

#### Built-in Badge Types

| Badge Type | Icon | Color | Use Case |
|-----------|------|-------|----------|
| **Verified** | ✓ Checkmark | Blue (#0073aa) | Verified users or content |
| **Moderator** | 🛡️ Shield | Red (#d63638) | Site moderators and admins |
| **Author** | ✏️ Edit | Green (#00a32a) | Content creators and authors |
| **Premium** | ⭐ Star | Gold (#f0b849) | Premium or paid members |

#### Custom Badges

Administrators can create custom badges with:
- Custom name and description
- Any WordPress Dashicon
- Custom hex color
- Flexible use cases

### Assigning Badges

#### Manual Assignment

**Via Admin UI:**
1. Navigate to Settings > Avatar Steward > Library
2. Find the avatar in the library grid
3. Click the "Assign Badge" button
4. Select badge type or create custom badge
5. Click "Assign"

**Via REST API:**
```bash
POST /wp-json/avatar-steward/v1/library/{avatar_id}/badge
Content-Type: application/json

{
  "badge_type": "verified",
  "custom_data": {
    "name": "Expert",
    "description": "Industry expert",
    "icon": "dashicons-awards",
    "color": "#7e3bd0"
  }
}
```

**Via PHP:**
```php
$badge_service = new AvatarSteward\Domain\Library\BadgeService();

// Assign predefined badge
$badge_service->assign_badge_to_avatar( $attachment_id, 'verified' );

// Assign custom badge
$badge_service->assign_badge_to_avatar( 
    $attachment_id, 
    'custom',
    array(
        'name'        => 'Expert',
        'description' => 'Industry expert',
        'icon'        => 'dashicons-awards',
        'color'       => '#7e3bd0',
    )
);
```

#### Automatic Assignment

Badges can be automatically assigned based on user roles:

```php
$badge_service = new AvatarSteward\Domain\Library\BadgeService();

// Auto-assign badge based on user role
$badge_service->auto_assign_badge_by_role( $user_id );
```

**Default Role Mappings:**
- `administrator` → Moderator badge
- `editor` → Moderator badge
- `author` → Author badge

You can customize this behavior using WordPress hooks:

```php
add_filter( 'avatar_steward_role_badge_map', function( $map ) {
    $map['shop_manager'] = 'premium';
    $map['teacher']      = 'author';
    return $map;
});
```

### Removing Badges

**Via Admin UI:**
1. Navigate to the avatar in the library
2. Click "Remove Badge"
3. Confirm the action

**Via REST API:**
```bash
DELETE /wp-json/avatar-steward/v1/library/{avatar_id}/badge
```

**Via PHP:**
```php
$badge_service->remove_badge_from_avatar( $attachment_id );
```

### Badge Display

Badges are rendered as HTML elements with Dashicons:

```php
$badge_service = new AvatarSteward\Domain\Library\BadgeService();
$badge_data    = $badge_service->get_avatar_badge( $attachment_id );

if ( $badge_data ) {
    echo $badge_service->render_badge( $badge_data, 20 ); // 20px size
}
```

**Output:**
```html
<span class="avatar-steward-badge dashicons dashicons-yes-alt" 
      style="color: #0073aa; font-size: 20px; width: 20px; height: 20px;" 
      title="Verified"></span>
```

### Badge Security

Badges are stored securely in WordPress post metadata with:
- Timestamp of assignment
- ID of user who assigned the badge
- Complete audit trail

This creates a simple but effective "digital signature" that:
- Prevents unauthorized badge assignment
- Provides accountability
- Enables audit logging
- Helps detect badge forgery attempts

---

## Sectoral Templates

### What are Sectoral Templates?

Sectoral templates are pre-configured plugin settings optimized for specific industries and use cases. Each template includes:
- Recommended moderation settings
- Appropriate user role permissions
- File size limits
- Industry-specific tags
- Default metadata

### Available Templates

#### 1. eLearning

**Best for:** Online courses, educational platforms, learning management systems

**Settings:**
- Moderation: **Enabled** (review student uploads)
- Allowed roles: Subscriber, Student, Teacher, Administrator
- Max file size: 1.5 MB
- Tags: education, learning, academic

**Use cases:**
- University portals
- Online course platforms (Udemy-style)
- Educational communities
- Student forums

---

#### 2. eCommerce

**Best for:** Online stores, marketplaces, product catalogs

**Settings:**
- Moderation: **Disabled** (fast customer experience)
- Allowed roles: Customer, Shop Manager, Administrator
- Max file size: 2.0 MB
- Tags: shop, store, commerce

**Use cases:**
- WooCommerce stores
- Multi-vendor marketplaces
- Product review sites
- Customer portals

---

#### 3. Community Forum

**Best for:** Discussion boards, Q&A sites, community platforms

**Settings:**
- Moderation: **Enabled** (maintain quality)
- Allowed roles: Subscriber, Contributor, Moderator, Administrator
- Max file size: 1.0 MB
- Tags: forum, community, discussion

**Use cases:**
- bbPress forums
- BuddyPress communities
- Support forums
- Discussion boards

---

#### 4. Membership Site

**Best for:** Subscription platforms, exclusive content, member areas

**Settings:**
- Moderation: **Disabled** (trust paid members)
- Allowed roles: Subscriber, Member, Administrator
- Max file size: 2.0 MB
- Tags: membership, subscription, members

**Use cases:**
- Paid membership sites
- Subscription platforms
- Exclusive communities
- Member-only areas

---

#### 5. Corporate

**Best for:** Business websites, intranets, employee portals

**Settings:**
- Moderation: **Enabled** (professional standards)
- Allowed roles: Employee, Manager, Administrator
- Max file size: 2.0 MB
- Tags: corporate, business, professional

**Use cases:**
- Company intranets
- Employee directories
- Corporate blogs
- Business networks

---

#### 6. Healthcare

**Best for:** Medical platforms, health portals, patient management

**Settings:**
- Moderation: **Enabled** (HIPAA compliance consideration)
- Allowed roles: Patient, Doctor, Administrator
- Max file size: 1.5 MB
- Tags: healthcare, medical, health

**Use cases:**
- Patient portals
- Telemedicine platforms
- Health forums
- Medical directories

---

### Applying Templates

#### Via PHP

```php
$template_service = new AvatarSteward\Domain\Library\SectoralTemplateService();

// Apply a template
$template_service->apply_template( 'elearning' );

// Get active template
$active = $template_service->get_active_template();

// Clear template
$template_service->clear_template();
```

#### Preview Template Settings

```php
$preview = $template_service->get_template_preview( 'forum' );

// Returns:
array(
    'sector'      => 'forum',
    'name'        => 'Community Forum',
    'description' => 'Templates for community forums...',
    'tags'        => array( 'forum', 'community', 'discussion' ),
    'settings'    => array(
        'require_approval' => true,
        'allowed_roles'    => array( 'subscriber', 'contributor', ... ),
        'max_file_size'    => 1.0,
    ),
)
```

### Importing Sectoral Avatars

Bulk import avatars with sectoral metadata:

```php
$template_service = new AvatarSteward\Domain\Library\SectoralTemplateService();

$files = array(
    '/path/to/avatar1.jpg',
    '/path/to/avatar2.png',
    '/path/to/avatar3.jpg',
);

$result = $template_service->import_sectoral_avatars( 'ecommerce', $files );

// Returns:
array(
    'success' => 3,
    'failed'  => 0,
    'errors'  => array(),
)
```

### Custom Templates

Extend the template system with custom configurations:

```php
add_filter( 'avatar_steward_sectoral_templates', function( $templates ) {
    $templates['nonprofit'] = array(
        'name'        => 'Non-Profit Organization',
        'description' => 'Templates for NGOs and non-profit organizations',
        'tags'        => array( 'nonprofit', 'ngo', 'charity' ),
        'settings'    => array(
            'require_approval' => false,
            'allowed_roles'    => array( 'volunteer', 'staff', 'administrator' ),
            'max_file_size'    => 1.0,
        ),
    );
    return $templates;
});
```

---

## Integration Examples

### Example 1: Auto-Badge on Upload

Automatically assign a badge when a user uploads an avatar:

```php
add_action( 'avatar_steward_avatar_uploaded', function( $user_id, $attachment_id ) {
    $user = get_userdata( $user_id );
    
    // Verified badge for editors
    if ( in_array( 'editor', $user->roles, true ) ) {
        $badge_service = new AvatarSteward\Domain\Library\BadgeService();
        $badge_service->assign_badge_to_avatar( $attachment_id, 'verified' );
    }
}, 10, 2 );
```

### Example 2: Template-Based Onboarding

Apply different templates based on site purpose:

```php
function setup_site_for_sector( $sector ) {
    $template_service = new AvatarSteward\Domain\Library\SectoralTemplateService();
    
    if ( $template_service->template_exists( $sector ) ) {
        $template_service->apply_template( $sector );
        
        // Log the change
        error_log( sprintf( 
            'Avatar Steward configured for %s sector', 
            $sector 
        ) );
        
        return true;
    }
    
    return false;
}

// Usage:
setup_site_for_sector( 'elearning' );
```

### Example 3: Badge-Based Access Control

Grant special permissions based on badges:

```php
add_filter( 'user_has_cap', function( $allcaps, $caps, $args, $user ) {
    $badge_service = new AvatarSteward\Domain\Library\BadgeService();
    $badge         = $badge_service->get_user_badge( $user->ID );
    
    // Premium badge holders get extra capabilities
    if ( $badge && 'premium' === $badge['type'] ) {
        $allcaps['upload_large_avatars'] = true;
        $allcaps['bypass_moderation']    = true;
    }
    
    return $allcaps;
}, 10, 4 );
```

---

## Best Practices

### Badge Usage

1. **Be Consistent**: Use the same badge types across your site
2. **Document Badges**: Explain what each badge means in your community guidelines
3. **Limit Badge Types**: Too many badge types can confuse users
4. **Review Regularly**: Audit badge assignments periodically
5. **Use Automation**: Set up auto-assignment for common roles

### Template Usage

1. **Choose Early**: Apply templates during initial site setup
2. **Test Thoroughly**: Preview template settings before applying
3. **Document Changes**: Keep records of template applications
4. **Monitor Impact**: Track how templates affect user behavior
5. **Adjust as Needed**: Templates are starting points, customize as you grow

### Security Considerations

1. **Restrict Badge Assignment**: Only administrators should assign badges
2. **Audit Badge Changes**: Log all badge assignments/removals
3. **Validate Custom Data**: Always sanitize custom badge data
4. **Monitor Abuse**: Watch for badge impersonation attempts
5. **Use HTTPS**: Ensure badge data is transmitted securely

---

## Troubleshooting

### Badges Not Appearing

**Problem**: Badges are assigned but not visible

**Solutions**:
1. Check that the avatar is in the library (has library meta flag)
2. Verify the badge service is initialized in Plugin.php
3. Clear WordPress transient cache
4. Check browser console for JavaScript errors
5. Verify Dashicons are loaded on the page

### Template Not Applying

**Problem**: Template settings don't take effect

**Solutions**:
1. Check user permissions (need `manage_options` capability)
2. Verify template exists using `template_exists()`
3. Check for plugin conflicts that might override settings
4. Clear all caches (object cache, page cache, etc.)
5. Verify settings are saved in `avatar_steward_settings` option

### Custom Badge Not Displaying

**Problem**: Custom badges don't show correct icon or color

**Solutions**:
1. Verify icon class is a valid Dashicon name
2. Check color is a valid hex code (e.g., #ff0000)
3. Ensure custom_data is properly sanitized
4. Check CSS conflicts that might override styles
5. Use browser dev tools to inspect rendered HTML

---

## API Reference

### BadgeService Methods

```php
// Get badge types
$types = $badge_service->get_badge_types();

// Check if badge type is valid
$is_valid = $badge_service->is_valid_badge_type( 'verified' );

// Assign badge to avatar
$result = $badge_service->assign_badge_to_avatar( $attachment_id, 'verified' );

// Assign badge to user
$result = $badge_service->assign_badge_to_user( $user_id, 'moderator' );

// Remove badge from avatar
$result = $badge_service->remove_badge_from_avatar( $attachment_id );

// Remove badge from user
$result = $badge_service->remove_badge_from_user( $user_id );

// Get avatar badge
$badge = $badge_service->get_avatar_badge( $attachment_id );

// Get user badge
$badge = $badge_service->get_user_badge( $user_id );

// Auto-assign by role
$result = $badge_service->auto_assign_badge_by_role( $user_id );

// Render badge HTML
$html = $badge_service->render_badge( $badge_data, 20 );
```

### SectoralTemplateService Methods

```php
// Get all templates
$templates = $template_service->get_templates();

// Get single template
$template = $template_service->get_template( 'elearning' );

// Check if template exists
$exists = $template_service->template_exists( 'forum' );

// Apply template
$result = $template_service->apply_template( 'ecommerce' );

// Get active template
$active = $template_service->get_active_template();

// Clear template
$result = $template_service->clear_template();

// Get template preview
$preview = $template_service->get_template_preview( 'healthcare' );

// Import sectoral avatars
$result = $template_service->import_sectoral_avatars( 'corporate', $files );
```

---

## Future Enhancements

Planned features for future releases:

### Badges
- Badge expiration dates
- Badge achievement system (unlock badges through actions)
- Badge categories and hierarchies
- Public badge directory
- Badge export/import
- Multi-badge support (assign multiple badges to one avatar)

### Templates
- Template marketplace
- Template versioning
- Template inheritance (extend existing templates)
- A/B testing for template settings
- Template analytics
- Migration tools between templates

---

## Support

For questions or issues related to badges and templates:

1. Check this documentation
2. Review [Avatar Library Documentation](avatar-library.md)
3. Check [GitHub Issues](https://github.com/JPMarichal/avataria/issues)
4. Contact support via the plugin support forum

---

## Changelog

### Version 1.0.0
- Initial release of verification badges
- Initial release of sectoral templates
- 4 built-in badge types
- 6 sectoral templates
- REST API support for badges
- Complete documentation
