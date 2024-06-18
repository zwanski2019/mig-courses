<?php
/**
 * Educational Blocks: Block Patterns
 *
 * @since Educational Blocks 1.0
 */

/**
 * Registers block patterns and categories.
 *
 * @since Educational Blocks 1.0
 *
 * @return void
 */
function educational_blocks_register_block_patterns() {
	$block_pattern_categories = array(
		'educational-blocks'    => array( 'label' => __( 'Educational Blocks', 'educational-blocks' ) ),
	);

	$block_pattern_categories = apply_filters( 'educational_blocks_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}
}
add_action( 'init', 'educational_blocks_register_block_patterns', 9 );
