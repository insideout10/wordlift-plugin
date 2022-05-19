<?php

/**
 * Get the Redlink dataset URI.
 *
 * @return string The Redlink dataset URI.
 * @since      3.10.0 deprecated.
 * @since      3.0.0
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
 *
 */
function wl_configuration_get_redlink_dataset_uri() {

	return Wordlift_Configuration_Service::get_instance()->get_dataset_uri();
}

/**
 * Get the URL to perform UPDATE queries.
 *
 * @return string The URL to call to perform the UPDATE query.
 * @since 3.0.0
 *
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
 * @return string The URL to call to perform the indexing operation.
 * @since 3.0.0
 *
 */
function wl_configuration_get_dataset_index_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = Wordlift_Configuration_Service::get_instance()->get_key();
	if ( empty( $key ) ) {
		return null;
	}

	return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/index";
}
