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
		error_log( "=== AVATAR STEWARD UPLOAD DEBUG ===" );
		error_log( "UploadHandler::handle_profile_update called with user_id: " . $user_id );
		error_log( "POST data keys: " . ( ! empty( $_POST ) ? implode( ', ', array_keys( $_POST ) ) : 'empty' ) );
		error_log( "FILES data keys: " . ( ! empty( $_FILES ) ? implode( ', ', array_keys( $_FILES ) ) : 'empty' ) );

		// Check user permissions.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			error_log( "Permission check failed for user_id: " . $user_id );
			return;
		}
		error_log( "Permission check passed" );

		// Check nonce for security.
		$has_nonce = isset( $_POST['avatar_steward_nonce'] );
		error_log( "Nonce exists in POST: " . ( $has_nonce ? 'yes' : 'no' ) );
		if ( $has_nonce ) {
			$nonce_value = sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) );
			$nonce_valid = wp_verify_nonce( $nonce_value, 'avatar_steward_update' );
			error_log( "Nonce value: " . $nonce_value );
			error_log( "Nonce valid: " . ( $nonce_valid ? 'yes' : 'no' ) );
		}
		
		if ( ! $has_nonce || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) ), 'avatar_steward_update' ) ) {
			error_log( "Nonce verification failed" );
			return;
		}
		error_log( "Nonce verification passed" );

		// Handle avatar removal.
		$remove_requested = isset( $_POST['avatar_steward_remove'] ) && 'yes' === $_POST['avatar_steward_remove'];
		error_log( "Avatar removal requested: " . ( $remove_requested ? 'yes' : 'no' ) );
		if ( $remove_requested ) {
			error_log( "Processing avatar removal" );
			$this->upload_service->delete_avatar( $user_id );
			return;
		}

		// Check if file was uploaded.
		$has_files = ! empty( $_FILES['avatar_steward_file'] );
		$has_tmp_name = ! empty( $_FILES['avatar_steward_file']['tmp_name'] );
		error_log( "Files array exists: " . ( $has_files ? 'yes' : 'no' ) );
		error_log( "Temp file exists: " . ( $has_tmp_name ? 'yes' : 'no' ) );
		
		if ( ! $has_files || ! $has_tmp_name ) {
			error_log( "No file upload detected, exiting" );
			return;
		}

		// Process the upload.
		error_log( "Processing file upload..." );
		$result = $this->upload_service->process_upload( $_FILES['avatar_steward_file'], $user_id );
		error_log( "Upload result: " . wp_json_encode( $result ) );

		if ( $result['success'] && isset( $result['attachment_id'] ) ) {
			// Store the attachment ID in user meta.
			error_log( "Storing attachment_id " . $result['attachment_id'] . " for user " . $user_id );
			update_user_meta( $user_id, 'avatar_steward_avatar', $result['attachment_id'] );
		} else {
			// Store error message in a transient to display on next page load.
			$error_message = $result['message'] ?? __( 'Unknown error occurred.', 'avatar-steward' );
			error_log( "Upload failed, storing error: " . $error_message );
			set_transient( 'avatar_steward_error_' . $user_id, $error_message, 30 );
		}
		error_log( "=== END UPLOAD DEBUG ===" );
	}
}
