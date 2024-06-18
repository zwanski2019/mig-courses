<?php
namespace Academy\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy\API\Controller;

class Settings extends Controller {

	/**
	 * Initialize hooks and option name
	 */
	public static function init() {
		$self = new self();
		add_action( 'rest_api_init', array( $self, 'register_routes' ) );
		add_action( 'academy/api/settings/after_save_settings', array( $self, 'after_save_settings' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$endpoint = '/settings/';

		register_rest_route(
			$this->namespace,
			$endpoint,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_value' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'                => array(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$endpoint,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_value' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$endpoint,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_value' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$endpoint,
			array(
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_value' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => array(),
				),
			)
		);
	}

	/**
	 * Get wprs
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function get_value( $request ) {
		$response = \Academy\Admin\Settings::get_settings_saved_data();
		return rest_ensure_response( $response );
	}

	/**
	 * Create OR Update wprs
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function create_value( $request ) {
		$updated = \Academy\Admin\Settings::save_settings( $request->get_params() );
		do_action( 'academy/api/settings/after_save_settings', $updated );
		$response = \Academy\Admin\Settings::get_settings_saved_data();
		return rest_ensure_response( $response );
	}

	/**
	 * Create OR Update wprs
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function update_value( $request ) {
		$updated = \Academy\Admin\Settings::save_settings( $request->get_params() );
		do_action( 'academy/api/settings/after_save_settings', $updated );
		$response = \Academy\Admin\Settings::get_settings_saved_data();
		return rest_ensure_response( $response );
	}

	/**
	 * Delete wprs
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function delete_value( $request ) {
		$deleted = delete_option( ACADEMY_SETTINGS_NAME );
		$response = \Academy\Admin\Settings::get_settings_saved_data();
		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to get setting
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_permissions_check( $request ) {
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_academy_instructor' );
	}

	/**
	 * Check if a given request has access to update a setting
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function after_save_settings( $is_changed_settings ) {
		if ( $is_changed_settings ) {
			update_option( 'academy_flash_role_management', true );
		}
	}
}
