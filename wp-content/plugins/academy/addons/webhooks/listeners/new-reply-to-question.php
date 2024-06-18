<?php
namespace AcademyWebhooks\Listeners;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AcademyWebhooks\Interfaces\ListenersInterface;
use AcademyWebhooks\Classes\Payload;


class NewReplyToQuestion implements ListenersInterface {
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

		if ( 'answered' === $comment->comment_approved ) {
			$instructor = '';
			$status = get_user_meta( $comment->user_id, 'academy_instructor_status', true );
			$new_comment = Payload::get_question_data( $comment );
			// unset question title
			unset( $new_comment['title'] );

			if ( 'approved' === $status ) {
				$instructor = 'academy_instructor';
			}

			$parent_comment = get_comment( $comment->comment_parent );
			$new_comment['sender'] = $instructor;

			$update_reply_comment = array_merge( $new_comment,
				[ '_question' => Payload::get_question_data( $parent_comment ) ],
				[ '_course' => Payload::get_course_data( $comment->comment_post_ID ) ]
			);

			return apply_filters( 'academy_webhooks/new_reply_to_question_payload', $update_reply_comment );
		}//end if
	}
}
