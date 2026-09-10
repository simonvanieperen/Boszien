<?php
/**
 * Tests for the new procedural functions added to functions.php in this
 * release: native stylesheet enqueueing, editor-only asset dequeueing, the
 * conditional form-accessibility script, and the non-editorial taxonomy
 * hygiene helpers (redirects, robots and sitemap filtering).
 */

namespace Boszien\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	// -----------------------------------------------------------------
	// boszien_base_enqueue_styles()
	// -----------------------------------------------------------------

	public function test_enqueue_styles_registers_the_native_versioned_stylesheet(): void {
		$theme = new class() {
			public function get( $key ) {
				return 'Version' === $key ? '0.6.0' : null;
			}
		};

		Functions\expect( 'get_stylesheet_uri' )
			->once()
			->andReturn( 'https://boszien.org/wp-content/themes/boszien-base/style.css' );

		Functions\expect( 'wp_get_theme' )
			->once()
			->andReturn( $theme );

		Functions\expect( 'wp_enqueue_style' )
			->once()
			->with(
				'boszien-base-public',
				'https://boszien.org/wp-content/themes/boszien-base/style.css',
				array(),
				'0.6.0'
			);

		boszien_base_enqueue_styles();
	}

	// -----------------------------------------------------------------
	// boszien_base_dequeue_editor_only_ai_assets()
	// -----------------------------------------------------------------

	public function test_dequeue_editor_only_ai_assets_does_nothing_in_admin(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( true );
		Functions\expect( 'wp_dequeue_script' )->never();
		Functions\expect( 'wp_dequeue_style' )->never();

		boszien_base_dequeue_editor_only_ai_assets();
	}

	public function test_dequeue_editor_only_ai_assets_removes_bundle_on_the_public_site(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_dequeue_script' )->once()->with( 'ai_type_ahead' );
		Functions\expect( 'wp_dequeue_style' )->once()->with( 'ai_type_ahead' );

		boszien_base_dequeue_editor_only_ai_assets();
	}

	// -----------------------------------------------------------------
	// boszien_base_enqueue_form_accessibility()
	// -----------------------------------------------------------------

	public function test_enqueue_form_accessibility_skips_unrelated_pages(): void {
		Functions\expect( 'is_page' )->once()->with( 'stuur-iets-in' )->andReturn( false );
		Functions\expect( 'wp_enqueue_script' )->never();

		boszien_base_enqueue_form_accessibility();
	}

	public function test_enqueue_form_accessibility_loads_script_on_the_intake_page(): void {
		$script_path = dirname( __DIR__ ) . '/assets/js/form-a11y.js';

		Functions\expect( 'is_page' )->once()->with( 'stuur-iets-in' )->andReturn( true );
		Functions\expect( 'get_theme_file_path' )
			->once()
			->with( 'assets/js/form-a11y.js' )
			->andReturn( $script_path );
		Functions\expect( 'get_theme_file_uri' )
			->once()
			->with( 'assets/js/form-a11y.js' )
			->andReturn( 'https://boszien.org/wp-content/themes/boszien-base/assets/js/form-a11y.js' );

		Functions\expect( 'wp_enqueue_script' )
			->once()
			->withArgs(
				function ( $handle, $src, $deps, $ver, $in_footer ) {
					return 'boszien-form-a11y' === $handle
						&& 'https://boszien.org/wp-content/themes/boszien-base/assets/js/form-a11y.js' === $src
						&& array() === $deps
						&& is_string( $ver ) && ctype_digit( $ver )
						&& true === $in_footer;
				}
			);

		boszien_base_enqueue_form_accessibility();
	}

	// -----------------------------------------------------------------
	// boszien_base_is_technical_tag()
	// -----------------------------------------------------------------

	public function test_is_technical_tag_flags_type_prefixed_post_tags(): void {
		$term = new \WP_Term( 1, 'post_tag', 'type-external-source' );

		$this->assertTrue( boszien_base_is_technical_tag( $term ) );
	}

	public function test_is_technical_tag_ignores_non_post_tag_taxonomies(): void {
		$term = new \WP_Term( 2, 'category', 'type-external-source' );

		$this->assertFalse( boszien_base_is_technical_tag( $term ) );
	}

	public function test_is_technical_tag_ignores_regular_post_tags(): void {
		$term = new \WP_Term( 3, 'post_tag', 'gezondheid' );

		$this->assertFalse( boszien_base_is_technical_tag( $term ) );
	}

	public function test_is_technical_tag_requires_an_exact_prefix_match(): void {
		$term = new \WP_Term( 4, 'post_tag', 'atype-not-a-prefix-match' );

		$this->assertFalse( boszien_base_is_technical_tag( $term ) );
	}

	public function test_is_technical_tag_requires_the_trailing_hyphen(): void {
		$term = new \WP_Term( 5, 'post_tag', 'type' );

		$this->assertFalse( boszien_base_is_technical_tag( $term ) );
	}

	// -----------------------------------------------------------------
	// boszien_base_redirect_non_editorial_taxonomies()
	// -----------------------------------------------------------------

	public function test_redirect_skips_admin_requests(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( true );
		Functions\expect( 'wp_doing_ajax' )->never();
		Functions\expect( 'wp_safe_redirect' )->never();

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_skips_ajax_requests(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( true );
		Functions\expect( 'is_category' )->never();
		Functions\expect( 'wp_safe_redirect' )->never();

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_skips_non_taxonomy_requests(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object' )->never();
		Functions\expect( 'wp_safe_redirect' )->never();

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_skips_when_the_queried_object_is_not_a_term(): void {
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( null );
		Functions\expect( 'wp_safe_redirect' )->never();

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_sends_technical_tag_archives_to_claimchecks(): void {
		$term = new \WP_Term( 10, 'post_tag', 'type-source', 3 );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );
		Functions\expect( 'home_url' )
			->once()
			->with( '/claimchecks/' )
			->andReturn( 'https://boszien.org/claimchecks/' );
		Functions\expect( 'wp_safe_redirect' )
			->once()
			->with( 'https://boszien.org/claimchecks/', 301, 'Boszien Base' )
			->andThrow( new \Exception( 'boszien-base test: exit after redirect' ) );

		$this->expectException( \Exception::class );

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_sends_empty_taxonomy_archives_to_the_topic_overview(): void {
		$term = new \WP_Term( 11, 'category', 'zeldzame-claim', 0 );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );
		Functions\expect( 'home_url' )
			->once()
			->with( '/onderwerpen/' )
			->andReturn( 'https://boszien.org/onderwerpen/' );
		Functions\expect( 'wp_safe_redirect' )
			->once()
			->with( 'https://boszien.org/onderwerpen/', 302, 'Boszien Base' )
			->andThrow( new \Exception( 'boszien-base test: exit after redirect' ) );

		$this->expectException( \Exception::class );

		boszien_base_redirect_non_editorial_taxonomies();
	}

	public function test_redirect_leaves_populated_editorial_archives_untouched(): void {
		$term = new \WP_Term( 12, 'category', 'gezondheid', 5 );

		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'wp_doing_ajax' )->once()->andReturn( false );
		Functions\expect( 'is_category' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );
		Functions\expect( 'wp_safe_redirect' )->never();

		boszien_base_redirect_non_editorial_taxonomies();

		$this->addToAssertionCount( 1 );
	}

	// -----------------------------------------------------------------
	// boszien_base_noindex_non_editorial_taxonomies()
	// -----------------------------------------------------------------

	public function test_noindex_filter_ignores_non_taxonomy_requests(): void {
		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( false );
		Functions\expect( 'get_queried_object' )->never();

		$robots = boszien_base_noindex_non_editorial_taxonomies( array( 'index' => true ) );

		$this->assertSame( array( 'index' => true ), $robots );
	}

	public function test_noindex_filter_flags_empty_archives(): void {
		$term = new \WP_Term( 20, 'category', 'zeldzame-claim', 0 );

		Functions\expect( 'is_category' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );

		$robots = boszien_base_noindex_non_editorial_taxonomies( array( 'index' => true ) );

		$this->assertTrue( $robots['noindex'] );
		$this->assertTrue( $robots['index'] );
	}

	public function test_noindex_filter_flags_technical_tag_archives_even_when_populated(): void {
		$term = new \WP_Term( 21, 'post_tag', 'type-source', 4 );

		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );

		$robots = boszien_base_noindex_non_editorial_taxonomies( array() );

		$this->assertTrue( $robots['noindex'] );
	}

	public function test_noindex_filter_leaves_populated_editorial_archives_untouched(): void {
		$term = new \WP_Term( 22, 'post_tag', 'gezondheid', 4 );

		Functions\expect( 'is_category' )->once()->andReturn( false );
		Functions\expect( 'is_tag' )->once()->andReturn( true );
		Functions\expect( 'get_queried_object' )->once()->andReturn( $term );

		$robots = boszien_base_noindex_non_editorial_taxonomies( array() );

		$this->assertArrayNotHasKey( 'noindex', $robots );
	}

	// -----------------------------------------------------------------
	// boszien_base_filter_taxonomy_sitemap_args()
	// -----------------------------------------------------------------

	public function test_sitemap_args_only_forces_hide_empty_for_other_taxonomies(): void {
		Functions\expect( 'get_terms' )->never();

		$result = boszien_base_filter_taxonomy_sitemap_args( array( 'foo' => 'bar' ), 'category' );

		$this->assertTrue( $result['hide_empty'] );
		$this->assertSame( 'bar', $result['foo'] );
		$this->assertArrayNotHasKey( 'exclude', $result );
	}

	public function test_sitemap_args_tolerates_a_wp_error_from_get_terms(): void {
		Functions\expect( 'get_terms' )
			->once()
			->with( array( 'taxonomy' => 'post_tag', 'hide_empty' => false ) )
			->andReturn( 'wp-error-sentinel' );
		Functions\expect( 'is_wp_error' )->once()->with( 'wp-error-sentinel' )->andReturn( true );

		$result = boszien_base_filter_taxonomy_sitemap_args( array(), 'post_tag' );

		$this->assertTrue( $result['hide_empty'] );
		$this->assertArrayNotHasKey( 'exclude', $result );
	}

	public function test_sitemap_args_excludes_technical_tags_and_dedupes_with_existing_exclusions(): void {
		$terms = array(
			new \WP_Term( 1, 'post_tag', 'type-a' ),
			new \WP_Term( 2, 'post_tag', 'gewoon' ),
			new \WP_Term( 3, 'post_tag', 'type-b' ),
		);

		Functions\expect( 'get_terms' )->once()->andReturn( $terms );
		Functions\expect( 'is_wp_error' )->once()->andReturn( false );

		$result = boszien_base_filter_taxonomy_sitemap_args( array( 'exclude' => array( 3, 99 ) ), 'post_tag' );

		$this->assertTrue( $result['hide_empty'] );
		sort( $result['exclude'] );
		$this->assertSame( array( 1, 3, 99 ), $result['exclude'] );
	}

	public function test_sitemap_args_leaves_exclude_untouched_when_no_technical_tags_exist(): void {
		$terms = array( new \WP_Term( 1, 'post_tag', 'gewoon' ) );

		Functions\expect( 'get_terms' )->once()->andReturn( $terms );
		Functions\expect( 'is_wp_error' )->once()->andReturn( false );

		$result = boszien_base_filter_taxonomy_sitemap_args( array(), 'post_tag' );

		$this->assertTrue( $result['hide_empty'] );
		$this->assertArrayNotHasKey( 'exclude', $result );
	}
}