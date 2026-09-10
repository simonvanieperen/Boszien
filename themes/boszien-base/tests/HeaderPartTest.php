<?php
/**
 * Tests for parts/header.html, which now delegates the stylesheet link to
 * WordPress' own enqueue system and the header links to a shared Navigation
 * object (ref 4) instead of duplicated desktop/mobile markup.
 */

namespace Boszien\Tests;

use PHPUnit\Framework\TestCase;

final class HeaderPartTest extends TestCase {

	private function header(): string {
		return file_get_contents( dirname( __DIR__ ) . '/parts/header.html' );
	}

	public function test_no_longer_hand_links_a_versioned_stylesheet(): void {
		$html = $this->header();

		$this->assertStringNotContainsString( 'boszien-base-css', $html );
		$this->assertStringNotContainsString( '<link', $html );
		$this->assertStringNotContainsString( 'style.css?ver=', $html );
	}

	public function test_no_longer_renders_its_own_skip_link(): void {
		// The native WordPress skip link is now the single sitewide bypass control (CHANGELOG 0.5.0).
		$this->assertStringNotContainsString( 'bz-skip-link', $this->header() );
	}

	public function test_uses_the_shared_navigation_object_for_header_links(): void {
		$html = $this->header();

		$this->assertMatchesRegularExpression( '/wp:navigation\s*\{"ref":4\b/', $html );
		$this->assertStringContainsString( 'bz-primary-nav', $html );
		$this->assertStringContainsString( '"overlayMenu":"mobile"', $html );
	}

	public function test_no_longer_duplicates_desktop_and_mobile_link_lists(): void {
		$html = $this->header();

		$this->assertStringNotContainsString( 'bz-desktop-nav', $html );
		$this->assertStringNotContainsString( 'bz-mobile-menu', $html );
		$this->assertStringNotContainsString( 'bz-mobile-drawer', $html );
	}

	public function test_keeps_a_dedicated_search_control(): void {
		$html = $this->header();

		$this->assertStringContainsString( 'bz-search-menu', $html );
		$this->assertStringContainsString( 'bz-search-panel', $html );
		$this->assertStringContainsString( 'name="s"', $html );
		$this->assertStringContainsString( 'action="/"', $html );
	}

	public function test_search_toggle_uses_an_icon_instead_of_a_text_glyph(): void {
		$html = $this->header();

		$this->assertStringContainsString( 'bz-icon-search', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringNotContainsString( '⌕', $html );
	}

	public function test_brand_logo_link_is_preserved(): void {
		$html = $this->header();

		$this->assertStringContainsString( 'bz-brand-logo', $html );
		$this->assertStringContainsString( 'aria-label="Boszien home"', $html );
		$this->assertStringContainsString( 'href="/"', $html );
	}

	public function test_markup_is_wrapped_in_a_single_header_shell_group(): void {
		$html = $this->header();

		$this->assertStringStartsWith( '<!-- wp:group {"tagName":"div","className":"bz-header-shell"', $html );
		$this->assertStringContainsString( '<!-- /wp:group -->', $html );
	}
}