<?php
/**
 * This file contains WordLift-related constants.
 *
 * @package Wordlift
 */

// Set the temporary files folder.
defined( 'WL_TEMP_DIR' ) || define( 'WL_TEMP_DIR', wl_temp_dir() );

// Define the meta name used to store the entity URL.
define( 'WL_ENTITY_URL_META_NAME', 'entity_url' );

// WordLift Directory URL.
defined( 'WL_DIR_URL' ) || define( 'WL_DIR_URL', plugin_dir_url( __FILE__ ) );

// Use the WordLift API URL set on the command line.
if ( ! defined( 'WORDLIFT_API_URL' ) && false !== getenv( 'WORDLIFT_API_URL' ) ) {
	define( 'WORDLIFT_API_URL', getenv( 'WORDLIFT_API_URL' ) );
}

// 3.13.0, we use by default WLS 1.11 which provides us with the new, faster
// chunked analysis.
define( 'WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE', defined( 'WORDLIFT_API_URL' ) ? WORDLIFT_API_URL . '/' : 'https://api.wordlift.io/' );

// @since 3.29.0 we do not use https://developers.google.com/structured-data/testing-tool/?url=
define( 'WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL', 'https://search.google.com/test/rich-results?url=' );

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

/**
 * Get a site unique directory under the system or WordPress temporary directory.
 * The WordPress get_temp_dir API, do not take into account that different sites
 * might need to store segregated information from each other. We will use the
 * site URL and blog number to create a "private" area below the temp directory
 * provided by the WordPress API.
 *
 * @return string The path to the temp directory for the specific site.
 * @since 3.16.0
 */
function wl_temp_dir() {
	$tempdir         = get_temp_dir();
	$unique          = md5( site_url() . get_current_blog_id() );
	$unique_temp_dir = $tempdir . 'wl_' . $unique; // $tempdir should have a trailing slash.

	// If directory do not exist, create it.
	if ( ! file_exists( $unique_temp_dir ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		@mkdir( $unique_temp_dir );
	}

	return $unique_temp_dir . '/';
}
