<?php
/**
 * Upload Service class for avatar file handling.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Uploads;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Handles avatar file uploads, validation, and storage.
 */
class UploadService {

	/**
	 * Maximum file size in bytes (2MB default).
	 *
	 * @var int
	 */
	private int $max_file_size;

	/**
	 * Maximum width in pixels.
	 *
	 * @var int
	 */
	private int $max_width;

	/**
	 * Maximum height in pixels.
	 *
	 * @var int
	 */
	private int $max_height;

	/**
	 * Allowed MIME types.
	 *
	 * @var array<string>
	 */
	private array $allowed_mime_types;

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param int                  $max_file_size      Maximum file size in bytes.
	 * @param int                  $max_width          Maximum width in pixels.
	 * @param int                  $max_height         Maximum height in pixels.
	 * @param array<string>        $allowed_mime_types Allowed MIME types.
	 * @param LoggerInterface|null $logger             Optional logger instance.
	 */
	public function __construct(
		int $max_file_size = 2097152,
		int $max_width = 4000,
		int $max_height = 4000,
		array $allowed_mime_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ),
		?LoggerInterface $logger = null
	) {
		$this->max_file_size      = $max_file_size;
		$this->max_width          = $max_width;
		$this->max_height         = $max_height;
		$this->allowed_mime_types = $allowed_mime_types;
		$this->logger             = $logger;
	}

	/**
	 * Validate an uploaded file.
	 *
	 * @param array<string, mixed> $file The $_FILES array element for the uploaded file.
	 *
	 * @return array{success: bool, message?: string} Validation result.
	 */
	public function validate_file( array $file ): array {
		if ( $this->logger ) {
			$this->logger->debug( 'Starting file validation', array( 'file_name' => $file['name'] ?? 'unknown' ) );
		}

		// Check if file was uploaded.
		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			if ( $this->logger ) {
				$this->logger->warning( 'File upload validation failed: no file uploaded' );
			}
			return array(
				'success' => false,
				'message' => __( 'No file was uploaded.', 'avatar-steward' ),
			);
		}

		// Check for upload errors.
		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			if ( $this->logger ) {
				$this->logger->error( 'File upload error', array( 'error_code' => $file['error'] ) );
			}
			return array(
				'success' => false,
				'message' => $this->get_upload_error_message( $file['error'] ),
			);
		}

		// Check file size.
		if ( $file['size'] > $this->max_file_size ) {
			if ( $this->logger ) {
				$this->logger->warning(
					'File size exceeds limit',
					array(
						'file_size'     => $file['size'],
						'max_file_size' => $this->max_file_size,
					)
				);
			}
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: maximum file size in MB */
					__( 'File size exceeds the maximum allowed size of %s MB.', 'avatar-steward' ),
					number_format( $this->max_file_size / 1048576, 2 )
				),
			);
		}

		// Check MIME type.
		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		if ( ! in_array( $mime_type, $this->allowed_mime_types, true ) ) {
			if ( $this->logger ) {
				$this->logger->warning(
					'Invalid MIME type',
					array(
						'detected_mime' => $mime_type,
						'allowed_types' => $this->allowed_mime_types,
					)
				);
			}
			return array(
				'success' => false,
				'message' => __( 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.', 'avatar-steward' ),
			);
		}

		// Check image dimensions.
		$image_info = getimagesize( $file['tmp_name'] );
		if ( false === $image_info ) {
			if ( $this->logger ) {
				$this->logger->error( 'Unable to read image file dimensions' );
			}
			return array(
				'success' => false,
				'message' => __( 'Unable to read image file.', 'avatar-steward' ),
			);
		}

		list( $width, $height ) = $image_info;

		if ( $width > $this->max_width || $height > $this->max_height ) {
			if ( $this->logger ) {
				$this->logger->warning(
					'Image dimensions exceed limit',
					array(
						'width'      => $width,
						'height'     => $height,
						'max_width'  => $this->max_width,
						'max_height' => $this->max_height,
					)
				);
			}
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: 1: maximum width, 2: maximum height */
					__( 'Image dimensions exceed the maximum allowed size of %1$dx%2$d pixels.', 'avatar-steward' ),
					$this->max_width,
					$this->max_height
				),
			);
		}

		if ( $this->logger ) {
			$this->logger->info(
				'File validation successful',
				array(
					'file_name' => $file['name'],
					'mime_type' => $mime_type,
					'width'     => $width,
					'height'    => $height,
					'size'      => $file['size'],
				)
			);
		}

		return array( 'success' => true );
	}

	/**
	 * Get error message for upload error code.
	 *
	 * @param int $error_code PHP upload error code.
	 *
	 * @return string Error message.
	 */
	private function get_upload_error_message( int $error_code ): string {
		$messages = array(
			UPLOAD_ERR_INI_SIZE   => __( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'avatar-steward' ),
			UPLOAD_ERR_FORM_SIZE  => __( 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.', 'avatar-steward' ),
			UPLOAD_ERR_PARTIAL    => __( 'The uploaded file was only partially uploaded.', 'avatar-steward' ),
			UPLOAD_ERR_NO_FILE    => __( 'No file was uploaded.', 'avatar-steward' ),
			UPLOAD_ERR_NO_TMP_DIR => __( 'Missing a temporary folder.', 'avatar-steward' ),
			UPLOAD_ERR_CANT_WRITE => __( 'Failed to write file to disk.', 'avatar-steward' ),
			UPLOAD_ERR_EXTENSION  => __( 'A PHP extension stopped the file upload.', 'avatar-steward' ),
		);

		return $messages[ $error_code ] ?? __( 'Unknown upload error.', 'avatar-steward' );
	}

	/**
	 * Process and store uploaded avatar.
	 *
	 * @param array<string, mixed> $file    The $_FILES array element for the uploaded file.
	 * @param int                  $user_id User ID to associate the avatar with.
	 *
	 * @return array{success: bool, attachment_id?: int, message?: string} Processing result.
	 */
	public function process_upload( array $file, int $user_id ): array {
		if ( $this->logger ) {
			$this->logger->info( 'Processing avatar upload', array( 'user_id' => $user_id ) );
		}

		// Validate the file first.
		$validation = $this->validate_file( $file );
		if ( ! $validation['success'] ) {
			return $validation;
		}

		// Prepare for WordPress upload.
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// Handle the upload using WordPress API.
		$upload_overrides = array(
			'test_form' => false,
			'mimes'     => array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png'          => 'image/png',
				'gif'          => 'image/gif',
				'webp'         => 'image/webp',
			),
		);

		$uploaded_file = wp_handle_upload( $file, $upload_overrides );

		if ( isset( $uploaded_file['error'] ) ) {
			if ( $this->logger ) {
				$this->logger->error(
					'WordPress upload handler failed',
					array(
						'error'   => $uploaded_file['error'],
						'user_id' => $user_id,
					)
				);
			}
			return array(
				'success' => false,
				'message' => $uploaded_file['error'],
			);
		}

		// Create attachment in media library.
		$attachment = array(
			'post_mime_type' => $uploaded_file['type'],
			'post_title'     => sprintf( 'Avatar for user %d', $user_id ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attachment_id = wp_insert_attachment( $attachment, $uploaded_file['file'], 0 );

		if ( is_wp_error( $attachment_id ) ) {
			if ( $this->logger ) {
				$this->logger->error(
					'Failed to create attachment',
					array(
						'error'   => $attachment_id->get_error_message(),
						'user_id' => $user_id,
					)
				);
			}
			return array(
				'success' => false,
				'message' => $attachment_id->get_error_message(),
			);
		}

		// Generate attachment metadata.
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $uploaded_file['file'] );
		wp_update_attachment_metadata( $attachment_id, $attach_data );

		if ( $this->logger ) {
			$this->logger->info(
				'Avatar upload processed successfully',
				array(
					'user_id'       => $user_id,
					'attachment_id' => $attachment_id,
					'file_path'     => $uploaded_file['file'],
				)
			);
		}

		return array(
			'success'       => true,
			'attachment_id' => $attachment_id,
		);
	}

	/**
	 * Delete a user's avatar.
	 *
	 * @param int  $user_id            User ID.
	 * @param bool $delete_attachment  Whether to delete the attachment file from media library.
	 *
	 * @return bool Whether the avatar was deleted successfully.
	 */
	public function delete_avatar( int $user_id, bool $delete_attachment = false ): bool {
		if ( $this->logger ) {
			$this->logger->info(
				'Deleting avatar',
				array(
					'user_id'           => $user_id,
					'delete_attachment' => $delete_attachment,
				)
			);
		}

		$avatar_id = get_user_meta( $user_id, 'avatar_steward_avatar', true );

		if ( ! $avatar_id ) {
			if ( $this->logger ) {
				$this->logger->debug( 'No avatar found to delete', array( 'user_id' => $user_id ) );
			}
			return false;
		}

		// Remove user meta.
		delete_user_meta( $user_id, 'avatar_steward_avatar' );

		// Delete the attachment if requested.
		if ( $delete_attachment && function_exists( 'wp_delete_attachment' ) ) {
			// Check if the attachment is used by other users before deleting.
			if ( ! $this->is_attachment_used_by_others( (int) $avatar_id, $user_id ) ) {
				$deleted = wp_delete_attachment( (int) $avatar_id, true );
				if ( $this->logger ) {
					if ( $deleted ) {
						$this->logger->info(
							'Avatar attachment deleted from media library',
							array(
								'user_id'   => $user_id,
								'avatar_id' => $avatar_id,
							)
						);
					} else {
						$this->logger->warning(
							'Failed to delete avatar attachment',
							array(
								'user_id'   => $user_id,
								'avatar_id' => $avatar_id,
							)
						);
					}
				}
			} elseif ( $this->logger ) {
				$this->logger->info(
					'Avatar attachment not deleted - it is used by other users',
					array(
						'user_id'   => $user_id,
						'avatar_id' => $avatar_id,
					)
				);
			}
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Avatar deleted successfully',
				array(
					'user_id'   => $user_id,
					'avatar_id' => $avatar_id,
				)
			);
		}

		return true;
	}

	/**
	 * Check if an attachment is used by other users as their avatar.
	 *
	 * @param int $attachment_id  Attachment ID to check.
	 * @param int $exclude_user_id User ID to exclude from the check.
	 *
	 * @return bool True if attachment is used by other users, false otherwise.
	 */
	private function is_attachment_used_by_others( int $attachment_id, int $exclude_user_id ): bool {
		if ( ! function_exists( 'get_users' ) ) {
			return false;
		}

		// Query for users who have this attachment as their avatar, excluding the current user.
		$users = get_users(
			array(
				'meta_key'    => 'avatar_steward_avatar',
				'meta_value'  => $attachment_id,
				'exclude'     => array( $exclude_user_id ),
				'count_total' => false,
				'number'      => 1,
			)
		);

		return ! empty( $users );
	}

	/**
	 * Get avatar URL for a user.
	 *
	 * @param int $user_id User ID.
	 * @param int $size    Avatar size in pixels.
	 *
	 * @return string|false Avatar URL or false if not found.
	 */
	public function get_avatar_url( int $user_id, int $size = 96 ) {
		$avatar_id = get_user_meta( $user_id, 'avatar_steward_avatar', true );

		if ( ! $avatar_id ) {
			return false;
		}

		$avatar_data = wp_get_attachment_image_src( $avatar_id, array( $size, $size ) );

		if ( ! $avatar_data ) {
			return false;
		}

		return $avatar_data[0];
	}
}
