# Avatar Moderation Feature

## Overview

The Avatar Moderation feature provides administrators with comprehensive tools to review and approve user-uploaded avatars before they become visible on the site. This ensures content quality, safety, and compliance with community guidelines.

## Requirements

**Functional Requirement**: RF-P03 from Product Requirements Document

> **RF-P03: Herramientas de Moderación:** El administrador tendrá un panel para ver los avatares subidos recientemente y podrá aprobarlos o rechazarlos. Si se rechaza, el usuario volverá a su avatar anterior.

## Architecture

The moderation system is built using a modular, SOLID-principles-based architecture:

### Domain Layer
- **`ModerationQueue`** (`src/AvatarSteward/Domain/Moderation/ModerationQueue.php`)
  - Manages avatar queue retrieval and filtering
  - Handles status metadata (pending, approved, rejected)
  - Stores moderation history
  - Manages previous avatar backups

- **`DecisionService`** (`src/AvatarSteward/Domain/Moderation/DecisionService.php`)
  - Processes approve/reject decisions
  - Handles bulk operations
  - Manages avatar restoration on rejection
  - Fires extensibility hooks

### Admin Layer
- **`ModerationPage`** (`src/AvatarSteward/Admin/ModerationPage.php`)
  - Renders moderation UI in WordPress admin
  - Handles form submissions and actions
  - Provides filtering and search interface
  - Manages pagination

### Integration Points
- **`UploadHandler`** integration: Sets pending status on upload when moderation is enabled
- **`AvatarHandler`** integration: Filters avatar display based on moderation status
- **`Plugin`** initialization: Wires up all moderation services

## Data Model

### User Meta Keys

1. **`avatar_steward_moderation_status`**
   - Values: `'pending'`, `'approved'`, `'rejected'`
   - Default: `'approved'` (when no meta exists)
   - Purpose: Current moderation status

2. **`avatar_steward_moderation_history`**
   - Type: Array of history entries
   - Structure:
     ```php
     array(
         'action'       => 'approved' | 'rejected',
         'moderator_id' => int,
         'timestamp'    => int,
         'comment'      => string
     )
     ```
   - Purpose: Audit trail of all moderation decisions

3. **`avatar_steward_previous_avatar`**
   - Type: Integer (attachment ID)
   - Purpose: Stores previous avatar for restoration on rejection

## User Flows

### 1. Avatar Upload with Moderation Enabled

```
User uploads avatar
    → UploadHandler processes upload
    → Checks settings: require_approval = true
    → Stores previous avatar (if exists)
    → Sets status to 'pending'
    → Stores new avatar in user meta
    → User sees previous/default avatar (new one hidden)
```

### 2. Moderator Approval

```
Admin opens moderation panel
    → Sees pending avatars in table
    → Clicks "Approve" on avatar
    → DecisionService::approve()
        → Updates status to 'approved'
        → Adds history entry
        → Clears previous avatar backup
        → Fires 'avatarsteward/avatar_approved' hook
    → Avatar becomes visible to all users
```

### 3. Moderator Rejection

```
Admin opens moderation panel
    → Sees pending avatars in table
    → Clicks "Reject" on avatar
    → DecisionService::reject()
        → Gets current avatar ID
        → Gets previous avatar ID
        → Updates status to 'rejected'
        → Restores previous avatar (or removes if none)
        → Deletes rejected attachment
        → Adds history entry
        → Clears previous avatar backup
        → Fires 'avatarsteward/avatar_rejected' hook
    → User sees previous/default avatar
```

### 4. Bulk Operations

```
Admin selects multiple avatars
    → Chooses bulk action (Approve/Reject)
    → Clicks "Apply"
    → DecisionService::bulk_approve() or bulk_reject()
        → Iterates through user IDs
        → Applies individual approve/reject to each
        → Counts successes and failures
    → Shows summary message
```

## UI Components

### Moderation Menu
- **Location**: Top-level admin menu (after Dashboard)
- **Icon**: `dashicons-id-alt`
- **Badge**: Shows pending count (e.g., "Avatar Moderation (5)")
- **Capability**: `moderate_comments`
- **Position**: 30 (after Media)

### Status Tabs
- **Pending**: Shows avatars awaiting review (default)
- **Approved**: Shows previously approved avatars
- **Rejected**: Shows previously rejected avatars
- Each tab displays count: "Pending (5)"

### Avatar Table
- **Columns**:
  - Checkbox (for bulk selection)
  - Avatar (64x64 thumbnail)
  - User (name + email)
  - Role (primary role)
  - Uploaded (relative time)
  - Status (colored badge)
  - Actions (Approve/Reject buttons)

### Filters and Search
- **Search box**: Filter by username, email, or display name
- **Status filter**: Built into tabs
- **Role filter**: (Planned feature)
- **Pagination**: 20 items per page

### Bulk Actions
- **Dropdown**: "Bulk Actions" with Approve/Reject options
- **Apply button**: Processes selected avatars
- **Select all checkbox**: In table header

## API and Hooks

### Actions Fired

```php
// After avatar is approved
do_action( 'avatarsteward/avatar_approved', $user_id, $moderator_id, $comment );

// After avatar is rejected
do_action( 'avatarsteward/avatar_rejected', $user_id, $moderator_id, $comment );
```

### Public Methods

#### ModerationQueue

```php
// Get avatars with filtering
$queue->get_pending_avatars( array $args ): array

// Get count by status
$queue->get_count_by_status( string $status ): int

// Status management
$queue->set_status( int $user_id, string $status ): bool
$queue->get_status( int $user_id ): string

// History tracking
$queue->add_history_entry( int $user_id, string $action, int $moderator_id, string $comment ): bool
$queue->get_history( int $user_id ): array

// Previous avatar management
$queue->store_previous_avatar( int $user_id, int $avatar_id ): bool
$queue->get_previous_avatar( int $user_id ): int|false
$queue->clear_previous_avatar( int $user_id ): bool
```

#### DecisionService

```php
// Individual decisions
$service->approve( int $user_id, int $moderator_id, string $comment = '' ): array
$service->reject( int $user_id, int $moderator_id, string $comment = '' ): array

// Bulk operations
$service->bulk_approve( array $user_ids, int $moderator_id, string $comment = '' ): array
$service->bulk_reject( array $user_ids, int $moderator_id, string $comment = '' ): array
```

## Settings Integration

The moderation feature respects the following settings:

- **`avatar_steward_options['require_approval']`** (boolean)
  - When `true`: New uploads are set to pending status
  - When `false`: New uploads are automatically approved
  - Default: `false`

## Security Considerations

1. **Nonce Verification**: All form submissions require valid nonce
2. **Capability Checks**: `moderate_comments` capability required
3. **Input Sanitization**: All inputs sanitized before processing
4. **Permission Checks**: Users can only moderate others' avatars
5. **SQL Injection Prevention**: Uses WordPress functions with prepared statements
6. **XSS Prevention**: All output escaped with `esc_*` functions

## Performance Considerations

1. **Query Optimization**: 
   - Uses `WP_User_Query` with meta_key/meta_value for efficient filtering
   - Pagination limits results to 20 per page
   - No full table scans

2. **Caching**: 
   - User meta is cached by WordPress automatically
   - Status checks use single `get_user_meta()` calls

3. **Bulk Operations**: 
   - Process one user at a time
   - No transaction overhead
   - Counts provided for feedback

## Testing

### Unit Tests
- `tests/phpunit/Domain/Moderation/ModerationQueueTest.php`
- `tests/phpunit/Domain/Moderation/DecisionServiceTest.php`

### Test Coverage
- ✅ Class instantiation
- ✅ Method existence and signatures
- ✅ Return type validation
- ✅ Status constant definitions
- ✅ Invalid input handling
- ✅ Non-existent user handling
- ⚠️ WordPress function mocking (requires WordPress test environment)

### Manual Testing Checklist
- [ ] Enable "Require Approval" setting
- [ ] Upload avatar as non-admin user
- [ ] Verify avatar is hidden from public display
- [ ] Open moderation panel as admin
- [ ] Verify pending avatar appears in queue
- [ ] Approve avatar and verify it becomes visible
- [ ] Upload new avatar
- [ ] Reject avatar and verify previous avatar is restored
- [ ] Test bulk approval of multiple avatars
- [ ] Test bulk rejection of multiple avatars
- [ ] Verify search and filtering work correctly
- [ ] Verify pagination works with 20+ pending avatars
- [ ] Check moderation history is recorded correctly

## Future Enhancements

### Planned Features (Phase 4)
1. **Email Notifications**: Notify users when avatars are approved/rejected
2. **Moderator Comments**: Add text field for rejection reasons
3. **Advanced Filters**: Filter by upload date, file size, dimensions
4. **Quick View Modal**: Preview large avatar before decision
5. **Repeat Offender Detection**: Flag users with multiple rejections
6. **Scheduled Auto-Approval**: Auto-approve after X days
7. **Integration with Akismet**: Spam detection for avatars
8. **Moderation Reports**: Analytics on approval/rejection rates

### API Extensions
1. **REST Endpoints**: AJAX-based moderation actions
2. **Webhook Support**: Trigger external systems on decisions
3. **Custom Decision Types**: Beyond approve/reject (e.g., "pending review")

## Compliance and Audit

### GDPR Considerations
- Moderation history stored as user meta
- History can be exported via WordPress data export tools
- History can be erased via WordPress data erasure tools
- Moderator IDs logged for accountability

### Audit Trail
- Complete history of all moderation decisions
- Timestamp and moderator recorded for each action
- Optional comment field for rejection reasons
- Compatible with external audit systems via hooks

## Support and Troubleshooting

### Common Issues

**Q: Avatars not showing after approval**
- Check browser cache and clear it
- Verify status is actually 'approved' in user meta
- Check AvatarHandler is initialized with moderation queue

**Q: Moderation menu not appearing**
- Verify user has `moderate_comments` capability
- Check Plugin initialization completed successfully
- Ensure ModerationPage::init() was called

**Q: Bulk actions not working**
- Check server timeout limits for large batches
- Verify nonce is being passed correctly
- Check for JavaScript errors in browser console

**Q: Previous avatar not restored on rejection**
- Verify previous avatar was stored before new upload
- Check attachment still exists in media library
- Review moderation history for details

## References

- [Product Requirements Document](../../documentacion/01_Requerimiento_Producto.md) - RF-P03
- [Architecture Document](../../documentacion/13_Arquitectura.md) - Moderation module design
- [Development Guide](../../documentacion/06_Guia_de_Desarrollo.md) - SOLID principles
- [WordPress Codex - User Meta](https://codex.wordpress.org/Function_Reference/update_user_meta)
- [WordPress Codex - WP_User_Query](https://developer.wordpress.org/reference/classes/wp_user_query/)
