<?php
namespace Academy\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy;
use Academy\Classes\Query;

class Ajax {
	public static function init() {
		$self = new self();
		// lesson
		add_action( 'wp_ajax_academy/frontend/render_lesson', array( $self, 'render_lesson' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/render_lesson', array( $self, 'render_lesson' ) );
		add_action( 'wp_ajax_academy/frontend/mark_topic_complete', array( $self, 'mark_topic_complete' ) );
		add_action( 'wp_ajax_academy/frontend/render_enrolled_courses', array( $self, 'render_enrolled_courses' ) );
		add_action( 'wp_ajax_academy/frontend/render_pending_enrolled_courses', array( $self, 'render_pending_enrolled_courses' ) );
		add_action( 'wp_ajax_academy/frontend/render_wishlist_courses', array( $self, 'render_wishlist_courses' ) );
		add_action( 'wp_ajax_academy/frontend/render_popup_lesson', array( $self, 'render_popup_lesson' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/render_popup_lesson', array( $self, 'render_popup_lesson' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/course_add_to_wishlist', array( $self, 'course_add_to_wishlist' ) );
		add_action( 'wp_ajax_academy/frontend/course_add_to_wishlist', array( $self, 'course_add_to_wishlist' ) );
		add_action( 'wp_ajax_academy/frontend/course_add_to_favorite', array( $self, 'course_add_to_favorite' ) );
		add_action( 'wp_ajax_academy/frontend/saved_user_info', array( $self, 'saved_user_info' ) );
		add_action( 'wp_ajax_academy/frontend/reset_password', array( $self, 'reset_password' ) );
		add_action( 'wp_ajax_academy/frontend/get_user_given_reviews', array( $self, 'get_user_given_reviews' ) );
		add_action( 'wp_ajax_academy/frontend/get_user_received_reviews', array( $self, 'get_user_received_reviews' ) );
		add_action( 'wp_ajax_academy/frontend/get_user_purchase_history', array( $self, 'get_user_purchase_history' ) );
		add_action( 'wp_ajax_academy/frontend/archive_course_filter', array( $self, 'archive_course_filter' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/archive_course_filter', array( $self, 'archive_course_filter' ) );
		add_action( 'wp_ajax_academy/frontend/get_my_courses', array( $self, 'get_my_courses' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/render_popup_login', array( $self, 'render_popup_login' ) );
		add_action( 'wp_ajax_nopriv_academy/frontend/enroll_course', array( $self, 'enroll_course' ) );
		add_action( 'wp_ajax_academy/frontend/enroll_course', array( $self, 'enroll_course' ) );
		add_action( 'wp_ajax_academy/frontend/complete_course', array( $self, 'complete_course' ) );
		add_action( 'wp_ajax_academy/frontend/add_course_review', array( $self, 'add_course_review' ) );
		// student dashboard
		add_action( 'wp_ajax_academy/frontend/get_course_details', array( $self, 'get_course_details' ) );
	}

	public function render_lesson() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$lesson_id = (int) sanitize_text_field( $_POST['lesson_id'] );
		$user_id   = (int) get_current_user_id();

		$is_administrator = current_user_can( 'administrator' );
		$is_accessible = Academy\Helper::get_lesson_meta( $lesson_id, 'is_previewable' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_public_course = \Academy\Helper::is_public_course( $course_id );

		if ( $is_accessible || $is_administrator || $is_instructor || $enrolled || $is_public_course ) {
			$lesson = \Academy\Helper::get_lesson( $lesson_id );
			$lesson->lesson_title  = stripslashes( $lesson->lesson_title );
			$lesson->lesson_content  = [
				'raw' => stripslashes( $lesson->lesson_content ),
				'rendered' => \Academy\Helper::get_content_html( stripslashes( $lesson->lesson_content ) ),
			];
			$lesson->author_name = get_the_author_meta( 'display_name', $lesson->lesson_author );
			$lesson->meta            = \Academy\Helper::get_lesson_meta_data( $lesson_id );

			if ( empty( $lesson ) ) {
				wp_send_json_error( array( 'message' => __( 'Sorry, something went wrong!', 'academy' ) ) );
				wp_die();
			}

			do_action( 'academy/frontend/before_render_lesson', $lesson, $course_id, $lesson_id );

			if ( count( $lesson->meta ) > 0 ) {
				if ( isset( $lesson->meta['attachment'] ) && ! empty( $lesson->meta['attachment'] ) ) {
					$lesson->meta['attachment'] = wp_get_attachment_url( $lesson->meta['attachment'] );
				}
				if ( isset( $lesson->meta['video_source'] ) && ! empty( $lesson->meta['video_source'] ) ) {
					$video = $lesson->meta['video_source'];
					if ( 'html5' === $video['type'] && isset( $video['id'] ) ) {
						$attachment_id = (int) $video['id'];
						$att_url       = wp_get_attachment_url( $attachment_id );
						$video['url']  = $att_url;
					} elseif ( 'youtube' === $video['type'] ) {
						$video['url'] = \Academy\Helper::youtube_id_from_url( $video['url'] );
					} elseif ( 'vimeo' === $video['type'] ) {
						$video['url'] = \Academy\Helper::youtube_id_from_url( $video['url'] );
					} elseif ( 'embedded' === $video['type'] ) {
						$video['url'] = \Academy\Helper::parse_embedded_url( wp_unslash( $video['url'] ) );
					} elseif ( 'external' === $video['type'] ) {
						// first check external URL contain html5 video or not
						if ( \Academy\Helper::is_html5_video_link( $video['url'] ) ) {
							$video['type'] = 'html5';
							$embed_url = \Academy\Helper::get_basic_url_to_embed_url( $video['url'] );
							if ( isset( $embed_url['url'] ) && ! empty( $embed_url['url'] ) ) {
								$video['url'] = $embed_url['url'];
							}
						} else {
							$video['url'] = \Academy\Helper::get_basic_url_to_embed_url( $video['url'] );
						}
					} else {
						$video['type'] = 'external';
						$video['url'] = $video['url'];
					}//end if
					$lesson->meta['video_source'] = $video;
				}//end if
			}//end if
			wp_send_json_success( $lesson );
		}//end if
		wp_send_json_error( array( 'message' => __( 'Access Denied', 'academy' ) ) );
		wp_die();
	}

	public function mark_topic_complete() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) ( isset( $_POST['course_id'] ) ? sanitize_text_field( $_POST['course_id'] ) : '' );
		$topic_type = ( isset( $_POST['topic_type'] ) ? sanitize_text_field( $_POST['topic_type'] ) : '' );
		$topic_id = (int) ( isset( $_POST['topic_id'] ) ? sanitize_text_field( $_POST['topic_id'] ) : '' );
		$user_id   = (int) get_current_user_id();
		if ( empty( $topic_type ) || ! $course_id || ! $topic_id ) {
			wp_send_json_error( __( 'Request is not valid.', 'academy' ) );
		}

		do_action( 'academy/frontend/before_mark_topic_complete', $topic_type, $course_id, $topic_id, $user_id );

		$option_name = 'academy_course_' . $course_id . '_completed_topics';
		$saved_topics_lists = (array) json_decode( get_user_meta( $user_id, $option_name, true ), true );

		if ( isset( $saved_topics_lists[ $topic_type ][ $topic_id ] ) ) {
			unset( $saved_topics_lists[ $topic_type ][ $topic_id ] );
		} else {
			$saved_topics_lists[ $topic_type ][ $topic_id ] = \Academy\Helper::get_time();
		}
		$saved_topics_lists = wp_json_encode( $saved_topics_lists );
		update_user_meta( $user_id, $option_name, $saved_topics_lists );
		do_action( 'academy/frontend/after_mark_topic_complete', $topic_type, $course_id, $topic_id, $user_id );
		wp_send_json_success( $saved_topics_lists );
	}

	public function render_enrolled_courses() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$request_type = ( isset( $_POST['request_type'] ) ? sanitize_text_field( $_POST['request_type'] ) : 'enrolled' );
		$user_id = get_current_user_id();
		$enrolled_course_ids = \Academy\Helper::get_enrolled_courses_ids_by_user( $user_id );
		$complete_course_ids = \Academy\Helper::get_complete_courses_ids_by_user( $user_id );
		$post_in = $enrolled_course_ids;
		if ( 'complete' === $request_type ) {
			$post_in = $complete_course_ids;
		} elseif ( 'active' === $request_type ) {
			$post_in      = array_diff( $enrolled_course_ids, $complete_course_ids );
		}

		$course_args = array(
			'post_type'      => 'academy_courses',
			'post_status'    => 'publish',
			'post__in'       => $post_in,
			'posts_per_page' => -1,
		);
		$courses = new \WP_Query( $course_args );
		ob_start();
		?>
		<div class="academy-row"> 
			<?php
			if ( count( $post_in ) && $courses && $courses->have_posts() ) :
				while ( $courses->have_posts() ) :
					$courses->the_post();
					$ID                      = get_the_ID();
					$rating                  = \Academy\Helper::get_course_rating( $ID );
					$total_topics           = \Academy\Helper::get_total_number_of_course_topics( $ID );
					$total_completed_topics = \Academy\Helper::get_total_number_of_completed_course_topics_by_course_and_student_id( $ID );
					$percentage              = \Academy\Helper::calculate_percentage( $total_topics, $total_completed_topics );
					?>
			<div class="academy-col-xl-3 academy-col-lg-4 academy-col-md-6 academy-col-sm-12">
				<div class="academy-mycourse academy-mycourse-<?php the_ID(); ?>">
					<div class="academy-mycourse__thumbnail">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>">
							<img class="academy-course__thumbnail-image" src="<?php echo esc_url( Academy\Helper::get_the_course_thumbnail_url( 'academy_thumbnail' ) ); ?>" alt="<?php esc_html_e( 'thumbnail', 'academy' ); ?>">
						</a>
					</div>
					<div class="academy-mycourse__content">
						<div class="academy-course__rating">
								<?php
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo \Academy\Helper::star_rating_generator( $rating->rating_avg );
								?>
								<?php echo esc_html( $rating->rating_avg ); ?> <span
								class="academy-course__rating-count"><?php echo esc_html( '(' . $rating->rating_count . ')' ); ?></span>
						</div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<div class="academy-course__meta">
							<div class="academy-course__meta-item"><?php esc_html_e( 'Total Topics:', 'academy' ); ?><span><?php echo esc_html( $total_topics ); ?></span></div>
							<div class="academy-course__meta-item"><?php esc_html_e( 'Completed Topics:', 'academy' ); ?><span><?php echo esc_html( $total_topics . '/' . $total_completed_topics ); ?></span>
							</div>
						</div>
						<div class="academy-progress-wrap">
							<div class="academy-progress">
								<div class="academy-progress-bar"
									style="width: <?php echo esc_attr( $percentage ) . '%'; ?>;">
								</div>
							</div>
							<span class="academy-progress-wrap__percent"><?php echo esc_html( $percentage ) . esc_html__( '%  Complete', 'academy' ); ?></span>
						</div>
						<?php
							\Academy\Helper::get_template( 'single-course/enroll/continue.php' );
						?>
						<div class="academy-widget-enroll__view_details" data-id="<?php echo esc_attr( get_the_ID() ); ?>">
							<button class="academy-btn academy-btn--bg-purple">
								<?php
								esc_html_e( 'View Details', 'academy' );
								?>
							</button>
						</div>
					</div>
				</div>
			</div>
					<?php
				endwhile;
				?>
		</div>
				<?php

				wp_reset_query(); else : ?>
				<div class='academy-mycourse'>
					<h3 class='academy-not-found'>
						<?php
						if ( 'active' === $request_type ) {
							esc_html_e( 'You have no active courses.', 'academy' );
						} elseif ( 'complete' === $request_type ) {
							esc_html_e( 'You have no complete courses.', 'academy' );
						} else {
							esc_html_e( 'You are not enrolled in any course yet.', 'academy' );
						}
						?>
					</h3>
				</div>
					<?php
		endif;
				$output = ob_get_clean();
				wp_send_json_success( array(
					'html' => $output
				) );
	}

	public function render_pending_enrolled_courses() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id = get_current_user_id();
		$pending_enrolled_course_ids = \Academy\Helper::get_pending_enrolled_courses_ids_by_user( $user_id );
		if ( ! count( $pending_enrolled_course_ids ) ) {
			wp_send_json_success( [] );
		}
		$course_args = array(
			'post_type'      => 'academy_courses',
			'post_status'    => 'publish',
			'post__in'       => $pending_enrolled_course_ids,
			'posts_per_page' => -1,
		);
		$courses = new \WP_Query( $course_args );
		$response = [];
		if ( count( $pending_enrolled_course_ids ) && $courses->have_posts() ) {
			while ( $courses->have_posts() ) :
				$courses->the_post();
				$response[] = array(
					'ID' => get_the_ID(),
					'permalink' => get_the_permalink(),
					'title' => get_the_title(),
				);
			endwhile;
			wp_reset_query();
		}
		wp_send_json_success( $response );
	}

	public function render_wishlist_courses() {
		check_ajax_referer( 'academy_nonce', 'security' );

		$courses = \Academy\Helper::get_wishlist_courses_by_user( get_current_user_id(), array( 'private', 'publish' ) );

		ob_start();
		?>

<div class="academy-courses">
	<div class="academy-row">
		<?php
		if ( $courses && $courses->have_posts() ) :
			while ( $courses->have_posts() ) :
				$courses->the_post();
				\Academy\Helper::get_template_part( 'content', 'course' );
			endwhile;
			wp_reset_query();
		else :
			?>
		<div class='academy-mycourse'>
			<h3 class='academy-not-found'><?php esc_html_e( 'Your wishlist is empty!', 'academy' ); ?></h3>
		</div>
			<?php
			endif;
		?>
	</div>
</div>
		<?php
		$output = ob_get_clean();
		wp_send_json_success( $output );
		wp_die();
	}

	public function course_add_to_wishlist() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! is_user_logged_in() ) {
			if ( \Academy\Helper::get_settings( 'is_enabled_academy_login', true ) ) {
				ob_start();
				echo do_shortcode( '[academy_login_form form_title="' . esc_html__( 'Hi, Welcome back!', 'academy' ) . '" show_logged_in_message="false"]' );
				$markup = ob_get_clean();
				wp_send_json_error( array( 'markup' => $markup ) );
			}
			wp_send_json_error( array( 'redirect_url' => wp_login_url( wp_get_referer() ) ) );
		}

		global $wpdb;
		$course_id          = (int) sanitize_text_field( $_POST['course_id'] );
		$user_id            = get_current_user_id();
		$is_already_in_list = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 'academy_course_wishlist' AND meta_value = %d;", $user_id, $course_id ) );
		if ( $is_already_in_list ) {
			$wpdb->delete(
				$wpdb->usermeta,
				array(
					'user_id'    => $user_id,
					'meta_key'   => 'academy_course_wishlist',
					'meta_value' => $course_id,
				)
			);
			wp_send_json_success( array( 'is_added' => false ) );
		}
		add_user_meta( $user_id, 'academy_course_wishlist', $course_id );
		wp_send_json_success( array( 'is_added' => true ) );
	}
	public function course_add_to_favorite() {
		check_ajax_referer( 'academy_nonce', 'security' );
		global $wpdb;
		$course_id          = (int) sanitize_text_field( $_POST['course_id'] );
		$user_id            = get_current_user_id();
		$is_already_in_list = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 'academy_course_favorite' AND meta_value = %d;", $user_id, $course_id ) );
		if ( $is_already_in_list ) {
			$wpdb->delete(
				$wpdb->usermeta,
				array(
					'user_id'    => $user_id,
					'meta_key'   => 'academy_course_favorite',
					'meta_value' => $course_id,
				)
			);
			wp_send_json_success( array( 'is_added' => false ) );
		}
		add_user_meta( $user_id, 'academy_course_favorite', $course_id );
		wp_send_json_success( array( 'is_added' => true ) );
	}
	public function saved_user_info() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! is_user_logged_in() ) {
			wp_die();
		}

		$user_info = [
			'first_name' => ( isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '' ),
			'last_name' => ( isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '' ),
			'academy_profile_photo' => ( isset( $_POST['academy_profile_photo'] ) ? sanitize_text_field( $_POST['academy_profile_photo'] ) : '' ),
			'academy_cover_photo' => ( isset( $_POST['academy_cover_photo'] ) ? sanitize_text_field( $_POST['academy_cover_photo'] ) : '' ),
			'academy_phone_number' => ( isset( $_POST['academy_phone_number'] ) ? sanitize_text_field( $_POST['academy_phone_number'] ) : '' ),
			'academy_profile_designation' => ( isset( $_POST['academy_profile_designation'] ) ? sanitize_text_field( $_POST['academy_profile_designation'] ) : '' ),
			'academy_profile_bio' => ( isset( $_POST['academy_profile_bio'] ) ? sanitize_text_field( $_POST['academy_profile_bio'] ) : '' ),
			'academy_website_url' => ( isset( $_POST['academy_website_url'] ) ? sanitize_text_field( $_POST['academy_website_url'] ) : '' ),
			'academy_github_url' => ( isset( $_POST['academy_github_url'] ) ? sanitize_text_field( $_POST['academy_github_url'] ) : '' ),
			'academy_facebook_url' => ( isset( $_POST['academy_facebook_url'] ) ? sanitize_text_field( $_POST['academy_facebook_url'] ) : '' ),
			'academy_twitter_url' => ( isset( $_POST['academy_twitter_url'] ) ? sanitize_text_field( $_POST['academy_twitter_url'] ) : '' ),
			'academy_linkedin_url' => ( isset( $_POST['academy_linkedin_url'] ) ? sanitize_text_field( $_POST['academy_linkedin_url'] ) : '' )
		];

		$user_id = get_current_user_id();
		foreach ( $user_info as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}
		wp_send_json_success( $user_info );
		wp_die();
	}
	public function reset_password() {
		check_ajax_referer( 'academy_nonce', 'security' );

		$current_password = ( $_POST['current_password'] ? sanitize_text_field( $_POST['current_password'] ) : '' );
		$new_password = ( $_POST['new_password'] ? sanitize_text_field( $_POST['new_password'] ) : '' );
		$confirm_new_password = ( $_POST['confirm_new_password'] ? sanitize_text_field( $_POST['confirm_new_password'] ) : '' );
		$message      = '';
		$current_user = wp_get_current_user();
		if ( $current_user && wp_check_password( $current_password, $current_user->data->user_pass, $current_user->ID ) ) {
			if ( ! empty( $new_password ) && $new_password === $confirm_new_password ) {
				$user = wp_get_current_user();
				// Change password.
				wp_set_password( $new_password, $user->ID );
				// Log-in again.
				wp_set_auth_cookie( $user->ID );
				wp_set_current_user( $user->ID );
				do_action( 'wp_login', $user->user_login, $user );
				wp_send_json_success( esc_html__( 'Successfully, updated your password.', 'academy' ) );
				wp_die();
			} else {
				$message .= esc_html__( 'New Password and Confirm New password do not match equally.', 'academy' );
			}
		} else {
			$message .= esc_html__( 'Current password is incorrect.', 'academy' );
		}

		wp_send_json_error( $message );
		wp_die();
	}
	public function get_user_given_reviews() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id = get_current_user_id();
		$reviews = \Academy\Helper::get_reviews_by_user( $user_id );
		$results = [];
		if ( is_array( $reviews ) ) {
			foreach ( $reviews as $review ) {
				$review->post_title     = get_the_title( $review->comment_post_ID );
				$review->post_permalink = esc_url( get_the_permalink( $review->comment_post_ID ) );
				$results[]              = $review;
			}
		}
		wp_send_json_success( $results );
		wp_die();
	}
	public function get_user_received_reviews() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id = get_current_user_id();
		$reviews = \Academy\Helper::get_reviews_by_instructor( $user_id );
		$results = [];
		if ( is_array( $reviews ) ) {
			foreach ( $reviews as $review ) {
				$review->post_title     = get_the_title( $review->comment_post_ID );
				$review->post_permalink = esc_url( get_the_permalink( $review->comment_post_ID ) );
				$results[]              = $review;
			}
		}
		wp_send_json_success( $results );
		wp_die();
	}

	public function get_user_purchase_history() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! \Academy\Helper::is_active_woocommerce() ) {
			wp_die();
		}
		$user_id = get_current_user_id();
		$orders  = \Academy\Helper::get_orders_by_user_id( $user_id );
		$results = [];
		if ( is_array( $orders ) ) {
			foreach ( $orders as $order ) {
				$courses_order = \Academy\Helper::get_course_enrolled_ids_by_order_id( $order->ID );
				$courses       = [];
				if ( is_array( $courses_order ) ) {
					foreach ( $courses_order as $course ) {
						$courses[] = [
							'ID'        => $course['course_id'],
							'title'     => get_the_title( $course['course_id'] ),
							'permalink' => esc_url( get_the_permalink( $course['course_id'] ) ),
						];
					}
				}
				$wc_order  = wc_get_order( $order->ID );
				$price     = $wc_order->get_total();
				$status    = \Academy\Helper::order_status_context( $order->post_status );
				$results[] = [
					'ID'      => $order->ID,
					'courses' => $courses,
					'price'   => wc_price( $price, array( 'currency' => $wc_order->get_currency() ) ),
					'status'  => $status,
					'date'    => date_i18n( get_option( 'date_format' ), strtotime( $order->post_date ) ),
				];
			}//end foreach
		}//end if
		wp_send_json_success( array_reverse( $results ) );
		wp_die();
	}

	public function archive_course_filter() {
		check_ajax_referer( 'academy_nonce', 'security' );

		$search   = ( isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '' );
		$category = ( isset( $_POST['category'] ) ? \Academy\Helper::sanitize_text_or_array_field( $_POST['category'] ) : [] );
		$tags     = ( isset( $_POST['tags'] ) ? \Academy\Helper::sanitize_text_or_array_field( $_POST['tags'] ) : [] );
		$levels   = ( isset( $_POST['levels'] ) ? \Academy\Helper::sanitize_text_or_array_field( $_POST['levels'] ) : [] );
		$type     = ( isset( $_POST['type'] ) ? \Academy\Helper::sanitize_text_or_array_field( $_POST['type'] ) : [] );
		$orderby  = ( isset( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'DESC' );
		$paged    = ( isset( $_POST['paged'] ) ) ? absint( sanitize_text_field( $_POST['paged'] ) ) : 1;
		$per_row  = ( isset( $_POST['per_row'] ) ? array(
			'desktop' => absint( sanitize_text_field( $_POST['per_row'] ) ),
			'tablet'  => 2,
			'mobile'  => 1
		) : Academy\Helper::get_settings( 'course_archive_courses_per_row', array(
			'desktop' => 3,
			'tablet'  => 2,
			'mobile'  => 1
		) ) );
		$per_page = ( isset( $_POST['per_page'] ) ? absint( sanitize_text_field( $_POST['per_page'] ) ) : (int) \Academy\Helper::get_settings( 'course_archive_courses_per_page', 12 ) );

		$args = \Academy\Helper::prepare_course_search_query_args(
			[
				'search'         => $search,
				'category'       => $category,
				'tags'           => $tags,
				'levels'         => $levels,
				'type'           => $type,
				'paged'          => $paged,
				'orderby'        => $orderby,
				'posts_per_page' => $per_page,
			]
		);

		$grid_class = \Academy\Helper::get_responsive_column( $per_row );
		// phpcs:ignore WordPress.WP.DiscouragedFunctions.query_posts_query_posts
		wp_reset_query();
		wp_reset_postdata();
		$courses_query = new \WP_Query( apply_filters( 'academy_courses_filter_args', $args ) );
		ob_start();
		?>
		<div class="academy-row">
			<?php
			if ( $courses_query->have_posts() ) {
				// Load posts loop.
				while ( $courses_query->have_posts() ) {
					$courses_query->the_post();
					/**
					 * Hook: academy/templates/course_loop.
					 */
					do_action( 'academy/templates/course_loop' );
					\Academy\Helper::get_template( 'content-course.php', array( 'grid_class' => $grid_class ) );
				}
				\Academy\Helper::get_template( 'archive/pagination.php' );
				wp_reset_query();
				wp_reset_postdata();
			} else {
				\Academy\Helper::get_template( 'archive/course-none.php' );
			}
			?>
		</div>
		<?php
		$markup = ob_get_clean();
		wp_send_json_success(
			[
				'markup'      => $markup,
				'found_posts' => $courses_query->found_posts,
			]
		);
	}

	public function get_my_courses() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_academy_instructor' ) ) {
			wp_die();
		}

		$response = [];
		$course_args = array(
			'post_type'         => 'academy_courses',
			'post_status'       => 'publish',
			'author'            => get_current_user_id(),
			'posts_per_page'    => -1,
		);
		$courses = new \WP_Query( $course_args );
		if ( $courses->have_posts() ) :
			while ( $courses->have_posts() ) :
				$courses->the_post();
				$ID                      = get_the_ID();
				$rating                  = \Academy\Helper::get_course_rating( $ID );
				$rating_markup = \Academy\Helper::star_rating_generator( $rating->rating_avg );
				$total_enrolled = \Academy\Helper::count_course_enrolled( $ID );
				$response[] = array(
					'title'             => html_entity_decode( get_the_title( $ID ) ),
					'permalink'         => get_the_permalink( $ID ),
					'rating'            => $rating,
					'rating_markup'     => $rating_markup,
					'total_enrolled'    => $total_enrolled
				);
			endwhile;
			wp_reset_query();
		endif;
		wp_send_json_success( $response );
	}

	public function render_popup_login() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( is_user_logged_in() ) {
			wp_die();
		}
		$current_permalink = sanitize_text_field( $_REQUEST['current_permalink'] );
		$register_url = esc_url(add_query_arg( array(
			'redirect_url' => $current_permalink
		), \Academy\Helper::get_page_permalink( 'frontend_student_reg_page' ) ));
		ob_start();
		echo do_shortcode( '[academy_login_form 
			form_title="' . esc_html__( 'Hi, Welcome back!', 'academy' ) . '" 
			show_logged_in_message="false" 
			student_register_url="' . $register_url . '"
			login_redirect_url="' . $current_permalink . '"]'
		);
		$markup = ob_get_clean();
		wp_send_json_success( $markup );
	}
	public function enroll_course() {
		check_ajax_referer( 'academy_nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'is_required_logged_in' => true ) );
		}

		$user_id = get_current_user_id();

		$course_id = (int) sanitize_text_field( $_POST['course_id'] );

		if ( 'paid' === \Academy\Helper::get_course_type( $course_id ) ) {
			wp_send_json_error( __( 'Failed to enrolled the course', 'academy' ) );
		}

		$is_enrolled = \Academy\Helper::do_enroll( $course_id, $user_id );

		if ( $is_enrolled ) {
			wp_send_json_success( __( 'Successfully Enrolled.', 'academy' ) );
		}
		wp_send_json_error( __( 'Failed to enrolled course.', 'academy' ) );
	}

	public function complete_course() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id = get_current_user_id();
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$has_incomplete_topic = false;
		$curriculum_lists = \Academy\Helper::get_course_curriculum( $course_id );
		foreach ( $curriculum_lists as $curriculum_list ) {
			if ( is_array( $curriculum_list['topics'] ) ) {
				foreach ( $curriculum_list['topics'] as $topic ) {
					if ( empty( $topic['is_completed'] ) && 'sub-curriculum' !== $topic['type'] ) {
						$has_incomplete_topic = true;
						break;
					}
					if ( isset( $topic['topics'] ) && is_array( $topic['topics'] ) ) {
						foreach ( $topic['topics'] as $child_topic ) {
							if ( empty( $child_topic['is_completed'] ) ) {
								$has_incomplete_topic = true;
								break;
							}
						}
					}
				}
			}
			// found incomplete topic then break loop
			if ( $has_incomplete_topic ) {
				break;
			}
		}//end foreach

		if ( $has_incomplete_topic ) {
			wp_send_json_error( __( 'To complete this course, please make sure that you have finished all the topics.', 'academy' ) );
		}

		do_action( 'academy/admin/course_complete_before', $course_id );

		global $wpdb;
		$date = gmdate( 'Y-m-d H:i:s', \Academy\Helper::get_time() );

		// hash is unique.
		do {
			$hash    = substr( md5( wp_generate_password( 32 ) . $date . $course_id . $user_id ), 0, 16 );
			$hasHash = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(comment_ID) from {$wpdb->comments} 
				WHERE comment_agent = 'academy' AND comment_type = 'course_completed' AND comment_content = %s ",
					$hash
				)
			);

		} while ( $hasHash > 0 );

		$data = array(
			'comment_post_ID'  => $course_id,
			'comment_author'   => $user_id,
			'comment_date'     => $date,
			'comment_date_gmt' => get_gmt_from_date( $date ),
			'comment_content'  => $hash,
			'comment_approved' => 'approved',
			'comment_agent'    => 'academy',
			'comment_type'     => 'course_completed',
			'user_id'          => $user_id,
		);
		$is_complete = $wpdb->insert( $wpdb->comments, $data );

		do_action( 'academy/admin/course_complete_after', $course_id, $user_id );

		if ( $is_complete ) {
			wp_send_json_success( __( 'Successfully Completed.', 'academy' ) );
		}
		wp_send_json_error( __( 'Failed, try again.', 'academy' ) );
	}
	public function add_course_review() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$course_id = (int) sanitize_text_field( $_POST['course_id'] );
		$user_id = get_current_user_id();
		$current_user = get_userdata( $user_id );

		if ( ! \Academy\Helper::is_completed_course( $course_id, $user_id ) ) {
			wp_send_json_error( __( 'Sorry, you have to complete the course first.', 'academy' ) );
		}

		$rating = (int) sanitize_text_field( $_POST['rating'] );
		$review = wp_kses_post( $_POST['review'] );

		$data = array(
			'comment_post_ID'       => $course_id,
			'comment_content'       => $review,
			'user_id'               => $current_user->ID,
			'comment_author'        => $current_user->user_login,
			'comment_author_email'  => $current_user->user_email,
			'comment_author_url'    => $current_user->user_url,
			'comment_type'          => 'academy_courses',
			'comment_approved'      => '1',
			'comment_meta'          => array(
				'academy_rating'    => $rating,
			)
		);

		// get all review of current user
		$existing_reviews = get_comments(array(
			'comment_type' => 'academy_courses',
			'post_id' => $course_id,
			'user_id' => $current_user->ID,
		));

		// if the review exist then update it
		if ( count( $existing_reviews ) ) {
			$existing_review = current( $existing_reviews );

			$data['comment_ID'] = $existing_review->comment_ID;

			$is_update = wp_update_comment( $data );

			if ( $is_update ) {
				wp_send_json_success(array(
					'message'       => __( 'Successfully Updated Review.', 'academy' ),
					'redirect_url' => get_the_permalink( $course_id ),
				));
			}
		}

		// insert the review
		$comment_id = wp_insert_comment( $data );
		if ( $comment_id ) {
			wp_send_json_success(array(
				'message'       => __( 'Successfully Added Review.', 'academy' ),
				'redirect_url' => get_the_permalink( $course_id ),
			));
		}
		wp_send_json_error( __( 'Sorry, Failed to add review.', 'academy' ) );
	}
	public function get_course_details() {
		check_ajax_referer( 'academy_nonce', 'security' );

		$student_id = get_current_user_id();
		$course_id = isset( $_POST['courseID'] ) ? sanitize_text_field( $_POST['courseID'] ) : 0;
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $student_id, $course_id );
		$enrolled         = \Academy\Helper::is_enrolled( $course_id, $student_id );
		$response = [];
		if ( $is_administrator || $is_instructor || $enrolled ) {
			$analytics_data = \Academy\Helper::prepare_analytics_for_user( $student_id, $course_id );
			$analytics_data['title'] = html_entity_decode( get_the_title( $course_id ) );
			$analytics_data['course_link'] = get_post_permalink( $course_id );
			$response['enrolled_info'][] = $analytics_data;
		}
		wp_send_json_success( $response );
	}
}
