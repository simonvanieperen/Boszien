<?php
/**
 * Block style registrations for Boszien Base.
 *
 * @package BoszienBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register visible editor and frontend block styles.
 */
function boszien_base_register_block_styles(): void {
	register_block_style(
		'core/group',
		array(
			'name'  => 'bz-card',
			'label' => __( 'Sobere Boszien-kaart', 'boszien-base' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'bz-secondary',
			'label' => __( 'Boszien secundair', 'boszien-base' ),
		)
	);
}
add_action( 'init', 'boszien_base_register_block_styles' );
