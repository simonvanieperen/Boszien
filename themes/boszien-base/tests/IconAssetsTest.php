<?php
/**
 * Tests for the locally vendored Lucide icon set added in this release:
 * assets/icons/*.svg, README.md and LICENSE-LUCIDE.txt.
 */

namespace Boszien\Tests;

use PHPUnit\Framework\TestCase;

final class IconAssetsTest extends TestCase {

	private const ICON_DIR = __DIR__ . '/../assets/icons';

	/**
	 * @return array<int, array<int, string>>
	 */
	public static function icon_names(): array {
		return array(
			array( 'arrow-right' ),
			array( 'flag' ),
			array( 'layers' ),
			array( 'menu' ),
			array( 'scale' ),
			array( 'search' ),
			array( 'shield' ),
			array( 'shield-check' ),
		);
	}

	/**
	 * @dataProvider icon_names
	 */
	public function test_icon_file_exists( string $name ): void {
		$this->assertFileExists( self::ICON_DIR . "/{$name}.svg" );
	}

	/**
	 * @dataProvider icon_names
	 */
	public function test_icon_is_well_formed_xml( string $name ): void {
		$contents = file_get_contents( self::ICON_DIR . "/{$name}.svg" );

		$previous_setting = libxml_use_internal_errors( true );
		$element          = simplexml_load_string( $contents );
		$errors           = libxml_get_errors();
		libxml_clear_errors();
		libxml_use_internal_errors( $previous_setting );

		$this->assertNotFalse( $element, "{$name}.svg is not well-formed XML" );
		$this->assertSame( array(), $errors );
		$this->assertSame( 'svg', $element->getName() );
	}

	/**
	 * @dataProvider icon_names
	 */
	public function test_icon_shares_the_boszien_stroke_style_conventions( string $name ): void {
		$contents = file_get_contents( self::ICON_DIR . "/{$name}.svg" );

		$this->assertStringContainsString( 'xmlns="http://www.w3.org/2000/svg"', $contents );
		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $contents );
		$this->assertStringContainsString( 'fill="none"', $contents );
		$this->assertStringContainsString( 'stroke="currentColor"', $contents );
		$this->assertStringContainsString( 'stroke-width="2"', $contents );
	}

	/**
	 * @dataProvider icon_names
	 */
	public function test_icon_contains_at_least_one_path_or_shape( string $name ): void {
		$contents = file_get_contents( self::ICON_DIR . "/{$name}.svg" );
		$element  = simplexml_load_string( $contents );

		$drawableChildren = 0;
		foreach ( array( 'path', 'circle', 'rect', 'line', 'polyline', 'polygon' ) as $tag ) {
			$drawableChildren += count( $element->{$tag} );
		}

		$this->assertGreaterThan( 0, $drawableChildren, "{$name}.svg has no drawable child elements" );
	}

	public function test_no_stray_svg_files_are_shipped(): void {
		$expected = array_map(
			static function ( array $row ) {
				return $row[0] . '.svg';
			},
			self::icon_names()
		);

		$actual = array_map( 'basename', glob( self::ICON_DIR . '/*.svg' ) );

		sort( $expected );
		sort( $actual );

		$this->assertSame( $expected, $actual );
	}

	public function test_readme_documents_every_shipped_icon(): void {
		$readme = file_get_contents( self::ICON_DIR . '/README.md' );

		foreach ( self::icon_names() as [ $name ] ) {
			$this->assertStringContainsString( "`{$name}`", $readme, "README.md does not mention {$name}" );
		}

		$this->assertStringContainsString( 'Lucide 1.10.0', $readme );
		$this->assertStringContainsString( 'LICENSE-LUCIDE.txt', $readme );
	}

	public function test_license_file_documents_the_isc_and_mit_terms(): void {
		$license = file_get_contents( self::ICON_DIR . '/LICENSE-LUCIDE.txt' );

		$this->assertStringContainsString( 'ISC License', $license );
		$this->assertStringContainsString( 'Lucide Icons and Contributors', $license );
		$this->assertStringContainsString( 'MIT License', $license );
		// The bundled "search" icon is one of the Feather-derived, MIT-licensed icons.
		$this->assertMatchesRegularExpression( '/\bsearch\b/', $license );
	}
}