# User Profile Avatar Section Mockup

## Overview

The avatar upload interface integrates seamlessly into the WordPress user profile page, maintaining native WordPress admin design patterns.

**Location:** Users → Profile (or Users → Edit User)  
**Context:** Appears in the user profile editing screen  
**Required Capability:** `edit_user` (for own profile) or `edit_users` (for other users)

---

## Profile Page Integration

### Position in Profile

The avatar section appears **at the top** of the profile page, immediately after the page heading and before the "Personal Options" section.

```
┌─────────────────────────────────────────────────────────────────┐
│ Profile                                                           │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  [Avatar section content - see below]                           │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ Personal Options                                                 │
│ ... (standard WordPress profile fields)                          │
└─────────────────────────────────────────────────────────────────┘
```

---

## Avatar Section Layout

### Scenario 1: User Has No Avatar

```
┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │    JD    │    No avatar uploaded yet.                        │
│  │          │    Your initials are displayed by default.        │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Upload New Avatar]                                             │
│                                                                  │
│  Maximum upload file size: 2 MB                                  │
│  Recommended size: At least 96x96 pixels                         │
│  Allowed formats: JPEG, PNG, GIF, WebP                           │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- **Current Avatar** (label)
- Avatar preview (96x96px) showing initials-based avatar
- Description text explaining no avatar uploaded
- "Upload New Avatar" button (button-secondary)
- Help text with file requirements

---

### Scenario 2: User Has Uploaded Avatar

```
┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │  [PHOTO] │    Uploaded on January 15, 2025                   │
│  │          │                                                    │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Change Avatar]  [Remove Avatar]                                │
│                                                                  │
│  Maximum upload file size: 2 MB                                  │
│  Recommended size: At least 96x96 pixels                         │
│  Allowed formats: JPEG, PNG, GIF, WebP                           │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- **Current Avatar** (label)
- Avatar preview (96x96px) showing uploaded photo
- Upload date information
- "Change Avatar" button (button-secondary)
- "Remove Avatar" button (button-link-delete)
- Help text with file requirements

---

### Scenario 3: User Has Default Avatar

```
┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │ [DEFIMG] │    Using site default avatar.                     │
│  │          │    Upload your own to personalize your profile.   │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Upload New Avatar]                                             │
│                                                                  │
│  Maximum upload file size: 2 MB                                  │
│  Recommended size: At least 96x96 pixels                         │
│  Allowed formats: JPEG, PNG, GIF, WebP                           │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- Similar to Scenario 1, but shows site default avatar instead of initials
- Explanatory text about default avatar

---

## Admin Editing Another User's Profile

When an administrator is editing another user's profile, an additional note appears:

```
┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ⚠️ You are editing another user's profile                       │
│  Changes to the avatar will be visible immediately.             │
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │  [PHOTO] │    Uploaded by user on January 15, 2025           │
│  │          │                                                    │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Change Avatar]  [Remove Avatar]                                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Additional Elements:**
- Warning notice explaining admin is editing another user
- Modified upload date text to clarify "uploaded by user"

---

## Upload Flow

### Step 1: Click "Upload New Avatar" or "Change Avatar"

The WordPress media uploader modal opens:

```
┌─────────────────────────────────────────────────────────────────┐
│ Select or Upload Media                                      [×]  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  [Upload Files]  [Media Library]                                │
│                                                                  │
│  ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐               │
│  │        │  │        │  │        │  │        │               │
│  │ Image1 │  │ Image2 │  │ Image3 │  │ Image4 │               │
│  │        │  │        │  │        │  │        │               │
│  └────────┘  └────────┘  └────────┘  └────────┘               │
│                                                                  │
│  ... or drag and drop files here                                │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                 [Set as Avatar]  │
└─────────────────────────────────────────────────────────────────┘
```

**Features:**
- Standard WordPress media uploader modal
- Can upload new file or select from media library
- Custom button text: "Set as Avatar" instead of "Insert into post"
- Drag and drop support
- Filter by image type automatically applied

---

### Step 2: Select Image

When an image is selected:

```
┌─────────────────────────────────────────────────────────────────┐
│ Attachment Details                                          [×]  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐                                               │
│  │              │   profile-pic.jpg                             │
│  │              │   January 18, 2025                            │
│  │   [IMAGE]    │   1200 × 1200 pixels                          │
│  │   PREVIEW    │   456 KB                                      │
│  │              │                                               │
│  │              │   Title: Profile Picture                      │
│  └──────────────┘   Alt Text: [                ]                │
│                     Caption: [                ]                 │
│                                                                  │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│  [◀ Previous]  [Next ▶]                        [Set as Avatar]  │
└─────────────────────────────────────────────────────────────────┘
```

**Information Displayed:**
- Image preview
- Filename
- Upload date
- Dimensions
- File size
- Title and Alt text fields (optional)

---

### Step 3: Validation

If the selected image doesn't meet requirements, an error message appears:

```
┌─────────────────────────────────────────────────────────────────┐
│ ⚠️ Error                                                         │
│                                                                  │
│ This image cannot be used as an avatar:                         │
│ • File size (3.5 MB) exceeds maximum allowed size (2 MB)        │
│                                                                  │
│ Please select a different image.                                │
│                                                                  │
│ [OK]                                                             │
└─────────────────────────────────────────────────────────────────┘
```

**Validation Errors:**
- File size exceeds limit
- Dimensions exceed maximum
- File type not allowed
- File appears corrupted
- Upload failed for technical reasons

---

### Step 4: Success

After successful upload, the profile page updates:

```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Avatar updated successfully.                              [×] │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │ [NEWPIC] │    Uploaded just now                              │
│  │          │                                                    │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Change Avatar]  [Remove Avatar]                                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Success Indicators:**
- Admin notice: "Avatar updated successfully"
- Avatar preview updates to show new image
- Upload date shows "just now" or current timestamp
- Previous avatar is replaced (old attachment remains in media library)

---

## Remove Avatar Flow

### Step 1: Click "Remove Avatar"

Confirmation dialog appears:

```
┌─────────────────────────────────────────────────────────────────┐
│ Confirm Avatar Removal                                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Are you sure you want to remove your avatar?                    │
│                                                                  │
│ You will revert to the site default or an automatically         │
│ generated avatar with your initials.                             │
│                                                                  │
│ The image will remain in your media library.                     │
│                                                                  │
│ [Cancel]                                 [Yes, Remove Avatar]    │
└─────────────────────────────────────────────────────────────────┘
```

**Dialog Elements:**
- Clear explanation of what will happen
- Note that media library file is preserved
- "Cancel" button (secondary)
- "Yes, Remove Avatar" button (primary/destructive)

---

### Step 2: Confirmation

After removal:

```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Avatar removed successfully.                              [×] │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ Profile Picture                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current Avatar                                                  │
│  ┌──────────┐                                                   │
│  │          │                                                    │
│  │    JD    │    No avatar uploaded yet.                        │
│  │          │    Your initials are displayed by default.        │
│  └──────────┘                                                   │
│  96x96 pixels                                                    │
│                                                                  │
│  [Upload New Avatar]                                             │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Post-Removal State:**
- Admin notice: "Avatar removed successfully"
- Avatar preview shows initials or default avatar
- "Remove Avatar" button no longer visible
- Only "Upload New Avatar" button present

---

## Responsive Behavior

### Desktop (> 1024px)
- Two-column layout (if part of larger profile form)
- Avatar preview on left, information/buttons on right
- 96x96px avatar preview

### Tablet (768px - 1024px)
- Single column layout
- Avatar preview centered
- Buttons full width
- Same 96x96px preview size

### Mobile (< 768px)
- Single column, centered layout
- Avatar preview 64x64px (smaller for mobile)
- Buttons stack vertically, full width
- Reduced padding and spacing

---

## Accessibility

### Keyboard Navigation
- Tab through interactive elements in logical order
- Enter/Space activates buttons
- Media uploader fully keyboard accessible
- Focus visible on all interactive elements

### Screen Readers
- Avatar preview has appropriate alt text
- Button purposes clearly announced
- Upload status changes announced
- Error messages announced immediately
- Success confirmations announced

### ARIA Attributes
```html
<button aria-label="Upload new profile avatar">Upload New Avatar</button>
<img src="..." alt="Current profile avatar" role="img" />
<div role="status" aria-live="polite">Avatar updated successfully</div>
```

---

## HTML Structure Example

```html
<table class="form-table" role="presentation">
  <tr class="avatarsteward-profile-avatar">
    <th scope="row">Profile Picture</th>
    <td>
      <div class="avatarsteward-avatar-container">
        <div class="avatarsteward-avatar-preview">
          <img src="..." alt="Current profile avatar" width="96" height="96" />
          <span class="avatarsteward-avatar-info">Uploaded on January 15, 2025</span>
        </div>
        
        <div class="avatarsteward-avatar-actions">
          <button type="button" class="button button-secondary avatarsteward-upload-avatar">
            Change Avatar
          </button>
          <button type="button" class="button button-link-delete avatarsteward-remove-avatar">
            Remove Avatar
          </button>
        </div>
        
        <p class="description">
          Maximum upload file size: 2 MB<br>
          Recommended size: At least 96x96 pixels<br>
          Allowed formats: JPEG, PNG, GIF, WebP
        </p>
      </div>
    </td>
  </tr>
</table>
```

---

## CSS Classes

### Avatar Container
- `.avatarsteward-profile-avatar` - Main row
- `.avatarsteward-avatar-container` - Container for all avatar elements
- `.avatarsteward-avatar-preview` - Preview image wrapper
- `.avatarsteward-avatar-info` - Upload date/status text
- `.avatarsteward-avatar-actions` - Button container

### States
- `.avatarsteward-has-avatar` - When user has uploaded avatar
- `.avatarsteward-no-avatar` - When using default/initials
- `.avatarsteward-uploading` - During upload process
- `.avatarsteward-error` - When validation error occurs

---

## JavaScript Behavior

### Events
- `avatarsteward.upload.start` - Fired when upload begins
- `avatarsteward.upload.success` - Fired when upload succeeds
- `avatarsteward.upload.error` - Fired when upload fails
- `avatarsteward.remove.confirm` - Fired when remove is confirmed
- `avatarsteward.remove.success` - Fired when removal succeeds

### AJAX Operations
- Upload happens via WordPress media upload
- Remove action via AJAX with nonce verification
- Real-time preview update without page reload
- Loading indicators during async operations

---

## Integration Points

### WordPress Hooks Used
- `show_user_profile` - Display field on own profile
- `edit_user_profile` - Display field when editing other user
- `personal_options_update` - Save on own profile update
- `edit_user_profile_update` - Save when admin edits user

### Meta Keys
- `avatarsteward_avatar` - Stores attachment ID of avatar
- `avatarsteward_avatar_rating` - Stores avatar rating (future use)

### Capabilities Required
- `edit_user` - Edit own profile and avatar
- `edit_users` - Edit other users' profiles (admin)
- `upload_files` - Upload avatar images

---

## Related Files

- **Implementation:** `src/Admin/ProfileFields.php`
- **Styles:** `assets/css/admin.css`
- **Scripts:** `assets/js/profile-upload.js`
- **Specifications:** `documentacion/mvp-spec.json`

---

## Notes

- All text must be in English per project requirement C-05
- Design follows WordPress admin conventions exactly
- No custom media upload flow - uses WordPress native uploader
- Avatar changes are immediate (no "Save Profile" required)
- Compatible with multisite installations
