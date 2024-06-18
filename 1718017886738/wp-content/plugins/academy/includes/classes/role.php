<?php
namespace Academy\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Role {
	public static function add_student_role() {
		remove_role( 'academy_student' );
		add_role( 'academy_student', esc_html__( 'Academy Student', 'academy' ), array() );
		$role_permission = array(
			'read',
			'edit_posts',
		);
		$student = get_role( 'academy_student' );
		if ( $student ) {
			$can_upload_files = (bool) \Academy\Helper::get_settings( 'is_student_can_upload_files' );
			if ( $can_upload_files ) {
				$role_permission[] = 'upload_files';
			}
			foreach ( $role_permission as $cap ) {
				$student->add_cap( $cap );
			}
		}
	}

	public static function add_instructor_role() {
		remove_role( 'academy_instructor' );

		add_role( 'academy_instructor', esc_html__( 'Academy Instructor', 'academy' ), array() );
		$role_permission = array(
			'manage_academy_instructor',
			// course
			'edit_academy_course',
			'read_academy_course',
			'delete_academy_course',
			'delete_academy_courses',
			'edit_academy_courses',
			'edit_others_academy_courses',
			'read_private_academy_courses',
			'edit_academy_courses',
			// quizzes
			'edit_academy_quiz',
			'read_academy_quiz',
			'delete_academy_quiz',
			'delete_academy_quizzes',
			'edit_academy_quizzes',
			'edit_others_academy_quizzes',
			'publish_academy_quizzes',
			'read_private_academy_quizzes',
			'edit_academy_quizzes',
			// zoom
			'edit_academy_zoom',
			'read_academy_zoom',
			'delete_academy_zoom',
			'delete_academy_zooms',
			'edit_academy_zooms',
			'edit_others_academy_zooms',
			'publish_academy_zooms',
			'read_private_academy_zooms',
			'edit_academy_zooms',
			// assignment
			'edit_academy_assignment',
			'read_academy_assignment',
			'delete_academy_assignment',
			'delete_academy_assignments',
			'edit_academy_assignments',
			'edit_others_academy_assignments',
			'publish_academy_assignments',
			'read_private_academy_assignments',
			'edit_academy_assignments',
			// tutor booking
			'edit_academy_booking',
			'read_academy_booking',
			'delete_academy_booking',
			'delete_academy_bookings',
			'edit_academy_bookings',
			'edit_others_academy_bookings',
			'publish_academy_bookings',
			'read_private_academy_bookings',
			'edit_academy_bookings',
			// Announcement
			'edit_academy_announcement',
			'read_academy_announcement',
			'delete_academy_announcement',
			'delete_academy_announcements',
			'edit_academy_announcements',
			'edit_others_academy_announcements',
			'publish_academy_announcements',
			'read_private_academy_announcements',
			'edit_academy_announcements',
			// course bundle
			'edit_academy_course_bundle',
			'read_academy_course_bundle',
			'delete_academy_course_bundle',
			'delete_academy_course_bundles',
			'edit_academy_course_bundles',
			'edit_others_academy_course_bundles',
			'publish_academy_course_bundles',
			'read_private_academy_course_bundles',
			'edit_academy_course_bundles',
			// webhook
			'edit_academy_webhook',
			'read_academy_webhook',
			'delete_academy_webhook',
			'delete_academy_webhooks',
			'edit_academy_webhooks',
			'edit_others_academy_webhooks',
			'publish_academy_webhooks',
			'read_private_academy_webhooks',
			'edit_academy_webhooks',
			// lesson
			'publish_academy_lessons',
			'edit_academy_lesson',
			'read_academy_lesson',
			'delete_academy_lesson',
			// common
			'edit_post',
			'edit_posts',
			'read',
			'upload_files',
			'edit_others_posts',
		);

		$instructor = get_role( 'academy_instructor' );
		if ( $instructor ) {
			$can_publish_course = (bool) \Academy\Helper::get_settings( 'is_instructor_can_publish_course' );
			if ( $can_publish_course ) {
				$role_permission[] = 'publish_academy_courses';
			}
			foreach ( $role_permission as $cap ) {
				$instructor->add_cap( $cap );
			}
		}

		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			$administrator->add_cap( 'manage_academy_instructor' );
			$administrator->add_cap( 'publish_academy_courses' );
		}

		if ( current_user_can( 'administrator' ) ) {
			$user_id = get_current_user_id();
			\Academy\Helper::set_instructor_role( $user_id );
		}
	}
}
