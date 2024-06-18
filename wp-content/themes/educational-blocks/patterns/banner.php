<?php
/**
 * Title: Banner
 * Slug: educational-blocks/banner
 * Categories: educational-blocks, banner
 */
?>

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0px"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"bannerimage","layout":{"type":"constrained","contentSize":"100%"}} -->
<div class="wp-block-group bannerimage" style="margin-top:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() . '/images/banner.png'); ?>","id":53,"dimRatio":0,"focalPoint":{"x":0.5,"y":0.5},"minHeight":800,"minHeightUnit":"px","align":"wide","className":"banner-image-cover","layout":{"type":"constrained","contentSize":"80%"}} -->
<div class="wp-block-cover alignwide banner-image-cover" style="min-height:800px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-53" alt="" src="<?php echo esc_url( get_template_directory_uri() . '/images/banner.png'); ?>" style="object-position:50% 50%" data-object-fit="cover" data-object-position="50% 50%"/><div class="wp-block-cover__inner-container"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"className":"banner-content wow zoomIn"} -->
<div class="wp-block-column banner-content wow zoomIn"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"60px"}},"fontFamily":"roboto-slab"} -->
<h1 class="wp-block-heading has-text-align-center has-roboto-slab-font-family" style="font-size:60px"><?php esc_html_e('Lorem Ipsum','educational-blocks'); ?></h1>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"400","fontSize":"40px","lineHeight":"1.1"},"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
<h3 class="wp-block-heading has-text-align-center" style="margin-top:0;margin-bottom:0;font-size:40px;font-style:normal;font-weight:400;line-height:1.1"><?php esc_html_e('How to Create New Things','educational-blocks'); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"typography":{"fontStyle":"normal","fontWeight":"300"}}} -->
<p class="has-text-align-center" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);font-style:normal;font-weight:300"><?php esc_html_e("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's",'educational-blocks'); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->