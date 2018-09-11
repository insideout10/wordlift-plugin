<?php

/*
 * Enable `All Entity Types`.
 *
 * This can be removed one `All Entity Types` is enabled by default.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/835
 */
define( 'WL_ALL_ENTITY_TYPES', true );

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../src/wordlift.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

require_once( 'functions.php' );
require_once( 'class-wordlift-unittest-factory-for-entity.php' );
require_once( 'class-wordlift-unit-test-case.php' );
require_once( 'class-wordlift-ajax-unit-test-case.php' );
require_once( 'class-wordlift-test.php' );
