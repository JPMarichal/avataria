# Avatar Override System

This document describes the Avatar Steward avatar override system and how it integrates with WordPress.

## Overview

Avatar Steward implements a sophisticated avatar override system that seamlessly replaces WordPress's default Gravatar system with locally uploaded avatars. When a user has uploaded a local avatar, it will be displayed instead of their Gravatar across all areas of WordPress.

## Architecture

### Core Components

1. **AvatarHandler** (`src/AvatarSteward/Core/AvatarHandler.php`)
   - Main class responsible for avatar override logic
   - Hooks into WordPress avatar filters
   - Manages avatar storage and retrieval

2. **WordPress Filters**
   - `pre_get_avatar_data`: Primary filter for avatar data manipulation
   - `get_avatar_url`: Additional compatibility for direct URL requests

### How It Works

1. When WordPress needs to display an avatar (via `get_avatar()` or `get_avatar_url()`), it triggers the `pre_get_avatar_data` filter.

2. AvatarHandler intercepts this filter and:
   - Extracts the user ID from various identifier types (ID, email, WP_User, WP_Comment, WP_Post)
   - Checks if the user has a local avatar stored in user meta (`avatar_steward_avatar`)
   - If found, retrieves the appropriate image size from the WordPress media library
   - Returns the local avatar URL, which WordPress then uses

3. If no local avatar exists, the original arguments are returned unchanged, allowing WordPress to fall back to Gravatar or the default avatar.

## Features

### Automatic Gravatar Fallback

When a user doesn't have a local avatar, the system automatically falls back to Gravatar. This ensures a seamless experience where avatars are always displayed, even for users who haven't uploaded one.

```php
// User with local avatar: displays local image
echo get_avatar( $user_with_avatar, 96 );

// User without local avatar: displays Gravatar
echo get_avatar( $user_without_avatar, 96 );
```

### Multiple Identifier Support

The system supports all WordPress avatar identifier types:

- **User ID** (integer): `get_avatar( 1, 96 )`
- **Email address** (string): `get_avatar( 'user@example.com', 96 )`
- **WP_User object**: `get_avatar( $user, 96 )`
- **WP_Comment object**: `get_avatar( $comment, 96 )`
- **WP_Post object**: `get_avatar( $post, 96 )` (displays author's avatar)

### Intelligent Image Sizing

The system automatically selects the appropriate WordPress image size based on the requested dimensions:

- **≤ 96px**: Uses `thumbnail` (typically 150x150)
- **≤ 300px**: Uses `medium` (typically 300x300)
- **> 300px**: Uses `medium_large` (typically 768x768)

This ensures optimal performance by serving appropriately sized images without unnecessary bandwidth usage.

### WordPress Compatibility

The avatar override system is designed to work seamlessly with:

- **Comments**: Avatars display automatically in comment lists
- **User profiles**: Admin profile pages show local avatars
- **Author archives**: Author pages display author avatars
- **Widgets**: Any widget that displays avatars
- **Themes**: All WordPress themes that use standard avatar functions
- **Plugins**: Compatible with plugins that use WordPress avatar APIs

## Usage

### Basic Usage

Once Avatar Steward is active, no code changes are needed. Standard WordPress avatar functions automatically use local avatars:

```php
// Display avatar for current user
echo get_avatar( get_current_user_id(), 96 );

// Get avatar URL
$avatar_url = get_avatar_url( $user_id, array( 'size' => 128 ) );

// Display avatar in comments
wp_list_comments( array( 'avatar_size' => 64 ) );
```

### Programmatic Avatar Management

For advanced use cases, you can manage avatars programmatically:

```php
$plugin  = AvatarSteward\Plugin::instance();
$handler = $plugin->get_avatar_handler();

// Set a local avatar
$handler->set_local_avatar( $user_id, $attachment_id );

// Check if user has local avatar
if ( $handler->has_local_avatar( $user_id ) ) {
    // User has a local avatar
}

// Remove local avatar
$handler->delete_local_avatar( $user_id );
```

## Storage

Local avatars are stored using WordPress's native systems:

- **User Meta**: Avatar assignment is stored in the `avatar_steward_avatar` user meta key
- **Media Library**: Avatar images are stored as regular WordPress attachments
- **File System**: Physical files are stored in the standard WordPress uploads directory

## Performance

The avatar override system is designed for optimal performance:

1. **Minimal Database Queries**: Uses efficient user meta lookups
2. **WordPress Caching**: Leverages WordPress's built-in object caching
3. **No External Requests**: Eliminates Gravatar API calls when local avatars exist
4. **Optimized Image Sizes**: Serves appropriately sized images based on context

## Hooks and Filters

### WordPress Core Hooks Used

- `pre_get_avatar_data` (priority 10): Main filter for avatar data
- `get_avatar_url` (priority 10): Additional URL filter for compatibility

### Custom Hooks (Future)

The following custom hooks are planned for extensibility:

- `avatarsteward/avatar_set`: Fired when an avatar is assigned to a user
- `avatarsteward/avatar_deleted`: Fired when an avatar is removed
- `avatarsteward/avatar_url`: Filter for modifying avatar URLs

## Testing

The avatar override system includes comprehensive unit tests covering:

- Filter registration and execution
- User ID extraction from various identifier types
- Avatar URL retrieval and fallback logic
- Avatar management operations (set, delete, check)
- Integration with WordPress objects (WP_User, WP_Comment, WP_Post)

Run tests with:

```bash
composer test
```

## Examples

See `examples/avatar-override.php` for comprehensive usage examples including:

- Setting and removing avatars
- Working with different identifier types
- Integration with themes and plugins
- Bulk operations
- Programmatic uploads

## Future Enhancements

Planned improvements to the avatar override system:

1. **Caching**: Transient-based caching for frequently accessed avatars
2. **CDN Support**: Integration with CDN services for avatar delivery
3. **Image Optimization**: Automatic image compression and format conversion
4. **Retina Support**: 2x resolution images for high-DPI displays
5. **Lazy Loading**: Native lazy loading for avatar images
6. **WebP Support**: Modern image format support for better compression

## See Also

- [WordPress Avatar API](https://developer.wordpress.org/reference/functions/get_avatar/)
- [WordPress User Meta](https://developer.wordpress.org/reference/functions/get_user_meta/)
- [WordPress Media Library](https://developer.wordpress.org/reference/functions/wp_get_attachment_image_url/)
