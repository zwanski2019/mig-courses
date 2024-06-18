<?php
/**
 * Title: Header
 * Slug: educational-blocks/header
 * Categories: educational-blocks, header
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"primary","className":"upper-header ","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-group upper-header has-primary-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:columns {"verticalAlignment":"center","className":"inner-upper-header"} -->
<div class="wp-block-columns are-vertically-aligned-center inner-upper-header"><!-- wp:column {"width":""} -->
<div class="wp-block-column"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"45%","className":"top-info"} -->
<div class="wp-block-column is-vertically-aligned-center top-info" style="flex-basis:45%"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|20"}}},"className":"header-details"} -->
<div class="wp-block-columns are-vertically-aligned-center header-details"><!-- wp:column {"verticalAlignment":"center","width":"35%","className":"info-call wow slideInLeft"} -->
<div class="wp-block-column is-vertically-aligned-center info-call wow slideInLeft" style="flex-basis:35%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"className":"contact-info","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group contact-info"><!-- wp:image {"id":27,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/telephone.png'); ?>" alt="" class="wp-image-27"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"extra-small"} -->
<p class="has-white-color has-text-color has-link-color has-extra-small-font-size"><?php esc_html_e('+44 785 462 358','educational-blocks'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"35%","className":"info-mail wow slideInDown"} -->
<div class="wp-block-column is-vertically-aligned-center info-mail wow slideInDown" style="flex-basis:35%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"className":"contact-info","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group contact-info"><!-- wp:image {"id":29,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/envelope.png'); ?>" alt="" class="wp-image-29"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"extra-small"} -->
<p class="has-white-color has-text-color has-link-color has-extra-small-font-size"><?php esc_html_e('support@example.com','educational-blocks'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"30%","className":"top-links wow slideInRight"} -->
<div class="wp-block-column is-vertically-aligned-center top-links wow slideInRight" style="flex-basis:30%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"className":"contact-info","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group contact-info"><!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"extra-small"} -->
<p class="has-white-color has-text-color has-link-color has-extra-small-font-size"><a href="#"><?php esc_html_e('LOGIN','educational-blocks'); ?></a><?php esc_html_e(' /','educational-blocks'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"textColor":"white","fontSize":"extra-small"} -->
<p class="has-white-color has-text-color has-link-color has-extra-small-font-size"><a href="#"><?php esc_html_e('REGISTER','educational-blocks'); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"backgroundColor":"white","className":"menu-header","layout":{"type":"constrained","contentSize":"80%","wideSize":"85%"}} -->
<div class="wp-block-group menu-header has-white-background-color has-background"><!-- wp:columns {"className":"inner-menu-header"} -->
<div class="wp-block-columns inner-menu-header"><!-- wp:column {"verticalAlignment":"center","width":"30%","className":"nav-block-left"} -->
<div class="wp-block-column is-vertically-aligned-center nav-block-left" style="flex-basis:30%"><!-- wp:navigation {"textColor":"heading","overlayBackgroundColor":"secondary","overlayTextColor":"white","className":"wow rubberBand","layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"textTransform":"uppercase"}},"fontSize":"small"} -->
<!-- wp:navigation-link {"label":"HOME","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"COURSES","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"FEATURES","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"GALLERY","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- /wp:navigation --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"30%","className":"logo-block"} -->
<div class="wp-block-column is-vertically-aligned-center logo-block" style="flex-basis:30%"><!-- wp:group {"style":{"border":{"radius":{"bottomLeft":"50%","bottomRight":"50%","topLeft":"50%","topRight":"50%"}},"spacing":{"padding":{"top":"30px","bottom":"30px","left":"31px","right":"31px"}}},"backgroundColor":"white","className":"logo-box wow rotateIn","layout":{"type":"constrained"}} -->
<div class="wp-block-group logo-box wow rotateIn has-white-background-color has-background" style="border-top-left-radius:50%;border-top-right-radius:50%;border-bottom-left-radius:50%;border-bottom-right-radius:50%;padding-top:30px;padding-right:31px;padding-bottom:30px;padding-left:31px"><!-- wp:site-logo {"shouldSyncIcon":true,"align":"center"} /-->

<!-- wp:site-title {"textAlign":"center","style":{"typography":{"textTransform":"uppercase","fontSize":"20px"},"elements":{"link":{"color":{"text":"#271706"}}},"color":{"text":"#271706"}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"40%","className":"menu-right-box"} -->
<div class="wp-block-column is-vertically-aligned-center menu-right-box" style="flex-basis:40%"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"70%","className":"nav-block-right"} -->
<div class="wp-block-column is-vertically-aligned-center nav-block-right" style="flex-basis:70%"><!-- wp:navigation {"textColor":"heading","overlayBackgroundColor":"secondary","overlayTextColor":"white","className":"wow rubberBand","layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"textTransform":"uppercase"}},"fontSize":"small"} -->
<!-- wp:navigation-link {"label":"EVENTS","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"BLOGS","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"CONTACT","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"SHOP","type":"","url":"#","kind":"custom","isTopLevelLink":true} /-->

<!-- /wp:navigation --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"30%","className":"social-block"} -->
<div class="wp-block-column is-vertically-aligned-center social-block" style="flex-basis:30%"><!-- wp:social-links {"size":"has-small-icon-size","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"className":"is-style-default","layout":{"type":"flex","justifyContent":"right"}} -->
<ul class="wp-block-social-links has-small-icon-size is-style-default"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"twitter"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"pinterest"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->