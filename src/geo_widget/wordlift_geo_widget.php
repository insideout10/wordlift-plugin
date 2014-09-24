<?php

function wordlift_geo_widget_shortcode( $atts, $content = null ) {

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'width'     => '100%',
        'height'    => '300px',
        'latitude'  => 0.0,
        'longitude' => 0.0,
        'zoom'      => 5

    ), $atts );

    // Add leaflet css and library.
    wp_enqueue_style( 'leaflet_css', plugins_url( 'bower_components/leaflet/dist/leaflet.css', __FILE__ ) );
    wp_enqueue_script( 'leaflet_js', plugins_url( 'bower_components/leaflet/dist/leaflet.js', __FILE__ ) );

    wordlift_geo_widget_html(
        $params['width'],
        $params['height'],
        $params['latitude'],
        $params['longitude'],
        $params['zoom'],
        $content
    );

}
add_shortcode( 'wl_geo', 'wordlift_geo_widget_shortcode' );

function wl_geo_widget_layer_shortcode( $atts ) {

    // Extract attributes and set default values.
    $params = shortcode_atts( array(
        'name' => ''
    ), $atts );

    // Define the AJAX Url.
    $ajax_url = admin_url( 'admin-ajax.php?action=wl_sparql&slug=' . $params['name'] );
    $name_j   = json_encode( $params['name'] );

    echo <<<EOF
        $.ajax( '$ajax_url', {
            success: function( data ) {

                L.geoJson( data, {
                    onEachFeature: function (feature, layer) {
                        // does this feature have a property named popupContent?
                        if (feature.properties && feature.properties.popupContent) {
                            layer.bindPopup(feature.properties.popupContent);
                        }
                    }
                } ).addTo(map);

            }

        } );
EOF;

}
add_shortcode( 'wl_geo_layer', 'wl_geo_widget_layer_shortcode' );

function wordlift_geo_widget_html( $width, $height, $latitude, $longitude, $zoom, $content ) {

    // Create a unique Id for this widget.
    $div_id   = uniqid( 'wl-geo-' );

    echo <<<EOF
<div id="$div_id" style="width: $width; height: $height;"></div>

<script type="text/javascript">
    jQuery( function( $ ) {

        var map = L.map( '$div_id', {
            center: [$latitude, $longitude],
            zoom: $zoom
        } );

        L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
				'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
				'Imagery © <a href="http://mapbox.com">Mapbox</a>',
            id: 'examples.map-20v6611k'
        }).addTo(map);

EOF;

    // Run inner shortcodes.
    do_shortcode( $content );

    echo <<<EOF
    } );
</script>';
EOF;

}