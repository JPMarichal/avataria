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
			return;
		}

		// Find the "About Yourself" section (h2 with text "About Yourself" or localized version)
		// In WordPress, this is typically identified by looking for specific form-table sections
		const allH2s = document.querySelectorAll('.user-edit-php h2, .profile-php h2');
		let aboutYourselfSection = null;
		
		// Look for the "About Yourself" heading or similar
		for (let i = 0; i < allH2s.length; i++) {
			const h2Text = allH2s[i].textContent.trim().toLowerCase();
			// Match English or Spanish versions of "About Yourself"
			if (h2Text.includes('about yourself') || 
				h2Text.includes('acerca de ti') ||
				h2Text.includes('sobre ti') ||
				h2Text.includes('name') && i === 0) {
				aboutYourselfSection = allH2s[i];
				break;
			}
		}

		// If we found the "About Yourself" section, move Avatar section after it
		if (aboutYourselfSection) {
			// Find the next form-table after "About Yourself"
			let nextElement = aboutYourselfSection.nextElementSibling;
			while (nextElement && !nextElement.classList.contains('form-table')) {
				nextElement = nextElement.nextElementSibling;
			}
			
			if (nextElement && nextElement.classList.contains('form-table')) {
				// Insert Avatar section after the "About Yourself" form-table
				nextElement.parentNode.insertBefore(avatarSection, nextElement.nextSibling);
			}
		} else {
			// Fallback: try to position after the first form-table
			const firstFormTable = document.querySelector('.user-edit-php .form-table, .profile-php .form-table');
			if (firstFormTable && firstFormTable.nextElementSibling) {
				firstFormTable.parentNode.insertBefore(avatarSection, firstFormTable.nextElementSibling);
			}
		}
	}

	// Run when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', repositionAvatarSection);
	} else {
		// DOM is already loaded
		repositionAvatarSection();
	}
})();
