<?php

/**
 * All custom WL_Metabox_Filed(s) must extend this class.
 * This class deals with saving the most basic data type, strings.
 * Use the methods that are useful or overwrite them if you need custom behaviour.
 */
class WL_Metabox_Field {
    
    public $meta_name;
    protected $raw_custom_field;
    protected $predicate;
    protected $label;
    protected $expected_wl_type;
    protected $expected_uri_type;
    protected $cardinality;
    protected $data;

    /**
     * Constructor. Recevies.... TODO write docs
     */
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
    
    /**
     * Print nonce in page.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function print_nonce(){
        // write NONCE
        wp_nonce_field( 'wordlift_' . $this->meta_name . '_entity_box', 'wordlift_' . $this->meta_name . '_entity_box_nonce' );
    }
    
    /**
     * Verify nonce.
     * Overwrite this method in a child class to obtain custom behaviour.
     * 
     * @return boolean Nonce verification
     */
    public function verify_nonce(){
        
		$nonce_name   = 'wordlift_' . $this->meta_name . '_entity_box_nonce';
		$nonce_verify = 'wordlift_' . $this->meta_name . '_entity_box';
		if ( ! isset( $nonce_name ) ) {
			return false;
		}

		// Verify that the nonce is valid.
		return wp_verify_nonce( $nonce_name, $nonce_verify );
    }
    
    /**
     * Load data from DB.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function get_data() {
      
        $data = get_post_meta( get_the_ID(), $this->meta_name );
        
        // Values are always contained in an array (makes it easier to manage cardinality)
        if( !is_array( $data ) ){
            $data = array( $data );
        }
        
        $this->data = $data;
    }
    
    /**
     * Sanitize and save data to DB.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function save_data( $values ){
        
        $entity_id = get_the_ID();
        
        // Take away old values
        delete_post_meta( $entity_id, $this->meta_name );
        
        // insert new values, respecting cardinality
        $single = ( $this->cardinality == 1 );
        foreach( $values as $value ){
            if( !is_null( $value ) && $value !== '' ){         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/
                add_post_meta( $entity_id, $this->meta_name, $value, $single );
            }
        }
    }
    
    /**
     * Print metabox in page.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function html(){
        
        echo '<h3>' . $this->label . '</h3>';
        var_dump($this->data);
        
        if( empty( $this->data ) ){
            $this->html_input( '' );    // Will print an empty <input>
        } else {
            // print data loaded from DB
            $count = 0;
            foreach( $this->data as $value ){
                if( $count < $this->cardinality ) {
                    $this->html_input( $value );
                }
                $count++;
            }
        }
        
        // If cardiality allows it, print button to add new values.
        if( $this->cardinality > 1 ) {
            echo '<button>Add</button>';
        }
    }
    
    /**
     * Print a single <input> tag for the Field.
     * 
     * @param mixed $value Input value
     */
    public function html_input( $value ){
        echo '<input type="text" id="' . $this->meta_name . '" name="wl_metaboxes[' . $this->meta_name . '][]" value="' . $value . '" style="width:100%" />';
    }
}

/**
 * From now on, child classes.
 */

class WL_Metabox_Field_uri extends WL_Metabox_Field {
   
}

class WL_Metabox_Field_date extends WL_Metabox_Field {
    
    public function html(){
        
        echo '<h3>' . $this->label . '</h3>';
        
        var_dump($this->data);
        foreach( $this->data as $date){
            
            $pickerDate  = '';
            if( !empty( $date ) ){
                $pickerDate = date( 'Y/m/d H:i', strtotime( $date ) );
            }
            $pickerDate = esc_attr( $pickerDate );

            // Two input fields, one for the datetimepicker and another to store the time in the required format
            echo '<input type="text" class="' . $this->meta_name . '" value="' . $pickerDate . '" style="width:100%" />';
            echo '<input type="hidden" class="' . $this->meta_name . '_hidden" name="wl_metaboxes[' . $this->meta_name . ']" value="' . $date . '" style="width:100%" />';
        }
            
        echo "<script type='text/javascript'>
        $ = jQuery;
        $(document).ready(function() {

            var lastDateTimePickerClicked;

            $('." . $this->meta_name . "').datetimepicker({
                onChangeDateTime:function(dp, input){
                    // format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
                    var currentDate = input.val();
                    currentDate = currentDate.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z')
                    // store value to save in the hidden input field
                    $('." . $this->meta_name . "_hidden').val( currentDate );
                }
            });
        });
        </script>";
    }
}