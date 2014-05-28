<?php
/**
 * This file contains WordLift-related constants.
 */

// Define the basic options for HTTP calls to REDLINK.
define('WL_REDLINK_API_HTTP_OPTIONS', serialize(array(
    'timeout' => 60,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking' => true,
    'cookies' => array(),
    'sslverify' => true,
    'sslcertificates' => dirname(__FILE__) . '/ssl/ca-bundle.crt'
)));

// Create a unique ID for this request, useful to hook async HTTP requests.
define('WL_REQUEST_ID', uniqid());

// Set the temporary files folder.
define('WL_TEMP_DIR', get_temp_dir());

//write_log( "getenv('WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING' :: " . ( 'true' !== getenv('WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING' ) ? 'true' : 'false' ) );
define('WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING', 'true' !== getenv('WL_DISABLE_SPARQL_UPDATE_QUERIES_BUFFERING'));

// Define the meta name used to store the entity URL.
define('WL_ENTITY_URL_META_NAME', 'entity_url');

// Define the field name for the dataset base URI.
define('WL_CONFIG_DATASET_BASE_URI_NAME', 'dataset_base_uri');

define('WL_CONFIG_APPLICATION_KEY_NAME', 'application_key');

define('WL_CONFIG_USER_ID_NAME', 'user_id');

define('WL_CONFIG_DATASET_NAME', 'dataset_name');

define('WL_CONFIG_ANALYSIS_NAME', 'analysis_name');

define('WL_CONFIG_SITE_LANGUAGE_NAME', 'site_language');

define('WL_CONFIG_DEFAULT_SITE_LANGUAGE', 'en');

// Define the Redlink API version (it is used to build API URLs).
define('WL_REDLINK_API_VERSION', '1.0-BETA');

// Define the Redlink API base URI (with end slash).
define('WL_REDLINK_API_BASE_URI', 'https://api.redlink.io/');

// the WordLift options identifier.
define('WL_OPTIONS_NAME', 'wordlift_options');

// The field name where the properties are hold (from a POST).
define('WL_POST_ENTITY_PROPS', 'wl_props');

define('WL_CUSTOM_FIELD_GEO_LATITUDE', 'wl_geo_latitude');
define('WL_CUSTOM_FIELD_GEO_LONGITUDE', 'wl_geo_longitude');
define('WL_CUSTOM_FIELD_CAL_DATE_START', 'wl_cal_date_start');
define('WL_CUSTOM_FIELD_CAL_DATE_END', 'wl_cal_date_end');

// The entity type.
define( 'WL_ENTITY_TYPE_NAME', 'entity' );

// The name for the entity type taxonomy.
define('WL_ENTITY_TYPE_TAXONOMY_NAME', 'wl_entity_type');

// The name of the custom field that stores the IDs of entities referenced by posts.
define('WL_CUSTOM_FIELD_REFERENCED_ENTITY', 'wordlift_related_entities');

/**
 * Get an array with commonly supported prefixes.
 *
 * @return array An array of prefixes and URIs
 */
function wl_prefixes() {
    return array(
        'geo'    => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
        'dct'    => 'http://purl.org/dc/terms/',
        'rdfs'   => 'http://www.w3.org/2000/01/rdf-schema#',
        'owl'    => 'http://www.w3.org/2002/07/owl#',
        'schema' => 'http://schema.org/'
    );
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