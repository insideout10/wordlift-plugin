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
	wp_enqueue_style( 'leaflet_css', dirname( dirname( plugin_dir_url( __FILE__ ) ) ) . '/bower_components/leaflet/dist/leaflet.css' );
	wp_enqueue_script( 'leaflet_js', dirname( dirname( plugin_dir_url( __FILE__ ) ) ) . '/bower_components/leaflet/dist/leaflet.js' );

	ob_start(); // Collect the buffer.
	wordlift_geo_widget_html(
		$params['width'],
		$params['height'],
		$params['latitude'],
		$params['longitude'],
		$params['zoom'],
		$content
	);

	// Return the accumulated buffer.
	return ob_get_clean();

}

add_shortcode( 'wl_geo', 'wordlift_geo_widget_shortcode' );

function wl_geo_widget_layer_shortcode( $atts ) {

	// Extract attributes and set default values.
	$params = shortcode_atts( array(
		'name'  => '',
		'label' => ''
	), $atts );

	// Return if a SPARQL Query name hasn't been provided.
	if ( empty( $params['name'] ) ) {
		return;
	}

	// Set the layer label.
	$label_j = json_encode( empty( $params['label'] ) ? $params['name'] : $params['label'] );

	// Define the AJAX Url.
	$ajax_url = admin_url( 'admin-ajax.php?action=wl_sparql&format=geojson&slug=' . urlencode( $params['name'] ) );

	echo <<<EOF

        $.ajax( '$ajax_url', {
            success: function( data ) {

                controlLayer.addOverlay( L.geoJson( data, {
                    onEachFeature: function (feature, layer) {
                        // does this feature have a property named popupContent?
                        if (feature.properties && feature.properties.popupContent) {
                            layer.bindPopup(feature.properties.popupContent);
                        }
                    }
                } ).addTo(map), $label_j);

            },
            error: function( xhr, status, error ) {
                console.log( xhr );
                console.log( status );
                console.log( error );
            }

        } );
EOF;

}

add_shortcode( 'wl_geo_layer', 'wl_geo_widget_layer_shortcode' );


function wl_geo_widget_marker_shortcode( $atts ) {

	// Extract attributes and set default values.
	$params = shortcode_atts( array(
		'latitude'  => null,
		'longitude' => null
	), $atts );

	// Return if either latitude or longitude haven't been provided.
	if ( empty( $params['latitude'] ) || empty( $params['longitude'] ) ) {
		return;
	}

	$latitude_j  = json_encode( $params['latitude'] );
	$longitude_j = json_encode( $params['longitude'] );

	echo <<<EOF

		L.marker([$latitude_j, $longitude_j]).addTo(map);
EOF;

}

add_shortcode( 'wl_geo_marker', 'wl_geo_widget_marker_shortcode' );


function wordlift_geo_widget_html( $width, $height, $latitude, $longitude, $zoom, $content ) {

	// Create a unique Id for this widget.
	$div_id = uniqid( 'wl-geo-' );

	echo <<<EOF
<div id="$div_id" style="width: $width; height: $height;"></div>

<script type="text/javascript">
    jQuery( function( $ ) {

        // Create the map.
        var map = L.map( '$div_id', {
            center: [$latitude, $longitude],
            zoom: $zoom
        } );

        // Create an instance of the control layer.
        var controlLayer = L.control.layers(null, null).addTo(map);

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
</script>
EOF;

}