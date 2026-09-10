<?php
/**
 * Tests for boszien-base-manifest.json, which this release bumps to 0.6.0
 * and re-labels as "theme-and-fse-synchronized".
 */

namespace Boszien\Tests;

use PHPUnit\Framework\TestCase;

final class ManifestTest extends TestCase {

	private function manifest_path(): string {
		return dirname( __DIR__ ) . '/boszien-base-manifest.json';
	}

	/**
	 * @return array<string, mixed>
	 */
	private function manifest(): array {
		$decoded = json_decode( file_get_contents( $this->manifest_path() ), true );
		$this->assertIsArray( $decoded, 'boszien-base-manifest.json must decode to an array' );

		return $decoded;
	}

	public function test_manifest_is_valid_json(): void {
		json_decode( file_get_contents( $this->manifest_path() ) );

		$this->assertSame( JSON_ERROR_NONE, json_last_error(), json_last_error_msg() );
	}

	public function test_version_matches_the_release(): void {
		$this->assertSame( '0.6.0', $this->manifest()['version'] );
	}

	public function test_status_reflects_the_synchronized_state(): void {
		$this->assertSame( 'theme-and-fse-synchronized', $this->manifest()['status'] );
	}

	public function test_theme_identity_fields_are_unchanged(): void {
		$manifest = $this->manifest();

		$this->assertSame( 'boszien-base', $manifest['theme'] );
		$this->assertSame( 'Boszien Base', $manifest['name'] );
		$this->assertSame( 'boszien-base', $manifest['textDomain'] );
	}

	public function test_publicatie_veiligheid_flags_are_present_and_typed_as_booleans(): void {
		$flags = $this->manifest()['publicatieVeiligheid'];

		$this->assertIsBool( $flags['queryLoops'] );
		$this->assertIsBool( $flags['commentsTemplate'] );
		$this->assertIsBool( $flags['relatedPosts'] );
		$this->assertIsBool( $flags['automaticLatestPosts'] );
		$this->assertTrue( $flags['queryLoops'], 'home.html and index.html now render live Query Loops' );
		$this->assertFalse( $flags['commentsTemplate'] );
		$this->assertFalse( $flags['relatedPosts'] );
		$this->assertFalse( $flags['automaticLatestPosts'] );
	}

	public function test_interactive_blocks_list_still_declares_the_evidence_meter(): void {
		$manifest = $this->manifest();

		$this->assertTrue( $manifest['blockTheme'] );
		$this->assertSame( 3, $manifest['themeJsonVersion'] );
		$this->assertContains( 'boszien/evidence-meter', $manifest['interactiveBlocks'] );
	}
}