<?php

/**
 * Set a configuration option.
 * @deprecated use {@link Wordlift_Configuration_Service}.
 *
 * @param string $settings The configuration settings group.
 * @param string $key      The setting name.
 * @param string $value    The setting value.
 */
function wl_configuration_set( $settings, $key, $value ) {

	$options         = get_option( $settings );
	$options         = isset( $options ) ? $options : array();
	$options[ $key ] = $value;
	update_option( $settings, $options );
}


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

//	$options = get_option( 'wl_general_settings' );
//
//	if ( isset( $options['key'] ) ) {
//		return $options['key'];
//	}
//
//	return '';
}

/**
 * Set the WordLift key.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->set_key( $value );
 *
 * @since      3.0.0
 *
 * @param string $value The WordLift key.
 */
function wl_configuration_set_key( $value ) {

	Wordlift_Configuration_Service::get_instance()->set_key( $value );

//	wl_configuration_set( 'wl_general_settings', 'key', $value );

}

/**
 * Get the *Site Language* configuration setting.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->get_language_code();
 *
 * @since      3.0.0
 *
 * @return string It returns the two-letter code of the site language.
 */
function wl_configuration_get_site_language() {

	return Wordlift_Configuration_Service::get_instance()->get_language_code();

//	$options = get_option( 'wl_general_settings' );
//
//	return ( empty( $options['site_language'] ) ? 'en' : $options['site_language'] );
}

/**
 * Set the *Site Language* configuration setting.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->set_language_code( $value );
 *
 * @since      3.0.0
 *
 * @param string $value The two-letter language code.
 */
function wl_configuration_set_site_language( $value ) {

	Wordlift_Configuration_Service::get_instance()->set_language_code( $value );

//	wl_configuration_set( 'wl_general_settings', 'site_language', $value );
}

/**
 * Get the API URL.
 *
 * @since 3.0.0
 *
 * @return string Get the API URL.
 */
function wl_configuration_get_api_url() {

	$options = get_option( 'wl_advanced_settings', '' );

	return ( empty( $options['api_url'] ) ? '' : $options['api_url'] );

}

/**
 * Set the API URL.
 *
 * @deprecated used only for testing.
 *
 * @since      3.0.0
 *
 * @param string $value The API URL.
 */
function wl_configuration_set_api_url( $value ) {

	wl_configuration_set( 'wl_advanced_settings', 'api_url', $value );
}

/**
 * Get the Redlink application key.
 *
 * @since 3.0.0
 *
 * @return string The Redlink application key.
 */
function wl_configuration_get_redlink_key() {

	$options = get_option( 'wl_advanced_settings', '' );

	return ( empty( $options['redlink_key'] ) ? '' : $options['redlink_key'] );

}

/**
 * Set the Redlink application key.
 *
 * @deprecated used only for testing.
 *
 * @param      3.0.0
 *
 * @param string $value The Redlink application key.
 */
function wl_configuration_set_redlink_key( $value ) {

	wl_configuration_set( 'wl_advanced_settings', 'redlink_key', $value );
}

/**
 * Get the Redlink user id.
 *
 * @since 3.0.0
 *
 * @return string The Redlink user id.
 */
function wl_configuration_get_redlink_user_id() {

	$options = get_option( 'wl_advanced_settings', '' );

	return ( empty( $options['redlink_user_id'] ) ? '' : $options['redlink_user_id'] );

}

/**
 * Set the Redlink user id.
 *
 * @deprecated used only for testing.
 *
 * @since      3.0.0
 *
 * @param string $value The Redlink user id.
 */
function wl_configuration_set_redlink_user_id( $value ) {

	wl_configuration_set( 'wl_advanced_settings', 'redlink_user_id', $value );
}

/**
 * Get the Redlink dataset name.
 *
 * @since 3.0.0
 *
 * @return string The Redlink dataset name.
 */
function wl_configuration_get_redlink_dataset_name() {

	$options = get_option( 'wl_advanced_settings', '' );

	return ( empty( $options['redlink_dataset_name'] ) ? '' : $options['redlink_dataset_name'] );
}


/**
 * Set the Redlink dataset name.
 *
 * @deprecated used only for testing.
 *
 * @since      3.0.0
 *
 * @param string $value The Redlink dataset name.
 */
function wl_configuration_set_redlink_dataset_name( $value ) {

	wl_configuration_set( 'wl_advanced_settings', 'redlink_dataset_name', $value );
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
 * Set the Redlink dataset URI.
 *
 * @deprecated use Wordlift_Configuration_Service::get_instance()->set_dataset_uri( $value );
 *
 * @since      3.10.0 deprecated.
 * @since      3.0.0
 *
 * @param string $value The Redlink dataset URI.
 */
function wl_configuration_set_redlink_dataset_uri( $value ) {

	Wordlift_Configuration_Service::get_instance()->set_dataset_uri( $value );
}

/**
 * Get the Redlink application name.
 *
 * @since 3.0.0
 *
 * @return string The Redlink application name.
 */
function wl_configuration_get_redlink_application_name() {

	$options = get_option( 'wl_advanced_settings', '' );

	return ( empty( $options['redlink_application_name'] ) ? '' : $options['redlink_application_name'] );
}


/**
 * Set the Redlink application name (once called the Analysis name).
 *
 * @since 3.0.0
 *
 * @param string $value The Redlink application name.
 */
function wl_configuration_set_redlink_application_name( $value ) {

	wl_configuration_set( 'wl_advanced_settings', 'redlink_application_name', $value );
}

/**
 * Get the URL to use for running analyses. If a WordLift key is set, then a WordLift Server URL is returned, otherwise
 * a Redlink URL.
 *
 * @since 3.0.0
 *
 * @uses  wl_configuration_get_key() to get the WordLift key.
 * @uses  wl_configuration_get_redlink_key() to get the application key.
 * @uses  wl_configuration_get_redlink_application_name() to get the analysis name.
 *
 * @return string The analysis URL.
 */
function wl_configuration_get_analyzer_url() {

	// If the WordLift Key is set, we use WordLift.
	$key = wl_configuration_get_key();
	if ( ! empty( $key ) ) {
		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "analyses?key=$key"
		       . ( defined( 'WL_EXCLUDE_IMAGES_REGEX' ) ? '&exclimage=' . urlencode( WL_EXCLUDE_IMAGES_REGEX ) : '' );
	}

	// Otherwise use Redlink.
	$app_key       = wl_configuration_get_redlink_key();
	$analysis_name = wl_configuration_get_redlink_application_name();

	$ldpath = <<<EOF
        @prefix ex: <http://example.org/>;
        @prefix cal: <http://www.w3.org/2002/12/cal#>;
        @prefix gn: <http://www.geonames.org/ontology#>;
        @prefix lode: <http://linkedevents.org/ontology/>;
        @prefix vcard: <http://www.w3.org/2006/vcard/ns#>;
        vcard:locality = lode:atPlace/gn:name :: xsd:string;
EOF;

	return wl_configuration_get_api_url() . "/analysis/$analysis_name/enhance?key=$app_key" .
	       '&enhancer.engines.dereference.ldpath=' . urlencode( $ldpath );

}

/**
 * Get the API URI to retrieve the dataset URI using the WordLift Key.
 *
 * @deprecated use `Wordlift_Configuration_Service::get_instance()->get_accounts_by_key_dataset_uri( $key );`.
 *
 * @since      3.11.0 deprecated
 * @since      3.0.0
 *
 * @param string $key The WordLift key to use.
 *
 * @return string The API URI.
 */
function wl_configuration_get_accounts_by_key_dataset_uri( $key ) {

	return Wordlift_Configuration_Service::get_instance()->get_accounts_by_key_dataset_uri( $key );

	//return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "accounts/key=$key/dataset_uri";
}


/**
 * Get the URL to perform SELECT queries.
 *
 * @since 3.0.0
 *
 * @param string      $output  The output format, either 'json', 'xml', 'tabs' or 'csv'.
 * @param string|null $dataset The dataset to use for the query. Only valid for queries straight to Redlink.
 *
 * @return string The URL to call to perform the SELECT query.
 */
function wl_configuration_get_query_select_url( $output, $dataset = null ) {

	// If the WordLift Key is set, we use WordLift.
	$key = wl_configuration_get_key();
	if ( ! empty( $key ) ) {
		// TODO: handle the output format for WordLift.
		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/queries?q=";
	}

	// If no dataset has been specified then use the default configured, otherwise use the one provided.
	$redlink_dataset = ( empty( $dataset ) ? wl_configuration_get_redlink_dataset_name() : $dataset );
	$redlink_key     = wl_configuration_get_redlink_key();

	// construct the API URL.
	return wl_configuration_get_api_url() . "/data/$redlink_dataset/sparql/select?key=$redlink_key&out=$output&query=";

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
	$key = wl_configuration_get_key();
	if ( ! empty( $key ) ) {
		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/queries";
	}

	// get the configuration.
	$redlink_dataset = wl_configuration_get_redlink_dataset_name();
	$redlink_key     = wl_configuration_get_redlink_key();

	// construct the API URL.
	return wl_configuration_get_api_url() . "/data/$redlink_dataset/sparql/update?key=$redlink_key";

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
	$key = wl_configuration_get_key();
	if ( ! empty( $key ) ) {
		return WL_CONFIG_WORDLIFT_API_URL_DEFAULT_VALUE . "datasets/key=$key/index";
	}

	// get the configuration.
	$redlink_dataset = wl_configuration_get_redlink_dataset_name();
	$redlink_key     = wl_configuration_get_redlink_key();

	// construct the API URL.
	return wl_configuration_get_api_url() . "/data/$redlink_dataset/release?key=$redlink_key";

}