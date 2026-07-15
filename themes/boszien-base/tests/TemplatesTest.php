<?php
/**
 * Tests for templates/*.html. This release adds an explicit "theme" slug to
 * every header/footer template-part reference, replaces the safe fallback
 * copy on home.html and index.html with live Query Loops, and restructures
 * page-plain.html to show the automatic title above a dedicated reading
 * column.
 */

namespace Boszien\Tests;

use PHPUnit\Framework\TestCase;

final class TemplatesTest extends TestCase {

	private const TEMPLATES_DIR = __DIR__ . '/../templates';

	/**
	 * @return array<int, array<int, string>>
	 */
	public static function template_files(): array {
		return array(
			array( '404.html' ),
			array( 'archive.html' ),
			array( 'front-page.html' ),
			array( 'home.html' ),
			array( 'index.html' ),
			array( 'page-plain.html' ),
			array( 'page.html' ),
			array( 'search.html' ),
			array( 'single.html' ),
		);
	}

	private function template( string $file ): string {
		return file_get_contents( self::TEMPLATES_DIR . "/{$file}" );
	}

	/**
	 * @dataProvider template_files
	 */
	public function test_header_template_part_declares_the_theme_slug( string $file ): void {
		$this->assertStringContainsString(
			'<!-- wp:template-part {"slug":"header","tagName":"header","className":"bz-site-header","theme":"boszien-base"} /-->',
			$this->template( $file ),
			"{$file} header template-part is missing the explicit theme slug"
		);
	}

	/**
	 * @dataProvider template_files
	 */
	public function test_footer_template_part_declares_the_theme_slug( string $file ): void {
		$this->assertStringContainsString(
			'<!-- wp:template-part {"slug":"footer","tagName":"footer","className":"bz-site-footer","theme":"boszien-base"} /-->',
			$this->template( $file ),
			"{$file} footer template-part is missing the explicit theme slug"
		);
	}

	/**
	 * @dataProvider template_files
	 */
	public function test_template_references_exactly_one_header_and_one_footer_part( string $file ): void {
		$html = $this->template( $file );

		$this->assertSame( 1, preg_match_all( '/"slug":"header"/', $html ), "{$file} should reference the header part exactly once" );
		$this->assertSame( 1, preg_match_all( '/"slug":"footer"/', $html ), "{$file} should reference the footer part exactly once" );
		$this->assertSame( 0, preg_match_all( '/"className":"bz-site-header"}(?![^}]*"theme")/', $html ), "{$file} must not keep an un-themed header template-part reference" );
	}

	public function test_home_and_index_render_a_live_inherited_query_loop(): void {
		foreach ( array( 'home.html', 'index.html' ) as $file ) {
			$html = $this->template( $file );

			$this->assertStringContainsString( '<!-- wp:query', $html, "{$file} should render a Query Loop" );
			$this->assertStringContainsString( '"inherit":true', $html );
			$this->assertStringContainsString( '<!-- wp:post-title', $html );
			$this->assertStringContainsString( '<!-- wp:post-excerpt', $html );
			$this->assertStringContainsString( '<!-- wp:query-pagination', $html );
			$this->assertStringContainsString( '<!-- wp:query-no-results', $html );
			$this->assertStringNotContainsString( 'wordt nog ingericht', $html, "{$file} should drop the placeholder fallback copy" );
		}
	}

	public function test_home_and_index_use_distinct_query_ids(): void {
		preg_match( '/"queryId":(\d+)/', $this->template( 'home.html' ), $home_match );
		preg_match( '/"queryId":(\d+)/', $this->template( 'index.html' ), $index_match );

		$this->assertNotEmpty( $home_match );
		$this->assertNotEmpty( $index_match );
		$this->assertNotSame( $home_match[1], $index_match[1] );
	}

	public function test_page_plain_shows_the_automatic_title_above_a_dedicated_reading_column(): void {
		$html = $this->template( 'page-plain.html' );

		$this->assertStringContainsString( 'bz-page-header', $html );
		$this->assertStringContainsString( '<!-- wp:post-title {"level":1} /-->', $html );
		$this->assertStringContainsString( 'bz-reading', $html );
		$this->assertStringContainsString( '<!-- wp:post-content', $html );
	}

	public function test_single_html_keeps_its_dedicated_reading_progress_markup(): void {
		$html = $this->template( 'single.html' );

		$this->assertStringContainsString( 'bz-reading-progress', $html );
	}

	public function test_front_page_still_delegates_entirely_to_post_content(): void {
		$html = $this->template( 'front-page.html' );

		$this->assertStringContainsString( 'bz-homepage-wrapper', $html );
		$this->assertStringContainsString( '<!-- wp:post-content {"layout":{"type":"default"}} /-->', $html );
	}
}