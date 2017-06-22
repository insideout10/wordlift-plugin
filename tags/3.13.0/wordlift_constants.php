<?php
/**
 * This file contains WordLift-related constants.
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
define( 'WL_REQUEST_ID', uniqid() );

// Set the temporary files folder.
defined( 'WL_TEMP_DIR' ) || define( 'WL_TEMP_DIR', get_temp_dir() );

define( 'WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING', 'true' !== getenv( 'WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING' ) );

// Define the meta name used to store the entity URL.
define( 'WL_ENTITY_URL_META_NAME', 'entity_url' );

// Max number of recursions when printing microdata
define( 'WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING', 3 );

// 3.13.0, we use by default WLS 1.11 which provides us with the new, faster
// chunked analysis.
define( 'WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE', defined( 'WORDLIFT_API_URL' ) ? WORDLIFT_API_URL . '/' : 'https://api.wordlift.it/1.11/' );

define( 'WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL', 'https://developers.google.com/structured-data/testing-tool/?url=' );


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
 * Get an array with commonly used predicates.
 *
 * @see wl_prefixes for prefixes used here.
 *
 * @return array An array of predicates.
 */
function wl_predicates() {

	return array(
		'a',
		'dct:references',
		'dct:relation',
		'dct:title',
		'dct:subject',
		'owl:sameAs',
		'rdfs:label',
		'schema:author',
		'schema:dateModified',
		'schema:datePublished',
		'schema:locationCreated',
		'schema:description',
		'schema:image',
		'schema:interactionCount',
		'schema:url',
	);

}
