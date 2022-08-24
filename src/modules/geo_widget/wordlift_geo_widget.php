<?php
/**
 * Geo Widget.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/modules/geo_widget
 */

function wordlift_geo_widget_shortcode( $atts, $content = null ) {

	// Extract attributes and set default values.
	$params = shortcode_atts( array(
		'width'     => '100%',
		'height'    => '300px',
		'latitude'  => 0.0,
		'longitude' => 0.0,
		'zoom'      => 5,

	), $atts );

	// Add leaflet css and library.
	wl_enqueue_leaflet( true );

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
