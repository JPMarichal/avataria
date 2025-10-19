/**
 * Avatar Steward - Profile Page Avatar Section Repositioning
 *
 * Moves the avatar section to appear after the "About Yourself" section
 * and before the "Account Management" section on the WordPress user profile page.
 *
 * @package AvatarSteward
 */

(function() {
	'use strict';

	/**
	 * Reposition the Avatar section on page load
	 */
	function repositionAvatarSection() {
		// Find the Avatar section
		const avatarSection = document.getElementById('avatar-steward-section');
		if (!avatarSection) {
			console.log('Avatar Steward: Avatar section not found');
			return;
		}

		console.log('Avatar Steward: Found avatar section, attempting to reposition...');

		// Strategy 1: Look for WordPress profile form structure
		// In WordPress admin, profile forms have specific classes and structures
		const profileForm = document.querySelector('.user-edit-php, .profile-php');
		if (!profileForm) {
			console.log('Avatar Steward: Profile form not found');
			return;
		}

		// Strategy 2: Find the first form-table (usually "Personal Options" or similar)
		const formTables = profileForm.querySelectorAll('.form-table');
		if (formTables.length === 0) {
			console.log('Avatar Steward: No form tables found');
			return;
		}

		// Insert after the first form-table (which is typically "Personal Options")
		const firstTable = formTables[0];
		const insertionPoint = firstTable.nextElementSibling;
		
		if (insertionPoint) {
			firstTable.parentNode.insertBefore(avatarSection, insertionPoint);
			console.log('Avatar Steward: Avatar section repositioned after first form table');
		} else {
			// If no next sibling, append after the first table
			firstTable.parentNode.insertBefore(avatarSection, firstTable.nextSibling);
			console.log('Avatar Steward: Avatar section repositioned as next sibling of first form table');
		}
	}	// Run when DOM is ready
	function initAvatarSteward() {
		console.log('Avatar Steward JS: Initializing...');
		try {
			repositionAvatarSection();
		} catch (error) {
			console.error('Avatar Steward JS: Error during repositioning:', error);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAvatarSteward);
		console.log('Avatar Steward JS: Waiting for DOMContentLoaded...');
	} else {
		// DOM is already loaded
		console.log('Avatar Steward JS: DOM already loaded, executing immediately...');
		initAvatarSteward();
	}

	// Confirm script loaded
	console.log('Avatar Steward JS: Script loaded and ready');
})();
