<?php
namespace AcademyQuizzes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {
	public static function prepare_given_answer( $question_type, $attempt_item ) {
		if ( 'imageAnswer' === $question_type ) {
			$response = array();
			$answers = json_decode( $attempt_item->given_answer, true );
			foreach ( $answers as $id => $answer ) {
				$quiz_answer = Classes\Query::get_quiz_answer( $id );
				$image = wp_get_attachment_image_src( $quiz_answer->image_id );
				$response[] = array(
					'id'            => $id,
					'image_url'     => $image[0],
					'answer_title' => $answer,
				);
			}
			return $response;
		} elseif ( 'fillInTheBlanks' === $question_type ) {
			$replacement = explode( ',', $attempt_item->given_answer );
			return array(
				array(
					'answer_title' => preg_replace_callback('/\{dash\}/', function( $match ) use ( $replacement ) {
						static $index = 0;
						$value = '{' . trim( $replacement[ $index ] ) . '}';
						$index++;
						return $value;
					}, $attempt_item->correct_answer),
				)
			);
		} elseif ( 'shortAnswer' === $question_type ) {
			return array(
				array(
					'answer_title' => $attempt_item->given_answer,
				)
			);
		}//end if
		$answers = \AcademyQuizzes\Classes\Query::get_quiz_all_answer_title_by_ids( explode( ',', $attempt_item->given_answer ) );
		// convert image id to image url
		foreach ( $answers as $answer ) {
			if ( ! $answer->image_id ) {
				unset( $answer->image_id );
			} else {
				$image = wp_get_attachment_image_src( $answer->image_id );
				$answer->image_url = $image[0];
				unset( $answer->image_id );
			}
		}

		return $answers;
	}
	public static function prepare_correct_answer( $question_type, $attempt_item ) {
		if ( 'imageAnswer' === $question_type ) {
			$image_answers = \AcademyQuizzes\Classes\Query::get_quiz_answers_by_question_id( $attempt_item->question_id, 'imageAnswer' );
			$response = [];
			foreach ( $image_answers as $image_answer ) {
				$image = wp_get_attachment_image_src( $image_answer->image_id );
				$response[] = array(
					'id'            => $image_answer->answer_id,
					'image_url'     => $image[0],
					'answer_title' => $image_answer->answer_title,
				);
			}
			return $response;
		} elseif ( 'fillInTheBlanks' === $question_type ) {
			$replacement = explode( '|', $attempt_item->answer_content );
			return array(
				'answer_title' => preg_replace_callback('/\{dash\}/', function( $match ) use ( $replacement ) {
					static $index = 0;
					$value = '{' . trim( $replacement[ $index ] ) . '}';
					$index++;
					return $value;
				}, $attempt_item->correct_answer),
			);
		} elseif ( 'shortAnswer' === $question_type ) {
			return array(
				'answer_title' => __( 'Manually Reviewed Required.', 'academy' ),
			);
		}//end if
		$answers = \AcademyQuizzes\Classes\Query::get_quiz_correct_answers( $attempt_item->question_id, $question_type );

		// convert image id to image url
		foreach ( $answers as $answer ) {
			// unset unnecessary keys
			unset( $answer->answer_id );
			unset( $answer->quiz_id );
			unset( $answer->view_format );
			unset( $answer->answer_order );
			unset( $answer->answer_created_at );
			unset( $answer->answer_updated_at );
			// check if the image id is not null
			if ( ! $answer->image_id ) {
				unset( $answer->image_id );
			} else {
				$image = wp_get_attachment_image_src( $answer->image_id );
				$answer->image_url = $image[0];
				unset( $answer->image_id );
			}
		}

		return $answers;
	}
}
