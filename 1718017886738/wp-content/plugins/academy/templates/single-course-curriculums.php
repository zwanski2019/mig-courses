<?php
/**
 * The Template for displaying all single courses
 *
 * This template can be overridden by copying it to yourtheme/academy/single-course.php.
 *
 * the readme will list any important changes.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$is_enabled_lessons_theme_header_footer = \Academy\Helper::get_settings( 'is_enabled_lessons_theme_header_footer', false );
if ( $is_enabled_lessons_theme_header_footer ) :
	academy_get_header( 'course' );
else :
	?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	endif;
?>

	<?php
		/**
		 * @hook - academy/templates/before_main_content
		 */
		do_action( 'academy/templates/before_main_content', 'single-course-curriculums.php' );

		$topbar_logo = wp_get_attachment_image_src( \Academy\Helper::get_settings( 'lessons_topbar_logo', '' ), array( '80', '80' ) );
		$user_id = get_current_user_id();
		$course_id = get_the_ID();
		$enrolled  = \Academy\Helper::is_enrolled( $course_id, $user_id );
		$is_administrator = current_user_can( 'administrator' );
		$is_instructor    = \Academy\Helper::is_instructor_of_this_course( $user_id, $course_id );
		$is_public_course = \Academy\Helper::is_public_course( $course_id );
		$is_topics_accessible = $is_administrator || $enrolled || $is_instructor || $is_public_course;
	?>
		<div 
			id="academyLessonsWrap" 
			class="academy-lessons" 
			data-course-title="<?php echo esc_attr( get_the_title() ); ?>" 
			data-course-id="<?php echo esc_attr( get_the_ID() ); ?>" 
			data-course-permalink="<?php echo esc_url( get_the_permalink() ); ?>"
			data-exit-permalink="<?php echo esc_url( apply_filters( 'academy/templates/learn_page_exit_permalink', get_the_permalink() ) ); ?>"
			data-enabled-course-qa="<?php echo esc_attr( get_post_meta( get_the_ID(), 'academy_is_enabled_course_qa', true ) ); ?>"
			data-enabled-course-announcements="<?php echo esc_attr( get_post_meta( get_the_ID(), 'academy_is_enabled_course_announcements', true ) ); ?>"
			data-course-type="<?php echo esc_attr( get_post_meta( get_the_ID(), 'academy_course_type', true ) ); ?>"
			data-is-completed-course="<?php echo esc_attr( \Academy\Helper::is_completed_course( get_the_ID(), get_current_user_id() ) ); ?>"
			data-auto-load-next-lesson="<?php echo esc_attr( \Academy\Helper::is_auto_load_next_lesson() ); ?>"
			data-auto-complete-topic="<?php echo esc_attr( \Academy\Helper::is_auto_complete_topic() ); ?>"
			data-is-favorite="<?php echo esc_attr( \Academy\Helper::is_favorite_course( get_the_ID() ) ); ?>"
			data-topbar-logo="<?php echo esc_attr( is_array( $topbar_logo ) ? $topbar_logo[0] : '' ); ?>"
			data-topics-accessible="<?php echo esc_attr( $is_topics_accessible ); ?>"
			data-enabled-academy-player="<?php echo esc_attr( \Academy\Helper::get_settings( 'is_enabled_academy_player', false ) ); ?>"
		>
			<?php
				$preloader = apply_filters( 'academy/preloader', academy_get_preloader_html() );
				echo wp_kses_post( $preloader );
			?>
		</div>
	<?php
		/**
		 * @hook - academy/templates/after_main_content
		 */
		do_action( 'academy/templates/after_main_content', 'single-course-curriculums.php' );


	if ( $is_enabled_lessons_theme_header_footer ) :
		academy_get_footer( 'course' );
else :
	?>

	<?php wp_footer(); ?>

</body>
</html>
	<?php
endif;
