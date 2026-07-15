<?php
/**
 * PHPUnit bootstrap for the Boszien Base theme.
 *
 * Loads Composer's autoloader, provides the minimal WordPress stand-ins the
 * theme's procedural files need at require-time, and then requires
 * functions.php so its plain PHP function declarations become available to
 * every test. Brain\Monkey is used only to stub the WordPress hook
 * registration calls (`add_action`/`add_filter`) that functions.php makes as
 * soon as the file is parsed; the individual test cases install their own
 * expectations for every WordPress function the functions actually call
 * when invoked.
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! class_exists( 'WP_Term' ) ) {
	/**
	 * Minimal stand-in for WordPress' WP_Term, exposing only the public
	 * properties boszien_base_is_technical_tag() and its callers read.
	 */
	class WP_Term {
		public $term_id;
		public $taxonomy;
		public $slug;
		public $count;

		public function __construct( $term_id, $taxonomy, $slug, $count = 0 ) {
			$this->term_id  = $term_id;
			$this->taxonomy = $taxonomy;
			$this->slug     = $slug;
			$this->count    = $count;
		}
	}
}

Brain\Monkey\setUp();

Brain\Monkey\Functions\when( 'add_action' )->justReturn( true );
Brain\Monkey\Functions\when( 'add_filter' )->justReturn( true );
Brain\Monkey\Functions\when( 'get_template_directory' )->justReturn( dirname( __DIR__ ) );
Brain\Monkey\Functions\when( 'register_block_style' )->justReturn( true );
Brain\Monkey\Functions\when( 'register_block_type' )->justReturn( true );
Brain\Monkey\Functions\when( '__' )->returnArg( 1 );

require_once dirname( __DIR__ ) . '/functions.php';

Brain\Monkey\tearDown();