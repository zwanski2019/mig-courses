<?php

add_action( 'admin_menu', 'educational_blocks_gettingstarted' );
function educational_blocks_gettingstarted() {
	add_theme_page( esc_html__('Theme Documentation', 'educational-blocks'), esc_html__('Theme Documentation', 'educational-blocks'), 'edit_theme_options', 'educational-blocks-guide-page', 'educational_blocks_guide');
}

function educational_blocks_admin_theme_style() {
   wp_enqueue_style('educational-blocks-custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/dashboard/dashboard.css');
}
add_action('admin_enqueue_scripts', 'educational_blocks_admin_theme_style');

if ( ! defined( 'EDUCATIONAL_BLOCKS_SUPPORT' ) ) {
define('EDUCATIONAL_BLOCKS_SUPPORT',__('https://wordpress.org/support/theme/educational-blocks/','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_REVIEW' ) ) {
define('EDUCATIONAL_BLOCKS_REVIEW',__('https://wordpress.org/support/theme/educational-blocks/reviews/','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_LIVE_DEMO' ) ) {
define('EDUCATIONAL_BLOCKS_LIVE_DEMO',__('https://www.ovationthemes.com/demos/education-insight/','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_BUY_PRO' ) ) {
define('EDUCATIONAL_BLOCKS_BUY_PRO',__('https://www.ovationthemes.com/wordpress/education-wordpress-theme/','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_PRO_DOC' ) ) {
define('EDUCATIONAL_BLOCKS_PRO_DOC',__('https://ovationthemes.com/docs/ot-education-insight-pro/','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_FREE_DOC' ) ) {
define('EDUCATIONAL_BLOCKS_FREE_DOC',__('https://ovationthemes.com/docs/ot-education-insight-free-doc','educational-blocks'));
}
if ( ! defined( 'EDUCATIONAL_BLOCKS_THEME_NAME' ) ) {
define('EDUCATIONAL_BLOCKS_THEME_NAME',__('Premium Educational Blocks Theme','educational-blocks'));
}

/**
 * Theme Info Page
 */
function educational_blocks_guide() {

	// Theme info
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( '' ); ?>

	<div class="getting-started__header">
		<div class="col-md-10">
			<h2><?php echo esc_html( $theme ); ?></h2>
			<p><?php esc_html_e('Version: ', 'educational-blocks'); ?><?php echo esc_html($theme['Version']);?></p>
		</div>
		<div class="col-md-2">
			<div class="btn_box">
				<a class="button-primary" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support', 'educational-blocks'); ?></a>
				<a class="button-primary" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_REVIEW ); ?>" target="_blank"><?php esc_html_e('Review', 'educational-blocks'); ?></a>
			</div>
		</div>
	</div>

	<div class="wrap getting-started">
		<div class="container">
			<div class="col-md-9">
				<div class="leftbox">
					<h3><?php esc_html_e('Documentation','educational-blocks'); ?></h3>
					<p><?php $theme = wp_get_theme(); 
						echo wp_kses_post( apply_filters( 'description', esc_html( $theme->get( 'Description' ) ) ) );
					?></p>

					<h4><?php esc_html_e('Edit Your Site','educational-blocks'); ?></h4>
					<p><?php esc_html_e('Now create your website with easy drag and drop powered by gutenburg.','educational-blocks'); ?></p>
					<a class="button-primary" href="<?php echo esc_url( admin_url() . 'site-editor.php' ); ?>" target="_blank"><?php esc_html_e('Edit Your Site','educational-blocks'); ?></a>

					<h4><?php esc_html_e('Visit Your Site','educational-blocks'); ?></h4>
					<p><?php esc_html_e('To check your website click here','educational-blocks'); ?></p>
					<a class="button-primary" href="<?php echo esc_url( home_url() ); ?>" target="_blank"><?php esc_html_e('Visit Your Site','educational-blocks'); ?></a>

					<h4><?php esc_html_e('Theme Documentation','educational-blocks'); ?></h4>
					<p><?php esc_html_e('Check the theme documentation to easily set up your website.','educational-blocks'); ?></p>
					<a class="button-primary" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_FREE_DOC ); ?>" target="_blank"><?php esc_html_e('Documentation','educational-blocks'); ?></a>
				</div>
       	</div>
			<div class="col-md-3">
				<h3><?php echo esc_html(EDUCATIONAL_BLOCKS_THEME_NAME); ?></h3>
				<img class="educational_blocks_img_responsive" style="width: 100%;" src="<?php echo esc_url( $theme->get_screenshot() ); ?>" />
				<div class="pro-links">
					<hr>
			    	<a class="button-primary livedemo" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'educational-blocks'); ?></a>
					<a class="button-primary buynow" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_BUY_PRO ); ?>" target="_blank"><?php esc_html_e('Buy Now', 'educational-blocks'); ?></a>
					<a class="button-primary docs" href="<?php echo esc_url( EDUCATIONAL_BLOCKS_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Documentation', 'educational-blocks'); ?></a>
					<hr>
				</div>
				<ul style="padding-top:10px">
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Responsive Design', 'educational-blocks');?> </li>                 
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Demo Importer', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Section Reordering', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Contact Page Template', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Multiple Blog Layouts', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Unlimited Color Options', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Cross Browser Support', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Detailed Documentation Included', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('WPML Compatible (Translation Ready)', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Woo-commerce Compatible', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Full Support', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('10+ Sections', 'educational-blocks');?> </li>
					<li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('SEO Friendly', 'educational-blocks');?> </li>
               <li class="upsell-educational_blocks"> <div class="dashicons dashicons-yes"></div> <?php esc_html_e('Supper Fast', 'educational-blocks');?> </li>
            </ul>
        	</div>
		</div>
	</div>

<?php }?>