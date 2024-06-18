<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Academy Templates Related all Hooks write here
 */

/**
 * Page Template
 */
add_filter( 'theme_page_templates', 'academy_load_canvas_page_template' );
add_filter( 'page_template', 'academy_redirect_canvas_page_template' );

/**
 * Course Details Page
 */
add_action( 'academy/templates/single_course_sidebar', 'academy_single_course_sidebar', 10 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_header', 10 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_instructors', 15 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_description', 20 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_benefits', 25 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_additional_info', 25 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_curriculums', 30 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_feedback', 35 );
add_action( 'academy/templates/single_course_content', 'academy_single_course_reviews', 40 );

// sidebar
add_action( 'academy/templates/single_course_sidebar_widgets', 'academy_single_course_enroll', 10 );

// enroll widget
add_action( 'academy/templates/single_course_enroll_content', 'academy_single_course_enroll_content', 10 );
add_action( 'academy/templates/single_course_enroll_content', 'academy_course_enroll_form', 15 );
add_action( 'academy/templates/single_course_enroll_content', 'academy_course_enroll_wishlist_and_share', 20 );


/**
 * Archive Course Page
 */
add_action( 'academy/templates/archive_course_header', 'academy_archive_course_header', 10 );
add_action( 'academy/templates/archive_course_content', 'academy_global_courses', 10 );
add_action( 'academy/templates/archive_course_description', 'academy_archive_course_header_filter', 10 );
add_action( 'academy/templates/no_course_found', 'academy_no_course_found', 10 );
add_action( 'academy/templates/after_course_loop', 'academy_course_pagination', 10 );
add_action( 'academy/templates/archive_course_sidebar', 'academy_archive_course_sidebar', 10 );
// widgets
add_action( 'academy/templates/archive/course_sidebar_content', 'academy_archive_course_filter_widget', 10 );


/**
 * Course Loop
 */
add_action( 'academy/templates/course_loop_header', 'academy_course_loop_header', 10 );
add_action( 'academy/templates/course_loop_content', 'academy_course_loop_content', 11 );
add_action( 'academy/templates/course_loop_footer', 'academy_course_loop_footer', 12 );
add_action( 'academy/templates/course_loop_footer_inner', 'academy_course_loop_footer_inner_price', 11 );
add_action( 'academy/templates/course_loop_footer_inner', 'academy_course_loop_rating', 10 );

/**
 * Review
 */
add_action( 'academy/templates/review_thumbnail', 'academy_review_display_gravatar' );
add_action( 'academy/templates/review_thumbnail', 'academy_review_display_rating' );
add_action( 'academy/templates/review_meta', 'academy_review_display_meta' );
add_action( 'academy/templates/review_comment_text', 'academy_review_display_comment_text' );

/**
 * Instructor Public Profile
 */
add_action( 'academy/templates/instructor_public_profile_sidebar', 'academy_instructor_public_profile_sidebar' );
add_action( 'academy/templates/instructor_public_profile_content', 'academy_instructor_public_profile_tabs_nav', 10 );
add_action( 'academy/templates/instructor_public_profile_content', 'academy_instructor_public_profile_tabs_content', 10 );
add_action( 'academy/templates/instructor_public_profile_header', 'academy_instructor_public_profile_header' );
add_action( 'academy/templates/instructor/tabs_content_courses', 'academy_global_courses', 10 );
add_action( 'academy/templates/instructor/tabs_content_reviews', 'academy_instructor_public_profile_reviews', 10 );

/**
 * Avatar
 */
add_filter( 'get_avatar_url', 'academy_update_avatar_url', 10, 2 );
add_filter( 'get_avatar_data', 'academy_update_avatar_data', 10, 2 );

/**
 * Canvas Template
 */
add_filter( 'academy/templates/canvas_container_class', 'academy_frontend_dashbaord_container_class', 10, 2 );


/**
 * Enroll Form Shortcode
 */
add_action( 'academy/templates/shortcode/enroll_form_content', 'academy_course_enroll_form', 15 );
