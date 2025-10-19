<?php
/**
 * Upload Handler for WordPress hooks integration.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Uploads;

/**
 * Handles WordPress profile update hooks for avatar uploads.
 */
class UploadHandler {

	/**
	 * Upload service instance.
	 *
	 * @var UploadService
	 */
	private UploadService $upload_service;

	/**
	 * Constructor.
	 *
	 * @param UploadService $upload_service Upload service instance.
	 */
	public function __construct( UploadService $upload_service ) {
		$this->upload_service = $upload_service;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'personal_options_update', array( $this, 'handle_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'handle_profile_update' ) );
	}

	/**
	 * Handle avatar upload on profile update.
	 *
	 * @param int $user_id User ID being updated.
	 *
	 * @return void
	 */
	public function handle_profile_update( int $user_id ): void {
		// Check user permissions.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		// Check nonce for security.
		if ( ! isset( $_POST['avatar_steward_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) ), 'avatar_steward_update' ) ) {
			return;
		}

		// Handle avatar removal.
		if ( isset( $_POST['avatar_steward_remove'] ) && 'yes' === $_POST['avatar_steward_remove'] ) {
			$this->upload_service->delete_avatar( $user_id );
			return;
		}

		// Check if file was uploaded.
		if ( empty( $_FILES['avatar_steward_file'] ) || empty( $_FILES['avatar_steward_file']['tmp_name'] ) ) {
			return;
		}

		// Process the upload.
		$result = $this->upload_service->process_upload( $_FILES['avatar_steward_file'], $user_id );

		if ( $result['success'] && isset( $result['attachment_id'] ) ) {
			// Store the attachment ID in user meta.
			update_user_meta( $user_id, 'avatar_steward_avatar', $result['attachment_id'] );
		} else {
			// Store error message in a transient to display on next page load.
			set_transient( 'avatar_steward_error_' . $user_id, $result['message'] ?? __( 'Unknown error occurred.', 'avatar-steward' ), 30 );
		}
	}
}
