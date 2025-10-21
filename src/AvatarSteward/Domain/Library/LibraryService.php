<?php
/**
 * Library Service class for managing avatar library.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Domain\Library;

use AvatarSteward\Infrastructure\LoggerInterface;

/**
 * Handles avatar library management, including upload, metadata, and retrieval.
 */
class LibraryService {

	/**
	 * Metadata taxonomy name for author.
	 *
	 * @var string
	 */
	const TAXONOMY_AUTHOR = 'avatar_author';

	/**
	 * Metadata taxonomy name for license.
	 *
	 * @var string
	 */
	const TAXONOMY_LICENSE = 'avatar_license';

	/**
	 * Metadata taxonomy name for tags.
	 *
	 * @var string
	 */
	const TAXONOMY_TAGS = 'avatar_tags';

	/**
	 * Metadata taxonomy name for sector.
	 *
	 * @var string
	 */
	const TAXONOMY_SECTOR = 'avatar_sector';

	/**
	 * Post meta key for marking library avatars.
	 *
	 * @var string
	 */
	const META_IS_LIBRARY_AVATAR = 'avatar_steward_library_avatar';

	/**
	 * Cache group for library queries.
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'avatar_library';

	/**
	 * Cache expiration in seconds (1 hour).
	 *
	 * @var int
	 */
	const CACHE_EXPIRATION = 3600;

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
	 * Add an avatar to the library.
	 *
	 * @param int                  $attachment_id The attachment ID.
	 * @param array<string, mixed> $metadata      Metadata for the avatar (author, license, tags, sector).
	 *
	 * @return bool True on success, false on failure.
	 */
	public function add_to_library( int $attachment_id, array $metadata = array() ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Adding avatar to library', array( 'attachment_id' => $attachment_id ) );
		}

		// Verify attachment exists.
		if ( ! function_exists( 'get_post' ) || ! get_post( $attachment_id ) ) {
			if ( $this->logger ) {
				$this->logger->error( 'Attachment not found', array( 'attachment_id' => $attachment_id ) );
			}
			return false;
		}

		// Mark as library avatar.
		update_post_meta( $attachment_id, self::META_IS_LIBRARY_AVATAR, '1' );

		// Store metadata as post meta.
		if ( isset( $metadata['author'] ) && ! empty( $metadata['author'] ) ) {
			update_post_meta( $attachment_id, 'avatar_author', sanitize_text_field( $metadata['author'] ) );
		}

		if ( isset( $metadata['license'] ) && ! empty( $metadata['license'] ) ) {
			update_post_meta( $attachment_id, 'avatar_license', sanitize_text_field( $metadata['license'] ) );
		}

		if ( isset( $metadata['sector'] ) && ! empty( $metadata['sector'] ) ) {
			update_post_meta( $attachment_id, 'avatar_sector', sanitize_text_field( $metadata['sector'] ) );
		}

		if ( isset( $metadata['tags'] ) && ! empty( $metadata['tags'] ) ) {
			$tags = is_array( $metadata['tags'] ) ? $metadata['tags'] : explode( ',', $metadata['tags'] );
			$tags = array_map( 'sanitize_text_field', $tags );
			update_post_meta( $attachment_id, 'avatar_tags', $tags );
		}

		// Clear cache.
		$this->clear_cache();

		if ( $this->logger ) {
			$this->logger->info( 'Avatar added to library successfully', array( 'attachment_id' => $attachment_id ) );
		}

		return true;
	}

	/**
	 * Remove an avatar from the library.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove_from_library( int $attachment_id ): bool {
		if ( $this->logger ) {
			$this->logger->info( 'Removing avatar from library', array( 'attachment_id' => $attachment_id ) );
		}

		delete_post_meta( $attachment_id, self::META_IS_LIBRARY_AVATAR );

		// Clear cache.
		$this->clear_cache();

		return true;
	}

	/**
	 * Get library avatars with optional filters.
	 *
	 * @param array<string, mixed> $args Query arguments.
	 *
	 * @return array{items: array, total: int, page: int, per_page: int, total_pages: int}
	 */
	public function get_library_avatars( array $args = array() ): array {
		$defaults = array(
			'page'     => 1,
			'per_page' => 20,
			'search'   => '',
			'author'   => '',
			'license'  => '',
			'sector'   => '',
			'tags'     => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		// Check cache.
		$cache_key = 'library_avatars_' . md5( wp_json_encode( $args ) );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			if ( $this->logger ) {
				$this->logger->debug( 'Returning cached library avatars', array( 'cache_key' => $cache_key ) );
			}
			return $cached;
		}

		// Build query arguments.
		$query_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			'posts_per_page' => (int) $args['per_page'],
			'paged'          => (int) $args['page'],
			'meta_query'     => array(
				array(
					'key'     => self::META_IS_LIBRARY_AVATAR,
					'value'   => '1',
					'compare' => '=',
				),
			),
		);

		// Add search.
		if ( ! empty( $args['search'] ) ) {
			$query_args['s'] = sanitize_text_field( $args['search'] );
		}

		// Add metadata filters.
		if ( ! empty( $args['author'] ) ) {
			$query_args['meta_query'][] = array(
				'key'     => 'avatar_author',
				'value'   => sanitize_text_field( $args['author'] ),
				'compare' => 'LIKE',
			);
		}

		if ( ! empty( $args['license'] ) ) {
			$query_args['meta_query'][] = array(
				'key'     => 'avatar_license',
				'value'   => sanitize_text_field( $args['license'] ),
				'compare' => '=',
			);
		}

		if ( ! empty( $args['sector'] ) ) {
			$query_args['meta_query'][] = array(
				'key'     => 'avatar_sector',
				'value'   => sanitize_text_field( $args['sector'] ),
				'compare' => '=',
			);
		}

		// Execute query.
		$query = new \WP_Query( $query_args );

		$items = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$attachment_id = get_the_ID();

				$items[] = array(
					'id'       => $attachment_id,
					'title'    => get_the_title(),
					'url'      => wp_get_attachment_url( $attachment_id ),
					'thumb'    => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ),
					'metadata' => array(
						'author'  => get_post_meta( $attachment_id, 'avatar_author', true ),
						'license' => get_post_meta( $attachment_id, 'avatar_license', true ),
						'sector'  => get_post_meta( $attachment_id, 'avatar_sector', true ),
						'tags'    => get_post_meta( $attachment_id, 'avatar_tags', true ),
					),
				);
			}
			wp_reset_postdata();
		}

		$result = array(
			'items'       => $items,
			'total'       => (int) $query->found_posts,
			'page'        => (int) $args['page'],
			'per_page'    => (int) $args['per_page'],
			'total_pages' => (int) $query->max_num_pages,
		);

		// Cache the result.
		set_transient( $cache_key, $result, self::CACHE_EXPIRATION );

		if ( $this->logger ) {
			$this->logger->debug(
				'Library avatars retrieved',
				array(
					'total'       => $result['total'],
					'page'        => $result['page'],
					'total_pages' => $result['total_pages'],
				)
			);
		}

		return $result;
	}

	/**
	 * Get a single library avatar by ID.
	 *
	 * @param int $attachment_id The attachment ID.
	 *
	 * @return array|null Avatar data or null if not found.
	 */
	public function get_library_avatar( int $attachment_id ): ?array {
		// Check if it's a library avatar.
		if ( '1' !== get_post_meta( $attachment_id, self::META_IS_LIBRARY_AVATAR, true ) ) {
			return null;
		}

		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) {
			return null;
		}

		return array(
			'id'       => $attachment_id,
			'title'    => $attachment->post_title,
			'url'      => wp_get_attachment_url( $attachment_id ),
			'thumb'    => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ),
			'metadata' => array(
				'author'  => get_post_meta( $attachment_id, 'avatar_author', true ),
				'license' => get_post_meta( $attachment_id, 'avatar_license', true ),
				'sector'  => get_post_meta( $attachment_id, 'avatar_sector', true ),
				'tags'    => get_post_meta( $attachment_id, 'avatar_tags', true ),
			),
		);
	}

	/**
	 * Get available sectors.
	 *
	 * @return array<string> Array of sector names.
	 */
	public function get_sectors(): array {
		global $wpdb;

		$cache_key = 'library_sectors';
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$sectors = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != '' ORDER BY meta_value ASC",
				'avatar_sector'
			)
		);

		$sectors = $sectors ? $sectors : array();

		set_transient( $cache_key, $sectors, self::CACHE_EXPIRATION );

		return $sectors;
	}

	/**
	 * Get available licenses.
	 *
	 * @return array<string> Array of license names.
	 */
	public function get_licenses(): array {
		global $wpdb;

		$cache_key = 'library_licenses';
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$licenses = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != '' ORDER BY meta_value ASC",
				'avatar_license'
			)
		);

		$licenses = $licenses ? $licenses : array();

		set_transient( $cache_key, $licenses, self::CACHE_EXPIRATION );

		return $licenses;
	}

	/**
	 * Import sectoral template avatars.
	 *
	 * @param string $sector       The sector name.
	 * @param array  $avatar_files Array of file paths to import.
	 *
	 * @return array{success: int, failed: int, errors: array}
	 */
	public function import_sectoral_templates( string $sector, array $avatar_files ): array {
		if ( $this->logger ) {
			$this->logger->info(
				'Importing sectoral templates',
				array(
					'sector' => $sector,
					'count'  => count( $avatar_files ),
				)
			);
		}

		$success = 0;
		$failed  = 0;
		$errors  = array();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		foreach ( $avatar_files as $file_path ) {
			if ( ! file_exists( $file_path ) ) {
				$errors[] = sprintf( 'File not found: %s', $file_path );
				++$failed;
				continue;
			}

			$file_array = array(
				'name'     => basename( $file_path ),
				'tmp_name' => $file_path,
			);

			$attachment_id = media_handle_sideload( $file_array, 0 );

			if ( is_wp_error( $attachment_id ) ) {
				$errors[] = $attachment_id->get_error_message();
				++$failed;
				continue;
			}

			// Add to library with sector metadata.
			$this->add_to_library(
				$attachment_id,
				array(
					'sector'  => $sector,
					'license' => 'Template',
					'tags'    => array( 'template', $sector ),
				)
			);

			++$success;
		}

		if ( $this->logger ) {
			$this->logger->info(
				'Sectoral template import completed',
				array(
					'sector'  => $sector,
					'success' => $success,
					'failed'  => $failed,
				)
			);
		}

		return array(
			'success' => $success,
			'failed'  => $failed,
			'errors'  => $errors,
		);
	}

	/**
	 * Clear the library cache.
	 *
	 * @return void
	 */
	private function clear_cache(): void {
		// Delete all library-related transients.
		global $wpdb;

		$wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_library_%' OR option_name LIKE '_transient_timeout_library_%'"
		);

		if ( $this->logger ) {
			$this->logger->debug( 'Library cache cleared' );
		}
	}
}
