<?php
/**
 * Custom interactive block registration for Boszien Base.
 *
 * @package BoszienBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register native Interactivity API blocks.
 */
function boszien_base_register_interactive_blocks(): void {
	register_block_type( get_template_directory() . '/blocks/evidence-meter' );
}
add_action( 'init', 'boszien_base_register_interactive_blocks' );
