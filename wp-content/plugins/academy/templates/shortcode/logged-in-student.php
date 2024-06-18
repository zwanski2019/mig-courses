<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="academy-reg-thankyou">
	<h2 class="academy-reg-thankyou__heading"><?php esc_html_e( 'Congratulations! You are now registered as a student.', 'academy' ); ?></h2>
	<p class="academy-reg-thankyou__description"><?php esc_html_e( 'Start learning from today', 'academy' ); ?></p>
	<a class="academy-btn academy-btn--inline-block academy-btn--bg-purple" href="<?php echo esc_url( $dashboard_url ); ?>"><?php esc_html_e( 'Go to Dashboard', 'academy' ); ?></a>
</div>
