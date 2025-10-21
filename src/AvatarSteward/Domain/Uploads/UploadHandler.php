<?php
/**
 * Upload Handler for WordPress hooks integration.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Uploads;

use AvatarSteward\Domain\Moderation\ModerationQueue;

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
	 * Moderation queue instance.
	 *
	 * @var ModerationQueue|null
	 */
	private ?ModerationQueue $moderation_queue;

	/**
	 * Constructor.
	 *
	 * @param UploadService         $upload_service    Upload service instance.
	 * @param ModerationQueue|null  $moderation_queue  Optional moderation queue instance.
	 */
	public function __construct( UploadService $upload_service, ?ModerationQueue $moderation_queue = null ) {
		$this->upload_service    = $upload_service;
		$this->moderation_queue  = $moderation_queue;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		error_log( 'UploadHandler: Registering WordPress hooks for profile updates' );
		add_action( 'personal_options_update', array( $this, 'handle_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'handle_profile_update' ) );
		error_log( 'UploadHandler: Hooks registered successfully' );
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
		$has_nonce = isset( $_POST['avatar_steward_nonce'] );
		if ( ! $has_nonce || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['avatar_steward_nonce'] ) ), 'avatar_steward_update' ) ) {
			return;
		}

		// Handle avatar removal.
		$remove_requested = isset( $_POST['avatar_steward_remove'] ) && 'yes' === $_POST['avatar_steward_remove'];
		if ( $remove_requested ) {
			// Get the delete_attachment setting.
			$delete_attachment = false;
			if ( function_exists( 'get_option' ) ) {
				$options           = get_option( 'avatar_steward_options', array() );
				$delete_attachment = ! empty( $options['delete_attachment_on_remove'] );
			}

			$this->upload_service->delete_avatar( $user_id, $delete_attachment );
			return;
		}

		// Check if file was uploaded.
		$has_files    = ! empty( $_FILES['avatar_steward_file'] );
		$has_tmp_name = ! empty( $_FILES['avatar_steward_file']['tmp_name'] );

		if ( ! $has_files || ! $has_tmp_name ) {
			return;
		}

		// Process the upload.
		$result = $this->upload_service->process_upload( $_FILES['avatar_steward_file'], $user_id );

		if ( $result['success'] && isset( $result['attachment_id'] ) ) {
			// Check if moderation is required.
			$require_approval = $this->is_moderation_required();

			if ( $require_approval && $this->moderation_queue ) {
				// Store previous avatar for potential revert.
				$previous_avatar = get_user_meta( $user_id, 'avatar_steward_avatar', true );
				if ( $previous_avatar ) {
					$this->moderation_queue->store_previous_avatar( $user_id, (int) $previous_avatar );
				}

				// Set status to pending moderation.
				$this->moderation_queue->set_status( $user_id, ModerationQueue::STATUS_PENDING );
			}

			// Store the attachment ID in user meta.
			update_user_meta( $user_id, 'avatar_steward_avatar', $result['attachment_id'] );
		} else {
			// Store error message in a transient to display on next page load.
			$error_message = $result['message'] ?? __( 'Unknown error occurred.', 'avatar-steward' );
			set_transient( 'avatar_steward_error_' . $user_id, $error_message, 30 );
		}
	}

	/**
	 * Check if moderation is required based on settings.
	 *
	 * @return bool True if moderation is required.
	 */
	private function is_moderation_required(): bool {
		$options = get_option( 'avatar_steward_options', array() );
		return ! empty( $options['require_approval'] );
	}
}
