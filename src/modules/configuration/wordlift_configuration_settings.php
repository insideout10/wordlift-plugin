<?php

/**
 * Get the configured WordLift key.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->get_key()
 *
 * @since      3.0.0
 *
 * @return string The configured WordLift key or an empty string.
 */
function wl_configuration_get_key() {

	return Wordlift_Configuration_Service::get_instance()->get_key();
}

/**
 * Get the Redlink dataset URI.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
 *
 * @since      3.10.0 deprecated.
 * @since      3.0.0
 *
 * @return string The Redlink dataset URI.
 */
function wl_configuration_get_redlink_dataset_uri() {

	return Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
}

/**
 * Get the URL to use for running analyses. If a WordLift key is set, then a WordLift Server URL is returned, otherwise
 * a Redlink URL.
 *
 * @since 3.0.0
 *
 * @uses  wl_configuration_get_key() to get the WordLift key.
 *
 * @return string The analysis URL.
 */
function wl_configuration_get_analyzer_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = Wordlift_Configuration_Service::get_instance()->get_key();

	// Return a NULL URL if the key isn't set.
	if ( empty( $key ) ) {
		return null;
	}

	return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "analyses?key=$key"
	       . ( defined( 'WL_EXCLUDE_IMAGES_REGEX' ) ? '&exclimage=' . urlencode( WL_EXCLUDE_IMAGES_REGEX ) : '' );

}

/**
 * Get the URL to perform SELECT queries.
 *
 * @since 3.0.0
 *
 * @return string The URL to call to perform the SELECT query.
 */
function wl_configuration_get_query_select_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = Wordlift_Configuration_Service::get_instance()->get_key();

	if ( empty( $key ) ) {
		return null;
	}

	return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/queries?q=";
}

/**
 * Get the URL to perform UPDATE queries.
 *
 * @since 3.0.0
 *
 * @return string The URL to call to perform the UPDATE query.
 */
function wl_configuration_get_query_update_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = Wordlift_Configuration_Service::get_instance()->get_key();
	if ( empty( $key ) ) {
		return null;
	}

	return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/queries";
}


/**
 * Get the URL to perform indexing operations.
 *
 * @since 3.0.0
 *
 * @return string The URL to call to perform the indexing operation.
 */
function wl_configuration_get_dataset_index_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = Wordlift_Configuration_Service::get_instance()->get_key();
	if ( empty( $key ) ) {
		return null;
	}

	return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/index";
}
