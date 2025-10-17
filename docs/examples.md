# Usage Examples - Avatar Steward

## Example 1: Basic Configuration

```php
// In your theme's functions.php, customize the avatar size
add_filter('avatarsteward_default_size', function($size) {
    return 150; // 150px
});
```

## Example 2: Hook for Automatic Moderation

```php
// Automatically approve avatars for administrators
add_action('avatarsteward_avatar_uploaded', function($user_id, $attachment_id) {
    if (user_can($user_id, 'administrator')) {
        // Mark as approved
        update_post_meta($attachment_id, '_avatar_approved', '1');
    }
}, 10, 2);
```

## Example 3: Customize Initials Generator

```php
// Change default colors
add_filter('avatarsteward_initials_colors', function($colors) {
    return ['#FF5733', '#33FF57', '#3357FF']; // Your colors
});
```

## Use Cases

### Educational Site
- Use the Pro library to assign thematic avatars per course.
- Moderate avatars to maintain an appropriate environment.

### Professional Community
- Integrate with LinkedIn to import verified avatars.
- Configure verification badges for premium members.

### Large Forum
- Enable automatic moderation to reduce administrative load.
- Use exportable audit for compliance.

For more advanced examples, refer to the development documentation in `documentacion/`.