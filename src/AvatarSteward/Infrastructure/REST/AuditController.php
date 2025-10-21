<?php
/**
 * Audit REST API Controller.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Infrastructure\REST;

use AvatarSteward\Domain\Audit\AuditService;
use AvatarSteward\Domain\Licensing\LicenseManager;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class AuditController
 *
 * REST API controller for audit logs export.
 */
class AuditController extends WP_REST_Controller {

	/**
	 * API namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'avatar-steward/v1';

	/**
	 * REST base.
	 *
	 * @var string
	 */
	protected $rest_base = 'audit';

	/**
	 * Audit service instance.
	 *
	 * @var AuditService
	 */
	private AuditService $audit_service;

	/**
	 * License manager instance.
	 *
	 * @var LicenseManager
	 */
	private LicenseManager $license_manager;

	/**
	 * Constructor.
	 *
	 * @param AuditService   $audit_service   Audit service instance.
	 * @param LicenseManager $license_manager License manager instance.
	 */
	public function __construct( AuditService $audit_service, LicenseManager $license_manager ) {
		$this->audit_service   = $audit_service;
		$this->license_manager = $license_manager;
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/export',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'export_logs' ),
					'permission_callback' => array( $this, 'export_permissions_check' ),
					'args'                => $this->get_export_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_logs' ),
					'permission_callback' => array( $this, 'get_logs_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 * Export audit logs.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function export_logs( WP_REST_Request $request ) {
		$format = $request->get_param( 'format' );
		$args   = $this->prepare_query_args( $request );

		if ( 'csv' === $format ) {
			$content = $this->audit_service->export_to_csv( $args );
			return new WP_REST_Response(
				$content,
				200,
				array(
					'Content-Type'        => 'text/csv; charset=utf-8',
					'Content-Disposition' => 'attachment; filename="audit-logs-' . gmdate( 'Y-m-d' ) . '.csv"',
				)
			);
		} elseif ( 'json' === $format ) {
			$content = $this->audit_service->export_to_json( $args );
			return new WP_REST_Response(
				$content,
				200,
				array(
					'Content-Type'        => 'application/json; charset=utf-8',
					'Content-Disposition' => 'attachment; filename="audit-logs-' . gmdate( 'Y-m-d' ) . '.json"',
				)
			);
		}

		return new WP_Error(
			'invalid_format',
			__( 'Invalid export format. Use csv or json.', 'avatar-steward' ),
			array( 'status' => 400 )
		);
	}

	/**
	 * Get audit logs.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function get_logs( WP_REST_Request $request ): WP_REST_Response {
		$args  = $this->prepare_query_args( $request );
		$logs  = $this->audit_service->get_logs( $args );
		$total = $this->audit_service->count_logs( $args );

		$response = rest_ensure_response( $logs );
		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', ceil( $total / $args['limit'] ) );

		return $response;
	}

	/**
	 * Check export permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error True if has permission, error otherwise.
	 */
	public function export_permissions_check( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to export audit logs.', 'avatar-steward' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->license_manager->is_pro_active() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Audit log export requires an active Pro license.', 'avatar-steward' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Check get logs permissions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error True if has permission, error otherwise.
	 */
	public function get_logs_permissions_check( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to view audit logs.', 'avatar-steward' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $this->license_manager->is_pro_active() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Audit logs require an active Pro license.', 'avatar-steward' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Prepare query arguments from request.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array Query arguments.
	 */
	private function prepare_query_args( WP_REST_Request $request ): array {
		$args = array();

		if ( $request->has_param( 'user_id' ) ) {
			$args['user_id'] = absint( $request->get_param( 'user_id' ) );
		}

		if ( $request->has_param( 'event_type' ) ) {
			$args['event_type'] = sanitize_text_field( $request->get_param( 'event_type' ) );
		}

		if ( $request->has_param( 'event_action' ) ) {
			$args['event_action'] = sanitize_text_field( $request->get_param( 'event_action' ) );
		}

		if ( $request->has_param( 'date_from' ) ) {
			$args['date_from'] = sanitize_text_field( $request->get_param( 'date_from' ) ) . ' 00:00:00';
		}

		if ( $request->has_param( 'date_to' ) ) {
			$args['date_to'] = sanitize_text_field( $request->get_param( 'date_to' ) ) . ' 23:59:59';
		}

		$args['limit']  = isset( $request['per_page'] ) ? absint( $request['per_page'] ) : 50;
		$args['offset'] = isset( $request['page'] ) ? ( absint( $request['page'] ) - 1 ) * $args['limit'] : 0;

		if ( $request->has_param( 'orderby' ) ) {
			$args['orderby'] = sanitize_text_field( $request->get_param( 'orderby' ) );
		}

		if ( $request->has_param( 'order' ) ) {
			$args['order'] = strtoupper( sanitize_text_field( $request->get_param( 'order' ) ) );
		}

		return $args;
	}

	/**
	 * Get export endpoint parameters.
	 *
	 * @return array Export parameters.
	 */
	private function get_export_params(): array {
		return array(
			'format'       => array(
				'description' => __( 'Export format (csv or json).', 'avatar-steward' ),
				'type'        => 'string',
				'enum'        => array( 'csv', 'json' ),
				'required'    => true,
			),
			'user_id'      => array(
				'description' => __( 'Filter by user ID.', 'avatar-steward' ),
				'type'        => 'integer',
			),
			'event_type'   => array(
				'description' => __( 'Filter by event type.', 'avatar-steward' ),
				'type'        => 'string',
			),
			'event_action' => array(
				'description' => __( 'Filter by event action.', 'avatar-steward' ),
				'type'        => 'string',
			),
			'date_from'    => array(
				'description' => __( 'Filter logs from this date (Y-m-d).', 'avatar-steward' ),
				'type'        => 'string',
				'format'      => 'date',
			),
			'date_to'      => array(
				'description' => __( 'Filter logs to this date (Y-m-d).', 'avatar-steward' ),
				'type'        => 'string',
				'format'      => 'date',
			),
		);
	}

	/**
	 * Get collection parameters.
	 *
	 * @return array Collection parameters.
	 */
	private function get_collection_params(): array {
		return array(
			'page'         => array(
				'description' => __( 'Current page of the collection.', 'avatar-steward' ),
				'type'        => 'integer',
				'default'     => 1,
			),
			'per_page'     => array(
				'description' => __( 'Maximum number of items to return.', 'avatar-steward' ),
				'type'        => 'integer',
				'default'     => 50,
			),
			'user_id'      => array(
				'description' => __( 'Filter by user ID.', 'avatar-steward' ),
				'type'        => 'integer',
			),
			'event_type'   => array(
				'description' => __( 'Filter by event type.', 'avatar-steward' ),
				'type'        => 'string',
			),
			'event_action' => array(
				'description' => __( 'Filter by event action.', 'avatar-steward' ),
				'type'        => 'string',
			),
			'date_from'    => array(
				'description' => __( 'Filter logs from this date (Y-m-d).', 'avatar-steward' ),
				'type'        => 'string',
				'format'      => 'date',
			),
			'date_to'      => array(
				'description' => __( 'Filter logs to this date (Y-m-d).', 'avatar-steward' ),
				'type'        => 'string',
				'format'      => 'date',
			),
			'orderby'      => array(
				'description' => __( 'Sort collection by field.', 'avatar-steward' ),
				'type'        => 'string',
				'default'     => 'created_at',
				'enum'        => array( 'id', 'user_id', 'event_type', 'created_at' ),
			),
			'order'        => array(
				'description' => __( 'Order sort attribute ascending or descending.', 'avatar-steward' ),
				'type'        => 'string',
				'default'     => 'desc',
				'enum'        => array( 'asc', 'desc' ),
			),
		);
	}
}
