<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

$wp_config          = "$_tests_dir/wp-tests-config.php";
$wp_config_contents = file_get_contents( $wp_config );
preg_match_all( '@^\s*define\(\s*([\'"])(DB_.*?)\1\s*,\s*([\'"])(.*?)\3\s*\);@m', $wp_config_contents, $matches, PREG_SET_ORDER );
$db_config = array();
foreach ( $matches as $match ) {
	$db_config[ $match[2] ] = $match[4];
}

for ( $i = 1; $i <= 10; $i ++ ) {
	echo( "Try $i of 10 to connect to database...\n" );
	$mysqli = mysqli_connect( $db_config['DB_HOST'], $db_config['DB_USER'], $db_config['DB_PASSWORD'], $db_config['DB_NAME'] );
	if ( $mysqli ) {
		break;
	}
	sleep( 3 );
}
mysqli_close( $mysqli );
echo( "Successfully connected.\n" );

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

// Enable mappings for the tests
if ( ! defined( 'WL_ENABLE_MAPPINGS' ) ) {
	define( 'WL_ENABLE_MAPPINGS', true );
}
