# Avatar Removal - Manual Testing Scenarios

## Test Scenario 1: Remove Avatar with Deletion Disabled (Default)

**Prerequisites:**
- Avatar Steward plugin is installed and activated
- "Delete Attachment on Remove" setting is DISABLED (default)

**Steps:**
1. Log in as a user
2. Go to Users > Your Profile
3. Upload a custom avatar image
4. Click "Update Profile"
5. Verify the avatar appears correctly
6. Go to Media > Library
7. Note the avatar attachment is present
8. Go back to Users > Your Profile
9. Check the "Remove current avatar" checkbox
10. Click "Update Profile"

**Expected Results:**
- The avatar display shows initials-based SVG instead of uploaded image
- The attachment remains in Media Library
- No broken image appears
- User meta `avatar_steward_avatar` is removed

---

## Test Scenario 2: Remove Avatar with Deletion Enabled

**Prerequisites:**
- Avatar Steward plugin is installed and activated
- "Delete Attachment on Remove" setting is ENABLED

**Steps:**
1. Log in as a user
2. Go to Users > Your Profile
3. Upload a custom avatar image
4. Click "Update Profile"
5. Verify the avatar appears correctly
6. Go to Media > Library
7. Note the avatar attachment is present and its ID
8. Go back to Users > Your Profile
9. Check the "Remove current avatar" checkbox
10. Click "Update Profile"
11. Go to Media > Library

**Expected Results:**
- The avatar display shows initials-based SVG instead of uploaded image
- The attachment is DELETED from Media Library
- No broken image appears
- User meta `avatar_steward_avatar` is removed

---

## Test Scenario 3: Remove Avatar When Shared with Another User

**Prerequisites:**
- Avatar Steward plugin is installed and activated
- "Delete Attachment on Remove" setting is ENABLED
- Two user accounts exist

**Steps:**
1. Log in as User A
2. Upload a custom avatar
3. Note the attachment ID
4. Manually assign the same attachment ID to User B via:
   ```php
   update_user_meta( $user_b_id, 'avatar_steward_avatar', $attachment_id );
   ```
5. Verify both users show the same avatar
6. Log in as User A
7. Go to Users > Your Profile
8. Check "Remove current avatar"
9. Click "Update Profile"

**Expected Results:**
- User A's avatar changes to initials-based SVG
- User B still shows the uploaded avatar
- The attachment remains in Media Library (not deleted because it's still in use)
- Only User A's meta is removed

---

## Test Scenario 4: Orphaned Attachment (Manual Deletion)

**Prerequisites:**
- Avatar Steward plugin is installed and activated
- User has an avatar set

**Steps:**
1. Log in as a user with an avatar
2. Note the avatar displays correctly
3. As admin, go to Media > Library
4. Manually delete the user's avatar attachment
5. Go to Users > All Users
6. View the user's profile page

**Expected Results:**
- The avatar display shows initials-based SVG (fallback)
- No broken image appears
- The orphaned user meta is automatically cleaned up on next avatar display

---

## Test Scenario 5: Settings Configuration

**Prerequisites:**
- Avatar Steward plugin is installed and activated
- User has admin access

**Steps:**
1. Log in as admin
2. Go to Settings > Avatar Steward
3. Scroll to "Roles & Permissions" section
4. Verify "Delete Attachment on Remove" checkbox is present
5. Enable the checkbox
6. Click "Save Settings"
7. Refresh the page
8. Verify the checkbox remains checked

**Expected Results:**
- Setting is saved correctly
- Setting persists across page reloads
- Form validation works properly

---

## Verification Checklist

After implementing the fix, verify:

- [ ] No broken images appear when avatar is removed
- [ ] Initials-based fallback avatar displays correctly
- [ ] Setting controls attachment deletion behavior
- [ ] Attachments are not deleted when used by multiple users
- [ ] Orphaned user meta is cleaned up automatically
- [ ] All existing tests pass (222 tests)
- [ ] No PHP errors or warnings in debug log
- [ ] Settings page displays and saves correctly
- [ ] Backward compatibility is maintained (default behavior unchanged)

---

## Database Verification

After avatar removal, check the database:

```sql
-- Check user meta (should be empty after removal)
SELECT * FROM wp_usermeta 
WHERE meta_key = 'avatar_steward_avatar' 
AND user_id = [USER_ID];

-- Check if attachment exists
SELECT * FROM wp_posts 
WHERE ID = [ATTACHMENT_ID] 
AND post_type = 'attachment';

-- Check for other users using the same attachment
SELECT user_id, meta_value FROM wp_usermeta 
WHERE meta_key = 'avatar_steward_avatar' 
AND meta_value = [ATTACHMENT_ID];
```

---

## Performance Testing

Monitor performance impact:

1. Enable query monitoring plugin
2. Remove an avatar
3. Check number of queries executed
4. Verify the additional query for checking shared usage
5. Ensure overall performance is acceptable

Expected: +1 query when removing avatar with deletion enabled
