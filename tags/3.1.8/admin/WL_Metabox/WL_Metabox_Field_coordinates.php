<?php


class WL_Metabox_Field_coordinates extends WL_Metabox_Field {

	public function __construct( $args ) {

		// Just set up the necessary info without calling the parent constructor.
		// TODO: write a parent class for grouped properties

		// we use 'coordinates' to namespace the Field in $_POST data.
		// In  the DB the correct meta names will be used.
		$this->meta_name = 'coordinates';
	}

	public function get_data() {
		$entity_id  = get_the_ID();
		$this->data = wl_get_coordinates( $entity_id );
	}

	public function html() {

		// Open main <div> for the Field
		$html = $this->html_wrapper_open();

		// Label
		$html .= '<h3>coordinates</h3>';

		// print nonce
		$html .= $this->html_nonce();

		// Get coordinates
		$data        = $this->data;
		// TODO: We temporary use here 0,0 as default coordinates for the marker, but if no coordinates are given we
		// want to use the current user location for the marker.
		$coordinates = ( ! empty( $data['latitude'] ) && ! empty( $data['longitude'] ) ? sprintf( '[%f,%f]', $data['latitude'], $data['longitude'] ) : '[0,0]' );
		$map_init    = '[0,0]' === $coordinates
			? 'locate( {setView: true, maxZoom: 16} )'
			: sprintf( "setView( [%f,%f], 9 )", $data['latitude'], $data['longitude'] );

		// Print input fields
		$html .= '<label for="wl_place_lat">' . __( 'Latitude', 'wordlift' ) . '</label>';
		$html .= '<input type="text" id="wl_place_lat" name="wl_metaboxes[coordinates][]" value="' . $data['latitude'] . '" style="width:100%" />';

		$html .= '<label for="wl_place_lon">' . __( 'Longitude', 'wordlift' ) . '</label>';
		$html .= '<input type="text" id="wl_place_lon" name="wl_metaboxes[coordinates][]" value="' . $data['longitude'] . '" style="width:100%" />';

		// Show Leaflet map to pick coordinates
		$element_id = uniqid( 'wl-gep-map-' );
		$html .= <<<EOF

<div id="$element_id"></div>

<script type="text/javascript">

	(function ($) {

		$('#$element_id').width('100%').height('200px');

		var wlMap = L.map('$element_id').$map_init;

		L.tileLayer( 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
		    { attribution: '&copy; <a href=http://osm.org/copyright>OpenStreetMap</a> contributors'}
		).addTo( wlMap );

		var marker = L.marker($coordinates).addTo( wlMap );

		function refreshCoords(e) {
		    $('#wl_place_lat').val( e.latlng.lat );
		    $('#wl_place_lon').val( e.latlng.lng );
		    marker.setLatLng( e.latlng )
		}

		wlMap.on('click', refreshCoords);

	})(jQuery);

</script>
EOF;


		$html .= $this->html_wrapper_close();

		return $html;
	}

	public function save_data( $coords ) {

		$this->sanitize_data( $coords );

		$entity_id = get_the_ID();

		// Take away old values
		delete_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE );
		delete_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE );

		$latitude  = $this->data[0];
		$longitude = $this->data[1];

		// insert new coordinate values
		if ( ! empty( $latitude ) && ! empty( $longitude ) ) {
			add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $latitude, true );
			add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $longitude, true );
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

