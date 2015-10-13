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
        
        // Extract field constraints (numerosity, expected type)
        // Default constaints: accept one string.
        if( isset( $this->raw_custom_field['type'] ) ){
            $this->expected_wl_type = $this->raw_custom_field['type'];
        } else {
            $this->expected_wl_type = WL_DATA_TYPE_STRING;
        }
        
        $this->cardinality = 1;
        if( isset( $this->raw_custom_field['constraints'] ) ){
            
            $constraints = $this->raw_custom_field['constraints'];
            
            // Extract cardinality
            if( isset( $constraints['cardinality'] ) ) {
                $this->cardinality = $constraints['cardinality'];
            }
            
            // Which type of entity can we accept (e.g. Place, Event, ecc.)?
            if( $this->expected_wl_type === WL_DATA_TYPE_URI && isset( $constraints['uri_type'] ) ) {
                
                $expected_types = array();
                // We accept also children of this types
                $parent_expected_types = $constraints['uri_type'];

                if( !is_array( $parent_expected_types ) ){
                    $parent_expected_types = array( $parent_expected_types );
                }
                foreach ( $parent_expected_types as $term ){
                    $children = wl_entity_type_taxonomy_get_term_children( $term );
                    foreach( $children as $child ){
                        if( isset( $child->name ) ){
                            $expected_types[] = $child->name;
                        }
                    }
                }
                
                $this->expected_uri_type = array_unique( array_merge( $expected_types, $parent_expected_types ) ); 
            }
            
        }
    }
    
    /**
     * Return nonce HTML.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function print_nonce(){
        
        return wp_nonce_field( 'wordlift_' . $this->meta_name . '_entity_box', 'wordlift_' . $this->meta_name . '_entity_box_nonce', true, false );
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
        
        if( !isset( $_POST[ $nonce_name ] ) ){
            return false;
        }
        
		// Verify that the nonce is valid.
		return wp_verify_nonce( $_POST[ $nonce_name ], $nonce_verify );
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
     * Sanitize data before saving to DB. Default sanitization excludes empty values.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function sanitize_data( $values ){
        
        $sanitized_data = array();
        
        foreach( $values as $value ){
            $sanitized_value = $this->sanitize_data_filter( $value );
            if( !is_null( $sanitized_value ) ){
                $sanitized_data[] = $sanitized_value;
            }
        }
        
        $this->data = $sanitized_data;
    }
    
    /**
     * Sanitize a single value. Called from $this->sanitize_data. Default sanitization excludes empty values.
     * Overwrite this method in a child class to obtain custom behaviour.
     * 
     * @return mixed Returns sanitized value, or null.
     */
    public function sanitize_data_filter( $value ){
        
        if( !is_null( $value ) && $value !== '' ){         // do not use 'empty()' -> https://www.virendrachandak.com/techtalk/php-isset-vs-empty-vs-is_null/
            return $value;
        }
        return null;
    }
    
    /**
     * Save data to DB.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function save_data(){
        
        $entity_id = get_the_ID();
        
        // Take away old values
        delete_post_meta( $entity_id, $this->meta_name );
        
        // insert new values, respecting cardinality
        $single = ( $this->cardinality == 1 );
        foreach( $this->data as $value ){
            add_post_meta( $entity_id, $this->meta_name, $value, $single );
        }
    }
    
    /**
     * Returns the HTML tag that will contain the Field. By default the we return a <div> with data- attributes on cardinality and expected types.
     * It is useful to provide data for the JS scripts.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function html_wrapper_open(){
        $html = '';
        
        $html .= '<div class="wl-metabox" data-cardinality="' . $this->cardinality . '">';
        
        return $html;
    }
    
    /**
     * Returns Field HTML.
     * Overwrite this method in a child class to obtain custom behaviour.
     */
    public function html(){
        
        $html = '<h3>' . $this->label . '</h3>';
        
        if( empty( $this->data ) ){
            $html .= $this->html_input( '' );    // Will print an empty <input>
        } else {
            // print data loaded from DB
            $count = 0;
            foreach( $this->data as $value ){
                if( $count < $this->cardinality ) {
                    $html .= $this->html_input( $value );
                }
                $count++;
            }
        }
        
        // If cardiality allows it, print button to add new values.
        if( $this->cardinality > 1 ) {
            $html .= '<button>Add</button>';
        }
        
        return $html;
    }
    
    /**
     * Return a single <input> tag for the Field.
     * 
     * @param mixed $value Input value
     */
    public function html_input( $value ){
        return '<input type="text" id="' . $this->meta_name . '" name="wl_metaboxes[' . $this->meta_name . '][]" value="' . $value . '" style="width:100%" />';
    }
    
    /**
     * Returns closing for the wrapper HTML tag.
     */
    public function html_wrapper_close(){
        
        return '</div>';
    }
}

/**
 * From now on, child classes.
 */

class WL_Metabox_Field_uri extends WL_Metabox_Field {
    
    public function html_wrapper_open() {
        
        // The containing <div> contains info on cardinality and expected types
        $html = '<div class="wl-metabox" data-cardinality="' . $this->cardinality . '"';
        
        if( isset( $this->expected_uri_type ) && !is_null( $this->expected_uri_type ) ){
            
            if( is_array( $this->expected_uri_type ) ) {
                $html.= ' data-expected-types="' . implode( $this->expected_uri_type, ',') . '"';
            } else {
                $html.= ' data-expected-types="' . $this->expected_uri_type . '"';
            }
        }
        
        $html.= '>';
        
        return $html;
    }
    
    public function html() {
        
        // Prepare structure to host both labels and ids of the entities.
        $meta_values = array();

        foreach( $this->data as $default_entity_identifier ) {

            // The entity can be referenced as URI or ID.
            if ( is_numeric( $default_entity_identifier ) ) {
                $entity = get_post( $default_entity_identifier );
            } else {
                // It is an URI
                $entity = wl_get_entity_post_by_uri( $default_entity_identifier );
            }

            if( !is_null( $entity ) ){
                $meta_values[] = array(
                    'label' => $entity->post_title,
                    'value' => $entity->ID
                );
            } else {
                // No ID and no internal uri. Just leave as is.
                $meta_values[] = array(
                    'label' => $default_entity_identifier,
                    'value' => $default_entity_identifier
                );
            }
        }
        
        /*
         * Write saved values in page
         * The <input> tags host the meta values.
         * Each hosts one human readable value (i.e. entity name or uri)
         * and is accompained by an hidden <input> tag, the one passed to the server,
         * that contains its raw value (i.e. the uri or entity id)
         */
        $html = '<h3>' . $this->label . '</h3>';
        
        if( count( $meta_values ) === 0 ){
            $html .= '<div class="wl-autocomplete-wrapper">
                        <input type="text" class="' . $this->meta_name . ' wl-autocomplete" style="width:100%" />
                        <input type="hidden" class="' . $this->meta_name . '" name="wl_metaboxes[' . $this->meta_name . '][]" />
                    </div>';
        } else {
            $count = 0;
            foreach( $meta_values as $meta_value ){
                if( $count < $this->cardinality ) {
                    $label = $meta_value['label'];
                    $value = $meta_value['value'];

                    $html .= '<div class="wl-autocomplete-wrapper">
                                <input type="text" class="' . $this->meta_name . ' wl-autocomplete" value="' . $label . '" style="width:100%" />
                                <input type="hidden" class="' . $this->meta_name . '" name="wl_metaboxes[' . $this->meta_name . '][]" value="' . $value . '" />
                            </div>';
                }
                $count++;
            }
            
            // If cardiality allows it, print button to add new values.
            if( $this->cardinality > 1 ) {
                $html .= '<button>Add</button>';
            }
        }

        return $html;
        
        
        
        
        
        
        
        
        if( empty( $this->data ) ){
            $html .= $this->html_input( '' );    // Will print an empty <input>
        } else {
            // print data loaded from DB
            $count = 0;
            foreach( $this->data as $value ){
                if( $count < $this->cardinality ) {
                    $html .= $this->html_input( $value );
                }
                $count++;
            }
        }
        
        
        
        return $html;
    }
}

class WL_Metabox_Field_date extends WL_Metabox_Field {
    
    public function html(){
        
        $html = '';
        
        $html .= '<h3>' . $this->label . '</h3>';
        
        foreach( $this->data as $date){
            
            $pickerDate  = '';
            if( !empty( $date ) ){
                $pickerDate = date( 'Y/m/d H:i', strtotime( $date ) );
            }
            $pickerDate = esc_attr( $pickerDate );

            // Two input fields, one for the datetimepicker and another to store the time in the required format
            $html .= '<input type="text" class="' . $this->meta_name . '" value="' . $pickerDate . '" style="width:100%" />';
            $html .= '<input type="hidden" class="' . $this->meta_name . '_hidden" name="wl_metaboxes[' . $this->meta_name . ']" value="' . $date . '" style="width:100%" />';
        }
            
        $html .= "<script type='text/javascript'>
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
        
        return $html;
    }
}

class WL_Metabox_Field_sameas extends WL_Metabox_Field {

    public function __construct( $args ) {
        parent::__construct( $args['sameas'] );
    }
    
    /**
     * Only accept URIs
     */
    public function sanitize_data_filter( $value ) {

        if( !is_null( $value ) && $value !== '' && filter_var($value, FILTER_VALIDATE_URL) ){
            return $value;
        }
        return null;
    }
}