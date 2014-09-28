<?php

function wl_entity_view_shortcode( $atts, $content = null ) {

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'uri'    => wl_config_get_dataset_base_uri(), // The dataset base URI is the WordLift local dataset.
        'suffix' => '.json' // The suffix to add for remote queries.
    ), $atts );

    global $json;

    $url = $params['uri'] . get_query_var( WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );

    // TODO: validate the URI.

    $response = wp_remote_get( $url . $params['suffix'] );
    $json     = json_decode( $response['body'] );
    if ( ! is_array( $json ) ) {
        $json = array( $json );
    }

    return do_shortcode( $content );

}
add_shortcode( 'wl_entity_view', 'wl_entity_view_shortcode' );

function wl_entity_property_shortcode( $atts, $content = null ) {

    global $json;

    if ( empty( $json ) ) {
        return 'I need a wl_entity_view';
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'     => '',
        'language' => ''
    ), $atts );

    $value = wl_jsonld_get_property( $json, $params['name'], $params['language'] );
    $value = str_ireplace( "\n", "<br/>", $value );
    return $value;

}
add_shortcode( 'wl_entity_property', 'wl_entity_property_shortcode' );

function wl_entity_image_shortcode( $atts, $content = null ) {

    global $json;

    if ( empty( $json ) ) {
        return 'I need a wl_entity_view';
    }

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name'     => ''
    ), $atts );

    return '<img src="' . wl_jsonld_get_property( $json, $params['name'] ) . '" />';

}
add_shortcode( 'wl_entity_image', 'wl_entity_image_shortcode' );

function wl_jsonld_get_property( $jsonld, $name, $language = null ) {

    $values = $jsonld[0]->{'@graph'}[0]->{ $name };

    if ( empty( $language ) ) {

        if ( isset( $values[0]->{'@value'} ) ) {
            return $values[0]->{'@value'};
        }

        if ( isset( $values[0]->{'@id'} ) ) {
            return $values[0]->{'@id'};
        }
    }

    foreach ( $values as $value ) {
        if ( $language == $value->{'@language'} ) {
            return $value->{'@value'};
        }
    }

    return null;

}