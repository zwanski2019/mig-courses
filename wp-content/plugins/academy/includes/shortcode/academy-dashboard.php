<?php
namespace  Academy\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcademyDashboard {

	public function __construct() {
		add_shortcode( 'academy_dashboard', array( $this, 'frontend_dashboard' ) );
	}
	public function frontend_dashboard() {
		ob_start();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_action( 'academy/shortcode/before_academy_dashboard' );
		if ( ! is_user_logged_in() ) {
			echo do_shortcode( '[academy_login_form form_title="' . esc_html__( 'Please Sign-In to View Dashboard', 'academy' ) . '" show_logged_in_message="false"]' );
		} else {
			$instructor_status = get_user_meta( get_current_user_id(), 'academy_instructor_status', true );
			if ( 'pending' === $instructor_status ) {
				echo '<p class="academy-instructor-pending-status-message">' . esc_html__( 'Please wait for admin\'s to approve you as an instructor.', 'academy' ) . '</p>';
			}
			$preloader_html = apply_filters( 'academy/preloader', academy_get_preloader_html() );
			echo '<div id="academyFrontendDashWrap" class="academyFrontendDashWrap">' . wp_kses_post( $preloader_html ) . '</div>';
		}
		return apply_filters( 'academy/templates/shortcode/dashboard', ob_get_clean() );
	}
}
