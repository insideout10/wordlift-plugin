<?php
/**
 * This file contains WordLift-related constants.
 *
 * @package Wordlift
 */

// Define the basic options for HTTP calls to REDLINK.
define( 'WL_REDLINK_API_HTTP_OPTIONS', serialize( array(
	'timeout'         => 300,
	'redirection'     => 5,
	'httpversion'     => '1.1',
	'blocking'        => true,
	'cookies'         => array(),
	'sslverify'       => ( 'false' === getenv( 'WL_SSL_VERIFY_ENABLED' ) ) ? false : true,
	'sslcertificates' => dirname( __FILE__ ) . '/ssl/ca-bundle.crt',
	'decompress'      => false,
) ) );

// Create a unique ID for this request, useful to hook async HTTP requests.
define( 'WL_REQUEST_ID', uniqid( true ) );

// Set the temporary files folder.
defined( 'WL_TEMP_DIR' ) || define( 'WL_TEMP_DIR', wl_temp_dir() );

define( 'WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING', wl_is_sparql_update_queries_buffering_enabled() );

function wl_is_sparql_update_queries_buffering_enabled() {

	if ( isset( $_REQUEST['wl-async'] ) && 'false' === $_REQUEST['wl-async'] ) {
		return false;
	}

	return 'true' !== getenv( 'WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING' );
}

// Define the meta name used to store the entity URL.
define( 'WL_ENTITY_URL_META_NAME', 'entity_url' );

// Max number of recursions when printing microdata.
define( 'WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING', 3 );

// Use the WordLift API URL set on the command line.
if ( ! defined( 'WORDLIFT_API_URL' ) && false !== getenv( 'WORDLIFT_API_URL' ) ) {
	define( 'WORDLIFT_API_URL', getenv( 'WORDLIFT_API_URL' ) );
}

// 3.13.0, we use by default WLS 1.11 which provides us with the new, faster
// chunked analysis.
define( 'WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE', defined( 'WORDLIFT_API_URL' ) ? WORDLIFT_API_URL . '/' : 'https://api.wordlift.it/' );

define( 'WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL', 'https://developers.google.com/structured-data/testing-tool/?url=' );

// If is set to true, there will be additional button in 'Download Your Data' page
// that will allow users to download their data in JSON-LD format.
defined( 'WL_CONFIG_DOWNLOAD_GA_CONTENT_DATA' ) || define( 'WL_CONFIG_DOWNLOAD_GA_CONTENT_DATA', false );

/*
 * Define the default scope for autocomplete requests.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/839
 */
defined( 'WL_AUTOCOMPLETE_SCOPE' ) || define( 'WL_AUTOCOMPLETE_SCOPE', 'cloud' );

/*
 * Enable/disable the `all entity types` feature. Initially we keep the feature disabled to enture proper Q/A.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/835
 */
defined( 'WL_ALL_ENTITY_TYPES' ) || define( 'WL_ALL_ENTITY_TYPES', false );

/*
 * Enable/disable saving entities
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/940
 */
defined( 'WL_DISABLE_ENTITY_SAVE' ) || define( 'WL_DISABLE_ENTITY_SAVE', true );

/**
 * Get an array with commonly supported prefixes.
 *
 * @return array An array of prefixes and URIs
 */
function wl_prefixes() {

	$items    = wl_prefixes_list();
	$prefixes = array();

	foreach ( $items as $item ) {
		$prefixes[ $item['prefix'] ] = $item['namespace'];
	}

	return $prefixes;

}

/**
 * Get a site unique directory under the system or WordPress temporary directory.
 * The WordPress get_temp_dir API, do not take into account that different sites
 * might need to store segregated information from each other. We will use the
 * site URL and blog number to create a "private" area below the temp directory
 * provided by the WordPress API.
 *
 * @since 3.16.0
 *
 * @return string The path to the temp directory for the specific site.
 */
function wl_temp_dir() {
	$tempdir         = get_temp_dir();
	$unique          = md5( site_url() . get_current_blog_id() );
	$unique_temp_dir = $tempdir . 'wl_' . $unique; // $tempdir should have a trailing slash.

	// If directory do not exist, create it.
	if ( ! file_exists( $unique_temp_dir ) ) {
		@mkdir( $unique_temp_dir );
	}

	return $unique_temp_dir . '/';
}
