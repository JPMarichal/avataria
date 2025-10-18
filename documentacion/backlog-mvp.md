# Avatar Steward - MVP Backlog

## Overview

This document contains the initial backlog for the Avatar Steward MVP (free version). User stories and tasks are prioritized according to the acceptance criteria defined in the work plan and MVP specifications.

**Target Release:** 1.0.0  
**Last Updated:** October 18, 2025

---

## Priority Legend

- **P0 (Critical):** Must have for MVP launch
- **P1 (High):** Important for user experience
- **P2 (Medium):** Nice to have, can be deferred
- **P3 (Low):** Future enhancement

---

## Epic 1: Avatar Upload Management (RF-G01, RF-G03)

### User Stories

#### US-1.1: Upload Avatar from Profile
**As a** registered user  
**I want to** upload a profile picture from my device  
**So that** I can personalize my presence on the site

**Priority:** P0  
**Requirements:** RF-G01, RF-G03  
**Acceptance Criteria:**
- User can access avatar upload field in their profile page
- Upload interface uses WordPress media uploader
- Supported formats: JPEG, PNG, GIF, WebP
- Maximum file size: 2MB (configurable)
- Maximum dimensions: 2000x2000px (configurable)
- Invalid uploads show clear error messages
- Previous avatar is replaced when new one is uploaded

**Tasks:**
- [ ] Create UploadService class in `src/Domain/Uploads/`
- [ ] Implement file validation (type, size, dimensions)
- [ ] Add profile page upload field integration
- [ ] Create upload handler for personal_options_update hook
- [ ] Implement user meta storage for avatar attachment ID
- [ ] Add error handling and user feedback
- [ ] Write unit tests for validation logic
- [ ] Write integration tests for upload flow

**Estimated Effort:** 8 hours

---

#### US-1.2: Remove Avatar
**As a** registered user  
**I want to** remove my uploaded avatar  
**So that** I can revert to a generated or default avatar

**Priority:** P1  
**Requirements:** RF-G01, RF-G03  
**Acceptance Criteria:**
- User can delete their avatar from profile page
- Confirmation dialog prevents accidental deletion
- User reverts to initials or default avatar after deletion
- Deleted avatar attachment remains in media library (not permanently deleted)

**Tasks:**
- [ ] Add "Remove Avatar" button to profile interface
- [ ] Implement removal handler
- [ ] Clear user meta when avatar removed
- [ ] Show confirmation dialog before deletion
- [ ] Write tests for removal functionality

**Estimated Effort:** 3 hours

---

#### US-1.3: Admin Upload Avatar for Users
**As an** administrator  
**I want to** upload avatars for other users  
**So that** I can manage user profiles centrally

**Priority:** P2  
**Requirements:** RF-G01, RF-G03  
**Acceptance Criteria:**
- Admin can upload/change avatar when editing any user profile
- Same validation rules apply as for user uploads
- User receives notification when admin changes their avatar (optional)

**Tasks:**
- [ ] Add avatar field to edit user profile screen
- [ ] Implement edit_user_profile_update handler
- [ ] Verify admin capabilities
- [ ] Write tests for admin upload flow

**Estimated Effort:** 4 hours

---

## Epic 2: Gravatar Replacement (RF-G02)

### User Stories

#### US-2.1: Override Gravatar Lookups
**As a** site administrator  
**I want** WordPress to use local avatars instead of Gravatar  
**So that** my site is faster and respects user privacy

**Priority:** P0  
**Requirements:** RF-G02  
**Acceptance Criteria:**
- No HTTP requests made to gravatar.com
- get_avatar() returns local avatars consistently
- Works in all WordPress contexts (comments, admin bar, user lists)
- Performance overhead < 50ms per page load
- Optional Gravatar fallback can be configured

**Tasks:**
- [ ] Create AvatarService class in `src/Domain/Uploads/`
- [ ] Implement pre_get_avatar_data filter (priority 10)
- [ ] Create avatar URL resolver
- [ ] Implement caching strategy using transients
- [ ] Add fallback logic for missing avatars
- [ ] Write unit tests for avatar resolution
- [ ] Write integration tests for get_avatar() override
- [ ] Performance test and optimize

**Estimated Effort:** 10 hours

---

#### US-2.2: Configure Gravatar Fallback
**As a** site administrator  
**I want to** optionally allow Gravatar as fallback  
**So that** users without local avatars can still have their Gravatar displayed

**Priority:** P2  
**Requirements:** RF-G02  
**Acceptance Criteria:**
- Setting to enable/disable Gravatar fallback
- Fallback only used when no local avatar or initials
- Clear documentation of privacy implications

**Tasks:**
- [ ] Add fallback setting to admin options
- [ ] Implement conditional Gravatar lookup
- [ ] Add filter for per-user fallback control
- [ ] Document privacy considerations
- [ ] Write tests for fallback logic

**Estimated Effort:** 3 hours

---

## Epic 3: Initials Avatar Generator (RF-G04)

### User Stories

#### US-3.1: Generate Avatar from Initials
**As a** user without an uploaded avatar  
**I want** an automatically generated avatar with my initials  
**So that** I have a personalized avatar without uploading an image

**Priority:** P0  
**Requirements:** RF-G04  
**Acceptance Criteria:**
- Initials extracted from user display name
- Consistent color assignment per user
- Generated avatars are visually appealing
- Handles edge cases (single character, special characters)
- Cached appropriately to avoid regeneration

**Tasks:**
- [ ] Create Generator class in `src/Domain/Initials/`
- [ ] Implement initials extraction algorithm
- [ ] Create color assignment algorithm (consistent hashing)
- [ ] Generate PNG avatars using GD or Imagick
- [ ] Implement caching mechanism
- [ ] Handle edge cases (empty names, special characters)
- [ ] Write comprehensive unit tests
- [ ] Test with various user name formats

**Estimated Effort:** 12 hours

---

#### US-3.2: Customize Initials Style
**As a** site administrator  
**I want to** customize the appearance of initials avatars  
**So that** they match my site's branding

**Priority:** P1  
**Requirements:** RF-G04  
**Acceptance Criteria:**
- Admin can select from predefined color palettes
- Admin can choose shape (circular, rounded, square)
- Admin can set font style (sans-serif, serif, monospace)
- Admin can choose font weight (normal, bold)
- Preview shown in settings page

**Tasks:**
- [ ] Add initials settings to admin page
- [ ] Create PaletteRegistry class
- [ ] Implement style configuration storage
- [ ] Add live preview in settings
- [ ] Apply filters for customization hooks
- [ ] Write tests for style variations

**Estimated Effort:** 6 hours

---

#### US-3.3: Low Bandwidth Mode (SVG)
**As a** site administrator running a mobile-first site  
**I want** initials avatars to use SVG format  
**So that** page load times are minimized

**Priority:** P1  
**Requirements:** RF-G04, RFN-01  
**Acceptance Criteria:**
- Option to enable SVG format for initials
- SVG avatars are inline or data URIs
- Fallback to PNG for incompatible contexts
- Performance improvement documented

**Tasks:**
- [ ] Implement SVG generator
- [ ] Add low bandwidth mode setting
- [ ] Create format detector for context compatibility
- [ ] Benchmark performance improvement
- [ ] Write tests for SVG generation
- [ ] Document performance metrics

**Estimated Effort:** 6 hours

---

## Epic 4: Default Avatar Configuration (RF-G05)

### User Stories

#### US-4.1: Set Custom Default Avatar
**As a** site administrator  
**I want to** upload a custom default avatar  
**So that** users without avatars have a branded placeholder

**Priority:** P1  
**Requirements:** RF-G05  
**Acceptance Criteria:**
- Admin can upload default avatar image
- Default used when user has no uploaded avatar
- Can choose between custom, initials, mystery, or blank
- Preview shown in settings

**Tasks:**
- [ ] Add default avatar settings section
- [ ] Implement media uploader for default avatar
- [ ] Store default avatar attachment ID
- [ ] Apply default in avatar resolution logic
- [ ] Add preview to settings page
- [ ] Write tests for default avatar logic

**Estimated Effort:** 5 hours

---

#### US-4.2: Default Avatar Priority
**As a** site administrator  
**I want to** control whether initials or default avatar is used  
**So that** I have flexibility in avatar display logic

**Priority:** P2  
**Requirements:** RF-G04, RF-G05  
**Acceptance Criteria:**
- Setting to choose priority: initials first or default first
- Clear documentation of priority logic
- Filters available for custom priority logic

**Tasks:**
- [ ] Add priority setting to admin options
- [ ] Implement priority logic in AvatarService
- [ ] Add filter for custom priority decisions
- [ ] Document priority flow
- [ ] Write tests for different priority scenarios

**Estimated Effort:** 3 hours

---

## Epic 5: Admin Settings Interface

### User Stories

#### US-5.1: General Settings Page
**As a** site administrator  
**I want** a centralized settings page  
**So that** I can configure all avatar options

**Priority:** P0  
**Requirements:** All RF-G requirements  
**Acceptance Criteria:**
- Settings page in WordPress admin menu
- Organized sections for different features
- Form follows WordPress admin UI patterns
- Settings are saved and validated
- Contextual help and documentation links

**Tasks:**
- [ ] Create SettingsPage class in `src/Admin/`
- [ ] Register admin menu and page
- [ ] Create settings sections and fields
- [ ] Implement settings sanitization
- [ ] Add contextual help sidebar
- [ ] Style according to WordPress guidelines
- [ ] Write tests for settings save/load

**Estimated Effort:** 8 hours

---

#### US-5.2: Settings Import/Export
**As a** site administrator  
**I want to** export and import settings  
**So that** I can replicate configuration across sites

**Priority:** P3  
**Requirements:** None (enhancement)  
**Acceptance Criteria:**
- Export settings as JSON
- Import validates and sanitizes JSON
- Clear success/error messages

**Tasks:**
- [ ] Add export button to settings page
- [ ] Implement JSON export functionality
- [ ] Add import file uploader
- [ ] Validate imported settings
- [ ] Write tests for import/export

**Estimated Effort:** 4 hours  
**Status:** Deferred to post-MVP

---

## Epic 6: Testing & Quality Assurance

### Technical Debt & Testing Tasks

#### T-6.1: Unit Test Coverage
**Priority:** P0  
**Tasks:**
- [ ] Initials extraction tests (edge cases)
- [ ] Color assignment consistency tests
- [ ] File validation tests
- [ ] Settings sanitization tests
- [ ] Achieve >80% code coverage for domain logic

**Estimated Effort:** 6 hours

---

#### T-6.2: Integration Testing
**Priority:** P0  
**Tasks:**
- [ ] Test avatar upload flow end-to-end
- [ ] Test get_avatar() override in different contexts
- [ ] Test profile page integration
- [ ] Test settings save/load cycle
- [ ] Test with multisite environment

**Estimated Effort:** 8 hours

---

#### T-6.3: Compatibility Testing
**Priority:** P1  
**Tasks:**
- [ ] Test with popular themes (Twenty Twenty-Four, Astra, GeneratePress)
- [ ] Test with BuddyPress
- [ ] Test with WooCommerce
- [ ] Test with bbPress
- [ ] Document any compatibility issues

**Estimated Effort:** 10 hours

---

#### T-6.4: Security Audit
**Priority:** P0  
**Tasks:**
- [ ] Review file upload security
- [ ] Check input sanitization
- [ ] Verify output escaping
- [ ] Test capability checks
- [ ] Run SAST tools
- [ ] Address all findings

**Estimated Effort:** 6 hours

---

## Epic 7: Documentation

#### D-7.1: User Documentation
**Priority:** P0  
**Tasks:**
- [ ] Write installation guide
- [ ] Write user manual for profile uploads
- [ ] Write admin guide for settings
- [ ] Create FAQ section
- [ ] Add screenshots

**Estimated Effort:** 6 hours

---

#### D-7.2: Developer Documentation
**Priority:** P1  
**Tasks:**
- [ ] Document public hooks and filters
- [ ] Create code examples
- [ ] Write architecture overview
- [ ] Document API reference
- [ ] Add inline code documentation

**Estimated Effort:** 8 hours

---

## Sprint Planning Recommendation

### Sprint 1 (Week 1): Foundation
- US-1.1: Upload Avatar from Profile
- US-2.1: Override Gravatar Lookups
- US-5.1: General Settings Page

### Sprint 2 (Week 2): Core Features
- US-3.1: Generate Avatar from Initials
- US-3.2: Customize Initials Style
- US-4.1: Set Custom Default Avatar

### Sprint 3 (Week 3): Polish & Testing
- US-1.2: Remove Avatar
- US-3.3: Low Bandwidth Mode
- T-6.1: Unit Test Coverage
- T-6.2: Integration Testing

### Sprint 4 (Week 4): QA & Documentation
- T-6.3: Compatibility Testing
- T-6.4: Security Audit
- D-7.1: User Documentation
- D-7.2: Developer Documentation

---

## Definition of Done

A user story is considered done when:

1. Code is complete and follows WordPress Coding Standards
2. Unit tests are written and passing
3. Integration tests are written and passing (where applicable)
4. Code is peer-reviewed (or self-reviewed with checklist)
5. Security considerations are addressed
6. Documentation is updated
7. Feature is tested manually in development environment
8. No new linting errors introduced
9. Performance impact is measured and acceptable
10. Changes are committed with clear commit message

---

## Notes

- Estimated efforts are in developer hours and may vary
- Priorities may be adjusted based on user feedback or technical constraints
- Epic 5 (Admin Settings) should be developed incrementally alongside feature epics
- Testing tasks should be distributed throughout development, not left to the end
- Security review should happen before any public release

---

## Related Documents

- [MVP Specification](mvp-spec.json) - Technical specifications and acceptance criteria
- [Work Plan](04_Plan_de_Trabajo.md) - Overall project timeline and phases
- [Product Requirements](01_Requerimiento_Producto.md) - Complete functional requirements
- [Architecture Guide](13_Arquitectura.md) - Technical architecture and design patterns
