<?php
/**
 * REST API Controller for Avatar Library.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Admin;

use AvatarSteward\Domain\Library\LibraryService;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * REST API controller for avatar library operations.
 */
class LibraryRestController extends WP_REST_Controller {

	/**
	 * Library service instance.
	 *
	 * @var LibraryService
	 */
	private LibraryService $library_service;

	/**
	 * Constructor.
	 *
	 * @param LibraryService $library_service Library service instance.
	 */
	public function __construct( LibraryService $library_service ) {
		$this->namespace       = 'avatar-steward/v1';
		$this->rest_base       = 'library';
		$this->library_service = $library_service;
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_create_item_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'id' => array(
							'description' => __( 'Avatar attachment ID.', 'avatar-steward' ),
							'type'        => 'integer',
							'required'    => true,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'id' => array(
							'description' => __( 'Avatar attachment ID.', 'avatar-steward' ),
							'type'        => 'integer',
							'required'    => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/sectors',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_sectors' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/licenses',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_licenses' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/badge',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'assign_badge' ),
					'permission_callback' => array( $this, 'manage_badge_permissions_check' ),
					'args'                => array(
						'id'          => array(
							'description' => __( 'Avatar attachment ID.', 'avatar-steward' ),
							'type'        => 'integer',
							'required'    => true,
						),
						'badge_type'  => array(
							'description' => __( 'Badge type.', 'avatar-steward' ),
							'type'        => 'string',
							'required'    => true,
						),
						'custom_data' => array(
							'description' => __( 'Custom badge data.', 'avatar-steward' ),
							'type'        => 'object',
							'required'    => false,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'remove_badge' ),
					'permission_callback' => array( $this, 'manage_badge_permissions_check' ),
					'args'                => array(
						'id' => array(
							'description' => __( 'Avatar attachment ID.', 'avatar-steward' ),
							'type'        => 'integer',
							'required'    => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/badge-types',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_badge_types' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			)
		);
	}

	/**
	 * Get library avatars.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function get_items( $request ) {
		$page     = $request->get_param( 'page' ) ?? 1;
		$per_page = $request->get_param( 'per_page' ) ?? 20;
		$search   = $request->get_param( 'search' ) ?? '';
		$author   = $request->get_param( 'author' ) ?? '';
		$license  = $request->get_param( 'license' ) ?? '';
		$sector   = $request->get_param( 'sector' ) ?? '';

		$result = $this->library_service->get_library_avatars(
			array(
				'page'     => (int) $page,
				'per_page' => (int) $per_page,
				'search'   => $search,
				'author'   => $author,
				'license'  => $license,
				'sector'   => $sector,
			)
		);

		return new WP_REST_Response( $result, 200 );
	}

	/**
	 * Get a single library avatar.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function get_item( $request ) {
		$id = (int) $request->get_param( 'id' );

		$avatar = $this->library_service->get_library_avatar( $id );

		if ( ! $avatar ) {
			return new WP_Error(
				'avatar_not_found',
				__( 'Avatar not found in library.', 'avatar-steward' ),
				array( 'status' => 404 )
			);
		}

		return new WP_REST_Response( $avatar, 200 );
	}

	/**
	 * Add avatar to library.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function create_item( $request ) {
		$attachment_id = (int) $request->get_param( 'attachment_id' );
		$metadata      = array(
			'author'  => $request->get_param( 'author' ) ?? '',
			'license' => $request->get_param( 'license' ) ?? '',
			'sector'  => $request->get_param( 'sector' ) ?? '',
			'tags'    => $request->get_param( 'tags' ) ?? array(),
		);

		$result = $this->library_service->add_to_library( $attachment_id, $metadata );

		if ( ! $result ) {
			return new WP_Error(
				'add_failed',
				__( 'Failed to add avatar to library.', 'avatar-steward' ),
				array( 'status' => 500 )
			);
		}

		$avatar = $this->library_service->get_library_avatar( $attachment_id );

		return new WP_REST_Response( $avatar, 201 );
	}

	/**
	 * Remove avatar from library.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function delete_item( $request ) {
		$id = (int) $request->get_param( 'id' );

		$avatar = $this->library_service->get_library_avatar( $id );

		if ( ! $avatar ) {
			return new WP_Error(
				'avatar_not_found',
				__( 'Avatar not found in library.', 'avatar-steward' ),
				array( 'status' => 404 )
			);
		}

		$this->library_service->remove_from_library( $id );

		return new WP_REST_Response(
			array(
				'deleted' => true,
				'avatar'  => $avatar,
			),
			200
		);
	}

	/**
	 * Get available sectors.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function get_sectors( $request ) {
		$sectors = $this->library_service->get_sectors();

		return new WP_REST_Response( $sectors, 200 );
	}

	/**
	 * Get available licenses.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function get_licenses( $request ) {
		$licenses = $this->library_service->get_licenses();

		return new WP_REST_Response( $licenses, 200 );
	}

	/**
	 * Check permissions for getting library items.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool|WP_Error True if user has permission, error otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'read' );
	}

	/**
	 * Check permissions for getting a single item.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool|WP_Error True if user has permission, error otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'read' );
	}

	/**
	 * Check permissions for creating items.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool|WP_Error True if user has permission, error otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Check permissions for deleting items.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool|WP_Error True if user has permission, error otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get collection parameters.
	 *
	 * @return array<string, array> Collection parameters.
	 */
	public function get_collection_params(): array {
		return array(
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'avatar-steward' ),
				'type'              => 'integer',
				'default'           => 1,
				'minimum'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items per page.', 'avatar-steward' ),
				'type'              => 'integer',
				'default'           => 20,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => __( 'Search term.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'author'   => array(
				'description'       => __( 'Filter by author.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'license'  => array(
				'description'       => __( 'Filter by license.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'sector'   => array(
				'description'       => __( 'Filter by sector.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Get create item parameters.
	 *
	 * @return array<string, array> Create item parameters.
	 */
	public function get_create_item_params(): array {
		return array(
			'attachment_id' => array(
				'description'       => __( 'Attachment ID to add to library.', 'avatar-steward' ),
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => 'absint',
			),
			'author'        => array(
				'description'       => __( 'Author name.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'license'       => array(
				'description'       => __( 'License name.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'sector'        => array(
				'description'       => __( 'Sector name.', 'avatar-steward' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'tags'          => array(
				'description' => __( 'Tags array.', 'avatar-steward' ),
				'type'        => 'array',
				'items'       => array(
					'type' => 'string',
				),
			),
		);
	}

	/**
	 * Assign badge to avatar.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function assign_badge( $request ) {
		$id          = $request->get_param( 'id' );
		$badge_type  = $request->get_param( 'badge_type' );
		$custom_data = $request->get_param( 'custom_data' ) ?? array();

		$result = $this->library_service->assign_badge( $id, $badge_type, $custom_data );

		if ( ! $result ) {
			return new WP_Error(
				'badge_assignment_failed',
				__( 'Failed to assign badge.', 'avatar-steward' ),
				array( 'status' => 500 )
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Badge assigned successfully.', 'avatar-steward' ),
			),
			200
		);
	}

	/**
	 * Remove badge from avatar.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function remove_badge( $request ) {
		$id = $request->get_param( 'id' );

		$result = $this->library_service->remove_badge( $id );

		if ( ! $result ) {
			return new WP_Error(
				'badge_removal_failed',
				__( 'Failed to remove badge.', 'avatar-steward' ),
				array( 'status' => 500 )
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Badge removed successfully.', 'avatar-steward' ),
			),
			200
		);
	}

	/**
	 * Get available badge types.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response Response object.
	 */
	public function get_badge_types( $request ) {
		$badge_service = $this->library_service->get_badge_service();
		if ( ! $badge_service ) {
			return new WP_REST_Response( array(), 200 );
		}

		$badge_types = $badge_service->get_badge_types();

		return new WP_REST_Response( $badge_types, 200 );
	}

	/**
	 * Check permissions for managing badges.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool|WP_Error True if user has permission, error otherwise.
	 */
	public function manage_badge_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}
}

