# Admin Settings Page Mockup

## Page Overview

**Location:** WordPress Admin → Settings → Avatar Steward  
**Menu Item:** "Avatar Steward"  
**Icon:** Dashicons user icon (dashicons-admin-users)  
**Required Capability:** `manage_options`

---

## Page Header

```
┌─────────────────────────────────────────────────────────────────┐
│ Avatar Steward Settings                                [Save]   │
│ Manage local avatars, replace Gravatar, and customize defaults │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- Page title: "Avatar Steward Settings" (H1)
- Tagline: "Manage local avatars, replace Gravatar, and customize defaults"
- Primary action button: "Save Changes" (button-primary, top right)

---

## Layout Structure

Two-column layout:
- **Main Content Area:** 70% width, left side
- **Sidebar:** 30% width, right side

---

## Main Content Area

### Section 1: Upload Settings

```
┌─────────────────────────────────────────────────────────────────┐
│ Upload Settings                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ ☑ Enable User Avatar Uploads                                    │
│   Allow registered users to upload custom avatars               │
│                                                                  │
│ Maximum File Size                                                │
│ [2      ] MB                                                     │
│ Maximum file size for avatar uploads                             │
│                                                                  │
│ Maximum Dimensions                                               │
│ Width:  [2000   ] px    Height: [2000   ] px                    │
│ Maximum dimensions for uploaded images                           │
│                                                                  │
│ Allowed File Types                                               │
│ ☑ JPEG    ☑ PNG    ☑ GIF    ☑ WebP                             │
│                                                                  │
│ ☐ Convert Uploads to WebP                                       │
│   Automatically convert uploaded images to WebP format           │
│   (requires PHP GD or Imagick with WebP support)                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Fields:**
- **Enable User Avatar Uploads** (checkbox, default: checked)
- **Maximum File Size** (number input, default: 2, unit: MB)
- **Maximum Dimensions** (2 number inputs, default: 2000x2000, unit: px)
- **Allowed File Types** (checkboxes, default: all checked)
- **Convert Uploads to WebP** (checkbox, default: unchecked)

---

### Section 2: Gravatar Settings

```
┌─────────────────────────────────────────────────────────────────┐
│ Gravatar Settings                                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ ☑ Disable Gravatar                                              │
│   Replace all Gravatar lookups with local avatars               │
│   (improves privacy and performance)                             │
│                                                                  │
│ ☐ Use Gravatar as Fallback                                      │
│   Display Gravatar if user has no local avatar or initials      │
│   (not recommended for privacy-focused sites)                    │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Fields:**
- **Disable Gravatar** (checkbox, default: checked)
- **Use Gravatar as Fallback** (checkbox, default: unchecked)

**Note:** The fallback option is only visible when Gravatar is disabled.

---

### Section 3: Initials Generator

```
┌─────────────────────────────────────────────────────────────────┐
│ Initials Generator                                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ ☑ Enable Initials Generator                                     │
│   Automatically generate avatars with user initials              │
│                                                                  │
│ Initials Source                                                  │
│ ⚪ Display Name    ⚪ Username    ⚪ First & Last Name           │
│ Choose which user field to extract initials from                │
│                                                                  │
│ Avatar Shape                                                     │
│ [Circular ▼]                                                     │
│ Options: Circular, Rounded Square, Square                        │
│                                                                  │
│ Color Palette                                                    │
│ [●][●][●][●][●][●][●][●][●][●]  [+ Add Custom Color]           │
│ Colors are assigned consistently based on user ID                │
│                                                                  │
│ Text Color                                                       │
│ [#ffffff] 🎨                                                     │
│                                                                  │
│ Font Settings                                                    │
│ Family: [Sans-serif ▼]   Weight: [Bold ▼]                      │
│                                                                  │
│ ☐ Enable Low Bandwidth Mode (SVG)                               │
│   Use SVG format instead of PNG for smaller file sizes          │
│                                                                  │
│ Preview                                                          │
│ ┌────────────────────────────────────────┐                      │
│ │  [JD]  [SM]  [AC]  [KW]  [TH]         │                      │
│ │  Sample initials with current settings │                      │
│ └────────────────────────────────────────┘                      │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Fields:**
- **Enable Initials Generator** (checkbox, default: checked)
- **Initials Source** (radio buttons, default: Display Name)
- **Avatar Shape** (dropdown, default: Circular)
- **Color Palette** (color swatches, default: 10 predefined colors)
- **Text Color** (color picker, default: #ffffff)
- **Font Family** (dropdown, options: Sans-serif, Serif, Monospace)
- **Font Weight** (dropdown, options: Normal, Bold)
- **Enable Low Bandwidth Mode** (checkbox, default: unchecked)
- **Preview** (live preview showing sample initials)

---

### Section 4: Default Avatar

```
┌─────────────────────────────────────────────────────────────────┐
│ Default Avatar                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Default Avatar Type                                              │
│ ⚪ Initials (Generated)                                          │
│ ⚪ Custom Image                                                  │
│ ⚪ Mystery Person                                                │
│ ⚪ Blank (Transparent)                                           │
│                                                                  │
│ Custom Default Avatar                                            │
│ ┌──────────┐                                                    │
│ │          │  [Upload Image]  [Remove]                          │
│ │  [IMG]   │                                                     │
│ │          │  Upload a custom default avatar image              │
│ └──────────┘  (Displayed when type is "Custom Image")           │
│                                                                  │
│ ☐ Override Initials with Default                                │
│   Use default avatar instead of generated initials              │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Fields:**
- **Default Avatar Type** (radio buttons, default: Initials)
- **Custom Default Avatar** (media uploader, shown when type is Custom)
- **Override Initials** (checkbox, default: unchecked)

---

### Section 5: Roles & Permissions

```
┌─────────────────────────────────────────────────────────────────┐
│ Roles & Permissions                                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│ Allow avatar uploads for the following roles:                   │
│                                                                  │
│ ☑ Administrator                                                  │
│ ☑ Editor                                                         │
│ ☑ Author                                                         │
│ ☑ Contributor                                                    │
│ ☑ Subscriber                                                     │
│                                                                  │
│ Custom roles (if any) will appear here automatically            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

**Fields:**
- Dynamic checkboxes for each user role in the system
- All roles checked by default

---

## Sidebar

### Help Card

```
┌─────────────────────────────────────────────────┐
│ 📘 Need Help?                                   │
├─────────────────────────────────────────────────┤
│                                                  │
│ • View User Manual                              │
│ • Check FAQ                                     │
│ • Developer Documentation                       │
│ • Support Forum                                 │
│                                                  │
└─────────────────────────────────────────────────┘
```

### Privacy Card

```
┌─────────────────────────────────────────────────┐
│ 🔒 Privacy & Compliance                         │
├─────────────────────────────────────────────────┤
│                                                  │
│ Avatar Steward stores:                          │
│ • User avatar attachments                       │
│ • Avatar metadata (ratings, settings)           │
│                                                  │
│ Data is exportable via WordPress                │
│ privacy tools and permanently deleted           │
│ when users are removed.                         │
│                                                  │
│ [View Privacy Details]                          │
│                                                  │
└─────────────────────────────────────────────────┘
```

### System Info Card

```
┌─────────────────────────────────────────────────┐
│ ⚙️ System Information                           │
├─────────────────────────────────────────────────┤
│                                                  │
│ WordPress: 6.7                              ✓   │
│ PHP: 8.2.0                                  ✓   │
│ GD Library: Enabled                         ✓   │
│ WebP Support: Enabled                       ✓   │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## Page Footer

```
┌─────────────────────────────────────────────────────────────────┐
│ [Save Changes]  [Reset to Defaults]                             │
└─────────────────────────────────────────────────────────────────┘
```

**Elements:**
- **Save Changes** (button-primary)
- **Reset to Defaults** (button-secondary, with confirmation dialog)

---

## Interactions

### 1. Save Changes
- Click "Save Changes" button
- Form submits via POST
- Validation occurs server-side
- Admin notice appears: "Settings saved successfully" (success) or error message
- Page reloads with saved settings

### 2. Reset to Defaults
- Click "Reset to Defaults"
- Confirmation dialog: "Are you sure you want to reset all settings to defaults? This cannot be undone."
- If confirmed, settings reset to default values
- Page reloads with admin notice: "Settings reset to defaults"

### 3. Color Picker
- Click color swatch or picker icon
- WordPress color picker appears
- Select color or enter hex value
- Color updates in real-time in preview

### 4. Media Upload
- Click "Upload Image" button
- WordPress media uploader modal opens
- Select existing image or upload new one
- Image selected and preview updates
- "Remove" button appears to clear selection

### 5. Live Preview
- Initials preview updates in real-time as settings change
- Shows 5 sample initials with different colors from palette
- Reflects current shape, font, and color settings

### 6. Conditional Fields
- "Gravatar Fallback" only appears when "Disable Gravatar" is checked
- "Custom Default Avatar" uploader only appears when type is "Custom Image"
- "Override Initials" only appears when default type is not "Initials"

---

## Validation & Error Handling

### Client-Side
- Required fields validated before submission
- Number inputs restricted to valid ranges
- File size and dimension limits checked

### Server-Side
- All inputs sanitized using WordPress functions
- Settings validated against allowed values
- Invalid values reset to defaults
- Clear error messages displayed

### Error Messages
- "Maximum file size must be between 1 and 10 MB"
- "Maximum dimensions must be between 100 and 4000 pixels"
- "At least one file type must be allowed"
- "Invalid color value. Please use hex format (#000000)"

---

## Responsive Behavior

### Tablet (768px - 1024px)
- Two-column layout maintained
- Sidebar width increases to 35%
- Font sizes slightly reduced

### Mobile (< 768px)
- Single column layout
- Sidebar moves below main content
- Form fields stack vertically
- Color palette wraps to multiple rows
- Preview section scrolls horizontally if needed

---

## Accessibility

### Keyboard Navigation
- All form fields accessible via Tab key
- Logical tab order follows visual flow
- Skip links for main sections
- Enter key submits form

### Screen Readers
- Proper label associations with for/id attributes
- ARIA labels for custom controls
- Descriptive text for all interactive elements
- Form validation errors announced

### Color Contrast
- All text meets WCAG AA standards (4.5:1 minimum)
- Interactive elements have sufficient contrast
- Focus indicators clearly visible

---

## WordPress Integration

### Settings API
- Uses WordPress Settings API
- Settings registered in `register_setting()`
- Sections added with `add_settings_section()`
- Fields added with `add_settings_field()`

### Admin Notices
- Success: `add_settings_error()` with 'success' type
- Error: `add_settings_error()` with 'error' type
- Displayed with `settings_errors()`

### Nonce Security
- Form includes nonce field: `wp_nonce_field('avatarsteward_settings')`
- Verified on submission: `check_admin_referer('avatarsteward_settings')`

### Capability Check
- Page only accessible to users with `manage_options` capability
- Capability check: `current_user_can('manage_options')`

---

## Related Files

- **Implementation:** `src/Admin/SettingsPage.php`
- **Styles:** `assets/css/admin.css`
- **Scripts:** `assets/js/admin.js`
- **Specifications:** `documentacion/mvp-spec.json`
