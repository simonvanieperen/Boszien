<?php
/**
 * Tests for CHANGELOG.md (bumped to document release 0.6.0) and the newly
 * added RELEASE.md release/rollback workflow document.
 */

namespace Boszien\Tests;

use PHPUnit\Framework\TestCase;

final class ChangelogReleaseDocsTest extends TestCase {

	private function changelog(): string {
		return file_get_contents( dirname( __DIR__ ) . '/CHANGELOG.md' );
	}

	public function test_changelog_starts_with_the_current_release(): void {
		$lines = preg_split( '/\R/', trim( $this->changelog() ) );

		$this->assertSame( '# Changelog', $lines[0] );
		$this->assertStringContainsString( '## 0.6.0', $lines[2] ?? '' );
	}

	public function test_changelog_documents_the_native_stylesheet_enqueue_change(): void {
		$changelog = $this->changelog();

		$this->assertStringContainsString( 'native stylesheet delivery', $changelog );
		$this->assertStringContainsString( 'theme-versioned style enqueueing', $changelog );
	}

	public function test_changelog_entries_are_listed_newest_first(): void {
		preg_match_all( '/^## (\d+\.\d+\.\d+)/m', $this->changelog(), $matches );

		$versions = $matches[1];
		$this->assertNotEmpty( $versions );

		$sorted_desc = $versions;
		usort( $sorted_desc, 'version_compare' );
		$sorted_desc = array_reverse( $sorted_desc );

		$this->assertSame( $sorted_desc, $versions, 'CHANGELOG.md entries must be listed newest first' );
	}

	public function test_changelog_contains_no_duplicate_version_headings(): void {
		preg_match_all( '/^## (\d+\.\d+\.\d+)/m', $this->changelog(), $matches );

		$versions = $matches[1];

		$this->assertSame( array_unique( $versions ), array_values( array_unique( $versions ) ) );
		$this->assertCount( count( array_unique( $versions ) ), $versions );
	}

	public function test_release_document_exists_and_is_not_empty(): void {
		$path = dirname( __DIR__ ) . '/RELEASE.md';

		$this->assertFileExists( $path );
		$this->assertNotSame( '', trim( file_get_contents( $path ) ) );
	}

	public function test_release_document_covers_release_and_rollback_workflows(): void {
		$release = file_get_contents( dirname( __DIR__ ) . '/RELEASE.md' );

		$this->assertStringContainsString( '## Release', $release );
		$this->assertStringContainsString( '## Rollback', $release );
	}

	public function test_release_document_references_the_shared_navigation_objects(): void {
		$release = file_get_contents( dirname( __DIR__ ) . '/RELEASE.md' );

		$this->assertStringContainsString( 'Navigation-object `4`', $release );
		$this->assertStringContainsString( 'Navigation-object `178`', $release );
	}
}