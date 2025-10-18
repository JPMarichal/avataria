# Moderation Panel Mockup (Pro Feature)

## Overview

The moderation panel is a Pro feature that allows administrators to review, approve, or reject user-uploaded avatars before they become publicly visible.

**Location:** WordPress Admin → Avatar Steward → Moderation  
**Menu Item:** "Avatar Steward" → "Moderation" (submenu)  
**Required Capability:** `moderate_avatars` (custom) or `manage_options`  
**Feature Status:** Pro version only

---

## Page Header

```
┌─────────────────────────────────────────────────────────────────┐
│ Avatar Moderation                                                │
│ Review and manage user-uploaded avatars                          │
│                                                                  │
│ ⏳ 5 avatars pending review  │  ✓ 24 approved  │  ✗ 3 rejected │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- Page title: "Avatar Moderation" (H1)
- Description: "Review and manage user-uploaded avatars"
- Status summary with counts (pending, approved, rejected)

---

## Filter Bar

```
┌─────────────────────────────────────────────────────────────────┐
│ Filters                                                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Status:  [All ▼]    Role:  [All Roles ▼]                       │
│                                                                  │
│ Search:  [Search by username...        ] [Search]               │
│                                                                  │
│ Date Range:  [Last 7 days ▼]                                    │
│                                                                  │
│ [Apply Filters]  [Clear Filters]                                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Filter Options:**

### Status Dropdown
- All Statuses (default)
- Pending Review
- Approved
- Rejected
- Flagged (reported by users)

### Role Dropdown
- All Roles (default)
- Administrator
- Editor
- Author
- Contributor
- Subscriber
- (Custom roles if any)

### Date Range Dropdown
- All Time
- Last 24 hours
- Last 7 days (default)
- Last 30 days
- Last 90 days
- Custom range...

---

## Bulk Actions Bar

```
┌─────────────────────────────────────────────────────────────────┐
│ ☐ Select All    Bulk Actions: [Choose Action ▼]  [Apply]       │
└─────────────────────────────────────────────────────────────────┘
```

**Bulk Actions:**
- Approve Selected
- Reject Selected
- Delete Selected
- Mark as Reviewed

---

## Moderation Table

```
┌─────────────────────────────────────────────────────────────────┐
│ ☐│Avatar    │User          │Role       │Date      │Status│Action│
├─────────────────────────────────────────────────────────────────┤
│ ☐│ [IMG]    │johndoe       │Subscriber │2h ago    │⏳    │...   │
│  │ 64x64    │John Doe      │           │Jan 18    │      │      │
│  │          │john@email.com│           │2025      │      │      │
├─────────────────────────────────────────────────────────────────┤
│ ☐│ [IMG]    │janesmit      │Author     │5h ago    │⏳    │...   │
│  │ 64x64    │Jane Smith    │           │Jan 18    │      │      │
│  │          │jane@email.com│           │2025      │      │      │
├─────────────────────────────────────────────────────────────────┤
│ ☐│ [IMG]    │mwilliams     │Editor     │1d ago    │✓     │...   │
│  │ 64x64    │Mike Williams │           │Jan 17    │      │      │
│  │          │mike@email.com│           │2025      │      │      │
├─────────────────────────────────────────────────────────────────┤
│ ☐│ [IMG]    │sarahj        │Contributor│2d ago    │✗     │...   │
│  │ 64x64    │Sarah Johnson │           │Jan 16    │      │      │
│  │          │sarah@email.com│          │2025      │      │      │
└─────────────────────────────────────────────────────────────────┘
```

**Table Columns:**

1. **Checkbox** - Select for bulk actions
2. **Avatar** - 64x64 preview thumbnail
3. **User** - Username, display name, email
4. **Role** - User role
5. **Date** - Upload date (relative time + absolute)
6. **Status** - Visual badge (⏳ Pending, ✓ Approved, ✗ Rejected)
7. **Actions** - Quick action buttons

---

## Action Buttons (Per Row)

```
[View Details]  [Approve]  [Reject]
```

Or if already approved/rejected:

```
[View Details]  [Revert]  [Delete]
```

**Hover Menu (...):**
- View Details
- Approve
- Reject
- View User Profile
- View Previous Avatars
- Report Issue
- Delete Permanently

---

## Details Slide-Over Panel

Clicking "View Details" opens a slide-over panel from the right:

```
┌─────────────────────────────────────────────────────────────────┐
│ Avatar Details                                              [×]  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────────────┐                                             │
│  │                │                                             │
│  │                │                                             │
│  │    [IMAGE]     │                                             │
│  │   240x240      │                                             │
│  │                │                                             │
│  │                │                                             │
│  └────────────────┘                                             │
│                                                                  │
│  User Information                                                │
│  ├─ Username: johndoe                                           │
│  ├─ Display Name: John Doe                                      │
│  ├─ Email: john@email.com                                       │
│  ├─ Role: Subscriber                                            │
│  └─ Member Since: January 2024                                  │
│                                                                  │
│  Avatar Information                                              │
│  ├─ Upload Date: January 18, 2025 at 3:45 PM                   │
│  ├─ File Size: 234 KB                                           │
│  ├─ Dimensions: 800 × 800 pixels                                │
│  ├─ Format: JPEG                                                │
│  ├─ Upload IP: 192.168.1.100                                    │
│  └─ Previous Avatars: 2 (View History)                          │
│                                                                  │
│  Moderation History                                              │
│  ├─ Uploaded: Jan 18, 2025 3:45 PM                             │
│  └─ Status: Pending Review                                      │
│                                                                  │
│  Moderator Notes                                                 │
│  ┌────────────────────────────────────────────────┐            │
│  │                                                 │            │
│  │  [Text area for moderator comments]            │            │
│  │                                                 │            │
│  └────────────────────────────────────────────────┘            │
│                                                                  │
│  Automated Checks                                                │
│  ✓ File type valid                                              │
│  ✓ Dimensions within limits                                     │
│  ✓ File size acceptable                                         │
│  ✓ No malicious content detected                                │
│                                                                  │
│  ┌────────────────────────────────────────────────┐            │
│  │ [Approve Avatar]  [Reject Avatar]  [Delete]    │            │
│  └────────────────────────────────────────────────┘            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Slide-Over Sections:**

1. **Large Preview** - 240x240px avatar preview
2. **User Information** - Username, display name, email, role, registration date
3. **Avatar Information** - Upload details, file info, IP address
4. **Moderation History** - Timeline of status changes
5. **Moderator Notes** - Text area for internal comments
6. **Automated Checks** - Results of validation rules
7. **Action Buttons** - Primary actions for this avatar

---

## Approve Avatar Flow

### Step 1: Click "Approve"

Confirmation dialog (optional, can be disabled in settings):

```
┌─────────────────────────────────────────────────────────────────┐
│ Approve Avatar?                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Are you sure you want to approve this avatar?                   │
│                                                                  │
│ The avatar will become immediately visible on the site.          │
│                                                                  │
│ Notify user: ☐ Send approval notification email                 │
│                                                                  │
│ [Cancel]                                     [Yes, Approve]      │
└─────────────────────────────────────────────────────────────────┘
```

### Step 2: Success

```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Avatar approved successfully.                             [×] │
└─────────────────────────────────────────────────────────────────┘
```

Table row updates:
- Status badge changes to ✓ (green)
- Row background briefly highlights green
- Action buttons change to [View Details] [Revert] [Delete]

---

## Reject Avatar Flow

### Step 1: Click "Reject"

Rejection dialog with reason:

```
┌─────────────────────────────────────────────────────────────────┐
│ Reject Avatar                                                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Please provide a reason for rejecting this avatar:              │
│                                                                  │
│ Reason: [Select reason ▼]                                       │
│                                                                  │
│ • Inappropriate content                                          │
│ • Low quality / blurry                                           │
│ • Copyright violation                                            │
│ • Not a person                                                   │
│ • Violates terms of service                                      │
│ • Other (specify below)                                          │
│                                                                  │
│ Additional Comments (optional):                                  │
│ ┌────────────────────────────────────────────────┐             │
│ │                                                 │             │
│ │                                                 │             │
│ └────────────────────────────────────────────────┘             │
│                                                                  │
│ ☑ Notify user of rejection                                      │
│ ☐ Temporarily ban user from uploading (7 days)                  │
│                                                                  │
│ [Cancel]                                        [Reject Avatar]  │
└─────────────────────────────────────────────────────────────────┘
```

### Step 2: Success

```
┌─────────────────────────────────────────────────────────────────┐
│ ✓ Avatar rejected. User has been notified.                  [×] │
└─────────────────────────────────────────────────────────────────┘
```

Table row updates:
- Status badge changes to ✗ (red)
- Row background briefly highlights red
- User reverts to previous avatar or default
- Rejection logged in history

---

## Avatar History View

Clicking "View History" shows previous avatars:

```
┌─────────────────────────────────────────────────────────────────┐
│ Avatar History - John Doe                                   [×] │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Current                                                         │
│  ┌──────┐                                                       │
│  │[IMG] │  Uploaded: Jan 18, 2025                               │
│  │64x64 │  Status: Pending Review                               │
│  └──────┘                                                       │
│                                                                  │
│  Previous Avatars                                                │
│  ┌──────┐                                                       │
│  │[IMG] │  Uploaded: Dec 10, 2024                               │
│  │64x64 │  Status: Approved                                     │
│  └──────┘  Replaced: Jan 18, 2025                               │
│             Moderator: admin                                     │
│                                                                  │
│  ┌──────┐                                                       │
│  │[IMG] │  Uploaded: Nov 5, 2024                                │
│  │64x64 │  Status: Rejected                                     │
│  └──────┘  Reason: Low quality                                  │
│             Moderator: editor1                                   │
│             User notified: Yes                                   │
│                                                                  │
│  ┌──────┐                                                       │
│  │ JD   │  Generated: Oct 1, 2024                               │
│  │64x64 │  Type: Initials (Default)                             │
│  └──────┘  Active: Oct 1 - Nov 5, 2024                          │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Statistics Dashboard (Top of Page)

```
┌─────────────────────────────────────────────────────────────────┐
│ Moderation Statistics                                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Pending     │  │ Approved    │  │ Rejected    │            │
│  │     5       │  │    124      │  │     18      │            │
│  │ Review Now  │  │ This Month  │  │ This Month  │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                  │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │ Avg Time    │  │ Flagged     │  │ Repeat      │            │
│  │   2.5h      │  │     2       │  │ Offenders   │            │
│  │ To Review   │  │ Needs Attn  │  │     3       │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Settings Integration

Moderation-related settings in the admin settings page:

```
┌─────────────────────────────────────────────────────────────────┐
│ Moderation Settings (Pro)                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ ☑ Enable Avatar Moderation                                      │
│   Require approval before avatars become public                 │
│                                                                  │
│ Moderation Mode                                                  │
│ ⚪ All uploads require approval                                  │
│ ⚪ First upload requires approval (trusted after first)          │
│ ⚪ Auto-approve for trusted roles, moderate others               │
│                                                                  │
│ Trusted Roles                                                    │
│ ☑ Administrator    ☑ Editor    ☐ Author                         │
│ ☐ Contributor      ☐ Subscriber                                 │
│                                                                  │
│ Notifications                                                    │
│ ☑ Notify users when avatars are approved                        │
│ ☑ Notify users when avatars are rejected                        │
│ ☑ Notify moderators when new avatars are uploaded               │
│                                                                  │
│ Email: [moderator@site.com                  ]                   │
│                                                                  │
│ Auto-Actions                                                     │
│ ☑ Auto-reject obvious violations (adult content, violence)      │
│ ☐ Auto-approve after 7 days if no action taken                  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Email Notifications

### Approval Email (to user)

```
Subject: Your Avatar Has Been Approved

Hello John,

Good news! Your profile avatar has been approved and is now visible 
on the site.

Avatar uploaded: January 18, 2025 at 3:45 PM

Thank you for being part of our community!

Best regards,
The Team
```

### Rejection Email (to user)

```
Subject: Your Avatar Needs Review

Hello John,

Thank you for uploading a profile avatar. Unfortunately, we cannot 
approve the avatar you submitted for the following reason:

Reason: Inappropriate content

Moderator's comment: The image contains content that violates our 
community guidelines. Please review our terms of service and upload 
a different image.

You may upload a new avatar at any time from your profile page.

If you believe this was a mistake, please contact support.

Best regards,
The Team
```

### New Upload Email (to moderators)

```
Subject: New Avatar Pending Review

A new avatar has been uploaded and requires moderation:

User: John Doe (johndoe)
Email: john@email.com
Role: Subscriber
Upload Date: January 18, 2025 at 3:45 PM

View and moderate: [Link to moderation panel]

--
Avatar Steward Moderation System
```

---

## Responsive Behavior

### Desktop (> 1280px)
- Full table with all columns visible
- Slide-over panel 600px wide
- Statistics dashboard in 3-column grid

### Tablet (768px - 1280px)
- Table with fewer columns (hide role, show on row expansion)
- Slide-over panel 500px wide
- Statistics dashboard in 2-column grid

### Mobile (< 768px)
- Card-based layout instead of table
- Each avatar as a card with key info
- Slide-over becomes full-screen modal
- Statistics dashboard in single column

---

## Accessibility

### Keyboard Navigation
- Tab through all interactive elements
- Arrow keys to navigate table rows
- Enter to open details panel
- Escape to close panels/dialogs

### Screen Readers
- Table structure properly announced
- Status badges have text alternatives
- Action buttons clearly labeled
- Live regions for status updates

### Color Coding with Icons
- Pending: ⏳ + yellow badge
- Approved: ✓ + green badge
- Rejected: ✗ + red badge
- Flagged: 🚩 + orange badge

---

## Performance Considerations

- Paginated results (20 per page default)
- AJAX-powered filters (no page reload)
- Lazy loading of avatar thumbnails
- Cached approval status
- Background processing for bulk actions

---

## Related Files

- **Implementation:** `src/Admin/ModerationPanel.php` (Pro)
- **Styles:** `assets/css/moderation.css` (Pro)
- **Scripts:** `assets/js/moderation.js` (Pro)
- **API:** `src/API/ModerationEndpoint.php` (Pro)

---

## Notes

- This is a **Pro feature only** - not included in free MVP
- Requires additional database tables for moderation queue
- Integrates with WordPress notification system
- Supports multi-moderator workflows
- All text in English per requirement C-05
- Complies with GDPR (stores IP temporarily, logs are exportable)
