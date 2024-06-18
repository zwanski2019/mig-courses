<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$total_completed_lessons = \Academy\Helper::get_total_number_of_completed_course_topics_by_course_and_student_id( get_the_ID() );

?>
<div class="academy-widget-enroll__continue">
	<a class="academy-btn academy-btn--bg-purple" href="<?php echo esc_url( add_query_arg( array( 'source' => 'curriculums' ), get_the_permalink() ) ); ?>">
		<?php
		if ( $total_completed_lessons ) {
			esc_html_e( 'Continue Learning', 'academy' );
		} else {
			esc_html_e( 'Start Course', 'academy' );
		}
		?>
	</a>
</div>
