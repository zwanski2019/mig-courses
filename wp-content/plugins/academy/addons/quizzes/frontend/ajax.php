<?php
namespace AcademyQuizzes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {
	public static function init() {
		$self = new self();
		// mark as complete
		add_action( 'academy/frontend/before_mark_topic_complete', array( $self, 'mark_quiz_complete' ), 10, 4 );
		// quiz
		add_action( 'wp_ajax_academy_quizzes/frontend/render_quiz', array( $self, 'render_quiz' ) );
		add_action( 'wp_ajax_academy_quizzes/frontend/render_quiz_answers', array( $self, 'render_quiz_answers' ) );
		add_action( 'wp_ajax_academy_quizzes/frontend/insert_quiz_answers', array( $self, 'insert_quiz_answers' ) );
		add_action( 'wp_ajax_academy_quizzes/frontend/insert_quiz_answer', array( $self, 'insert_quiz_answer' ) );
		add_action( 'wp_ajax_academy_quizzes/frontend/get_student_quiz_attempt_details', array( $self, 'get_student_quiz_attempt_details' ) );
	}

	public function mark_quiz_complete( $topic_type, $course_id, $topic_id, $user_id ) {
		if ( 'quiz' === $topic_type && ! \AcademyQuizzes\Classes\Query::has_attempt_quiz( $course_id, $topic_id, $user_id ) ) {
			wp_send_json_error( __( 'Complete the quiz before marking it as done.', 'academy' ) );
		}
	}

	public function render_quiz() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$quiz_id = (int) sanitize_text_field( $_POST['quiz_id'] );
		$user_id   = (int) get_current_user_id();

		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public_course = \Academy\Helper::is_public_course( $course_id );

		if ( $is_administrator || $is_instructor || $enrolled || $is_public_course ) {
			$question_order = get_post_meta( $quiz_id, 'academy_quiz_questions_order', true );
			$questions = \AcademyQuizzes\Classes\Query::get_questions_by_quid_id( $quiz_id, $question_order );
			$order = get_post_meta( $quiz_id, 'academy_quiz_questions_order', true );
			if ( count( $questions ) && $order ) {
				do_action( 'academy_quizzes/frontend/before_render_quiz', $course_id, $quiz_id );
				$settings = \AcademyQuizzes\Classes\Query::get_question_settings_by_quiz_id( $quiz_id, $order );
				wp_send_json_success( [
					'questions' => $questions,
					'settings' => $settings
				] );
			}
			wp_send_json_error( esc_html__( 'Sorry, something went wrong!', 'academy' ) );
		}//end if
		wp_send_json_error( esc_html__( 'Access Denied', 'academy' ) );
	}

	public function render_quiz_answers() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$question_id = (int) sanitize_text_field( $_POST['question_id'] );
		$question_type = sanitize_text_field( $_POST['question_type'] );
		$user_id   = (int) get_current_user_id();

		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public        = \Academy\Helper::is_public_course( $course_id );

		if ( $is_administrator || $is_instructor || $enrolled || $is_public ) {
			$answers = \AcademyQuizzes\Classes\Query::get_quiz_answers_by_question_id( $question_id, $question_type );
			wp_send_json_success( $answers );
		}//end if
		wp_send_json_error( esc_html__( 'Access Denied', 'academy' ) );
		wp_die();
	}

	public function insert_quiz_answers() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$quiz_id = (int) sanitize_text_field( $_POST['quiz_id'] );
		$attempt_id = (int) sanitize_text_field( $_POST['attempt_id'] );

		$user_id   = (int) get_current_user_id();
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public = \Academy\Helper::is_public_course( $course_id );
		$attempt_answers = isset( $_POST['attempt_answers'] ) ? $_POST['attempt_answers'] : '';

		if ( $is_administrator || $is_instructor || $enrolled || $is_public ) {
			// Check if JSON data was received
			if ( ! empty( $attempt_answers ) ) {
				// Decode the JSON string into a PHP array
				$attempt_answers = json_decode( stripslashes( $attempt_answers ), true );
				$results = [];
				if ( is_array( $attempt_answers ) && count( $attempt_answers ) ) {
					foreach ( $attempt_answers as $attempt_answer ) {
						$question_id = (int) $attempt_answer['question_id'];
						$question_score = (float) $attempt_answer['question_score'];
						$question_type = (string) $attempt_answer['question_type'];
						$given_answer = $attempt_answer['given_answer'];

						$correct_answer = 0;
						if ( 'imageAnswer' === $question_type ) {
							$given_answer = wp_list_pluck( json_decode( stripslashes( $given_answer ) ), 'value', 'id' );
							$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_image_answer_quiz_correct_answer( $given_answer, $question_id );
							// Insert JSON Data
							$given_answer = wp_json_encode( $given_answer );
						} elseif ( 'multipleChoice' === $question_type ) {
							$IDs = ( is_array( $given_answer ) ? $given_answer : explode( ',', $given_answer ) );
							$given_answer = implode( ',', $IDs );
							$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_quiz_correct_answer( $IDs, $question_id );
						} elseif ( 'fillInTheBlanks' === $question_type ) {
							$given_answer_args = wp_list_pluck( json_decode( stripslashes( $given_answer ) ), 'value' );
							$given_answer = implode( ',', $given_answer_args );
							$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_fill_in_the_blanks_quiz_correct_answer( $given_answer_args, $question_id );
						} elseif ( 'shortAnswer' !== $question_type ) {
							$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_quiz_correct_answer( $given_answer, $question_id );
						}

						$results[] = \AcademyQuizzes\Classes\Query::quiz_attempt_answer_insert(array(
							'user_id'           => $user_id,
							'quiz_id'           => $quiz_id,
							'question_id'       => $question_id,
							'attempt_id'        => $attempt_id,
							'answer'            => $given_answer,
							'question_mark'     => $question_score,
							'achieved_mark'     => $correct_answer ? $question_score : '',
							'minus_mark'        => '',
							'is_correct'        => $correct_answer,
						));
					}//end foreach
				}//end if
				wp_send_json_success( $results );
			}//end if
			wp_send_json_error( esc_html__( 'Empty Submission', 'academy' ) );
		}//end if
		wp_send_json_error( esc_html__( 'Access Denied', 'academy' ) );
	}

	public function insert_quiz_answer() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$quiz_id = (int) sanitize_text_field( $_POST['quiz_id'] );
		$attempt_id = (int) sanitize_text_field( $_POST['attempt_id'] );
		$question_id = (int) sanitize_text_field( $_POST['question_id'] );
		$question_score = (float) sanitize_text_field( $_POST['question_score'] );
		$question_type = (string) sanitize_text_field( $_POST['question_type'] );
		$given_answer = sanitize_text_field( $_POST['given_answer'] );
		$user_id   = (int) get_current_user_id();
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public = \Academy\Helper::is_public_course( $course_id );

		if ( $is_administrator || $is_instructor || $enrolled || $is_public ) {
			$correct_answer = 0;
			if ( 'imageAnswer' === $question_type ) {
				$given_answer = wp_list_pluck( json_decode( stripslashes( $given_answer ) ), 'value', 'id' );
				$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_image_answer_quiz_correct_answer( $given_answer, $question_id );
				// Insert JSON Data
				$given_answer = wp_json_encode( $given_answer );
			} elseif ( 'multipleChoice' === $question_type ) {
				$IDs = explode( ',', $given_answer );
				$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_quiz_correct_answer( $IDs, $question_id );
			} elseif ( 'fillInTheBlanks' === $question_type ) {
				$given_answer_args = wp_list_pluck( json_decode( stripslashes( $given_answer ) ), 'value' );
				$given_answer = implode( ',', $given_answer_args );
				$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_fill_in_the_blanks_quiz_correct_answer( $given_answer_args, $question_id );
			} elseif ( 'shortAnswer' !== $question_type ) {
				$correct_answer = (int) \AcademyQuizzes\Classes\Query::is_quiz_correct_answer( $given_answer, $question_id );
			}

			$attempt_answer = \AcademyQuizzes\Classes\Query::quiz_attempt_answer_insert(array(
				'user_id'           => $user_id,
				'quiz_id'           => $quiz_id,
				'question_id'       => $question_id,
				'attempt_id'        => $attempt_id,
				'answer'            => $given_answer,
				'question_mark'     => $question_score,
				'achieved_mark'     => $correct_answer ? $question_score : '',
				'minus_mark'        => '',
				'is_correct'        => $correct_answer,
			));

			wp_send_json_success( $attempt_answer );
		}//end if
		wp_send_json_error( esc_html__( 'Access Denied', 'academy' ) );
		wp_die();
	}

	public function get_student_quiz_attempt_details() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$attempt_id = (int) sanitize_text_field( $_POST['attempt_id'] );
		$user_id = (int) ( isset( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : 0 );
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public = \Academy\Helper::is_public_course( $course_id );
		if ( $is_administrator || $is_instructor || $enrolled || $is_public ) {
			$prepare_response = [];
			$attempt_details = \AcademyQuizzes\Classes\Query::get_quiz_attempt_details( $attempt_id, $user_id );
			foreach ( $attempt_details as $attempt_item ) {
				$attempt_item->given_answer = \AcademyQuizzes\Helper::prepare_given_answer( $attempt_item->question_type, $attempt_item );
				$attempt_item->is_correct = (bool) $attempt_item->is_correct;
				$attempt_item->correct_answer = \AcademyQuizzes\Helper::prepare_correct_answer( $attempt_item->question_type, $attempt_item );
				$attempt_item->question_title = html_entity_decode( $attempt_item->question_title );
				$prepare_response[ $attempt_item->attempt_answer_id ] = $attempt_item;
			}

			wp_send_json_success( array_values( $prepare_response ) );
		}
		wp_send_json_error( esc_html__( 'Access Denied', 'academy' ) );
		wp_die();
	}
}
