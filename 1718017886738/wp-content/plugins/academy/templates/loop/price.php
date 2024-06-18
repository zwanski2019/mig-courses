<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="academy-course__price">
	<?php
	if ( ! empty( $price ) ) {
		echo wp_kses_post( $price );
	} elseif ( empty( $price ) && $is_paid ) {
		esc_html_e( 'Paid', 'academy' );
	} elseif ( 'public' === $course_type ) {
		esc_html_e( 'Public', 'academy' );
	} else {
		esc_html_e( 'Free', 'academy' );
	}
	?>
</div>
