<?php

namespace Academy\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy;
use Academy\Classes\Pages;

class Ajax {

	public static function init() {
		$self = new self();
		add_action( 'wp_ajax_academy/admin/get_admin_menu_items', array( $self, 'get_admin_menu_items' ) );
		add_action( 'wp_ajax_academy/admin/get_analytics', array( $self, 'get_analytics' ) );
		add_action( 'wp_ajax_academy/admin/get_enrolled_students', array( $self, 'get_enrolled_students' ) );
		add_action( 'wp_ajax_academy/admin/get_all_students', array( $self, 'get_all_students' ) );
		add_action( 'wp_ajax_academy/admin/remove_student', array( $self, 'remove_student' ) );
		add_action( 'wp_ajax_academy/admin/hide_promo_discount_offer', array( $self, 'hide_promo_discount_offer' ) );

		// instructor
		add_action( 'wp_ajax_academy/admin/get_all_instructors', array( $self, 'get_all_instructors' ) );
		add_action( 'wp_ajax_academy/admin/update_instructor_status', array( $self, 'update_instructor_status' ) );
		add_action( 'wp_ajax_academy/admin/get_approved_instructors_for_select', array(
			$self,
			'get_approved_instructors_for_select'
		) );

		// course slug
		add_action( 'wp_ajax_academy/admin/get_course_slug', array( $self, 'get_course_slug' ) );

		// fetch
		add_action( 'wp_ajax_academy/fetch_posts', array( $self, 'fetch_posts' ) );
		add_action( 'wp_ajax_academy/fetch_products', array( $self, 'fetch_products' ) );
		add_action( 'wp_ajax_academy/fetch_course_category', array( $self, 'fetch_course_category' ) );
		add_action( 'wp_ajax_academy/change_post_status', array( $self, 'change_post_status' ) );

		// Tools
		add_action( 'wp_ajax_academy/admin/fetch_academy_status', array( $self, 'fetch_academy_status' ) );
		add_action( 'wp_ajax_academy/admin/fetch_academy_pages', array( $self, 'fetch_academy_pages' ) );
		add_action( 'wp_ajax_academy/admin/regenerate_academy_pages', array( $self, 'regenerate_academy_pages' ) );

		// Register
		add_action( 'wp_ajax_academy/admin/register_student', array( $self, 'register_student' ) );
		add_action( 'wp_ajax_academy/admin/register_instructor', array( $self, 'register_instructor' ) );

		// Form Builder - Instructor
		add_action( 'wp_ajax_academy/admin/get_instructor_form_settings', array( $self, 'get_instructor_form_settings' ) );
		add_action( 'wp_ajax_academy/admin/save_instructor_form_settings', array( $self, 'save_instructor_form_settings' ) );

		// Form Builder - Student
		add_action( 'wp_ajax_academy/admin/get_student_form_settings', array( $self, 'get_student_form_settings' ) );
		add_action( 'wp_ajax_academy/admin/save_student_form_settings', array( $self, 'save_student_form_settings' ) );

		// Import/Export
		add_action( 'wp_ajax_academy/admin/import_lessons', array( $self, 'import_lessons' ) );
	}

	public function get_admin_menu_items() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$menu_items = wp_json_encode( \Academy\Helper::get_admin_menu_list() );
		wp_send_json_success( $menu_items );
	}

	public function get_analytics() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$analytics = new \Academy\Classes\Analytics();
		wp_send_json_success( $analytics->get_analytics() );
	}

	public function get_enrolled_students() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$total_enrolled_students = \Academy\Helper::get_total_number_of_students();
		wp_send_json_success( array( 'total_enrolled_students' => $total_enrolled_students ) );
	}

	public function get_all_students() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$page     = ( isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : 1 );
		$per_page = ( isset( $_POST['per_page'] ) ? sanitize_text_field( $_POST['per_page'] ) : 10 );
		$search   = ( isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '' );
		$offset   = ( $page - 1 ) * $per_page;

		$Analytics      = new \Academy\Classes\Analytics();
		$total_students = $Analytics->get_total_number_of_students();

		// Set the x-wp-total header
		header( 'x-wp-total: ' . $total_students );

		$students = \Academy\Helper::get_all_students( $offset, $per_page, $search );
		$students = \Academy\Helper::prepare_get_all_students_response( $students );
		wp_send_json_success( $students );
		wp_die();
	}

	public function remove_student() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$student_id       = (int) ( isset( $_POST['student_id'] ) ? sanitize_text_field( $_POST['student_id'] ) : 0 );
		$enrolled_courses = \Academy\Helper::get_enrolled_courses_ids_by_user( $student_id );

		if ( get_current_user_id() === $student_id ) {
			wp_send_json_error( __( 'Sorry, You can\'t remove yourself.', 'academy' ) );
		} elseif ( count( $enrolled_courses ) ) {
			wp_send_json_error( __( 'Sorry, You need to cancel all enrollment before remove student', 'academy' ) );
		}

		$has_removed = \Academy\Helper::remove_student( $student_id );
		if ( $has_removed ) {
			wp_send_json_success( $student_id );
		}
		wp_send_json_error( __( 'Something Wrong! Try again', 'academy' ) );
	}

	public function hide_promo_discount_offer() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$is_disabled = Academy\Classes\PromoDiscount::disable_offer();
		wp_send_json_success( $is_disabled );
	}

	public function get_all_instructors() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$page     = ( isset( $_POST['page'] ) ? sanitize_text_field( $_POST['page'] ) : 1 );
		$per_page = ( isset( $_POST['per_page'] ) ? sanitize_text_field( $_POST['per_page'] ) : 10 );
		$offset   = ( $page - 1 ) * $per_page;
		$search   = ( isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '' );
		$status   = ( isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'any' );

		$Analytics         = new \Academy\Classes\Analytics();
		$total_instructors = $Analytics->get_total_number_of_instructors();

		// Set the x-wp-total header
		header( 'x-wp-total: ' . $total_instructors );

		if ( 'any' === $status ) {
			$instructors = \Academy\Helper::get_all_instructors( $offset, $per_page, $search );
		} else {
			$instructors = \Academy\Helper::get_all_instructors_by_status( $status );
		}
		$results = \Academy\Helper::prepare_all_instructors_response( $instructors );
		wp_send_json_success( $results );
	}

	public function update_instructor_status() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$ID     = ( isset( $_POST['ID'] ) ? intval( sanitize_text_field( $_POST['ID'] ) ) : '' );
		$status = ( isset( $_POST['status'] ) ? $_POST['status'] : '' );

		if ( get_current_user_id() === $ID ) {
			wp_send_json_error( __( 'Same user will be not able to update status', 'academy' ) );
		}

		if ( 'approved' === $status ) {
			\Academy\Helper::set_instructor_role( $ID );
		} elseif ( 'pending' === $status ) {
			\Academy\Helper::pending_instructor_role( $ID );
		} elseif ( 'remove' === $status ) {
			\Academy\Helper::remove_instructor_role( $ID );
		}

		do_action( 'academy/admin/update_instructor_status', $ID, $status );

		$instructor = \Academy\Helper::get_instructor( $ID );
		$results     = \Academy\Helper::prepare_all_instructors_response( [ $instructor ] );

		wp_send_json_success( current( $results ) );
	}

	public function get_approved_instructors_for_select() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$results     = [];
		$instructors = \Academy\Helper::get_all_approved_instructors();
		foreach ( $instructors as $instructor ) {
			$instructor_id        = (int) $instructor->ID;
			$instructor_full_name = \Academy\Helper::get_the_author_name( $instructor_id );
			$results[]            = array(
				'label' => $instructor_full_name,
				'value' => $instructor_id
			);
		}
		wp_send_json_success( $results );
		wp_die();
	}

	public function get_course_slug() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die();
		}
		$post_id = ( isset( $_POST['ID'] ) ? sanitize_text_field( $_POST['ID'] ) : '' );
		$title   = isset( $_POST['new_title'] ) ? sanitize_text_field( $_POST['new_title'] ) : '';
		$slug    = isset( $_POST['new_slug'] ) ? sanitize_text_field( $_POST['new_slug'] ) : null;

		wp_send_json_success( Academy\Helper::get_sample_permalink_args( $post_id, $title, $slug ) );
	}

	public function fetch_posts() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die();
		}

		$post_type = ( isset( $_POST['postType'] ) ? sanitize_text_field( $_POST['postType'] ) : 'page' );
		$postId    = (int) ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : 0 );
		$keyword   = ( isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '' );

		if ( $postId ) {
			$args = array(
				'post_type' => $post_type,
				'p'         => $postId,
			);
		} else {
			$args = array(
				'post_type'      => $post_type,
				'posts_per_page' => 10,
			);
			if ( ! empty( $keyword ) ) {
				$args['s'] = $keyword;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				$args['author'] = get_current_user_id();
			}
		}
		$results = array();
		$posts   = get_posts( $args );
		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$results[] = array(
					'label' => $post->post_title,
					'value' => $post->ID,
				);
			}
		}
		wp_send_json_success( $results );
	}

	public function fetch_products() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die();
		}

		global $wpdb;
		$post_type               = 'product';
		$paid_course_product_ids = [];
		$postId = (int) ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : 0 );
		$keyword = ( isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '' );

		if ( $postId ) {
			$args = array(
				'post_type' => $post_type,
				'p'         => $postId,
			);
		} else {
			$args = array(
				'post_type'      => $post_type,
				'posts_per_page' => 10,
			);
			if ( ! empty( $keyword ) ) {
				$args['s'] = $keyword;
			}

			// fetch all paid course product id
			$paid_course_product_ids = $wpdb->get_results( $wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->postmeta} postmeta  WHERE postmeta.meta_key = 'academy_course_product_id' AND postmeta.meta_value != %d", 0
			), ARRAY_A );
			$paid_course_product_ids = wp_list_pluck( $paid_course_product_ids, 'meta_value', 'meta_value' );
		}

		$results = array();
		$posts   = get_posts( $args );

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( $postId !== (int) $post->ID && isset( $paid_course_product_ids[ $post->ID ] ) ) {
					continue;
				}
				$results[] = array(
					'label' => $post->post_title,
					'value' => $post->ID,
				);
			}
		}

		wp_send_json_success( $results );
	}

	public function fetch_course_category() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die();
		}

		$catId   = (int) ( isset( $_POST['postId'] ) ? sanitize_text_field( $_POST['postId'] ) : 0 );
		$keyword = ( isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '' );
		$type    = ( isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'single' );

		$categories = [];
		if ( ! empty( $keyword ) ) {
			$categories = get_term_by( 'name', $keyword, 'academy_courses_category' );
		} elseif ( $catId && 'single' === $type ) {
			$categories = get_term( $catId, 'academy_courses_category' );
		} else {
			$categories = get_terms( array(
				'taxonomy'   => 'academy_courses_category',
				'hide_empty' => false,
			) );
		}
		$results = [];
		if ( is_array( $categories ) && count( $categories ) ) {
			foreach ( $categories as $category ) {
				$results[] = array(
					'label' => $category->name,
					'value' => $category->term_id,
				);
			}
		}

		wp_send_json_success( $results );
	}

	public function change_post_status() {
		check_ajax_referer( 'academy_nonce', 'security' );

		if ( ! current_user_can( 'manage_academy_instructor' ) ) {
			wp_die();
		}

		$post_id   = sanitize_text_field( $_POST['post_id'] );
		$status    = sanitize_text_field( $_POST['status'] );
		$is_update = wp_update_post( array(
			'ID'          => $post_id,
			'post_status' => $status,
		), true, true );
		wp_send_json_success( $is_update );
	}

	public function fetch_academy_status() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$tools     = new \Academy\Classes\Tools();
		$wordpress = $tools->get_wordpress_environment_status();
		$server    = $tools->get_server_environment_status();
		wp_send_json_success( [
			'wordpress' => $wordpress,
			'server'    => $server
		] );
	}

	public function fetch_academy_pages() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$pages = Pages::get_necessary_pages();
		wp_send_json_success( $pages );
	}

	public function regenerate_academy_pages() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}
		$status = Pages::regenerate_necessary_pages();
		wp_send_json_success( $status );
	}
	/** TODO: File uploading mechanism through ajax */
	public function import_lessons() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		if ( ! isset( $_FILES['upload_file'] ) ) {
			wp_send_json_error( __( 'Upload File is empty.', 'academy' ) );
		}

		$file = $_FILES['upload_file'];
		if ( 'csv' !== pathinfo( $file['name'] )['extension'] ) {
			wp_send_json_error( __( 'Wrong File Format! Please import csv file.', 'academy' ) );
		}

		$link_header = [];
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		$file_open = fopen( $file['tmp_name'], 'r' );
		if ( false !== $file_open ) {
			$results = [];
			$count   = 0;
			$user_id = get_current_user_id();
			// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
			while ( false !== ( $item = fgetcsv( $file_open ) ) ) {
				if ( 0 === $count ) {
					$link_header = array_map( 'strtolower', $item );
					$count ++;
					continue;
				}

				$item = array_combine( $link_header, $item );

				if ( empty( $item['title'] ) ) {
					$results[] = __( 'Empty lesson data', 'academy' );
					continue;
				}

				if ( \Academy\Helper::is_lesson_slug_exists( sanitize_title( $item['title'] ) ) ) {
					$results[] = __( 'Already Exists', 'academy' ) . ' - ' . $item['title'];
					continue;
				}

				$user                  = get_user_by( 'login', sanitize_text_field( $item['author'] ) );
				$allowed_tags          = wp_kses_allowed_html( 'post' );
				$allowed_tags['input'] = array(
					'type'  => true,
					'name'  => true,
					'value' => true,
					'class' => true,
				);
				$allowed_tags['form']  = array(
					'action' => true,
					'method' => true,
					'class'  => true,
				);
				$content               = wp_kses_post( $item['content'], $allowed_tags );

				$lesson_id = \Academy\Classes\Query::lesson_insert( array(
					'lesson_author'  => $user ? $user->ID : $user_id,
					'lesson_title'   => sanitize_text_field( $item['title'] ),
					'lesson_name'    => \Academy\Helper::generate_unique_lesson_slug( sanitize_text_field( $item['title'] ) ),
					'lesson_content' => $content,
					'lesson_status'  => sanitize_text_field( $item['status'] ),
				) );
				if ( $lesson_id ) {
					\Academy\Classes\Query::lesson_meta_insert( $lesson_id, array(
						'featured_media' => 0,
						'attachment'     => 0,
						'is_previewable' => sanitize_text_field( $item['is_previewable'] ),
						'video_duration' => sanitize_text_field( $item['video_duration'] ),
						'video_source'   => wp_json_encode( array(
							'type' => sanitize_text_field( $item['video_source_type'] ),
							'url'  => sanitize_text_field( $item['video_source_url'] )
						) ),
					) );
					$results[] = __( 'Successfully Imported', 'academy' ) . ' - ' . $item['title'];
				}
			}//end while
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
			fclose( $file_open );

			wp_send_json_success( $results );
		}//end if
		wp_send_json_error( __( 'Failed to open the file', 'academy' ) );
	}

	public function register_student() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$first_name = sanitize_text_field( $_POST['first_name'] );
		$last_name  = sanitize_text_field( $_POST['last_name'] );
		$username   = sanitize_text_field( $_POST['username'] );
		$email      = sanitize_text_field( $_POST['email'] );
		$password   = sanitize_text_field( $_POST['password'] );

		$student_id = \Academy\Helper::insert_student( $email, $first_name, $last_name, $username, $password );

		if ( is_numeric( $student_id ) ) {
			do_action( 'academy/admin/after_student_registration', $student_id );
			wp_send_json_success( get_user_by( 'ID', $student_id ) );
		}

		wp_send_json_error( $student_id );
	}

	public function register_instructor() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$first_name = sanitize_text_field( $_POST['first_name'] );
		$last_name  = sanitize_text_field( $_POST['last_name'] );
		$username   = sanitize_text_field( $_POST['username'] );
		$email      = sanitize_text_field( $_POST['email'] );
		$password   = sanitize_text_field( $_POST['password'] );

		$instructor = \Academy\Helper::insert_instructor( $email, $first_name, $last_name, $username, $password );

		if ( is_numeric( $instructor ) ) {
			do_action( 'academy/admin/after_instructor_registration', $instructor );
			wp_send_json_success( get_user_by( 'ID', $instructor ) );
		}

		wp_send_json_error( $instructor );
	}

	public function get_instructor_form_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$form_settings = get_option( 'academy_form_builder_settings' );
		$form_settings = json_decode( $form_settings, true );
		wp_send_json_success( isset( $form_settings['instructor'] ) ? $form_settings['instructor'] : [] );
	}

	public function save_instructor_form_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		// Retrieve the JSON data sent via AJAX
		$json_data = isset( $_POST['form_fields'] ) ? $_POST['form_fields'] : '';
		$form_settings = get_option( 'academy_form_builder_settings' );
		$form_settings = json_decode( $form_settings, true );

		// Check if JSON data was received
		if ( ! empty( $json_data ) ) {
			// Decode the JSON string into a PHP array
			$json_data = json_decode( stripslashes( $json_data ), true );
			if ( is_array( $json_data ) ) {
				$settings = [];
				foreach ( $json_data as $json_data_item ) {
					if ( is_array( $json_data_item ) ) {
						$fields = [];
						foreach ( $json_data_item as $field_item ) {
							$fields[] = $field_item;
						}
						$settings[]['fields'] = $fields;
					}
				}
			}
			$form_settings['instructor'] = $settings;
			update_option( 'academy_form_builder_settings', wp_json_encode( $form_settings ) );
		}
		wp_send_json_success( isset( $form_settings['instructor'] ) ? $form_settings['instructor'] : [] );
	}


	public function get_student_form_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$form_settings = get_option( 'academy_form_builder_settings' );
		$form_settings = json_decode( $form_settings, true );
		wp_send_json_success( isset( $form_settings['student'] ) ? $form_settings['student'] : [] );
	}

	public function save_student_form_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		// Retrieve the JSON data sent via AJAX
		$json_data = isset( $_POST['form_fields'] ) ? $_POST['form_fields'] : '';
		$form_settings = get_option( 'academy_form_builder_settings' );
		$form_settings = json_decode( $form_settings, true );

		// Check if JSON data was received
		if ( ! empty( $json_data ) ) {
			// Decode the JSON string into a PHP array
			$json_data = json_decode( stripslashes( $json_data ), true );
			if ( is_array( $json_data ) ) {
				$settings = [];
				foreach ( $json_data as $json_data_item ) {
					if ( is_array( $json_data_item ) ) {
						$fields = [];
						foreach ( $json_data_item as $field_item ) {
							$fields[] = $field_item;
						}
						$settings[]['fields'] = $fields;
					}
				}
			}
			$form_settings['student'] = $settings;
			update_option( 'academy_form_builder_settings', wp_json_encode( $form_settings ) );
		}
		wp_send_json_success( isset( $form_settings['student'] ) ? $form_settings['student'] : [] );

	}
}
