<?php

abstract class WL_Metabox_Field_Abstract {
    
    protected $raw_custom_field;
    protected $meta_name;
    protected $predicate;
    protected $label;
    protected $expected_wl_type;
    protected $expected_uri_type;
    protected $cardinality;
    protected $data;

    
    public function __construct( $args ) {

        // Save a copy of the custom field's params
        $this->raw_custom_field = reset( $args );
        
        // Extract meta name (post_meta key for the DB)
        $this->meta_name = key( $args );
        
        // Extract linked data predicate
        $this->predicate = $this->raw_custom_field['predicate'];
        
        // Extract human readable label
        $exploded_predicate = explode( '/', $this->predicate );
        $this->label = end( $exploded_predicate );
        
        $this->expected_wl_type = $this->raw_custom_field['type'];
        
        // Extract field constraints (numerosity, expected type)
        // Default constaints: accept one string.
        $this->cardinality = 1;
        $this->expected_wl_type = WL_DATA_TYPE_STRING;
        if( isset( $this->raw_custom_field['constraints'] ) ){
            
            $constraints = $this->raw_custom_field['constraints'];
            
            if( $this->expected_wl_type === WL_DATA_TYPE_URI ) {
                $this->expected_uri_type = $constraints['uri_type'];
            }
            
            // Extract cardinality
            if( isset( $constraints['cardinality'] ) ) {
                $this->cardinality = $constraints['cardinality'];
            }
            
        }
    }
    
    public function print_nonce(){
        // write NONCE
        wp_nonce_field( 'wordlift_' . $this->meta_name . '_entity_box', 'wordlift_' . $this->meta_name . '_entity_box_nonce' );
    }
    
    public function verify_nonce(){
        
		$nonce_name   = 'wordlift_' . $this->meta_name . '_entity_box_nonce';
		$nonce_verify = 'wordlift_' . $this->meta_name . '_entity_box';
		if ( ! isset( $nonce_name ) ) {
			return false;
		}

		// Verify that the nonce is valid.
		return wp_verify_nonce( $nonce_name, $nonce_verify );
    }
    
    // The following methods MUST be defined in child class.
    
    // Load data from DB
    public function get_data() {

        $single = ( $this->cardinality > 1 );       
        $this->data = get_post_meta( get_the_ID(), $this->meta_name, $single );
    }
    
    // Save data to DB
    abstract public function save_data( $values );
    
    // Print metabox in page
    abstract public function html();
}

class WL_Metabox_Field_string extends WL_Metabox_Field_Abstract {
    
    public function __construct($args) {
        // Call parent constructor
        parent::__construct($args);
    }
    
    public function get_data(){
        // load data from DB
    }
    
    public function save_data( $values ){
        // sanitize and save data
        wl_write_log('piedo saves');
        wl_write_log( $_POST );
    }
    
    public function html(){
        
        echo '<h3>' . $this->label . '</h3>';
        var_dump($this);
        echo '<input>';
    }
}

class WL_Metabox_Field_uri extends WL_Metabox_Field_Abstract {
   
    public function __construct($args) {
        // Call parent constructor
        parent::__construct($args);
    }
    
    public function get_data(){
        // load data from DB
    }
    
    public function save_data( $values ){
        // sanitize and save data
        wl_write_log('piedo saves');
        wl_write_log( $_POST );
    }
    
    public function html(){
        
        echo '<h3>' . $this->label . '</h3>';
        var_dump($this);
        echo '<input>';
    }
}

class WL_Metabox_Field_date extends WL_Metabox_Field_Abstract {
   
    public function __construct($args) {
        // Call parent constructor
        parent::__construct($args);
    }
    
    public function save_data( $values ){
        // sanitize and save data
        wl_write_log('piedo saves');
        wl_write_log( $values );
    }
    
    public function html(){
        
        echo '<h3>' . $this->label . '</h3>';

        $date = esc_attr( $this->data );

        $pickerDate  = '';
        // Give the timepicker the date in its favourite format.
        if ( ! empty( $date ) ) {
            $pickerDate = date( 'Y/m/d H:i', strtotime( $date ) );
        }

        // Two input fields, one for the datetimepicker and another to store the time in the required format
        echo '<input type="text" id="' . $this->meta_name . '" value="' . $pickerDate . '" style="width:100%" />';
        echo '<input type="hidden" id="' . $this->meta_name . '_hidden" name="wl_metaboxes[' . $this->meta_name . ']" value="' . $date . '" style="width:100%" />';

        echo "<script type='text/javascript'>
        $ = jQuery;
        $(document).ready(function() {

            var lastDateTimePickerClicked;

            $('#" . $this->meta_name . "').datetimepicker({
                onChangeDateTime:function(dp, input){
                    // format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
                    var currentDate = input.val();
                    currentDate = currentDate.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z')
                    // store value to save in the hidden input field
                    $('#" . $this->meta_name . "_hidden').val( currentDate );
                }
            });
        });
        </script>";
    }
}