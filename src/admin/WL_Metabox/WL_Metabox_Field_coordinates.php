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
        $entity_id = get_the_ID();
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
        $coords = $this->data;

        // Print input fields
        $html .= '<label for="wl_place_lat">' . __( 'Latitude', 'wordlift' ) . '</label>';
        $html .= '<input type="text" id="wl_place_lat" name="wl_metaboxes[coordinates][]" value="' . $coords['latitude'] . '" style="width:100%" />';

        $html .= '<label for="wl_place_lon">' . __( 'Longitude', 'wordlift' ) . '</label>';
        $html .= '<input type="text" id="wl_place_lon" name="wl_metaboxes[coordinates][]" value="' . $coords['longitude'] . '" style="width:100%" />';

        // Show Leaflet map to pick coordinates
        $html .= "<div id='wl_place_coords_map'></div>";
        $html .= "<script type='text/javascript'>
        $ = jQuery;
        $(document).ready(function(){
            $('#wl_place_coords_map').width('100%').height('200px');
            var wlMap = L.map('wl_place_coords_map').setView([" . $coords['latitude'] . "," . $coords['longitude'] . "], 9);

            L.tileLayer( 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                { attribution: '&copy; <a href=http://osm.org/copyright>OpenStreetMap</a> contributors'}
            ).addTo( wlMap );

            var marker = L.marker([" . $coords['latitude'] . "," . $coords['longitude'] . "]).addTo( wlMap );

            function refreshCoords(e) {
                $('#wl_place_lat').val( e.latlng.lat );
                $('#wl_place_lon').val( e.latlng.lng );
                marker.setLatLng( e.latlng )
            }

            wlMap.on('click', refreshCoords);
        });
        </script>";
        
        $this->html_wrapper_close();
        
        return $html;
    }
    
    public function save_data( $coords ) {
        
        $this->sanitize_data( $coords );
        
        $entity_id = get_the_ID();
        
        // Take away old values
        delete_post_meta( $entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE );
        delete_post_meta( $entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE );
        
        $latitude = $this->data[0];
        $longitude = $this->data[1];
        
        // insert new coordinate values
        add_post_meta( $entity_id, WL_CUSTOM_FIELD_GEO_LATITUDE, $latitude, true );
        add_post_meta( $entity_id, WL_CUSTOM_FIELD_GEO_LONGITUDE, $longitude, true );
    }
    
    /**
     * Only accept float numbers
     */
    public function sanitize_data_filter( $value ) {

        if( !is_null( $value ) && $value !== '' && is_numeric( $value ) ){
            return $value;
        }
        return 0.0;
    }
}

