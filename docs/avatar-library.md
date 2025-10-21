# Avatar Library Documentation

## Overview

The Avatar Library is a feature that allows administrators and users to manage a centralized collection of avatar images. Users can select avatars from the library instead of uploading new ones, and administrators can organize avatars by metadata such as author, license, sector, and tags.

## Features

### Core Functionality

1. **Library Management**
   - Store avatars in a centralized library
   - Organize avatars with metadata (author, license, sector, tags)
   - Search and filter avatars
   - Pagination support for large libraries

2. **Admin Interface**
   - Accessible from Settings > Avatar Steward > Library
   - Upload avatars directly to the library
   - Import sectoral templates in bulk
   - View and manage all library avatars
   - Remove avatars from the library

3. **Profile Integration**
   - "Select from Library" button on user profile pages
   - Modal popup with avatar grid
   - Easy selection and preview
   - Works alongside direct upload functionality

4. **REST API**
   - RESTful endpoints for all library operations
   - Supports GET, POST, DELETE methods
   - Built-in authentication and authorization
   - Suitable for future integrations

### Architecture

The avatar library follows the Domain-Driven Design pattern with these key components:

```
src/AvatarSteward/
├── Domain/
│   └── Library/
│       └── LibraryService.php      # Core business logic
├── Admin/
│   ├── LibraryPage.php             # Admin UI
│   └── LibraryRestController.php   # REST API
└── Plugin.php                       # Initialization
```

## Usage

### For Administrators

#### Accessing the Library

1. Navigate to **Settings > Avatar Steward > Library** in WordPress admin
2. The library page displays all avatars with their metadata

#### Uploading Avatars

1. Click the **"Upload Avatar"** button
2. Select an image file
3. Fill in metadata fields:
   - Author (optional)
   - License (optional)
   - Sector (optional)
   - Tags (optional, comma-separated)
4. Click **"Add to Library"**

#### Importing Sectoral Templates

1. Click the **"Import Templates"** button
2. Enter the sector name
3. Select multiple image files
4. Click **"Import Templates"**
5. The system will import all selected images with the sector metadata

#### Managing Library Avatars

- **Search**: Use the search box to find avatars by title
- **Filter**: Select sector or license from dropdowns
- **Remove**: Click the "Remove" button on any avatar to remove it from the library
- **Select**: Click "Select" to use an avatar (for future features)

### For Users

#### Selecting from Library

1. Go to your **Profile** page
2. In the Avatar section, click **"Select from Library"**
3. A modal popup displays available avatars
4. Click on any avatar to select it
5. The selected avatar will be applied to your profile

#### Uploading Custom Avatar

Users can still upload their own avatars using the file upload field, just as before.

## REST API Reference

### Base URL

```
/wp-json/avatar-steward/v1/library
```

### Authentication

All endpoints require authentication. Use WordPress nonces for browser-based requests or application passwords for external integrations.

### Endpoints

#### Get Library Avatars

```
GET /avatar-steward/v1/library
```

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 20, max: 100)
- `search` (string): Search term
- `author` (string): Filter by author
- `license` (string): Filter by license
- `sector` (string): Filter by sector

**Response:**
```json
{
  "items": [
    {
      "id": 123,
      "title": "Avatar Title",
      "url": "https://example.com/avatar.jpg",
      "thumb": "https://example.com/avatar-thumb.jpg",
      "metadata": {
        "author": "John Doe",
        "license": "GPL",
        "sector": "Technology",
        "tags": ["avatar", "tech"]
      }
    }
  ],
  "total": 50,
  "page": 1,
  "per_page": 20,
  "total_pages": 3
}
```

#### Get Single Avatar

```
GET /avatar-steward/v1/library/{id}
```

**Response:**
```json
{
  "id": 123,
  "title": "Avatar Title",
  "url": "https://example.com/avatar.jpg",
  "thumb": "https://example.com/avatar-thumb.jpg",
  "metadata": {
    "author": "John Doe",
    "license": "GPL",
    "sector": "Technology",
    "tags": ["avatar", "tech"]
  }
}
```

#### Add Avatar to Library

```
POST /avatar-steward/v1/library
```

**Request Body:**
```json
{
  "attachment_id": 123,
  "author": "John Doe",
  "license": "GPL",
  "sector": "Technology",
  "tags": ["avatar", "tech"]
}
```

**Response:** Same as Get Single Avatar

#### Remove Avatar from Library

```
DELETE /avatar-steward/v1/library/{id}
```

**Response:**
```json
{
  "deleted": true,
  "avatar": {
    // Avatar data
  }
}
```

#### Get Available Sectors

```
GET /avatar-steward/v1/library/sectors
```

**Response:**
```json
["Technology", "Healthcare", "Education"]
```

#### Get Available Licenses

```
GET /avatar-steward/v1/library/licenses
```

**Response:**
```json
["GPL", "MIT", "CC-BY"]
```

## Performance

### Caching

The library uses WordPress transients to cache query results for 1 hour (3600 seconds). Cache is automatically cleared when:
- An avatar is added to the library
- An avatar is removed from the library

### Database Queries

The library uses WordPress `WP_Query` with optimized arguments:
- Pagination to limit results
- Indexed meta queries for fast filtering
- Efficient thumbnail generation

## Security

### Permissions

- **Read Access**: Any authenticated user can view the library
- **Upload Access**: Users with `upload_files` capability can add avatars
- **Management Access**: Users with `manage_options` capability can remove avatars

### Input Validation

All metadata is sanitized before storage:
- Text fields: `sanitize_text_field()`
- Tags: Array mapping with `sanitize_text_field()`
- Attachment IDs: Integer validation

### Nonce Verification

All AJAX requests require valid WordPress nonces for CSRF protection.

## Developer Hooks

### Filters

```php
// Modify library query arguments
add_filter('avatar_library_query_args', function($args) {
    // Modify $args
    return $args;
});

// Customize cache expiration
add_filter('avatar_library_cache_expiration', function($expiration) {
    return 7200; // 2 hours
});
```

### Actions

```php
// After avatar is added to library
add_action('avatar_library_added', function($attachment_id, $metadata) {
    // Custom logic
}, 10, 2);

// After avatar is removed from library
add_action('avatar_library_removed', function($attachment_id) {
    // Custom logic
}, 10, 1);
```

## Testing

### Unit Tests

Run LibraryService tests:
```bash
composer test -- --filter LibraryService
```

### Integration Tests

Test the admin interface manually:
1. Navigate to Library page
2. Upload an avatar
3. Search and filter
4. Select from profile

### API Tests

Use tools like Postman or cURL to test REST endpoints:
```bash
curl -X GET "https://example.com/wp-json/avatar-steward/v1/library" \
  -H "X-WP-Nonce: YOUR_NONCE"
```

## Troubleshooting

### Library Not Showing Avatars

1. Check that avatars are marked with the `avatar_steward_library_avatar` meta key
2. Verify user has read permissions
3. Clear cache: Delete transients starting with `library_`

### Upload Fails

1. Check file size and type restrictions
2. Verify user has `upload_files` capability
3. Check WordPress media upload settings

### REST API Returns 401

1. Ensure authentication is properly configured
2. Check nonce is being sent correctly
3. Verify user has required permissions

## Future Enhancements

Planned features for future releases:
- Bulk operations (select multiple, delete multiple)
- Advanced filtering (by date, by size)
- Avatar preview before selection
- Integration with external avatar services
- Avatar versioning and history
- Advanced analytics and usage tracking
