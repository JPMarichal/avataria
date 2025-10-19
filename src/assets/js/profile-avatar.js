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
		const profileForm = document.querySelector('.user-edit-php, .profile-php');
		if (!profileForm) {
			console.log('Avatar Steward: Profile form not found');
			return;
		}

		// Strategy 2: Find all form tables and position after "About Yourself" section
		const formTables = profileForm.querySelectorAll('.form-table');
		if (formTables.length < 5) {
			console.log('Avatar Steward: Expected at least 5 form tables, found ' + formTables.length);
			return;
		}

		// Look for the "About Yourself" section (usually the 5th table containing biographical info)
		// or search by content for more reliable positioning
		let aboutYourselfTable = null;
		
		// First try to find by looking for biographical info text
		for (let i = 0; i < formTables.length; i++) {
			const table = formTables[i];
			const text = table.textContent || table.innerText;
			if (text.includes('Información biográfica') || text.includes('biográfica') || 
			    text.includes('Biographical Info') || text.includes('About Yourself') ||
			    text.includes('Imagen de perfil') || text.includes('Profile Picture')) {
				aboutYourselfTable = table;
				console.log('Avatar Steward: Found "About Yourself" section by content search at table index ' + i);
				break;
			}
		}
		
		// Fallback: use the 5th table (index 4) if content search fails
		if (!aboutYourselfTable && formTables.length >= 5) {
			aboutYourselfTable = formTables[4];
			console.log('Avatar Steward: Using 5th table as fallback for "About Yourself" section');
		}
		
		if (!aboutYourselfTable) {
			console.log('Avatar Steward: Could not locate "About Yourself" section');
			return;
		}

		// Insert after the "About Yourself" table
		const insertionPoint = aboutYourselfTable.nextElementSibling;
		
		if (insertionPoint) {
			aboutYourselfTable.parentNode.insertBefore(avatarSection, insertionPoint);
			console.log('Avatar Steward: Avatar section repositioned after "About Yourself" section');
		} else {
			// If no next sibling, append after the about yourself table
			aboutYourselfTable.parentNode.appendChild(avatarSection);
			console.log('Avatar Steward: Avatar section appended after "About Yourself" section (no next sibling)');
		}
	}	// Run when DOM is ready
	function initAvatarSteward() {
		console.log('Avatar Steward JS: Initializing...');
		try {
			repositionAvatarSection();
			
			// Add form submission debug
			const form = document.querySelector('form#your-profile');
			if (form) {
				console.log('Avatar Steward: Profile form found, adding submit listener');
				
				form.addEventListener('submit', function(e) {
					console.log('🚀 Avatar Steward: Form submission detected!');
					
					const fileInput = document.querySelector('input[name="avatar_steward_file"]');
					const nonceInput = document.querySelector('input[name="avatar_steward_nonce"]');
					
					console.log('📁 File input found:', !!fileInput);
					console.log('🔐 Nonce input found:', !!nonceInput);
					
					if (fileInput) {
						console.log('📄 File selected:', fileInput.files.length > 0);
						if (fileInput.files.length > 0) {
							console.log('📝 File name:', fileInput.files[0].name);
							console.log('📏 File size:', fileInput.files[0].size, 'bytes');
						}
					}
					
					if (nonceInput) {
						console.log('🔑 Nonce value:', nonceInput.value);
					}
				});
			} else {
				console.log('Avatar Steward: Profile form NOT found');
			}
			
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
