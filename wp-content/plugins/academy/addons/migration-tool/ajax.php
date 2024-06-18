<?php
namespace AcademyMigrationTool;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {
	public static function init() {
		$self = new self();
		add_action( 'wp_ajax_academy_migration_tool/admin/prepare_other_lms_to_alms_migration', array( $self, 'prepare_other_lms_to_alms_migration' ) );
		add_action( 'wp_ajax_academy_migration_tool/admin/learnpress_to_academy_migration', array( $self, 'learnpress_to_academy_migration' ) );
		add_action( 'wp_ajax_academy_migration_tool/admin/tutor_to_academy_migration', array( $self, 'tutor_to_academy_migration' ) );
		add_action( 'wp_ajax_academy_migration_tool/admin/learndash_to_academy_migration', array( $self, 'learndash_to_academy_migration' ) );
		add_action( 'wp_ajax_academy_migration_tool/admin/masterstudy_to_academy_migration', array( $self, 'masterstudy_to_academy_migration' ) );
		add_action( 'wp_ajax_academy_migration_tool/admin/lifter_to_academy_migration', array( $self, 'lifter_to_academy_migration' ) );
	}

	public function prepare_other_lms_to_alms_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		global $wpdb;
		$pluginName = ( isset( $_POST['pluginName'] ) ? sanitize_text_field( $_POST['pluginName'] ) : '' );
		if ( empty( $pluginName ) ) {
			wp_send_json_error( __( 'Sorry, you haven\'t select any plugin to migrate.', 'academy' ) );
		}

		$pluginBaseName = '';
		$course_post_type = '';
		switch ( $pluginName ) {
			case 'learnpress':
				$pluginBaseName = 'learnpress/learnpress.php';
				$course_post_type = 'lp_course';
				break;
			case 'tutor':
				$pluginBaseName = 'tutor/tutor.php';
				$course_post_type = 'courses';
				break;
			case 'learndash':
				$pluginBaseName = 'sfwd-lms/sfwd_lms.php';
				$course_post_type = 'sfwd-courses';
				break;
			case 'masterstudy':
				$pluginBaseName = 'masterstudy-lms-learning-management-system/masterstudy-lms-learning-management-system.php';
				$course_post_type = 'stm-courses';
				break;
			case 'lifter':
				$pluginBaseName = 'lifterlms/lifterlms.php';
				$course_post_type = 'course';
				break;
		}//end switch

		if ( ! \Academy\Helper::is_plugin_active( $pluginBaseName ) ) {
			wp_send_json_error( sprintf( __( 'You need to Activated %s plugin to run this migration.', 'academy' ), $pluginName ) );
		}

		if ( ! \Academy\Helper::is_active_woocommerce() ) {
			wp_send_json_error( sprintf( __( 'You need to Activated WooCommerce to run this migration.', 'academy' ), $pluginName ) );
		}

		$courses = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish';", $course_post_type ) );

		if ( ! count( $courses ) ) {
			wp_send_json_error( __( 'Sorry, You have no courses to migrate.', 'academy' ) );
		}

		wp_send_json_success( $courses );
	}

	public function learnpress_to_academy_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : 0 );
		$LpToAlmsMigration = new Classes\Learnpress( $course_id );
		$LpToAlmsMigration->run_migration();
		$response = $LpToAlmsMigration->get_logs();
		wp_send_json_success( $response );
	}

	public function tutor_to_academy_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : 0 );

		$TrToAlmsMigration = new Classes\Tutor( $course_id );
		$TrToAlmsMigration->run_migration();
		$response = $TrToAlmsMigration->get_logs();
		wp_send_json_success( $response );
	}

	public function learndash_to_academy_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : 0 );
		$LDToAlmsMigration = new Classes\Learndash( $course_id );
		$LDToAlmsMigration->run_migration();
		$response = $LDToAlmsMigration->get_logs();
		wp_send_json_success( $response );
	}
	public function masterstudy_to_academy_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : 0 );
		$MasterstudyToAlmsMigration = new Classes\Masterstudy( $course_id );
		$MasterstudyToAlmsMigration->run_migration();
		$response = $MasterstudyToAlmsMigration->get_logs();
		wp_send_json_success( $response );
	}

	public function lifter_to_academy_migration() {
		check_admin_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : 0 );
		$LifterToAlmsMigration = new Classes\Lifterlms( $course_id );
		$LifterToAlmsMigration->run_migration();
		$response = $LifterToAlmsMigration->get_logs();
		wp_send_json_success( $response );
	}
}
