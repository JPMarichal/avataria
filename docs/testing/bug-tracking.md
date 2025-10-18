# Bug Tracking - Avatar Steward MVP

**Last Updated:** 2025-10-18  
**Phase:** Phase 2 - Task 2.5

## Bug Summary

| Status | Count |
|--------|-------|
| Open | 0 |
| In Progress | 0 |
| Fixed | 1 |
| Won't Fix | 0 |
| **Total** | **1** |

## Bugs by Severity

| Severity | Count |
|----------|-------|
| Critical | 0 |
| High | 0 |
| Medium | 1 |
| Low | 0 |

---

## Bug List

### [BUG-001] Array index error in Initials Generator with leading special characters

**Status:** Fixed  
**Severity:** Medium  
**Reported:** 2025-10-18  
**Component:** Initials Generator  
**Reporter:** GitHub Copilot

**Description:**
When a user name starts with special characters (e.g., "'; DROP TABLE users; --"), the initials extraction fails with an "Undefined array key 0" error. This happens because `array_filter()` preserves array keys, so when the first element is empty after splitting, index 0 doesn't exist.

**Steps to Reproduce:**
1. Call `Generator::extract_initials()` with a name starting with special characters
2. Example: `$generator->extract_initials("'; DROP TABLE users; --")`
3. PHP error occurs: "Undefined array key 0"

**Expected Behavior:**
The generator should handle special characters gracefully and extract initials from the remaining valid characters.

**Actual Behavior:**
PHP error: "Undefined array key 0" at line 161 of Generator.php

**Environment:**
- WordPress: 6.4
- PHP: 8.3.6
- Component: Domain/Initials/Generator

**Root Cause:**
After using `preg_replace()` to remove special characters, the name is split by whitespace using `preg_split()`. When special characters appear at the beginning, the first array element becomes an empty string. The `array_filter()` call removes empty strings but preserves array keys, leaving the array without an index 0.

**Fix:**
- Commit: (current changes)
- Files changed: src/AvatarSteward/Domain/Initials/Generator.php
- Description: Added `array_values()` wrapper around `array_filter()` to re-index the array after filtering empty elements

```php
// Before:
$parts = array_filter( $parts );

// After:
$parts = array_values( array_filter( $parts ) );
```

**Verified:**
- [x] Fix tested in dev environment
- [x] Regression tests passed (150 tests, 256 assertions)
- [x] Edge cases verified (SQL injection, XSS, special characters)
- [x] Linting passed

---

## Fixed Bugs Archive

### [BUG-001] - FIXED
Array index error with leading special characters in user names. Fixed by properly re-indexing array after filtering empty elements.


