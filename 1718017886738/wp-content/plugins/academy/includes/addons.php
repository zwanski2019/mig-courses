<?php
namespace Academy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Addons {
	public static function init() {
		$self = new self();
		// Load all addons
		$self->addons_loader();
		// Addons
		add_action( 'wp_ajax_academy/addons/get_all_addons', array( $self, 'get_all_addons' ) );
		add_action( 'wp_ajax_academy/addons/saved_addon_status', array( $self, 'saved_addon_status' ) );
	}

	private function addons_loader() {
		$Autoload = Autoload::get_instance();
		$addons = apply_filters('academy/addons/loader_args', [
			'multi-instructor' => 'MultiInstructor',
			'quizzes'          => 'Quizzes',
			'migration-tool'   => 'MigrationTool',
			'webhooks'         => 'Webhooks',
		]);

		foreach ( $addons as $addon_name => $addon_class_name ) {
			$addon_root_path = ACADEMY_ADDONS_DIR_PATH . $addon_name . '/';
			// Register the addon's root namespace and path.
			$addon_namespace = 'Academy' . $addon_class_name;
			$Autoload->add_namespace_directory( $addon_namespace, $addon_root_path );
			// Initialize the addon's main class.
			$class = $addon_namespace . '\\' . $addon_class_name;

			$class::init();
		}
	}

	public function get_all_addons() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$academy_addons = json_decode( get_option( ACADEMY_ADDONS_SETTINGS_NAME, '{}' ) );
		wp_send_json_success( $academy_addons );
	}

	public function saved_addon_status() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$addon = ( isset( $_POST['addon'] ) ? sanitize_text_field( $_POST['addon'] ) : '' );
		$status = ( isset( $_POST['status'] ) ? \Academy\Helper::sanitize_checkbox_field( $_POST['status'] ) : false );

		if ( empty( $addon ) ) {
			wp_send_json_error( __( 'Addon Name missing', 'academy' ) );
		}

		// Saved Data
		$saved_addons = (array) json_decode( get_option( ACADEMY_ADDONS_SETTINGS_NAME ), true );
		$saved_addons[ $addon ] = $status;
		update_option( ACADEMY_ADDONS_SETTINGS_NAME, wp_json_encode( $saved_addons ) );
		// Fire Addon Action
		if ( $status ) {
			do_action( "academy/addons/activated_{$addon}", $status );
		} else {
			do_action( "academy/addons/deactivated_{$addon}", $status );
		}
		// response
		wp_send_json_success( $saved_addons );
	}
}
