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
			
			// Fix form enctype to allow file uploads
			const form = document.querySelector('form#your-profile');
			if (form && form.getAttribute('enctype') !== 'multipart/form-data') {
				form.setAttribute('enctype', 'multipart/form-data');
			}

			// Initialize library selector button
			initLibrarySelector();
			
		} catch (error) {
			console.error('Avatar Steward JS: Error during repositioning:', error);
		}
	}

	/**
	 * Initialize library selector functionality
	 */
	function initLibrarySelector() {
		const libraryBtn = document.getElementById('avatar-library-select-btn');
		if (!libraryBtn) {
			console.log('Avatar Steward: Library selector button not found');
			return;
		}

		libraryBtn.addEventListener('click', function(e) {
			e.preventDefault();
			
			// Create modal for library selection
			showLibraryModal();
		});
	}

	/**
	 * Show library selection modal
	 */
	function showLibraryModal() {
		// Create modal overlay
		const modal = document.createElement('div');
		modal.id = 'avatar-library-modal';
		modal.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;';

		// Create modal content
		const modalContent = document.createElement('div');
		modalContent.style.cssText = 'background: #fff; padding: 30px; border-radius: 4px; max-width: 90%; max-height: 90%; overflow-y: auto; width: 800px;';
		modalContent.innerHTML = '<h2>' + (window.avatarStewardStrings ? window.avatarStewardStrings.selectFromLibrary : 'Select from Library') + '</h2><p>Loading avatars...</p>';

		modal.appendChild(modalContent);
		document.body.appendChild(modal);

		// Close on background click
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.remove();
			}
		});

		// Load library avatars via AJAX
		loadLibraryAvatars(modalContent);
	}

	/**
	 * Load library avatars via REST API
	 */
	function loadLibraryAvatars(container) {
		const apiUrl = window.wpApiSettings ? window.wpApiSettings.root + 'avatar-steward/v1/library' : '/wp-json/avatar-steward/v1/library';
		
		fetch(apiUrl, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.wpApiSettings ? window.wpApiSettings.nonce : ''
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.items && data.items.length > 0) {
				renderLibraryGrid(container, data.items);
			} else {
				container.innerHTML = '<h2>Select from Library</h2><p>No avatars found in the library.</p>';
			}
		})
		.catch(error => {
			console.error('Error loading library:', error);
			container.innerHTML = '<h2>Select from Library</h2><p>Error loading library avatars. Please try again.</p>';
		});
	}

	/**
	 * Render library avatar grid
	 */
	function renderLibraryGrid(container, items) {
		let html = '<h2>Select from Library</h2>';
		html += '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px; margin-top: 20px;">';

		items.forEach(function(item) {
			html += '<div class="library-avatar-item" data-id="' + item.id + '" style="cursor: pointer; border: 2px solid transparent; padding: 10px; text-align: center; transition: border-color 0.3s;">';
			html += '<img src="' + item.thumb + '" alt="' + item.title + '" style="max-width: 100%; display: block; margin: 0 auto 5px;">';
			html += '<div style="font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + item.title + '</div>';
			html += '</div>';
		});

		html += '</div>';
		html += '<button type="button" class="button" id="close-library-modal" style="margin-top: 20px;">Close</button>';

		container.innerHTML = html;

		// Add click handlers
		container.querySelectorAll('.library-avatar-item').forEach(function(item) {
			item.addEventListener('click', function() {
				selectLibraryAvatar(this.dataset.id);
				document.getElementById('avatar-library-modal').remove();
			});

			item.addEventListener('mouseenter', function() {
				this.style.borderColor = '#2271b1';
			});

			item.addEventListener('mouseleave', function() {
				this.style.borderColor = 'transparent';
			});
		});

		// Close button
		container.querySelector('#close-library-modal').addEventListener('click', function() {
			document.getElementById('avatar-library-modal').remove();
		});
	}

	/**
	 * Select an avatar from the library
	 */
	function selectLibraryAvatar(attachmentId) {
		// Set the avatar by updating a hidden field
		let hiddenField = document.getElementById('avatar_steward_library_id');
		if (!hiddenField) {
			hiddenField = document.createElement('input');
			hiddenField.type = 'hidden';
			hiddenField.name = 'avatar_steward_library_id';
			hiddenField.id = 'avatar_steward_library_id';
			document.getElementById('avatar-steward-container').appendChild(hiddenField);
		}
		hiddenField.value = attachmentId;

		// Update preview
		updateAvatarPreview(attachmentId);
	}

	/**
	 * Update avatar preview
	 */
	function updateAvatarPreview(attachmentId) {
		const apiUrl = (window.wpApiSettings ? window.wpApiSettings.root : '/wp-json/') + 'avatar-steward/v1/library/' + attachmentId;
		
		fetch(apiUrl, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window.wpApiSettings ? window.wpApiSettings.nonce : ''
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.thumb) {
				let preview = document.getElementById('avatar-steward-preview');
				if (!preview) {
					preview = document.createElement('div');
					preview.id = 'avatar-steward-preview';
					preview.style.marginBottom = '10px';
					document.getElementById('avatar-steward-container').insertBefore(preview, document.getElementById('avatar-steward-container').firstChild);
				}
				preview.innerHTML = '<img src="' + data.thumb + '" alt="Selected Avatar" style="max-width: 96px; max-height: 96px; display: block; margin-bottom: 10px;" />';
			}
		})
		.catch(error => {
			console.error('Error updating preview:', error);
		});
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
