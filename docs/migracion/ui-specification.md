# Avatar Migration Admin Page - UI Specification

This document describes the user interface of the Avatar Migration admin page.

## Page Location

**WordPress Admin Path**: Tools > Avatar Migration  
**Page Slug**: `avatar-steward-migration`  
**Required Capability**: `manage_options` (Administrators only)

## Page Layout

### Header Section

```
┌─────────────────────────────────────────────────────────────────┐
│ Avatar Migration                                                 │
└─────────────────────────────────────────────────────────────────┘

Migrate existing avatars from other plugins or services to Avatar Steward.

┌─────────────────────────────────────────────────────────────────┐
│ ⚠️ WARNING: Always test migrations on a staging site before     │
│    running on production!                                        │
│                                                                   │
│    It is recommended to backup your database before starting     │
│    any migration.                                                │
└─────────────────────────────────────────────────────────────────┘
```

### Results Section (After Migration)

Appears after migration is executed:

**Success Result:**
```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Migration complete! Migrated: 15, Skipped: 5, Total users: 20│
└─────────────────────────────────────────────────────────────────┘
```

**Gravatar Success Result:**
```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Migration complete! Imported: 12, Skipped: 6, Failed: 2,     │
│   Total users: 20                                                │
└─────────────────────────────────────────────────────────────────┘
```

**Error Result:**
```
┌─────────────────────────────────────────────────────────────────┐
│ ✗ Required WordPress functions are not available.               │
└─────────────────────────────────────────────────────────────────┘
```

### Current Statistics Section

```
Current Statistics
──────────────────────────────────────────────────────────────────
┌─────────────────────────────────────────────────────────────────┐
│ Metric                                              │ Count      │
├─────────────────────────────────────────────────────┼───────────┤
│ Total Users                                         │ 20         │
│ Users with Avatar Steward avatars                   │ 15         │
│ Migrated from Simple Local Avatars                  │ 8          │
│ Migrated from WP User Avatar                        │ 5          │
│ Imported from Gravatar                              │ 2          │
├─────────────────────────────────────────────────────┼───────────┤
│ Available Simple Local Avatars (not yet migrated)   │ 3          │
│ Available WP User Avatars (not yet migrated)        │ 2          │
└─────────────────────────────────────────────────────┴───────────┘
```

### Run Migration Form

```
──────────────────────────────────────────────────────────────────
Run Migration
──────────────────────────────────────────────────────────────────

Migration Source:
[Select a source...                                ▼]

Options:
  - Select a source...
  - Simple Local Avatars (3 available)
  - WP User Avatar (2 available)
  - Gravatar (download and import)

ℹ️ Select the source from which you want to migrate avatars. Only
   users without existing Avatar Steward avatars will be processed.

[Start Migration]
```

### Information Section

```
──────────────────────────────────────────────────────────────────
Migration Information
──────────────────────────────────────────────────────────────────

What gets migrated:
• User avatar associations from other plugins
• Uploaded avatar files (they stay in the same location in media library)
• Metadata indicating migration source

What does NOT get migrated:
• Old plugin settings (reconfigure in Avatar Steward settings)
• Historical logs from old plugins
• Plugin-specific custom fields

Migration Sources:

Simple Local Avatars
  Migrates existing avatar associations from the Simple Local Avatars
  plugin. Avatar files remain in the media library.

WP User Avatar
  Migrates avatar associations from the WP User Avatar plugin. Avatar
  files remain in the media library.

Gravatar
  Downloads Gravatars from gravatar.com for all users and saves them
  locally. Only downloads for users who have a Gravatar set. This may
  take some time depending on the number of users.
```

## Visual Elements

### Color Scheme
- Success notices: Green background (#d4edda), green border (#c3e6cb)
- Warning notices: Yellow background (#fff3cd), yellow border (#ffeeba)
- Error notices: Red background (#f8d7da), red border (#f5c6cb)
- Primary button: WordPress blue (#0071a1)

### Form Elements
- Dropdown: Standard WordPress select field with full width
- Submit button: Primary WordPress button style ("Start Migration")

### Typography
- Page title: H1, WordPress admin default
- Section headers: H2, WordPress admin default
- Subsection headers: H3, WordPress admin default
- Description text: P, smaller gray text
- Table: Standard WordPress widefat table class

## User Interaction Flow

### Normal Flow
1. Admin navigates to Tools > Avatar Migration
2. Page loads showing current statistics
3. Admin reviews available avatars for each source
4. Admin selects migration source from dropdown
5. Admin clicks "Start Migration"
6. Page reloads with results notice at top
7. Statistics table updates to reflect changes

### Error Handling
- Invalid migration type: Error notice displayed
- Missing nonce: WordPress nonce verification error
- No WordPress functions: Error notice with friendly message
- Network failure (Gravatar): Shows failed count in results

## Accessibility

- Form labels properly associated with inputs
- Table headers marked with `<th scope="row">` or `<th scope="col">`
- Success/error messages use ARIA live regions (WordPress notices)
- Dropdown has required attribute for validation
- Page title matches `<h1>` heading
- Keyboard navigation fully supported
- Screen reader friendly text and labels

## Responsive Design

- Uses WordPress admin responsive table classes
- Form elements stack appropriately on mobile
- Statistics table scrolls horizontally on small screens
- Notices remain prominent on all screen sizes

## Security Features Visible to User

- Nonce field (hidden from view, added by WordPress)
- "manage_options" capability check (page access denied if not admin)
- CSRF protection through WordPress nonce verification
- Sanitized inputs (transparent to user)

## Performance Indicators

- For Gravatar import: Message noting "This may take some time"
- Results show counts to indicate progress completion
- No loading indicators (synchronous operation)
- Suggested: Future enhancement for async processing with progress bar

## Example Screenshots

### Before Migration
```
[ Page shows statistics with available avatars ]
Available Simple Local Avatars: 10
Available WP User Avatars: 5
```

### After Simple Local Avatars Migration
```
✓ Migration complete! Migrated: 10, Skipped: 0, Total users: 50

[ Statistics updated ]
Users with Avatar Steward avatars: 10
Migrated from Simple Local Avatars: 10
Available Simple Local Avatars (not yet migrated): 0
```

### After Running Both Migrations
```
[ Statistics show combined results ]
Users with Avatar Steward avatars: 15
Migrated from Simple Local Avatars: 10
Migrated from WP User Avatar: 5
```

## Developer Notes

### Hooks for Customization
- No custom hooks exposed yet
- Future: `avatar_steward_before_migration` action
- Future: `avatar_steward_after_migration` action
- Future: Filter for modifying migration results display

### CSS Classes Used
- `.wrap` - WordPress admin page wrapper
- `.notice` - WordPress notice container
- `.notice-success` - Success message styling
- `.notice-warning` - Warning message styling
- `.notice-error` - Error message styling
- `.is-dismissible` - Makes notice dismissible
- `.form-table` - WordPress form table styling
- `.widefat` - WordPress wide table styling
- `.description` - Helper text under form fields

### JavaScript
- No custom JavaScript required
- Uses WordPress core form submission
- Future enhancement: AJAX migration with progress updates
