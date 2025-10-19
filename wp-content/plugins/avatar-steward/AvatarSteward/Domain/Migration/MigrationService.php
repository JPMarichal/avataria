<?php
/**
 * Migration Service class.
 *
 * Handles migration of avatars from Gravatar and other avatar plugins.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Migration;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Class MigrationService
 *
 * Provides methods to migrate avatars from various sources.
 */
class MigrationService {

	/**
	 * Avatar Steward meta key for storing avatar attachment ID.
	 *
	 * @var string
	 */
	private const AVATAR_META_KEY = 'avatar_steward_avatar';

	/**
	 * Meta key for tracking migration source.
	 *
	 * @var string
	 */
	private const MIGRATION_SOURCE_KEY = 'avatar_steward_migrated_from';

	/**
	 * Logger instance.
	 *
	 * @var LoggerInterface|null
	 */
	private ?LoggerInterface $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface|null $logger Optional logger instance.
	 */
	public function __construct( ?LoggerInterface $logger = null ) {
		$this->logger = $logger;
	}

	/**
	 * Migrate avatars from Simple Local Avatars plugin.
	 *
	 * @return array Migration results with counts.
	 */
	public function migrate_from_simple_local_avatars(): array {
		if ( $this->logger ) {
			$this->logger->info( 'Starting Simple Local Avatars migration' );
		}

		if ( ! function_exists( 'get_users' ) || ! function_exists( 'get_user_meta' ) || ! function_exists( 'update_user_meta' ) ) {
			if ( $this->logger ) {
				$this->logger->error( 'Required WordPress functions not available for migration' );
			}
			return array(
				'success'  => false,
				'error'    => __( 'Required WordPress functions are not available.', 'avatar-steward' ),
				'migrated' => 0,
				'skipped'  => 0,
				'total'    => 0,
			);
		}

		$users = get_users( array( 'fields' => 'ID' ) );

		$migrated = 0;
		$skipped  = 0;

		foreach ( $users as $user_id ) {
			// Check if user has Simple Local Avatars data.
			$old_avatar = get_user_meta( $user_id, 'simple_local_avatar', true );

			if ( empty( $old_avatar ) ) {
				++$skipped;
				continue;
			}

			// Check if already migrated.
			$existing = get_user_meta( $user_id, self::AVATAR_META_KEY, true );
			if ( ! empty( $existing ) ) {
				++$skipped;
				continue;
			}

			// Migrate the avatar data.
			update_user_meta( $user_id, self::AVATAR_META_KEY, $old_avatar );
			update_user_meta( $user_id, self::MIGRATION_SOURCE_KEY, 'simple_local_avatars' );

			if ( $this->logger ) {
				$this->logger->debug( 'Migrated avatar from Simple Local Avatars', array( 'user_id' => $user_id ) );
			}

			++$migrated;
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Simple Local Avatars migration completed',
				array(
					'total'    => count( $users ),
					'migrated' => $migrated,
					'skipped'  => $skipped,
				)
			);
		}

		return array(
			'success'  => true,
			'migrated' => $migrated,
			'skipped'  => $skipped,
			'total'    => count( $users ),
		);
	}

	/**
	 * Migrate avatars from WP User Avatar plugin.
	 *
	 * @return array Migration results with counts.
	 */
	public function migrate_from_wp_user_avatar(): array {
		if ( ! function_exists( 'get_users' ) || ! function_exists( 'get_user_meta' ) || ! function_exists( 'update_user_meta' ) ) {
			return array(
				'success'  => false,
				'error'    => __( 'Required WordPress functions are not available.', 'avatar-steward' ),
				'migrated' => 0,
				'skipped'  => 0,
				'total'    => 0,
			);
		}

		$users = get_users( array( 'fields' => 'ID' ) );

		$migrated = 0;
		$skipped  = 0;

		foreach ( $users as $user_id ) {
			// WP User Avatar stores attachment ID in this meta key.
			$old_avatar_id = get_user_meta( $user_id, 'wp_user_avatar', true );

			if ( empty( $old_avatar_id ) ) {
				++$skipped;
				continue;
			}

			// Check if already migrated.
			$existing = get_user_meta( $user_id, self::AVATAR_META_KEY, true );
			if ( ! empty( $existing ) ) {
				++$skipped;
				continue;
			}

			// Migrate the avatar attachment ID.
			update_user_meta( $user_id, self::AVATAR_META_KEY, $old_avatar_id );
			update_user_meta( $user_id, self::MIGRATION_SOURCE_KEY, 'wp_user_avatar' );

			++$migrated;
		}

		return array(
			'success'  => true,
			'migrated' => $migrated,
			'skipped'  => $skipped,
			'total'    => count( $users ),
		);
	}

	/**
	 * Import Gravatars for all users.
	 *
	 * Downloads Gravatars and saves them locally as user avatars.
	 *
	 * @param bool $force Force reimport even if local avatar exists.
	 * @return array Import results with counts.
	 */
	public function import_from_gravatar( bool $force = false ): array {
		if ( $this->logger ) {
			$this->logger->info( 'Starting Gravatar import', array( 'force' => $force ) );
		}

		if ( ! function_exists( 'get_users' ) || ! function_exists( 'get_user_meta' ) || ! function_exists( 'wp_remote_get' ) ) {
			if ( $this->logger ) {
				$this->logger->error( 'Required WordPress functions not available for Gravatar import' );
			}
			return array(
				'success'  => false,
				'error'    => __( 'Required WordPress functions are not available.', 'avatar-steward' ),
				'imported' => 0,
				'skipped'  => 0,
				'failed'   => 0,
				'total'    => 0,
			);
		}

		$users = get_users();

		$imported = 0;
		$skipped  = 0;
		$failed   = 0;

		foreach ( $users as $user ) {
			// Skip if user already has a local avatar (unless forcing).
			if ( ! $force && get_user_meta( $user->ID, self::AVATAR_META_KEY, true ) ) {
				++$skipped;
				continue;
			}

			// Get Gravatar URL.
			$hash         = md5( strtolower( trim( $user->user_email ) ) );
			$gravatar_url = sprintf( 'https://www.gravatar.com/avatar/%s?s=512&d=404', $hash );

			if ( $this->logger ) {
				$this->logger->debug(
					'Fetching Gravatar',
					array(
						'user_id'    => $user->ID,
						'email_hash' => $hash,
					)
				);
			}

			// Try to download the Gravatar.
			$response = wp_remote_get(
				$gravatar_url,
				array(
					'timeout' => 10,
					'headers' => array(
						'User-Agent' => 'Avatar Steward Migration',
					),
				)
			);

			if ( is_wp_error( $response ) || 404 === wp_remote_retrieve_response_code( $response ) ) {
				// No Gravatar found or error.
				if ( $this->logger && is_wp_error( $response ) ) {
					$this->logger->warning(
						'Gravatar fetch failed',
						array(
							'user_id' => $user->ID,
							'error'   => $response->get_error_message(),
						)
					);
				}
				++$skipped;
				continue;
			}

			// Get image data.
			$image_data = wp_remote_retrieve_body( $response );

			if ( empty( $image_data ) ) {
				if ( $this->logger ) {
					$this->logger->warning( 'Empty Gravatar response', array( 'user_id' => $user->ID ) );
				}
				++$failed;
				continue;
			}

			// Save as attachment.
			$attachment_id = $this->save_image_as_attachment( $image_data, $user );

			if ( ! $attachment_id ) {
				if ( $this->logger ) {
					$this->logger->error( 'Failed to save Gravatar as attachment', array( 'user_id' => $user->ID ) );
				}
				++$failed;
				continue;
			}

			// Link to user.
			update_user_meta( $user->ID, self::AVATAR_META_KEY, $attachment_id );
			update_user_meta( $user->ID, self::MIGRATION_SOURCE_KEY, 'gravatar' );

			if ( $this->logger ) {
				$this->logger->info(
					'Gravatar imported successfully',
					array(
						'user_id'       => $user->ID,
						'attachment_id' => $attachment_id,
					)
				);
			}

			++$imported;
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Gravatar import completed',
				array(
					'total'    => count( $users ),
					'imported' => $imported,
					'skipped'  => $skipped,
					'failed'   => $failed,
				)
			);
		}

		return array(
			'success'  => true,
			'imported' => $imported,
			'skipped'  => $skipped,
			'failed'   => $failed,
			'total'    => count( $users ),
		);
	}

	/**
	 * Save image data as WordPress attachment.
	 *
	 * @param string   $image_data Binary image data.
	 * @param \WP_User $user       User object for naming.
	 * @return int|false Attachment ID on success, false on failure.
	 */
	private function save_image_as_attachment( string $image_data, \WP_User $user ) {
		if ( ! function_exists( 'wp_upload_dir' ) || ! function_exists( 'wp_insert_attachment' ) ) {
			return false;
		}

		// Get upload directory.
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) {
			return false;
		}

		$filename = sprintf( 'avatar-%d-%s.jpg', $user->ID, md5( $user->user_email ) );
		$filepath = trailingslashit( $upload_dir['path'] ) . $filename;

		// Save file.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$saved = file_put_contents( $filepath, $image_data );

		if ( false === $saved ) {
			return false;
		}

		// Create attachment.
		$attachment = array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => sprintf( 'Avatar for %s', $user->display_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $filepath );

		if ( is_wp_error( $attach_id ) ) {
			return false;
		}

		// Generate metadata.
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	/**
	 * Get migration statistics.
	 *
	 * Returns count of users who have been migrated from various sources.
	 *
	 * @return array Statistics for each migration source.
	 */
	public function get_migration_stats(): array {
		if ( ! function_exists( 'get_users' ) || ! function_exists( 'get_user_meta' ) ) {
			return array();
		}

		$users = get_users( array( 'fields' => 'ID' ) );

		$stats = array(
			'total_users'         => count( $users ),
			'with_avatars'        => 0,
			'from_simple_local'   => 0,
			'from_wp_user_avatar' => 0,
			'from_gravatar'       => 0,
			'available_simple'    => 0,
			'available_wp_user'   => 0,
		);

		foreach ( $users as $user_id ) {
			// Check if user has Avatar Steward avatar.
			if ( get_user_meta( $user_id, self::AVATAR_META_KEY, true ) ) {
				++$stats['with_avatars'];

				// Check migration source.
				$source = get_user_meta( $user_id, self::MIGRATION_SOURCE_KEY, true );
				if ( 'simple_local_avatars' === $source ) {
					++$stats['from_simple_local'];
				} elseif ( 'wp_user_avatar' === $source ) {
					++$stats['from_wp_user_avatar'];
				} elseif ( 'gravatar' === $source ) {
					++$stats['from_gravatar'];
				}
			}

			// Check for available migration sources.
			if ( get_user_meta( $user_id, 'simple_local_avatar', true ) ) {
				++$stats['available_simple'];
			}
			if ( get_user_meta( $user_id, 'wp_user_avatar', true ) ) {
				++$stats['available_wp_user'];
			}
		}

		return $stats;
	}
}
