<?php
/**
 * This file contains WordLift-related constants.
 */

// Define the basic options for HTTP calls to REDLINK.
define( 'WL_REDLINK_API_HTTP_OPTIONS', serialize( array(
	'timeout'     => 10,
	'redirection' => 5,
	'httpversion' => '1.1',
	'blocking'    => true,
	'cookies'     => array(),
	'sslverify'   => ( 'false' === getenv( 'WL_SSL_VERIFY_ENABLED' )) ? false : true,
    'sslcertificates' => dirname(__FILE__) . '/ssl/ca-bundle.crt'
) ) );

// Create a unique ID for this request, useful to hook async HTTP requests.
define( 'WL_REQUEST_ID', uniqid() );

// Set the temporary files folder.
defined( 'WL_TEMP_DIR' ) || define( 'WL_TEMP_DIR', get_temp_dir() );

define( 'WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING', 'true' !== getenv( 'WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING' ) );

// Define the meta name used to store the entity URL.
define( 'WL_ENTITY_URL_META_NAME', 'entity_url' );

// Define the Redlink API version (it is used to build API URLs).
define( 'WL_REDLINK_API_VERSION', '1.0-BETA' );

// Define the Redlink API base URI (with end slash).
// define('WL_REDLINK_API_BASE_URI', 'https://api.staging.redlink.io/');

// WL internal data types
define( 'WL_DATA_TYPE_URI', 'uri' );
define( 'WL_DATA_TYPE_DATE', 'date' );
define( 'WL_DATA_TYPE_INTEGER', 'int' );
define( 'WL_DATA_TYPE_DOUBLE', 'double' );
define( 'WL_DATA_TYPE_BOOLEAN', 'bool' );
define( 'WL_DATA_TYPE_STRING', 'string' );

// Entities post-meta names
define( 'WL_CUSTOM_FIELD_SAME_AS', 'entity_same_as' );  // Different name for historical reasons
define( 'WL_CUSTOM_FIELD_GEO_LATITUDE', 'wl_geo_latitude' );
define( 'WL_CUSTOM_FIELD_GEO_LONGITUDE', 'wl_geo_longitude' );
define( 'WL_CUSTOM_FIELD_CAL_DATE_START', 'wl_cal_date_start' );
define( 'WL_CUSTOM_FIELD_CAL_DATE_END', 'wl_cal_date_end' );
define( 'WL_CUSTOM_FIELD_LOCATION', 'wl_location' );
define( 'WL_CUSTOM_FIELD_ADDRESS', 'wl_address' );
define( 'WL_CUSTOM_FIELD_FOUNDER', 'wl_founder');

// Max number of recursions when printing microdata
define( 'WL_RECURSION_DEPTH_ON_ENTITY_METADATA_PRINTING', 3 );

// The entity type.
define( 'WL_ENTITY_TYPE_NAME', 'entity' );

// The name for the entity type taxonomy.
define( 'WL_ENTITY_TYPE_TAXONOMY_NAME', 'wl_entity_type' );


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
		'owl:sameAs',
		'rdfs:label',
		'schema:author',
		'schema:dateModified',
		'schema:datePublished',
		'schema:description',
		'schema:image',
		'schema:interactionCount',
		'schema:url'
	);

}
