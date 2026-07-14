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
 * Load the canonical public stylesheet through WordPress.
 */
function boszien_base_enqueue_styles(): void {
	wp_enqueue_style(
		'boszien-base-public',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'boszien_base_enqueue_styles' );

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

/**
 * Keep the editor-only AI Type Ahead bundle off the public site.
 */
function boszien_base_dequeue_editor_only_ai_assets(): void {
	if ( is_admin() ) {
		return;
	}

	wp_dequeue_script( 'ai_type_ahead' );
	wp_dequeue_style( 'ai_type_ahead' );
}
add_action( 'wp_enqueue_scripts', 'boszien_base_dequeue_editor_only_ai_assets', PHP_INT_MAX );

/**
 * Load the small Jetpack form accessibility enhancement only where needed.
 */
function boszien_base_enqueue_form_accessibility(): void {
	if ( ! is_page( 'stuur-iets-in' ) ) {
		return;
	}

	$script_path = get_theme_file_path( 'assets/js/form-a11y.js' );
	wp_enqueue_script(
		'boszien-form-a11y',
		get_theme_file_uri( 'assets/js/form-a11y.js' ),
		array(),
		(string) filemtime( $script_path ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'boszien_base_enqueue_form_accessibility' );

/**
 * Identify internal classification tags that should not be visitor-facing.
 */
function boszien_base_is_technical_tag( WP_Term $term ): bool {
	return 'post_tag' === $term->taxonomy && 0 === strpos( $term->slug, 'type-' );
}

/**
 * Keep empty and technical taxonomy archives out of visitor journeys.
 */
function boszien_base_redirect_non_editorial_taxonomies(): void {
	if ( is_admin() || wp_doing_ajax() || ( ! is_category() && ! is_tag() ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	if ( boszien_base_is_technical_tag( $term ) ) {
		wp_safe_redirect( home_url( '/claimchecks/' ), 301, 'Boszien Base' );
		exit;
	}

	if ( 0 === (int) $term->count ) {
		wp_safe_redirect( home_url( '/onderwerpen/' ), 302, 'Boszien Base' );
		exit;
	}
}
add_action( 'template_redirect', 'boszien_base_redirect_non_editorial_taxonomies', 1 );

/**
 * Add a robots fallback in case a non-editorial archive is rendered upstream.
 *
 * @param array<string, bool> $robots Robots directives.
 * @return array<string, bool>
 */
function boszien_base_noindex_non_editorial_taxonomies( array $robots ): array {
	if ( ! is_category() && ! is_tag() ) {
		return $robots;
	}

	$term = get_queried_object();
	if ( $term instanceof WP_Term && ( 0 === (int) $term->count || boszien_base_is_technical_tag( $term ) ) ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'boszien_base_noindex_non_editorial_taxonomies' );

/**
 * Exclude empty terms and internal type tags from core XML sitemaps.
 *
 * @param array<string, mixed> $args     Taxonomy query arguments.
 * @param string               $taxonomy Taxonomy name.
 * @return array<string, mixed>
 */
function boszien_base_filter_taxonomy_sitemap_args( array $args, string $taxonomy ): array {
	$args['hide_empty'] = true;

	if ( 'post_tag' !== $taxonomy ) {
		return $args;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return $args;
	}

	$technical_ids = array();
	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term && boszien_base_is_technical_tag( $term ) ) {
			$technical_ids[] = $term->term_id;
		}
	}

	if ( $technical_ids ) {
		$args['exclude'] = array_values(
			array_unique(
				array_merge( (array) ( $args['exclude'] ?? array() ), $technical_ids )
			)
		);
	}

	return $args;
}
add_filter( 'wp_sitemaps_taxonomies_query_args', 'boszien_base_filter_taxonomy_sitemap_args', 10, 2 );

require_once get_template_directory() . '/inc/block-styles.php';
require_once get_template_directory() . '/inc/interactivity-api.php';
