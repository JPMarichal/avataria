# Avatar Moderation Workflow Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Avatar Steward                          │
│                     Moderation System                           │
└─────────────────────────────────────────────────────────────────┘
                                │
                ┌───────────────┼───────────────┐
                │               │               │
         ┌──────▼──────┐ ┌─────▼──────┐ ┌─────▼──────┐
         │   Domain    │ │   Admin    │ │    Core    │
         │   Layer     │ │   Layer    │ │   Layer    │
         └─────────────┘ └────────────┘ └────────────┘
                │               │               │
    ┌───────────┴───────┐       │       ┌───────┴────────┐
    │                   │       │       │                │
┌───▼────┐    ┌────────▼───┐   │   ┌───▼──────┐  ┌──────▼──────┐
│Moderat.│    │  Decision  │   │   │  Avatar  │  │    Upload   │
│ Queue  │    │  Service   │   │   │ Handler  │  │   Handler   │
└────────┘    └────────────┘   │   └──────────┘  └─────────────┘
                                │
                        ┌───────▼────────┐
                        │  Moderation    │
                        │     Page       │
                        └────────────────┘
```

## Component Relationships

```
┌────────────────┐
│     Plugin     │  Main orchestrator
└───────┬────────┘
        │ initializes
        ├──────────────────────┐
        │                      │
    ┌───▼───────────┐    ┌────▼─────────┐
    │ AvatarHandler │    │  Moderation  │
    │               │    │    Queue     │
    └───────┬───────┘    └──────┬───────┘
            │                   │
            │ uses              │ used by
            │                   │
            │            ┌──────▼───────┐
            │            │  Decision    │
            └────────────►  Service     │
                         └──────┬───────┘
                                │
                                │ used by
                         ┌──────▼───────┐
                         │  Moderation  │
                         │     Page     │
                         └──────────────┘
```

## Data Flow - Avatar Upload with Moderation

```
┌──────────┐
│   User   │
└────┬─────┘
     │ 1. Uploads avatar
     ▼
┌────────────────┐
│ WordPress      │
│ Profile Form   │
└────┬───────────┘
     │ 2. POST request
     ▼
┌────────────────┐
│ UploadHandler  │
└────┬───────────┘
     │ 3. Checks settings
     ▼
┌────────────────┐         ┌──────────────┐
│ Is moderation  ├─NO──────► Store avatar │
│   enabled?     │         │ as approved  │
└────┬───────────┘         └──────────────┘
     │ YES
     │ 4. Store previous avatar
     ▼
┌────────────────┐
│ ModerationQueue│
│ set_status()   │
│ (pending)      │
└────┬───────────┘
     │ 5. Avatar stored but hidden
     ▼
┌────────────────┐
│ User sees      │
│ previous or    │
│ default avatar │
└────────────────┘
```

## Data Flow - Moderator Approval

```
┌──────────┐
│   Admin  │
└────┬─────┘
     │ 1. Opens moderation panel
     ▼
┌────────────────┐
│ ModerationPage │
└────┬───────────┘
     │ 2. Displays pending avatars
     ▼
┌────────────────┐
│ Avatar Table   │
│ with Actions   │
└────┬───────────┘
     │ 3. Clicks "Approve"
     ▼
┌────────────────┐
│ DecisionService│
│ approve()      │
└────┬───────────┘
     │ 4. Updates status
     ▼
┌────────────────┐
│ ModerationQueue│
│ set_status()   │
│ (approved)     │
└────┬───────────┘
     │ 5. Adds history entry
     ▼
┌────────────────┐
│ Clear previous │
│ avatar backup  │
└────┬───────────┘
     │ 6. Fire hook
     ▼
┌────────────────────────────────┐
│ do_action(                     │
│   'avatarsteward/avatar_       │
│    approved',                  │
│   $user_id, $moderator_id      │
│ )                              │
└────┬───────────────────────────┘
     │ 7. Avatar becomes visible
     ▼
┌────────────────┐
│ AvatarHandler  │
│ displays avatar│
└────────────────┘
```

## Data Flow - Moderator Rejection

```
┌──────────┐
│   Admin  │
└────┬─────┘
     │ 1. Clicks "Reject"
     ▼
┌────────────────┐
│ DecisionService│
│ reject()       │
└────┬───────────┘
     │ 2. Get current avatar ID
     ▼
┌────────────────┐
│ Get previous   │
│ avatar ID      │
└────┬───────────┘
     │ 3. Update status
     ▼
┌────────────────┐
│ ModerationQueue│
│ set_status()   │
│ (rejected)     │
└────┬───────────┘
     │ 4. Restore previous avatar
     ▼
┌────────────────────┐
│ update_user_meta() │
│ avatar_steward_    │
│ avatar = previous  │
└────┬───────────────┘
     │ 5. Delete rejected file
     ▼
┌────────────────┐
│ wp_delete_     │
│ attachment()   │
└────┬───────────┘
     │ 6. Add history entry
     ▼
┌────────────────┐
│ Clear previous │
│ avatar backup  │
└────┬───────────┘
     │ 7. Fire hook
     ▼
┌────────────────────────────────┐
│ do_action(                     │
│   'avatarsteward/avatar_       │
│    rejected',                  │
│   $user_id, $moderator_id      │
│ )                              │
└────────────────────────────────┘
```

## Data Flow - Bulk Operations

```
┌──────────┐
│   Admin  │
└────┬─────┘
     │ 1. Selects multiple avatars
     │    via checkboxes
     ▼
┌────────────────┐
│ Select bulk    │
│ action         │
│ (Approve/      │
│  Reject)       │
└────┬───────────┘
     │ 2. Clicks "Apply"
     ▼
┌────────────────┐
│ ModerationPage │
│ handle_        │
│ moderation_    │
│ action()       │
└────┬───────────┘
     │ 3. Calls bulk method
     ▼
┌────────────────┐
│ DecisionService│
│ bulk_approve() │
│ or             │
│ bulk_reject()  │
└────┬───────────┘
     │ 4. Iterate user IDs
     │
     ├──────────┐
     │          │
     ▼          ▼
   User 1    User 2  ...  User N
     │          │           │
     │ 5. Call individual approve/reject
     ▼          ▼           ▼
   ✓ Success  ✗ Failed   ✓ Success
     │          │           │
     └──────────┴───────────┘
                │
                │ 6. Count results
                ▼
        ┌───────────────┐
        │ Return summary│
        │ {             │
        │  approved: 2, │
        │  failed: 1    │
        │ }             │
        └───────┬───────┘
                │ 7. Display message
                ▼
        ┌───────────────────┐
        │ "2 avatar(s)      │
        │  approved,        │
        │  1 failed."       │
        └───────────────────┘
```

## Avatar Display Filter Logic

```
┌────────────────┐
│  get_avatar()  │
│  WordPress     │
│  function      │
└────┬───────────┘
     │ 1. Apply filter
     ▼
┌────────────────────────┐
│ pre_get_avatar_data    │
│ filter                 │
└────┬───────────────────┘
     │ 2. Get user ID
     ▼
┌────────────────┐
│ AvatarHandler  │
│ get_local_     │
│ avatar_url()   │
└────┬───────────┘
     │ 3. Get avatar ID from user meta
     ▼
┌────────────────┐         ┌──────────────┐
│ Has moderation ├─NO──────► Return avatar│
│    queue?      │         │ URL          │
└────┬───────────┘         └──────────────┘
     │ YES
     │ 4. Check status
     ▼
┌────────────────┐
│ Get moderation │
│ status         │
└────┬───────────┘
     │
     ├────────────┬─────────────┐
     │            │             │
     ▼            ▼             ▼
  Pending      Rejected     Approved
     │            │             │
     │ 5. Return null (don't show)
     ▼            ▼             │
┌──────────────────┐            │
│ Return null      │            │
│ (use default     │            │
│  Gravatar)       │            │
└──────────────────┘            │
                                │ 6. Return avatar URL
                                ▼
                        ┌────────────────┐
                        │ Avatar displays│
                        │ publicly       │
                        └────────────────┘
```

## Database Schema - User Meta

```
wp_usermeta table:
┌───────┬──────────────────────────────────────┬─────────────────────┐
│ meta_ │ meta_key                             │ meta_value          │
│ id    │                                      │                     │
├───────┼──────────────────────────────────────┼─────────────────────┤
│ 1001  │ avatar_steward_avatar                │ 42 (attachment ID)  │
│ 1002  │ avatar_steward_moderation_status     │ 'pending'           │
│ 1003  │ avatar_steward_previous_avatar       │ 38 (attachment ID)  │
│ 1004  │ avatar_steward_moderation_history    │ [                   │
│       │                                      │   {                 │
│       │                                      │     action: 'appr..'│
│       │                                      │     moderator_id: 1 │
│       │                                      │     timestamp: ...  │
│       │                                      │     comment: ''     │
│       │                                      │   }                 │
│       │                                      │ ]                   │
└───────┴──────────────────────────────────────┴─────────────────────┘

Status Values:
• 'pending'  - Awaiting moderation (not displayed)
• 'approved' - Approved and visible
• 'rejected' - Rejected (not displayed, previous restored)
• '' (empty) - Default (equivalent to 'approved')
```

## State Transitions

```
            ┌─────────────────────────────────────┐
            │   UPLOAD (with moderation ON)       │
            └──────────────┬──────────────────────┘
                           │
                           ▼
                    ┌──────────────┐
            ┌───────│   PENDING    │◄──────┐
            │       └──────────────┘       │
            │              │                │
            │              │                │
  Approve() │              │ Reject()       │ Upload new
            │              │                │ avatar
            │              │                │
            ▼              ▼                │
      ┌──────────┐   ┌──────────┐          │
      │ APPROVED │   │ REJECTED │──────────┘
      └──────────┘   └──────────┘
            │              │
            │              │
            └──────┬───────┘
                   │
                   │ Upload new avatar
                   ▼
            ┌──────────────┐
            │   PENDING    │
            └──────────────┘

Notes:
• APPROVED: Avatar is publicly visible
• PENDING: Avatar hidden, previous/default shown
• REJECTED: Avatar deleted, previous restored, status set to rejected
• After rejection, new upload creates new pending state
```
