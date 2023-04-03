<?php

// Load deps including Polyfills, see https://make.wordpress.org/core/2021/09/27/changes-to-the-wordpress-core-php-test-suite/
require_once './vendor/autoload.php';

/**
 * empty() needs to access the value by reference (in order to check whether that reference points to something that exists),
 * and PHP before 5.5 didn't support references to temporary values returned from functions.
 *
 * @param $var
 *
 * @return bool
 */
function wl_is_empty( $var ) {
	return empty( $var );
}

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

$wordpress_version = substr( getenv( 'WORDPRESS_VERSION' ), - 3 );

echo version_compare( $wordpress_version, '5.2', '>=' ) ? "Loading polyfill library since WordPress >= 5.2\n"
	: "Not loading polyfill library since WordPress < 5.2\n";

if ( version_compare( $wordpress_version, '5.2', '>=' ) ) {
	require_once __DIR__ . '/polyfill/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
}

function _manually_load_plugin() {

	// Include/Exclude is loaded before the WLP filter, so we're adding our own filter based on the env variable.
	$include_exclude_env = getenv( 'WL_FEATURES__INCLUDE-EXCLUDE' );
	if ( $include_exclude_env ) {
		add_filter( 'wl_feature__enable__include-exclude', $include_exclude_env, PHP_INT_MAX );
	}

	require __DIR__ . '/../src/wordlift.php';

	if ( defined( 'WL_TESTS_INSTALL_RECIPE_MAKER' ) ) {
		if ( ! file_exists( ABSPATH . 'wp-content/plugins/wp-recipe-maker/' ) ) {
			download( 'https://downloads.wordpress.org/plugin/wp-recipe-maker.zip', '/tmp/wp-recipe-maker.zip' );
			unzip( '/tmp/wp-recipe-maker.zip', ABSPATH . 'wp-content/plugins/' );
		}
		update_option(
			'active_plugins',
			array_unique( array_merge( get_option( 'active_plugins' ), array( 'wp-recipe-maker/wp-recipe-maker.php' ) ) )
		);
	}

	if ( version_compare( get_bloginfo( 'version' ), '5.5', '>=' ) ) {

		if ( ! file_exists( ABSPATH . 'wp-content/plugins/woocommerce/' ) ) {
			download( 'https://downloads.wordpress.org/plugin/woocommerce.5.1.0.zip', '/tmp/woocommerce.5.1.0.zip' );
			unzip( '/tmp/woocommerce.5.1.0.zip', ABSPATH . 'wp-content/plugins/' );
		}

		if ( file_exists( ABSPATH . 'wp-content/plugins/wpsso/' ) ) {
			download( 'https://downloads.wordpress.org/plugin/wpsso.8.26.1.zip', '/tmp/wpsso.8.26.1.zip' );
			unzip( '/tmp/wpsso.8.26.1.zip', ABSPATH . 'wp-content/plugins/' );
		}

		if ( file_exists( ABSPATH . 'wp-content/plugins/wpsso-wc-shipping-delivery-time/' ) ) {
			download( 'https://downloads.wordpress.org/plugin/wpsso-wc-shipping-delivery-time.2.2.1.zip', '/tmp/wpsso-wc-shipping-delivery-time.2.2.1.zip' );
			unzip( '/tmp/wpsso-wc-shipping-delivery-time.2.2.1.zip', ABSPATH . 'wp-content/plugins/' );
		}

		update_option(
			'active_plugins',
			array(
				'woocommerce/woocommerce.php',
				'wpsso/wpsso.php',
				'wpsso-wc-shipping-delivery-time/wpsso-wc-shipping-delivery-time.php',
			)
		);
	}
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Required for woocommerce-shipping-data tests to work.
tests_add_filter( 'wl_feature__enable__shipping-sd', '__return_true' );

// Prevent WooCommerce to send ajax requests during tests.
tests_add_filter( 'action_scheduler_allow_async_request_runner', '__return_false', PHP_INT_MAX );

// Enable/disable features based on envs.
require __DIR__ . '/bootstrap.features.php';

require $_tests_dir . '/includes/bootstrap.php';

require_once 'functions.php';
require_once 'class-wordlift-unittest-factory-for-entity.php';
require_once 'class-wordlift-unit-test-case.php';
/**
 * @since 3.30.0
 * We add a new test case for wordlift vocabulary
 */
require_once 'class-wordlift-vocabulary-unit-test-case.php';
require_once 'class-wordlift-videoobject-unit-test-case.php';
require_once 'class-wordlift-vocabulary-terms-unit-test-case.php';
require_once 'class-wordlift-no-editor-analysis-unit-test-case.php';
require_once 'class-wordlift-ajax-unit-test-case.php';
require_once 'class-wordlift-test.php';

// Enable mappings for the tests
if ( ! defined( 'WL_ENABLE_MAPPINGS' ) ) {
	define( 'WL_ENABLE_MAPPINGS', true );
}

// Add a amp function to support tests
if ( ! function_exists( 'is_amp_endpoint' ) ) {
	function is_amp_endpoint() {
		return isset( $_GET['amp'] );
	}
}

function download( $url, $to ) {

	set_time_limit( 0 );
	$fp = fopen( $to, 'w+' );
	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 300 );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_exec( $ch );
	curl_close( $ch );
	fclose( $fp );

}

function unzip( $what, $to ) {

	$zip = new ZipArchive();
	$zip->open( $what );
	$zip->extractTo( $to );
	$zip->close();

}

define( 'FS_METHOD', 'direct' );
