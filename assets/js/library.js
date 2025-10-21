/**
 * Avatar Library Admin JavaScript
 */

(function ($) {
	'use strict';

	const AvatarLibrary = {
		/**
		 * Initialize the library.
		 */
		init: function () {
			this.bindEvents();
		},

		/**
		 * Bind event handlers.
		 */
		bindEvents: function () {
			// Upload button click.
			$(document).on('click', '.avatar-library-upload-btn', this.handleUploadClick.bind(this));

			// Import button click.
			$(document).on('click', '.avatar-library-import-btn', this.handleImportClick.bind(this));

			// Delete button click.
			$(document).on('click', '.avatar-library-delete', this.handleDeleteClick.bind(this));

			// Select button click.
			$(document).on('click', '.avatar-library-select', this.handleSelectClick.bind(this));
		},

		/**
		 * Handle upload button click.
		 */
		handleUploadClick: function (e) {
			e.preventDefault();

			const self = this;

			// Create media frame.
			const frame = wp.media({
				title: avatarLibrary.strings.uploadTitle,
				button: {
					text: avatarLibrary.strings.uploadButton
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			// When an image is selected.
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();

				// Show metadata form.
				self.showMetadataForm(attachment);
			});

			frame.open();
		},

		/**
		 * Show metadata form for uploaded avatar.
		 *
		 * @param {Object} attachment Attachment data.
		 */
		showMetadataForm: function (attachment) {
			const self = this;

			// Create modal HTML.
			const modal = $('<div class="avatar-library-metadata-modal"></div>');
			const form = $('<form></form>');

			form.html(`
				<h2>${avatarLibrary.strings.uploadTitle}</h2>
				<p>
					<label for="avatar-author">${wp.i18n.__('Author:', 'avatar-steward')}</label>
					<input type="text" id="avatar-author" name="author" class="regular-text">
				</p>
				<p>
					<label for="avatar-license">${wp.i18n.__('License:', 'avatar-steward')}</label>
					<input type="text" id="avatar-license" name="license" class="regular-text">
				</p>
				<p>
					<label for="avatar-sector">${wp.i18n.__('Sector:', 'avatar-steward')}</label>
					<input type="text" id="avatar-sector" name="sector" class="regular-text">
				</p>
				<p>
					<label for="avatar-tags">${wp.i18n.__('Tags (comma-separated):', 'avatar-steward')}</label>
					<input type="text" id="avatar-tags" name="tags" class="regular-text">
				</p>
				<p class="submit">
					<button type="submit" class="button button-primary">${wp.i18n.__('Add to Library', 'avatar-steward')}</button>
					<button type="button" class="button avatar-metadata-cancel">${wp.i18n.__('Cancel', 'avatar-steward')}</button>
				</p>
			`);

			modal.append(form);
			$('body').append(modal);

			// Handle form submission.
			form.on('submit', function (e) {
				e.preventDefault();

				const formData = new FormData();
				formData.append('action', 'avatar_library_upload');
				formData.append('nonce', avatarLibrary.nonce);
				formData.append('attachment_id', attachment.id);
				formData.append('author', $('#avatar-author').val());
				formData.append('license', $('#avatar-license').val());
				formData.append('sector', $('#avatar-sector').val());
				formData.append('tags', $('#avatar-tags').val());

				$.ajax({
					url: avatarLibrary.ajaxUrl,
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function (response) {
						if (response.success) {
							alert(response.data.message);
							location.reload();
						} else {
							alert(response.data.message || avatarLibrary.strings.errorGeneric);
						}
					},
					error: function () {
						alert(avatarLibrary.strings.errorGeneric);
					},
					complete: function () {
						modal.remove();
					}
				});
			});

			// Handle cancel button.
			form.on('click', '.avatar-metadata-cancel', function () {
				modal.remove();
			});

			// Style the modal.
			modal.css({
				position: 'fixed',
				top: 0,
				left: 0,
				right: 0,
				bottom: 0,
				background: 'rgba(0, 0, 0, 0.5)',
				zIndex: 9999,
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center'
			});

			form.css({
				background: '#fff',
				padding: '30px',
				borderRadius: '4px',
				maxWidth: '500px',
				width: '100%'
			});
		},

		/**
		 * Handle import button click.
		 */
		handleImportClick: function (e) {
			e.preventDefault();
			$('#avatar-library-import-modal').show();
		},

		/**
		 * Handle delete button click.
		 *
		 * @param {Event} e Event object.
		 */
		handleDeleteClick: function (e) {
			e.preventDefault();

			if (!confirm(avatarLibrary.strings.deleteConfirm)) {
				return;
			}

			const $button = $(e.currentTarget);
			const attachmentId = $button.data('id');
			const $item = $button.closest('.avatar-library-item');

			$item.addClass('avatar-library-loading');

			$.ajax({
				url: avatarLibrary.ajaxUrl,
				type: 'POST',
				data: {
					action: 'avatar_library_delete',
					nonce: avatarLibrary.nonce,
					attachment_id: attachmentId
				},
				success: function (response) {
					if (response.success) {
						$item.fadeOut(300, function () {
							$(this).remove();
						});
					} else {
						alert(response.data.message || avatarLibrary.strings.errorGeneric);
						$item.removeClass('avatar-library-loading');
					}
				},
				error: function () {
					alert(avatarLibrary.strings.errorGeneric);
					$item.removeClass('avatar-library-loading');
				}
			});
		},

		/**
		 * Handle select button click.
		 *
		 * @param {Event} e Event object.
		 */
		handleSelectClick: function (e) {
			e.preventDefault();

			const $button = $(e.currentTarget);
			const attachmentId = $button.data('id');

			// Trigger custom event for integration.
			$(document).trigger('avatar-library:select', [attachmentId]);

			// If in a WordPress media frame context, close it.
			if (window.parent !== window) {
				window.parent.jQuery(window.parent.document).trigger('avatar-library:select', [attachmentId]);
			}
		}
	};

	// Initialize when document is ready.
	$(document).ready(function () {
		AvatarLibrary.init();
	});

})(jQuery);
