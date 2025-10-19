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
		error_log( "UploadHandler: Registering WordPress hooks for profile updates" );
		add_action( 'personal_options_update', array( $this, 'handle_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'handle_profile_update' ) );
		error_log( "UploadHandler: Hooks registered successfully" );
	}

	/**
	 * Handle avatar upload on profile update.
	 *
	 * @param int $user_id User ID being updated.
	 *
	 * @return void
	 */
	public function handle_profile_update( int $user_id ): void {
		// Debug visible en la página
		echo '<div style="background: #ffeb3b; padding: 10px; margin: 10px; border: 2px solid #f57f17; border-radius: 5px; font-family: monospace;">';
		echo '<strong>🔧 AVATAR STEWARD DEBUG:</strong><br>';
		echo '📋 Function: handle_profile_update<br>';
		echo '👤 User ID: ' . $user_id . '<br>';
		echo '📤 POST keys: ' . ( ! empty( $_POST ) ? implode( ', ', array_keys( $_POST ) ) : 'empty' ) . '<br>';
		echo '📁 FILES keys: ' . ( ! empty( $_FILES ) ? implode( ', ', array_keys( $_FILES ) ) : 'empty' ) . '<br>';

		// Check user permissions.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			echo '❌ ERROR: Permission check failed for user_id: ' . $user_id . '<br>';
			echo '</div>';
			return;
		}
		echo '✅ Permission check passed<br>';

		// Check nonce for security.
		$has_nonce = isset( $_POST['avatar_steward_nonce'] );
		echo '🔐 Nonce present: ' . ( $has_nonce ? 'yes' : 'no' ) . '<br>';
		if ( $has_nonce ) {
			$nonce_value = sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) );
			$nonce_valid = wp_verify_nonce( $nonce_value, 'avatar_steward_update' );
			echo '🔑 Nonce value: ' . $nonce_value . '<br>';
			echo '🔍 Nonce valid: ' . ( $nonce_valid ? 'yes' : 'no' ) . '<br>';
		}
		
		if ( ! $has_nonce || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) ), 'avatar_steward_update' ) ) {
			echo '❌ ERROR: Nonce verification failed<br>';
			echo '</div>';
			return;
		}
		echo '✅ Nonce verification passed<br>';

		// Handle avatar removal.
		$remove_requested = isset( $_POST['avatar_steward_remove'] ) && 'yes' === $_POST['avatar_steward_remove'];
		echo '🗑️ Avatar removal requested: ' . ( $remove_requested ? 'yes' : 'no' ) . '<br>';
		if ( $remove_requested ) {
			echo '🗑️ Processing avatar removal<br>';
			$this->upload_service->delete_avatar( $user_id );
			echo '</div>';
			return;
		}

		// Check if file was uploaded.
		$has_files = ! empty( $_FILES['avatar_steward_file'] );
		$has_tmp_name = ! empty( $_FILES['avatar_steward_file']['tmp_name'] );
		echo '📁 Files array exists: ' . ( $has_files ? 'yes' : 'no' ) . '<br>';
		echo '📄 Temp file exists: ' . ( $has_tmp_name ? 'yes' : 'no' ) . '<br>';
		
		if ( ! $has_files || ! $has_tmp_name ) {
			echo '❌ No file upload detected, exiting<br>';
			echo '</div>';
			return;
		}

		// Process the upload.
		echo '📤 Processing file upload...<br>';
		$result = $this->upload_service->process_upload( $_FILES['avatar_steward_file'], $user_id );
		echo '📊 Upload result: ' . wp_json_encode( $result ) . '<br>';

		if ( $result['success'] && isset( $result['attachment_id'] ) ) {
			// Store the attachment ID in user meta.
			echo '✅ Storing attachment_id ' . $result['attachment_id'] . ' for user ' . $user_id . '<br>';
			update_user_meta( $user_id, 'avatar_steward_avatar', $result['attachment_id'] );
		} else {
			// Store error message in a transient to display on next page load.
			$error_message = $result['message'] ?? __( 'Unknown error occurred.', 'avatar-steward' );
			echo '❌ Upload failed, storing error: ' . $error_message . '<br>';
			set_transient( 'avatar_steward_error_' . $user_id, $error_message, 30 );
		}
		echo '📋 === END UPLOAD DEBUG ===<br>';
		echo '</div>';
	}
}
