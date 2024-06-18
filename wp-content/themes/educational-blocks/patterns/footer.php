<?php
/**
 * Title: Footer
 * Slug: educational-blocks/footer
 * Categories: educational-blocks, footer
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"60px","right":"20px","bottom":"60px","left":"20px"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"},"color":{"background":"#271706"},"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","className":"footer-widgets","layout":{"type":"constrained","contentSize":"80%"},"fontSize":"small"} -->
<div class="wp-block-group footer-widgets has-white-color has-text-color has-background has-link-color has-small-font-size" style="background-color:#271706;margin-top:0;margin-bottom:0;padding-top:60px;padding-right:20px;padding-bottom:60px;padding-left:20px"><!-- wp:group {"align":"wide","style":{"elements":{"link":{"color":{"text":"var:preset|color|Background"}}}},"textColor":"Background","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide has-background-color has-text-color has-link-color"><!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"className":"wow fadeInLeft"} -->
<div class="wp-block-column wow fadeInLeft"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:site-logo /-->

<!-- wp:site-title {"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary","className":"footer-site-title","fontSize":"large"} /--></div>
<!-- /wp:group -->

<!-- wp:paragraph {"fontSize":"extra-small"} -->
<p class="has-extra-small-font-size"><?php esc_html_e('Lorem Ipsum is simply dummy text of the printing and typesetting industry.','educational-blocks'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"heading","iconColorValue":"#2c2c2c","iconBackgroundColor":"white","iconBackgroundColorValue":"#ffffff","size":"has-normal-icon-size","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"className":"is-style-default footer-social"} -->
<ul class="wp-block-social-links has-normal-icon-size has-icon-color has-icon-background-color is-style-default footer-social"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"wow fadeInLeft"} -->
<div class="wp-block-column wow fadeInLeft"><!-- wp:heading {"level":5,"textColor":"white","fontFamily":"roboto-slab"} -->
<h5 class="wp-block-heading has-white-color has-text-color has-roboto-slab-font-family"><?php esc_html_e('Contact Us','educational-blocks'); ?></h5>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"id":479,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/location.png'); ?>" alt="" class="wp-image-479"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"extra-small"} -->
<p class="has-extra-small-font-size"><?php esc_html_e('9870 St Vincent Place, Glasgow,DC 45 Fr 45','educational-blocks'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"id":480,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/call.png'); ?>" alt="" class="wp-image-480"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"extra-small"} -->
<p class="has-extra-small-font-size"><?php esc_html_e('+44 785 462 358','educational-blocks'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"id":482,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/mail.png'); ?>" alt="" class="wp-image-482"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"extra-small"} -->
<p class="has-extra-small-font-size"><a href="mailto:support@example.com"><?php esc_html_e('support@example.com','educational-blocks'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"top","className":"wow fadeInRight"} -->
<div class="wp-block-column is-vertically-aligned-top wow fadeInRight"><!-- wp:heading {"level":5,"textColor":"white","fontFamily":"roboto-slab"} -->
<h5 class="wp-block-heading has-white-color has-text-color has-roboto-slab-font-family"><?php esc_html_e('Quick Links','educational-blocks'); ?></h5>
<!-- /wp:heading -->

<!-- wp:navigation-link {"label":"Home","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Courses","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Features","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Events","type":"","url":"#","kind":"custom","isTopLevelLink":true} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"top","className":"wow fadeInRight"} -->
<div class="wp-block-column is-vertically-aligned-top wow fadeInRight"><!-- wp:heading {"level":5,"textColor":"white","fontFamily":"roboto-slab"} -->
<h5 class="wp-block-heading has-white-color has-text-color has-roboto-slab-font-family"><?php esc_html_e('Search','educational-blocks'); ?></h5>
<!-- /wp:heading -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"search","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"bottom":"20px","left":"20px","right":"20px"}},"color":{"background":"#271706"}},"textColor":"white","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group has-white-color has-text-color has-background" style="background-color:#271706;padding-right:20px;padding-bottom:20px;padding-left:20px"><!-- wp:group {"align":"wide","className":"footer-rights","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide footer-rights"><!-- wp:group {"style":{"spacing":{"padding":{"top":"20px"}},"border":{"top":{"color":"#757070","width":"1px"},"right":{},"bottom":{},"left":{}}},"className":"copyright-text","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group copyright-text" style="border-top-color:#757070;border-top-width:1px;padding-top:20px"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"small"} -->
<p class="has-white-color has-text-color has-link-color has-small-font-size"><a rel="noreferrer noopener" href="https://www.ovationthemes.com/wordpress/free-education-wordpress-theme/" target="_blank"><?php esc_html_e('Educational Blocks WordPress Theme','educational-blocks'); ?></a>.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|Background"}}},"layout":{"selfStretch":"fit","flexSize":null}},"textColor":"Background","fontFamily":"roboto"} -->
<p class="has-background-color has-text-color has-link-color has-roboto-font-family"><?php esc_html_e('Proudly powered by ','educational-blocks'); ?><a rel="noreferrer noopener" href="https://www.ovationthemes.com/" target="_blank"><?php esc_html_e('Ovation Themes','educational-blocks'); ?></a> <?php esc_html_e('and','educational-blocks'); ?> <a href="https://wordpress.org"><?php esc_html_e('WordPress','educational-blocks'); ?></a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"scroll-top"} -->
<p class="scroll-top"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->