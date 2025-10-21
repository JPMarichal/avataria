<?php
/**
 * Visual Identity REST Controller.
 *
 * Provides REST API endpoints for accessing color palettes and visual styles.
 * API is versioned (v1) and includes caching for optimal performance.
 *
 * @package AvatarSteward
 */

declare(strict_types=1);

namespace AvatarSteward\Infrastructure\REST;

use AvatarSteward\Domain\VisualIdentity\VisualIdentityService;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * VisualIdentityController class for REST API endpoints.
 */
class VisualIdentityController extends WP_REST_Controller {

	/**
	 * Visual identity service instance.
	 *
	 * @var VisualIdentityService
	 */
	private VisualIdentityService $service;

	/**
	 * Namespace for the REST API.
	 *
	 * @var string
	 */
	protected $namespace = 'avatar-steward/v1';

	/**
	 * Rest base for the endpoints.
	 *
	 * @var string
	 */
	protected $rest_base = 'visual-identity';

	/**
	 * Constructor.
	 *
	 * @param VisualIdentityService|null $service Optional service instance.
	 */
	public function __construct( ?VisualIdentityService $service = null ) {
		$this->service = $service ?? new VisualIdentityService();
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Get all visual identity data.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_visual_identity' ),
					'permission_callback' => array( $this, 'get_public_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		// Get all palettes.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/palettes',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_palettes' ),
					'permission_callback' => array( $this, 'get_public_permissions_check' ),
				),
			)
		);

		// Get specific palette.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/palettes/(?P<palette_key>[a-zA-Z0-9_-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_palette' ),
					'permission_callback' => array( $this, 'get_public_permissions_check' ),
					'args'                => array(
						'palette_key' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => array( $this, 'validate_palette_key' ),
						),
					),
				),
			)
		);

		// Get all styles.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/styles',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_styles' ),
					'permission_callback' => array( $this, 'get_public_permissions_check' ),
				),
			)
		);

		// Get specific style.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/styles/(?P<style_key>[a-zA-Z0-9_-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_style' ),
					'permission_callback' => array( $this, 'get_public_permissions_check' ),
					'args'                => array(
						'style_key' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_key',
							'validate_callback' => array( $this, 'validate_style_key' ),
						),
					),
				),
			)
		);

		// Clear cache (admin only).
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/cache',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'clear_cache' ),
					'permission_callback' => array( $this, 'get_admin_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get complete visual identity data.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_visual_identity( WP_REST_Request $request ) {
		$data = $this->service->get_visual_identity();

		return $this->prepare_response( $data );
	}

	/**
	 * Get all color palettes.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_palettes( WP_REST_Request $request ) {
		$palettes = $this->service->get_palettes();

		return $this->prepare_response( $palettes );
	}

	/**
	 * Get a specific color palette.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_palette( WP_REST_Request $request ) {
		$palette_key = $request->get_param( 'palette_key' );
		$palette     = $this->service->get_palette( $palette_key );

		if ( null === $palette ) {
			return new WP_Error(
				'avatar_steward_palette_not_found',
				__( 'Palette not found.', 'avatar-steward' ),
				array( 'status' => 404 )
			);
		}

		return $this->prepare_response( $palette );
	}

	/**
	 * Get all visual styles.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_styles( WP_REST_Request $request ) {
		$styles = $this->service->get_styles();

		return $this->prepare_response( $styles );
	}

	/**
	 * Get a specific visual style.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_style( WP_REST_Request $request ) {
		$style_key = $request->get_param( 'style_key' );
		$style     = $this->service->get_style( $style_key );

		if ( null === $style ) {
			return new WP_Error(
				'avatar_steward_style_not_found',
				__( 'Style not found.', 'avatar-steward' ),
				array( 'status' => 404 )
			);
		}

		return $this->prepare_response( $style );
	}

	/**
	 * Clear the visual identity cache.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function clear_cache( WP_REST_Request $request ) {
		$result = $this->service->clear_cache();

		if ( ! $result ) {
			return new WP_Error(
				'avatar_steward_cache_clear_failed',
				__( 'Failed to clear cache.', 'avatar-steward' ),
				array( 'status' => 500 )
			);
		}

		return $this->prepare_response(
			array(
				'success' => true,
				'message' => __( 'Cache cleared successfully.', 'avatar-steward' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read public data.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_public_permissions_check( WP_REST_Request $request ) {
		// Public endpoint - anyone can read.
		return true;
	}

	/**
	 * Check if a given request has admin access.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has admin access, WP_Error object otherwise.
	 */
	public function get_admin_permissions_check( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to do that.', 'avatar-steward' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Validate palette key parameter.
	 *
	 * @param string          $param   The parameter value.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $key     The parameter key.
	 * @return bool True if valid, false otherwise.
	 */
	public function validate_palette_key( $param, WP_REST_Request $request, string $key ): bool {
		$palettes = $this->service->get_palettes();
		return isset( $palettes[ $param ] );
	}

	/**
	 * Validate style key parameter.
	 *
	 * @param string          $param   The parameter value.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $key     The parameter key.
	 * @return bool True if valid, false otherwise.
	 */
	public function validate_style_key( $param, WP_REST_Request $request, string $key ): bool {
		$styles = $this->service->get_styles();
		return isset( $styles[ $param ] );
	}

	/**
	 * Prepare response with caching headers.
	 *
	 * @param array $data Response data.
	 * @return WP_REST_Response Response object with cache headers.
	 */
	private function prepare_response( array $data ): WP_REST_Response {
		$response = new WP_REST_Response( $data );

		// Add cache control headers (1 hour cache).
		$response->header( 'Cache-Control', 'public, max-age=3600' );
		$response->header( 'Expires', gmdate( 'D, d M Y H:i:s', time() + 3600 ) . ' GMT' );

		return $response;
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_public_item_schema(): array {
		if ( $this->schema ) {
			return $this->schema;
		}

		$this->schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'visual-identity',
			'type'       => 'object',
			'properties' => array(
				'version'  => array(
					'description' => __( 'API version', 'avatar-steward' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'palettes' => array(
					'description' => __( 'Color palettes', 'avatar-steward' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'styles'   => array(
					'description' => __( 'Visual styles', 'avatar-steward' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->schema;
	}
}
