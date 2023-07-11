<?php

namespace Wordlift\Metabox\Field;

use Wordlift\Metabox\Field\Store\Store_Factory;
use Wordlift_Schema_Service;

class Wl_Metabox_Field_Coordinates extends Wl_Metabox_Field {

	public function __construct( $args, $id, $type ) {

		// Just set up the necessary info without calling the parent constructor.
		// TODO: write a parent class for grouped properties
		parent::__construct( $args, $id, $type );

		// we use 'coordinates' to namespace the Field in $_POST data.
		// In  the DB the correct meta names will be used.
		$this->meta_name = 'coordinates';
	}

	public function get_data() {
		$instance   = Store_Factory::get_instance( $this->type );
		$latitude   = $instance::get_data( $this->id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE );
		$longitude  = $instance::get_data( $this->id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE );
		$this->data = array(
			'latitude'  => isset( $latitude[0] ) && is_numeric( $latitude[0] ) ? $latitude[0] : '',
			'longitude' => isset( $longitude[0] ) && is_numeric( $longitude[0] ) ? $longitude[0] : '',
		);
	}

	public function html() {

		// Open main <div> for the Field
		$html = $this->html_wrapper_open();

		// Label
		$html .= '<h3>coordinates</h3>';

		// print nonce
		$html .= $this->html_nonce();

		// Get coordinates
		$data = $this->data;
		// TODO: We temporary use here 0,0 as default coordinates for the marker, but if no coordinates are given we
		// want to use the current user location for the marker.
		$coordinates = ( ! empty( $data['latitude'] ) && ! empty( $data['longitude'] ) ? sprintf( '[%f,%f]', (float) $data['latitude'], (float) $data['longitude'] ) : '[0,0]' );
		$map_init    = '[0,0]' === $coordinates
			? 'locate( {setView: true, maxZoom: 16} )'
			: sprintf( 'setView( [%f,%f], 9 )', (float) $data['latitude'], (float) $data['longitude'] );

		// Print input fields
		$html .= '<label for="wl_place_lat">' . esc_html__( 'Latitude', 'wordlift' ) . '</label>';
		$html .= '<input type="text" id="wl_place_lat" name="wl_metaboxes[coordinates][]" value="' . esc_attr( $data['latitude'] ) . '" style="width:100%" />';

		$html .= '<label for="wl_place_lon">' . esc_html__( 'Longitude', 'wordlift' ) . '</label>';
		$html .= '<input type="text" id="wl_place_lon" name="wl_metaboxes[coordinates][]" value="' . esc_attr( $data['longitude'] ) . '" style="width:100%" />';

		// Show Leaflet map to pick coordinates
		$element_id = uniqid( 'wl-geo-map-' );
		$html      .= "
<div id=\"$element_id\"></div>

<script type=\"text/javascript\">

	window.addEventListener( 'load', function () {

		(function ($) {
	
			$('#$element_id').width('100%').height('200px');
	
			var wlMap = L.map('$element_id').$map_init;
	
			L.tileLayer( 'https://{s}.tile.osm.org/{z}/{x}/{y}.png',
			    { attribution: '&copy; <a href=https://osm.org/copyright>OpenStreetMap</a> contributors'}
			).addTo( wlMap );
	
			var marker = L.marker($coordinates).addTo( wlMap );
	
			function refreshCoords(e) {
			    $('#wl_place_lat').val( e.latlng.lat );
			    $('#wl_place_lon').val( e.latlng.lng );
			    marker.setLatLng( e.latlng )
			}
	
			wlMap.on('click', refreshCoords);
	
		})(jQuery);
	})
</script>";

		$html .= $this->html_wrapper_close();

		return $html;
	}

	public function save_data( $coords ) {

		$data = $this->sanitize_data( $coords );

		$instance = Store_Factory::get_instance( $this->type );
		// Take away old values
		$instance::delete_meta( $this->id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE );
		$instance::delete_meta( $this->id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE );

		$latitude  = $data[0];
		$longitude = $data[1];
		// insert new coordinate values
		if ( ! empty( $latitude ) && ! empty( $longitude ) ) {
			$instance::add_meta( $this->id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $latitude, true );
			$instance::add_meta( $this->id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $longitude, true );
		}

	}

	/**
	 * Only accept float numbers
	 */
	public function sanitize_data_filter( $value ) {

		// DO NOT set latitude/longitude to 0/0 as default values. It's a specific place on the globe:
		// "The zero/zero point of this system is located in the Gulf of Guinea about 625 km (390 mi) south of Tema, Ghana."
		if ( ! is_numeric( $value ) ) {
			return '';
		}

		return $value;
	}
}

