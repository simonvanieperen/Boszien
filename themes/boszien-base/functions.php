<?php
/**
 * Boszien Base theme bootstrap.
 *
 * @package BoszienBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure theme supports.
 */
function boszien_base_setup(): void {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array(
			'caption',
			'gallery',
			'search-form',
			'style',
			'script',
		)
	);

	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'boszien_base_setup' );

/**
 * Register Boszien pattern category.
 */
function boszien_base_register_pattern_category(): void {
	register_block_pattern_category(
		'boszien-base',
		array( 'label' => __( 'Boszien Base', 'boszien-base' ) )
	);
}
add_action( 'init', 'boszien_base_register_pattern_category' );

require_once get_template_directory() . '/inc/block-styles.php';
require_once get_template_directory() . '/inc/interactivity-api.php';
