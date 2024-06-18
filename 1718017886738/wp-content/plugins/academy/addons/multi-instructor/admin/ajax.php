<?php
namespace AcademyMultiInstructor\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {
	public static function init() {
		$self = new self();
		// instructor related ajax.
		add_action( 'wp_ajax_academy_multi_instructor/admin/get_instructors_by_course_id', array( $self, 'get_instructors_by_course_id' ) );
		add_action( 'wp_ajax_academy_multi_instructor/admin/get_active_instructors', array( $self, 'get_active_instructors' ) );
		add_action( 'wp_ajax_academy_multi_instructor/admin/remove_instructor_from_course', array( $self, 'remove_instructor_from_course' ) );
		// withdraw related ajax.
		add_action( 'wp_ajax_academy_multi_instructor/admin/get_all_withdraw_request', array( $self, 'get_all_withdraw_request' ) );
		add_action( 'wp_ajax_academy_multi_instructor/admin/update_withdraw_status', array( $self, 'update_withdraw_status' ) );
	}

	public function get_instructors_by_course_id() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$results   = [];
		if ( $course_id ) {
			$results = \Academy\Helper::get_instructors_by_course_id( $course_id );
		} else {
			$results = \Academy\Helper::get_current_instructor();
			$results = \Academy\Helper::prepare_all_instructors_response( $results );
		}
		if ( $results ) {
			wp_send_json_success( $results );
			wp_die();
		}
		wp_send_json_error( $results );
		wp_die();
	}

	public function get_active_instructors() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$instructors = \Academy\Helper::get_all_approved_instructors();
		$results     = \Academy\Helper::prepare_all_instructors_response( $instructors );
		wp_send_json_success( $results );
		wp_die();
	}

	public function remove_instructor_from_course() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$course_id     = (int) sanitize_text_field( $_POST['course_id'] );
		$instructor_id = (int) sanitize_text_field( $_POST['instructor_id'] );
		$is_delete     = delete_user_meta( $instructor_id, 'academy_instructor_course_id', $course_id );
		if ( $is_delete ) {
			wp_send_json_success( $is_delete );
			wp_die();
		}
		wp_send_json_error( $is_delete );
		wp_die();
	}

	public function get_all_withdraw_request() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$page = ( isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : 1 );
		$per_page = ( isset( $_POST['per_page'] ) ? sanitize_text_field( $_POST['per_page'] ) : 10 );
		$status = ( isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'any' );
		$offset = ( $page - 1 ) * $per_page;

		$total_request = \Academy\Helper::get_total_number_of_withdraw_request();
		// Set the x-wp-total header
		header( 'x-wp-total: ' . $total_request );

		$results = \Academy\Helper::get_withdraw_request( $offset, $per_page, $status );
		wp_send_json_success( $results );
		wp_die();
	}

	public function update_withdraw_status() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$ID = (int) ( isset( $_POST['ID'] ) ? sanitize_text_field( $_POST['ID'] ) : 0 );
		$statusTo = ( isset( $_POST['statusTo'] ) ? sanitize_text_field( $_POST['statusTo'] ) : '' );

		$is_update = \Academy\Helper::update_withdraw_status_by_withdraw_id( $ID, $statusTo );
		if ( $is_update ) {
			$results = \Academy\Helper::get_withdraw_by_withdraw_id( $ID );
			wp_send_json_success( current( $results ) );
			wp_die();
		}
		wp_send_json_error( [ 'message' => esc_html__( 'Failed to update withdraw status', 'academy' ) ] );
		wp_die();
	}
}
