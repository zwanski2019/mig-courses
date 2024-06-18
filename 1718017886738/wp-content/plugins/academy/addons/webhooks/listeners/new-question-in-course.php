<?php
namespace AcademyWebhooks\Listeners;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AcademyWebhooks\Interfaces\ListenersInterface;
use AcademyWebhooks\Classes\Payload;


class NewQuestionInCourse implements ListenersInterface {
	public static function dispatch( $deliver_callback, $webhook ) {
		add_action(
			'rest_after_insert_comment',
			function( $comment, $request ) use ( $deliver_callback, $webhook ) {
				call_user_func_array(
					$deliver_callback,
					array(
						$webhook,
						self::get_payload( $comment, $request )
					)
				);
			}, 10, 2
		);
	}

	public static function get_payload( $comment, $request ) {

		if ( 'waiting_for_answer' === $comment->comment_approved ) {
			$data = array_merge( Payload::get_question_data( $comment ), array(
				'_course'            => Payload::get_course_data( $comment->comment_post_ID ),
			) );

			return apply_filters( 'academy_webhooks/new_question_in_course_payload', $data );
		}
	}
}
