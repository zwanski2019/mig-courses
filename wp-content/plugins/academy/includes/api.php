<?php
namespace Academy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class API {

	public static function init() {
		$self = new self();
		API\Course::init();
		API\User::init();
		API\Settings::init();
		API\Lessons::init();
		API\QuestionAnswer::init();
		add_filter( 'user_has_cap', array( $self, 'update_user_cap' ), 10, 3 );
		add_action( 'rest_after_insert_academy_courses', array( $self, 'course_instructor_meta_data_save' ), 10, 2 );
	}
	public function update_user_cap( $allcaps, $cap, $args ) {
		if ( isset( $allcaps['edit_posts'] ) && $allcaps['edit_posts'] ) {
			$allcaps['edit_post_meta'] = true;
		}
		return $allcaps;
	}

	public function course_instructor_meta_data_save( $post, $request ) {
		$params = $request->get_params();
		// make author as instructor
		if ( ! \Academy\Helper::has_user_meta_exists( (int) $params['author'], 'academy_instructor_course_id', $post->ID ) ) {
			add_user_meta( (int) $params['author'], 'academy_instructor_course_id', $post->ID );
		}
		// mark as sub instructor
		if ( isset( $params['instructors'] ) && is_array( $params['instructors'] ) ) {
			foreach ( $params['instructors'] as $author_id ) {

				if ( (int) $params['author'] === (int) $author_id ) {
					continue;
				}

				if ( ! \Academy\Helper::has_user_meta_exists( (int) $author_id, 'academy_instructor_course_id', $post->ID ) ) {
					add_user_meta( (int) $author_id, 'academy_instructor_course_id', $post->ID );
				}
			}
		}
	}
}
