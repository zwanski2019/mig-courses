<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Academy Templates Related all functions write here
 */

if ( ! function_exists( 'academy_load_canvas_page_template' ) ) {
	function academy_load_canvas_page_template( $templates ) {
		$templates['academy-canvas.php'] = esc_html__( 'Academy Canvas', 'academy' );
		return $templates;
	}
}

if ( ! function_exists( 'academy_redirect_canvas_page_template' ) ) {
	function academy_redirect_canvas_page_template( $template ) {
		$post = get_post();
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( 'academy-canvas.php' === basename( $page_template ) ) {
			$template = ACADEMY_ROOT_DIR_PATH . 'templates/academy-canvas.php';
		}
		return $template;
	}
}

if ( ! function_exists( 'academy_single_course_sidebar' ) ) {
	function academy_single_course_sidebar() {
		\Academy\Helper::get_template( 'single-course/sidebar.php' );
	}
}



if ( ! function_exists( 'academy_single_course_header' ) ) {
	function academy_single_course_header() {
		$difficulty_level = get_post_meta( get_the_ID(), 'academy_course_difficulty_level', true );
		$preview_video    = \Academy\Helper::get_course_preview_video( get_the_ID() );
		\Academy\Helper::get_template(
			'single-course/header.php',
			apply_filters(
				'academy/single_course_header_args',
				[
					'difficulty_level' => $difficulty_level,
					'preview_video'    => $preview_video,
				]
			)
		);
	}
}





if ( ! function_exists( 'academy_single_course_description' ) ) {
	function academy_single_course_description() {
		\Academy\Helper::get_template( 'single-course/description.php' );
	}
}


if ( ! function_exists( 'academy_single_course_curriculums' ) ) {
	function academy_single_course_curriculums() {
		$course_id = get_the_ID();
		$curriculums = \Academy\Helper::get_course_curriculum( $course_id, false );
		$topics_first_item_open_status = (bool) \Academy\Helper::get_settings( 'is_opened_course_single_first_topic', true );

		\Academy\Helper::get_template(
			'single-course/curriculums.php',
			array(
				'curriculums'                     => $curriculums,
				'topics_first_item_open_status'  => $topics_first_item_open_status,
			)
		);
	}
}//end if


if ( ! function_exists( 'academy_single_course_instructors' ) ) {
	function academy_single_course_instructors() {
		global $post;
		$author_id = $post->post_author;
		if ( \Academy\Helper::get_addon_active_status( 'multi_instructor' ) ) {
			$instructors = \Academy\Helper::get_instructors_by_course_id( get_the_ID() );
		} else {
			$instructors = \Academy\Helper::get_instructor_by_author_id( $author_id );
		}
		$instructor_reviews_status = (bool) \Academy\Helper::get_settings( 'is_enabled_instructor_review', true );
		if ( ! $instructors ) {
			return;
		}
		\Academy\Helper::get_template(
			'single-course/instructors.php',
			apply_filters(
				'academy/single_course_content_instructors_args',
				[
					'instructors' => $instructors,
					'instructor_reviews_status' => $instructor_reviews_status,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_single_course_benefits' ) ) {
	function academy_single_course_benefits() {
		$benefits = Academy\Helper::string_to_array( get_post_meta( get_the_ID(), 'academy_course_benefits', true ) );
		\Academy\Helper::get_template( 'single-course/benefits.php', apply_filters( 'academy/single_course_content_benefits_args', [ 'benefits' => $benefits ] ) );
	}
}

if ( ! function_exists( 'academy_single_course_additional_info' ) ) {
	function academy_single_course_additional_info() {
		$audience     = Academy\Helper::string_to_array( get_post_meta( get_the_ID(), 'academy_course_audience', true ) );
		$requirements = Academy\Helper::string_to_array( get_post_meta( get_the_ID(), 'academy_course_requirements', true ) );
		$materials    = Academy\Helper::string_to_array( get_post_meta( get_the_ID(), 'academy_course_materials_included', true ) );
		$tabs_nav     = [];
		$tabs_content = [];
		if ( is_array( $audience ) && count( $audience ) > 0 ) {
			$tabs_nav['audience']     = esc_html__( 'Targeted Audience', 'academy' );
			$tabs_content['audience'] = $audience;
		}
		if ( is_array( $requirements ) && count( $requirements ) > 0 ) {
			$tabs_nav['requirements']     = esc_html__( 'Requirements', 'academy' );
			$tabs_content['requirements'] = $requirements;
		}
		if ( is_array( $materials ) && count( $materials ) > 0 ) {
			$tabs_nav['materials']     = esc_html__( 'Materials Included', 'academy' );
			$tabs_content['materials'] = $materials;
		}

		\Academy\Helper::get_template(
			'single-course/additional-info.php',
			apply_filters(
				'academy/single_course_content_additional_info_args',
				[
					'tabs_nav'     => $tabs_nav,
					'tabs_content' => $tabs_content,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_single_course_feedback' ) ) {
	function academy_single_course_feedback() {
		if ( ! (bool) \Academy\Helper::get_settings( 'is_enabled_course_review', true ) ) {
			return;
		}
		$rating = \Academy\Helper::get_course_rating( get_the_ID() );
		\Academy\Helper::get_template( 'single-course/feedback.php', array( 'rating' => $rating ) );
	}
}

if ( ! function_exists( 'academy_single_course_reviews' ) ) {
	function academy_single_course_reviews() {
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
}

if ( ! function_exists( 'academy_archive_course_header' ) ) {
	function academy_archive_course_header() {
		\Academy\Helper::get_template( 'archive/header.php' );
	}
}

if ( ! function_exists( 'academy_archive_course_header_filter' ) ) {
	function academy_archive_course_header_filter() {
		global $wp_query;
		$orderby = ( get_query_var( 'orderby' ) ) ? get_query_var( 'orderby' ) : ''; ?>
		<div class="academy-courses__header-filter">
			<p class="academy-courses__header-result-count"><?php esc_html_e( 'Showing all', 'academy' ); ?>
				<span><?php echo esc_html( $wp_query->found_posts ); ?></span> <?php esc_html_e( 'results', 'academy' ); ?>
			</p>
			<form class="academy-courses__header-ordering" method="get">
				<select name="orderby" class="academy-courses__header-orderby" aria-label="Course order"
					onchange="this.form.submit()">
					<option value="DESC" <?php selected( $orderby, 'DESC' ); ?>>
						<?php esc_html_e( 'Default Sorting', 'academy' ); ?>
					</option>
					<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>>
						<?php esc_html_e( 'Menu Order', 'academy' ); ?>
					</option>
					<option value="name" <?php selected( $orderby, 'name' ); ?>>
						<?php esc_html_e( 'Order by course name', 'academy' ); ?>
					</option>
					<option value="date" <?php selected( $orderby, 'date' ); ?>>
						<?php esc_html_e( 'Order by Publish Date', 'academy' ); ?>
					</option>
					<option value="modified" <?php selected( $orderby, 'modified' ); ?>>
						<?php esc_html_e( 'Order by Modified Date', 'academy' ); ?>
					</option>
					<option value="ratings" <?php selected( $orderby, 'ratings' ); ?>>
						<?php esc_html_e( 'Order by Most Reviews', 'academy' ); ?>
					</option>
					<option value="ID" <?php selected( $orderby, 'ID' ); ?>>
						<?php esc_html_e( 'Order by ID', 'academy' ); ?>
					</option>
				</select>
				<input type="hidden" name="paged" value="1">
			</form>
		</div>
		<?php
	}
}//end if

if ( ! function_exists( 'academy_no_course_found' ) ) {
	function academy_no_course_found() {
		\Academy\Helper::get_template( 'archive/course-none.php' );
	}
}

if ( ! function_exists( 'academy_course_pagination' ) ) {
	function academy_course_pagination() {
		\Academy\Helper::get_template( 'archive/pagination.php' );
	}
}

if ( ! function_exists( 'academy_course_loop_header' ) ) {
	function academy_course_loop_header() {
		global $wpdb;
		$course_id              = get_the_ID();
		$user_id                = get_current_user_id();
		$is_already_in_wishlist = false;
		$wishlists_status = (bool) \Academy\Helper::get_settings( 'is_enabled_course_wishlist', true );
		if ( $wishlists_status ) {
			$is_already_in_wishlist = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 'academy_course_wishlist' AND meta_value = %d;", $user_id, $course_id ) );
		}
		\Academy\Helper::get_template( 'loop/header.php', array(
			'is_already_in_wishlist' => $is_already_in_wishlist,
			'wishlists_status' => $wishlists_status
		) );
	}
}

if ( ! function_exists( 'academy_course_loop_content' ) ) {
	function academy_course_loop_content() {
		\Academy\Helper::get_template( 'loop/content.php' );
	}
}

if ( ! function_exists( 'academy_course_loop_footer' ) ) {
	function academy_course_loop_footer() {
		\Academy\Helper::get_template( 'loop/footer.php' );
	}
}

if ( ! function_exists( 'academy_course_loop_rating' ) ) {
	function academy_course_loop_rating() {
		$rating = \Academy\Helper::get_course_rating( get_the_ID() );
		$reviews_status = \Academy\Helper::get_settings( 'is_enabled_course_review', true );
		if ( $reviews_status ) {
			\Academy\Helper::get_template( 'loop/rating.php', [ 'rating' => $rating ] );
		}
	}
}

if ( ! function_exists( 'academy_course_loop_enroll' ) ) {
	function academy_course_loop_enroll() {
		\Academy\Helper::get_template( 'loop/enroll.php' );
	}
}

if ( ! function_exists( 'academy_course_loop_footer_inner_price' ) ) {
	function academy_course_loop_footer_inner_price() {
		$course_id = get_the_ID();
		$course_type   = \Academy\Helper::get_course_type( $course_id );
		$is_paid   = \Academy\Helper::is_course_purchasable( $course_id );
		$price     = '';
		if ( \Academy\Helper::is_active_woocommerce() && $is_paid ) {
			$product_id = Academy\Helper::get_course_product_id( $course_id );
			if ( $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$price   = $product->get_price_html();
				}
			}
		}
		\Academy\Helper::get_template(
			'loop/price.php',
			array(
				'price'   => $price,
				'is_paid' => $is_paid,
				'course_type' => $course_type,
			)
		);
	}
}//end if


if ( ! function_exists( 'academy_review_lists' ) ) {
	function academy_review_lists( $comment, $args, $depth ) {
		\Academy\Helper::get_template(
			'single-course/review.php',
			array(
				'comment' => $comment,
				'args'    => $args,
				'depth'   => $depth,
			)
		);
	}
}


if ( ! function_exists( 'academy_review_display_gravatar' ) ) {
	/**
	 * Display the review authors gravatar
	 *
	 * @param array $comment WP_Comment.
	 * @return void
	 */
	function academy_review_display_gravatar( $comment ) {
		echo get_avatar( $comment->comment_author_email, apply_filters( 'academy/review_gravatar_size', '80' ), '' );
	}
}

if ( ! function_exists( 'academy_review_display_rating' ) ) {
	/**
	 * Display the reviewers star rating
	 *
	 * @return void
	 */
	function academy_review_display_rating() {
		if ( post_type_supports( 'academy_courses', 'comments' ) ) {
			$reviews_status = (bool) \Academy\Helper::get_settings( 'is_enabled_course_review', true );
			if ( $reviews_status ) {
				\Academy\Helper::get_template( 'single-course/review-rating.php' );
			}
		}
	}
}

if ( ! function_exists( 'academy_review_display_meta' ) ) {
	/**
	 * Display the review authors meta (name, verified owner, review date)
	 *
	 * @return void
	 */
	function academy_review_display_meta() {
		\Academy\Helper::get_template( 'single-course/review-meta.php' );
	}
}


if ( ! function_exists( 'academy_review_display_comment_text' ) ) {

	/**
	 * Display the review content.
	 */
	function academy_review_display_comment_text() {
		echo '<div class="academy-review-description">';
		comment_text();
		echo '</div>';
	}
}


if ( ! function_exists( 'academy_get_rating_html' ) ) {
	/**
	 * Get HTML for ratings.
	 *
	 * @param  float $rating Rating being shown.
	 * @param  int   $count  Total number of ratings.
	 * @return string
	 */
	function academy_get_rating_html( $rating, $count = 0 ) {
		$html = '';
		if ( 0 < $rating ) {
			$html = \Academy\Helper::single_star_rating_generator( $rating );
		}
		return apply_filters( 'academy/course_get_rating_html', $html, $rating, $count );
	}
}

if ( ! function_exists( 'academy_single_course_enroll' ) ) {
	function academy_single_course_enroll() {
		\Academy\Helper::get_template(
			'single-course/enroll/enroll.php'
		);
	}
}

if ( ! function_exists( 'academy_single_course_enroll_content' ) ) {
	function academy_single_course_enroll_content() {
		$course_id   = get_the_ID();
		$enrolled    = \Academy\Helper::is_enrolled( get_the_ID(), get_current_user_id() );
		$completed   = \Academy\Helper::is_completed_course( get_the_ID(), get_current_user_id(), true );
		$is_paid     = (bool) \Academy\Helper::is_course_purchasable( $course_id );
		$is_public = \Academy\Helper::is_public_course( $course_id );
		$price       = '';
		if ( $is_paid && \Academy\Helper::is_active_woocommerce() ) {
			$product_id = Academy\Helper::get_course_product_id( $course_id );
			if ( $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$price   = $product->get_price_html();
				}
			}
		}

		$duration       = \Academy\Helper::get_course_duration( $course_id );
		$total_lessons  = \Academy\Helper::get_total_number_of_course_lesson( $course_id );
		$total_enroll_count_status = \Academy\Helper::get_settings( 'is_enabled_course_single_enroll_count', true );
		$total_enrolled = \Academy\Helper::count_course_enrolled( $course_id );
		$skill          = \Academy\Helper::get_course_difficulty_level( $course_id );
		$language       = get_post_meta( $course_id, 'academy_course_language', true );
		$max_students   = (int) get_post_meta( $course_id, 'academy_course_max_students', true );
		$last_update    = get_the_modified_time( get_option( 'date_format' ), $course_id );

		ob_start();

		\Academy\Helper::get_template(
			'single-course/enroll/content.php',
			apply_filters(
				'academy/single/enroll_content_args',
				array(
					'enrolled'       => $enrolled,
					'completed'      => $completed,
					'is_paid'        => $is_paid,
					'is_public'      => $is_public,
					'price'          => $price,
					'duration'       => $duration,
					'total_lessons'  => $total_lessons,
					'total_enroll_count_status' => $total_enroll_count_status,
					'total_enrolled' => $total_enrolled,
					'skill'          => $skill,
					'language'       => $language,
					'max_students'   => $max_students,
					'last_update'    => $last_update,
				)
			)
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'academy/templates/single_course/enroll_content', ob_get_clean(), $course_id );
	}
}//end if

if ( ! function_exists( 'academy_course_enroll_form' ) ) {
	function academy_course_enroll_form( $course_id = null ) {
		global $post;
		$original_post = $post;
		if ( $course_id ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$GLOBALS['post'] = get_post( $course_id );
		}
		$user_ID   = get_current_user_id();
		$enrolled  = \Academy\Helper::is_enrolled( get_the_ID(), get_current_user_id(), 'any' );
		// Course Materials Access
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_ID, get_the_ID() );
		$is_public_course = \Academy\Helper::is_public_course( get_the_ID() );

		ob_start();

		if ( ( $enrolled && 'completed' === $enrolled->enrolled_status ) || $is_administrator || $is_instructor || $is_public_course ) {
			\Academy\Helper::get_template( 'single-course/enroll/continue.php' );
		}

		// is public course
		if ( $is_public_course ) {
			return;
		}
		// Enrollment Functionality
		if ( $enrolled && 'completed' === $enrolled->enrolled_status ) {
			$is_completed_course = \Academy\Helper::is_completed_course( get_the_ID(), $user_ID );
			$is_show_complete_form = apply_filters( 'academy/single/is_show_complete_form', true, $is_completed_course, get_the_ID() );
			if ( $is_show_complete_form ) {
				\Academy\Helper::get_template( 'single-course/enroll/complete-form.php', array( 'is_completed_course' => $is_completed_course ) );
			}
		} elseif ( $enrolled && ( 'on-hold' === $enrolled->enrolled_status || 'processing' === $enrolled->enrolled_status ) ) {
			\Academy\Helper::get_template( 'single-course/enroll/notice.php', array(
				'status' => $enrolled->enrolled_status
			) );
		} elseif ( \Academy\Helper::is_course_fully_booked( get_the_ID() ) ) {
			\Academy\Helper::get_template( 'single-course/enroll/closed-enrollment.php' );
		} elseif ( \Academy\Helper::is_active_woocommerce() && \Academy\Helper::is_course_purchasable( get_the_ID() ) ) {
			$product_id = Academy\Helper::get_course_product_id( get_the_ID() );
			$is_enabled_academy_login = \Academy\Helper::get_settings( 'is_enabled_academy_login', true );
			$force_login_before_enroll = $is_enabled_academy_login && \Academy\Helper::get_settings( 'woo_force_login_before_enroll', true );
			\Academy\Helper::get_template( 'single-course/enroll/add-to-cart-form.php', array(
				'product_id'                => $product_id,
				'force_login_before_enroll' => $force_login_before_enroll
			) );
		} else {
			$is_enabled_academy_login = \Academy\Helper::get_settings( 'is_enabled_academy_login', true );
			\Academy\Helper::get_template( 'single-course/enroll/enroll-form.php', array(
				'is_enabled_academy_login' => $is_enabled_academy_login
			) );
		}//end if

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'academy/templates/single_course/enroll_form', ob_get_clean(), get_the_ID() );
		if ( $course_id ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$GLOBALS['post'] = $original_post;
		}
	}
}//end if

if ( ! function_exists( 'academy_course_enroll_wishlist_and_share' ) ) {
	function academy_course_enroll_wishlist_and_share() {
		global $wpdb;
		$course_id              = get_the_ID();
		$user_id                = get_current_user_id();
		$is_already_in_wishlist = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->usermeta} WHERE user_id = %d AND meta_key = 'academy_course_wishlist' AND meta_value = %d;", $user_id, $course_id ) );
		$is_show_wishlist = (bool) \Academy\Helper::get_settings( 'is_enabled_course_wishlist', true );
		$is_show_course_share = (bool) \Academy\Helper::get_settings( 'is_enabled_course_share', true );
		if ( $is_show_wishlist || $is_show_course_share ) {
			\Academy\Helper::get_template(
				'single-course/enroll/wishlist-and-share.php',
				apply_filters(
					'academy/single/course_enroll_wishlist_and_share_args',
					[
						'is_already_in_wishlist'    => $is_already_in_wishlist,
						'is_show_wishlist'          => $is_show_wishlist,
						'is_show_course_share'      => $is_show_course_share,
					]
				)
			);
		}
	}
}//end if


if ( ! function_exists( 'academy_archive_course_filter_widget' ) ) {
	function academy_archive_course_filter_widget() {
		$filters = \Academy\Helper::get_settings(
			'course_archive_filters',
			[
				[
					'search'   => true,
				],
				[
					'category'   => true,
				],
				[
					'tags'   => true,
				],
				[
					'levels'   => true,
				],
				[
					'type'   => true,
				],
			]
		);
		// make it single array
		$filters = array_reduce($filters, function( $carry, $item ) {
			return array_merge( $carry, (array) $item );
		}, []);

		$filters = apply_filters( 'academy/archive/course_filter_widget_args', $filters );

		foreach ( $filters as $key => $value ) {
			$filter_function = 'academy_archive_course_filter_by_' . $key;
			if ( $value && function_exists( $filter_function ) ) {
				$filter_function();
			}
		}
	}
}//end if



if ( ! function_exists( 'academy_archive_course_filter_by_search' ) ) {
	function academy_archive_course_filter_by_search() {
		\Academy\Helper::get_template( 'archive/widgets/search.php', apply_filters( 'academy/archive/course_filter_by_search_args', [] ) );
	}
}

if ( ! function_exists( 'academy_archive_course_filter_by_category' ) ) {
	function academy_archive_course_filter_by_category() {
		$categories = Academy\Helper::get_all_courses_category_lists();
		\Academy\Helper::get_template(
			'archive/widgets/category.php',
			apply_filters(
				'academy/archive/course_filter_by_category_args',
				[
					'categories' => $categories,
				]
			)
		);
	}
}

if ( ! function_exists( 'academy_archive_course_filter_by_tags' ) ) {
	function academy_archive_course_filter_by_tags() {
		$tags = get_terms(
			array(
				'taxonomy'   => 'academy_courses_tag',
				'hide_empty' => true,
			)
		);

		\Academy\Helper::get_template(
			'archive/widgets/tags.php',
			apply_filters(
				'academy/archive/course_filter_by_tags_args',
				[
					'tags' => $tags,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_archive_course_filter_by_levels' ) ) {
	function academy_archive_course_filter_by_levels() {
		$levels = array(
			'beginner'     => __( 'Beginner', 'academy' ),
			'intermediate' => __( 'Intermediate', 'academy' ),
			'experts'      => __( 'Expert', 'academy' ),
		);

		\Academy\Helper::get_template(
			'archive/widgets/levels.php',
			apply_filters(
				'academy/archive/course_filter_by_levels_args',
				[
					'levels' => $levels,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_archive_course_filter_by_type' ) ) {
	function academy_archive_course_filter_by_type() {
		$type = apply_filters('academy/get_course_filter_types', array(
			'free' => __( 'Free', 'academy' ),
			'paid' => __( 'Paid', 'academy' ),
		));
		\Academy\Helper::get_template(
			'archive/widgets/type.php',
			apply_filters(
				'academy/archive/course_filter_by_type_args',
				[
					'type' => $type,
				]
			)
		);
	}
}


if ( ! function_exists( 'academy_archive_course_sidebar' ) ) {
	function academy_archive_course_sidebar() {
		\Academy\Helper::get_template( 'archive/sidebar.php' );
	}
}


if ( ! function_exists( 'academy_instructor_public_profile_sidebar' ) ) {
	function academy_instructor_public_profile_sidebar() {
		$author_ID    = Academy\Helper::get_the_author_id();
		$reviews      = \Academy\Helper::get_instructor_ratings( $author_ID );
		$website_url  = get_user_meta( $author_ID, 'academy_website_url', true );
		$facebook_url = get_user_meta( $author_ID, 'academy_facebook_url', true );
		$github_url   = get_user_meta( $author_ID, 'academy_github_url', true );
		$twitter_url  = get_user_meta( $author_ID, 'academy_twitter_url', true );
		$linkdin_url  = get_user_meta( $author_ID, 'academy_linkedin_url', true );
		$is_enabled_instructor_review = \Academy\Helper::get_settings( 'is_enabled_instructor_review', true );
		\Academy\Helper::get_template(
			'instructor/sidebar.php',
			apply_filters(
				'academy/instructor/instructor_public_profile_sidebar_args',
				[
					'author_ID'    => $author_ID,
					'reviews'      => $reviews,
					'website_url'  => $website_url,
					'facebook_url' => $facebook_url,
					'github_url'   => $github_url,
					'twitter_url'  => $twitter_url,
					'linkdin_url'  => $linkdin_url,
					'is_enabled_instructor_review'  => $is_enabled_instructor_review,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_global_courses' ) ) {
	function academy_global_courses() {
		\Academy\Helper::get_template( 'global/courses.php' );
	}
}

if ( ! function_exists( 'academy_instructor_public_profile_header' ) ) {
	function academy_instructor_public_profile_header() {
		$author_ID       = Academy\Helper::get_the_author_id();
		$cover_photo_url = get_the_author_meta( 'academy_cover_photo', $author_ID );
		if ( empty( $cover_photo_url ) ) {
			$cover_photo_url = apply_filters( 'academy/instructor/public_profile_placeholder_cover_photo_url', ACADEMY_ASSETS_URI . 'images/banner.jpg' );
		}
		$share_config    = array(
			'title' => Academy\Helper::get_the_author_name( $author_ID ),
			'text'  => get_the_author_meta( 'academy_profile_designation', $author_ID ),
			'image' => esc_url( Academy\Helper::get_the_author_thumbnail_url( $author_ID ) ),
		);
		\Academy\Helper::get_template(
			'instructor/header.php',
			apply_filters(
				'academy/instructor/public_profile_header_args',
				[
					'cover_photo_url' => $cover_photo_url,
					'share_config'    => $share_config,
				]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_instructor_public_profile_tabs_nav' ) ) {
	function academy_instructor_public_profile_tabs_nav() {
		$is_enabled_instructor_review = (bool) \Academy\Helper::get_settings( 'is_enabled_instructor_review', true );

		\Academy\Helper::get_template(
			'instructor/tab-navbar.php',
			apply_filters(
				'academy/instructor/public_profile_tabs_nav_args',
				[
					'is_enabled_instructor_review' => $is_enabled_instructor_review
				]
			)
		);
	}
}
if ( ! function_exists( 'academy_instructor_public_profile_tabs_content' ) ) {
	function academy_instructor_public_profile_tabs_content() {
		$is_enabled_instructor_review = (bool) \Academy\Helper::get_settings( 'is_enabled_instructor_review', true );
		\Academy\Helper::get_template(
			'instructor/tab-content.php',
			apply_filters(
				'academy/instructor/public_profile_tabs_content_args',
				[
					'is_enabled_instructor_review' => $is_enabled_instructor_review
				]
			)
		);
	}
}


if ( ! function_exists( 'academy_instructor_public_profile_reviews' ) ) {
	function academy_instructor_public_profile_reviews() {
		$author_ID = get_query_var( 'author' );
		$reviews   = \Academy\Helper::get_reviews_by_instructor( $author_ID );
		$results   = [];
		if ( is_array( $reviews ) ) {
			foreach ( $reviews as $review ) {
				$review->post_title     = get_the_title( $review->comment_post_ID );
				$review->post_permalink = esc_url( get_the_permalink( $review->comment_post_ID ) );
				$results[]              = $review;
			}
		}
		\Academy\Helper::get_template(
			'instructor/reviews.php',
			apply_filters(
				'academy/instructor/public_profile_reviews_args',
				[ 'reviews' => $results ]
			)
		);
	}
}//end if

if ( ! function_exists( 'academy_update_avatar_url' ) ) {
	function academy_update_avatar_url( $url, $user ) {
		if ( is_object( $user ) ) {
			$profile_photo = get_user_meta( $user->ID, 'academy_profile_photo', true );
			if ( ! empty( $profile_photo ) ) {
				return esc_url( $profile_photo );
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'academy_update_avatar_data' ) ) {
	function academy_update_avatar_data( $args, $user_id ) {
		if ( $user_id ) {
			$profile_photo = get_user_meta( $user_id, 'academy_profile_photo', true );
			if ( ! empty( $profile_photo ) ) {
				$args['url'] = esc_url( $profile_photo );
				return $args;
			}
		}
		return $args;
	}
}

if ( ! function_exists( 'academy_get_the_canvas_container_class' ) ) {
	function academy_get_the_canvas_container_class() {
		global $post;
		$class_name = apply_filters( 'academy/templates/canvas_container_class', 'academy-container', $post->ID );
		echo esc_attr( $class_name );
	}
}

if ( ! function_exists( 'academy_frontend_dashbaord_container_class' ) ) {
	function academy_frontend_dashbaord_container_class( $class_name, $page_id ) {
		if ( current_user_can( 'manage_academy_instructor' ) && (int) \Academy\Helper::get_settings( 'frontend_dashboard_page' ) === $page_id ) {
			return $class_name . '-fluid';
		}
		return $class_name;
	}
}

if ( ! function_exists( 'academy_get_preloader_html' ) ) {
	function academy_get_preloader_html() {
		ob_start();
		?>
			<div class="academy-initial-preloader"><?php esc_html_e( 'Loading...', 'academy' ); ?></div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'academy_get_header' ) ) {
	function academy_get_header( $header_name = 'course' ) {
		global $wp_version;
		if ( version_compare( $wp_version, '5.9', '>=' ) && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			?>
			<!doctype html>
				<html <?php language_attributes(); ?>>
				<head>
					<meta charset="<?php bloginfo( 'charset' ); ?>">
					<?php wp_head(); ?>
				</head>

				<body <?php body_class(); ?>>
				<?php wp_body_open(); ?>
					<div class="wp-site-blocks">
						<?php
						if ( apply_filters( 'academy/templates/is_allow_block_theme_header', true ) ) :
							?>
						<header class="wp-block-template-part site-header">
							<?php block_header_area(); ?>
						</header>
							<?php
							endif;
						?>
			<?php
		} else {
			get_header( $header_name );
		}//end if
	}
}//end if

if ( ! function_exists( 'academy_get_footer' ) ) {
	function academy_get_footer( $footer_name = 'course' ) {
		global $wp_version;
		if ( version_compare( $wp_version, '5.9', '>=' ) && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			if ( apply_filters( 'academy/templates/is_allow_block_theme_footer', true ) ) :
				?>
				<footer class="wp-block-template-part site-footer">
					<?php block_footer_area(); ?>
				</footer>
				<?php
				endif;
			?>
			</div>
			<?php wp_footer(); ?>
			</body>
			</html>
			<?php
		} else {
			get_footer( $footer_name );
		}
	}
}//end if
