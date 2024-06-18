<?php
namespace AcademyQuizzes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AcademyQuizzes\Classes\Query;

class Ajax {
	public static function init() {
		$self = new self();
		// quiz
		add_action( 'wp_ajax_academy_quizzes/admin/update_quiz_attempt_instructor_feedback', array( $self, 'update_quiz_attempt_instructor_feedback' ) );
		add_action( 'wp_ajax_academy_quizzes/admin/quiz_answer_manual_review', array( $self, 'quiz_answer_manual_review' ) );
	}

	public function update_quiz_attempt_instructor_feedback() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_academy_instructor' ) ) {
			wp_die();
		}
		$attempt_id = (int) ( isset( $_POST['attempt_id'] ) ? sanitize_text_field( $_POST['attempt_id'] ) : 0 );
		$instructor_feedback = ( isset( $_POST['instructor_feedback'] ) ? sanitize_text_field( $_POST['instructor_feedback'] ) : '' );
		// get exising attempt
		$attempt = (array) Query::get_quiz_attempt( $attempt_id );
		$attempt_info = json_decode( $attempt['attempt_info'], true );
		// prepare
		$attempt_info['instructor_feedback'] = $instructor_feedback;
		$attempt['attempt_info'] = wp_json_encode( $attempt_info );

		do_action( 'academy/frontend/quiz_attempt_status_' . $attempt['attempt_status'], $attempt );
		// update attempt
		$update = Query::quiz_attempt_insert( $attempt );
		if ( $update ) {
			wp_send_json_success( __( 'Successfully updated instructor feedback.', 'academy' ) );
		}
		wp_send_json_error( __( 'Sorry, Failed to update instructor feedback.', 'academy' ) );
	}

	public function quiz_answer_manual_review() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_academy_instructor' ) ) {
			wp_die();
		}
		$answer_id = (int) ( isset( $_POST['answer_id'] ) ? sanitize_text_field( $_POST['answer_id'] ) : 0 );
		$attempt_id = (int) ( isset( $_POST['attempt_id'] ) ? sanitize_text_field( $_POST['attempt_id'] ) : 0 );
		$question_id = (int) ( isset( $_POST['question_id'] ) ? sanitize_text_field( $_POST['question_id'] ) : 0 );
		$quiz_id = (int) ( isset( $_POST['quiz_id'] ) ? sanitize_text_field( $_POST['quiz_id'] ) : 0 );
		$user_id = (int) ( isset( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : 0 );
		$mark_as = ( isset( $_POST['mark_as'] ) ? sanitize_text_field( $_POST['mark_as'] ) : '' );
		// get question
		$question = Query::get_quiz_question( $question_id );
		$answer = Query::get_quiz_attempt_answer( $answer_id );
		$answer->attempt_answer_id = $answer_id;
		$answer->question_mark = $question->question_score;
		$answer->achieved_mark = 'correct' === $mark_as ? $question->question_score : '';
		$answer->is_correct = 'correct' === $mark_as ? 1 : 0;
		// update attempt answer
		Query::quiz_attempt_answer_insert( (array) $answer );
		// update attempt
		$total_questions_marks = Query::get_total_questions_marks_by_quiz_id( $quiz_id );
		$total_earned_marks = Query::get_quiz_attempt_answers_earned_marks( $user_id, $attempt_id );
		$attempt = (array) Query::get_quiz_attempt( $attempt_id );
		$passing_grade = (int) get_post_meta( $quiz_id, 'academy_quiz_passing_grade', true );
		$earned_percentage  = \Academy\Helper::calculate_percentage( $total_questions_marks, $total_earned_marks );
		$attempt['attempt_id'] = $attempt_id;
		$attempt['total_marks'] = $total_questions_marks;
		$attempt['earned_marks'] = $total_earned_marks;
		$attempt['attempt_status'] = ( $earned_percentage >= $passing_grade ? 'passed' : 'failed' );
		$attempt_info = json_decode( $attempt['attempt_info'], true );
		$attempt_info['total_correct_answers'] = Query::get_total_quiz_attempt_correct_answers( $attempt['attempt_id'] );
		$attempt['attempt_info'] = wp_json_encode( $attempt_info );
		$attempt['is_manually_reviewed'] = 1;
		$attempt['manually_reviewed_at'] = current_time( 'mysql' );
		// update attempt manually
		Query::update_quiz_attempt_by_manual_review( $attempt );
		// get updated attempt
		$attempt = (array) Query::get_quiz_attempt( $attempt_id );

		if ( isset( $attempt['attempt_info'] ) ) {
			$attempt['attempt_info'] = json_decode( $attempt['attempt_info'], true );
		}
		if ( isset( $attempt['course_id'] ) ) {
			$attempt['_course'] = array(
				'title' => get_the_title( $attempt['course_id'] ),
				'permalink' => get_the_permalink( $attempt['course_id'] )
			);
		}
		if ( isset( $attempt['quiz_id'] ) ) {
			$attempt['_quiz'] = array(
				'title' => get_the_title( $attempt['quiz_id'] ),
			);
		}
		if ( isset( $attempt['user_id'] ) ) {
			$user_data = get_userdata( $attempt['user_id'] );
			if ( $user_data ) {
				$user = $user_data->data;
				$user->admin_permalink = get_edit_user_link( $attempt['user_id'] );
				$attempt['_user'] = $user;
			}
		}

		wp_send_json_success( $attempt );
	}
}
