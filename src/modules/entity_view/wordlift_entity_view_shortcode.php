<?php

function wl_entity_view_before_header() {

	// If there is a post
	if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {

		$_post = get_queried_object();

		if ( !isset($_post->post_content) || -1 === stripos( $_post->post_content, '[wl_entity_view ' ) )
			return;

		ob_start( 'wl_entity_view_change_title' );
	}

}
add_action( 'template_redirect', 'wl_entity_view_before_header', 0 );

/**
 * Replaces the title.
 *
 * @since 3.0.0
 *
 * @param string $content The buffered content.
 *
 * @return string The updated content.
 */
function wl_entity_view_change_title( $content ) {

	global $wl_entity_view_title;

	$matches = array();
	if ( 1 !== preg_match( '/<title>([^<]*)<\/title>/i', $content, $matches ) ) {
		return $content;
	}


	return preg_replace('/<title>[^<]*<\/title>/i', "<title>$wl_entity_view_title " . $matches[1] . '</title>', $content);
}



function wl_entity_view_shortcode( $atts, $content = null ) {

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'uri'      => wl_config_get_dataset_base_uri(), // The dataset base URI is the WordLift local dataset.
        'suffix'   => '.json', // The suffix to add for remote queries.
	    'title'    => 'rdfs:label',
	    'language' => 'en'
    ), $atts );

	global $graph, $wl_entity_view_suffix, $wl_entity_view_title;
    $wl_entity_view_suffix = $params['suffix'];

	$url = $params['uri'] . get_query_var( WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );

    // Load the graph.
    $graph = wl_jsonld_load_remote( $url );

	// Get the title.
	$wl_entity_view_title  = wl_jsonld_get_property( $graph, $params['title'], $params['language'] );
	ob_end_flush();

    return do_shortcode( $content );

}
add_shortcode( 'wl_entity_view', 'wl_entity_view_shortcode' );

function wl_entity_property_shortcode( $atts, $content = null ) {

    global $graph;

    if ( empty( $graph ) ) {
        return 'I need a wl_entity_view';
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'     => '',
        'language' => ''
    ), $atts );

    $value = wl_jsonld_get_property( $graph, $params['name'], $params['language'] );
    $value = str_ireplace( "\n", "<br/>", $value );
    return $value;

}
add_shortcode( 'wl_entity_property', 'wl_entity_property_shortcode' );

function wl_entity_image_shortcode( $atts, $content = null ) {

    global $graph;

    if ( empty( $graph ) ) {
        return 'I need a wl_entity_view';
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'     => ''
    ), $atts );

    return '<img src="' . wl_jsonld_get_property( $graph, $params['name'] ) . '" />';

}
add_shortcode( 'wl_entity_image', 'wl_entity_image_shortcode' );

function wl_entity_date_shortcode( $atts, $content = null ) {

    global $graph;

    if ( empty( $graph ) ) {
        return 'I need a wl_entity_view';
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'   => '',
        'format' => 'Y m d'
    ), $atts );

    $time = strtotime( wl_jsonld_get_property( $graph, $params['name'] ) );

    return date( $params['format'], $time );


}
add_shortcode( 'wl_entity_date', 'wl_entity_date_shortcode' );

function wl_entity_duration_shortcode( $atts, $content = null ) {

    global $graph;

    if ( empty( $graph ) ) {
        return __( 'I need a wl_entity_view', 'wordlift' );
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'   => '',
        'format' => '%d day(s), %h hour(s)'
    ), $atts );


    $interval = new DateInterval( wl_jsonld_get_property( $graph, $params['name'] ) );
    return $interval->format( $params['format'] );

}
add_shortcode( 'wl_entity_duration', 'wl_entity_duration_shortcode' );

/**
 * Get a property value using the specified name, language and graph. The property name can be a concatenated tree of
 * keys, e.g. schema:location>schema:latitude.
 *
 * @param object $graph The graph.
 * @param string $name  The property tree.
 * @param null|string $language If provided, a two-characters language code.
 * @return null|string The value or null if not found.
 */
function wl_jsonld_get_property( $graph, $name, $language = null )
{

    $keys  = explode( '>', html_entity_decode( $name ) );
    $value = null;

    foreach ( $keys as $key ) {

        if ( null !== $value ) {
            $graph = wl_jsonld_load_remote( $value );
        }

        $key_exp = wl_prefixes_expand( $key );
        $value   = wl_jsonld_get_property_value( $graph, $key_exp, $language );

//        echo $key . '@' . $language . ' = ' . $value . '<br />';

    }

    return $value;

}

/**
 * Get the value for the property with the specified *key* in the provided *graph*. If the property is a reference to
 * another entity, the remote graph is returned.
 *
 * @param object $graph         A JSON-LD graph.
 * @param string $key           The property key to load.
 * @param string|null $language The desired language (if null, the value with no language set).
 * @return object|string A new graph if the property is a reference, or a value.
 */
function wl_jsonld_get_property_value( $graph, $key, $language = null ) {

    // If the property is not found, return null.
    if ( ! isset( $graph->{ $key } ) ) {
        return null;
    }

    // Get the values.
    $values = $graph->{ $key };

    // It's a reference to another entity.
    if ( isset( $values[0]->{ '@id' } ) ) {
        return $values[0]->{ '@id' };
    }

    // Get the value.
    foreach ( $values as $value ) {

        // Get the value for an empty language.
        if ( empty( $language ) && ! isset( $value->{ '@language' } ) ) {
            return $value->{ '@value' };
        }

        // Get the value for the specified language.
        if ( ! empty( $language ) && isset( $value->{ '@language' } ) && $language === $value->{ '@language' } ) {
            return $value->{ '@value' };
        }

    }

    // No value found with the provided specs.
    return null;

}

/**
 * Load a remote graph. A suffix is added automatically to the URL using the $wl_entity_view_suffix.
 *
 * @since 3.0.0
 *
 * @param string $url The URL.
 * @return null|object A graph instance or null if the JSON is invalid.
 */
function wl_jsonld_load_remote( $url ) {

    global $wl_entity_view_suffix;

    // TODO: validate the URI.

	// Use the caching method if it's loaded.
	if ( function_exists( 'wl_caching_remote_request' ) ) {
		$response = wl_caching_remote_request( $url . $wl_entity_view_suffix, array( 'method' => 'GET' ) );
	} else {
		$response = wp_remote_get( $url . $wl_entity_view_suffix );
	}

    $json     = json_decode( $response['body'] );

    // The json is invalid.
    if ( null === $json ) {
        return null;
    }

    return $json[0]->{ '@graph' }[0];

}