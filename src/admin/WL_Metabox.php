<?php

class WL_Metabox {
    
    protected $fields = array();
    
    public function __construct( $args ) {
        
        // TODO: default args        
        
        // register metabox
        add_meta_box(
            'uniqueMetaboxId', 'WL_METABOXXX!', array( $this, 'html'), WL_ENTITY_TYPE_NAME, 'normal', 'high', array(1,2,3,4)
        );
        
        // TODO: hook to form submission
        add_action( 'wl_linked_data_save_post', array( $this, 'save_form_data' ) );
        
        // TODO: enqueue scriptz
    }
    
    public function add_field( $args ){
        
        // TODO default args
        
        // build field
        $field_class = 'WL_Metabox_Field';
        $this->fields[] = new $field_class( $args );
    }
    
    public function html(){
        
        foreach( $this->fields as $field ) {
            
            // write custom HTML
            $field->html();
            echo '<hr>';
        }
    }
    
    public function save_form_data(){
        
        // TODO: verify NONCE
        
        foreach( $this->fields as $field ) {
            
            // TODO: pass pertinent data            
            $field->save_data( $data );
        }
    }
}

class WL_Metabox_Field {
    
    protected $predicate;
    protected $cardinality;
    protected $expected_type;
    protected $expected_uri_type;
    protected $meta_names;
    protected $raw_custom_fields;
    
    public function __construct( $args ) {
        
        // TODO: default args
        
        $defaults = array(
            'cardinality'       => 1,
            'expected_type'     => WL_DATA_TYPE_STRING,
            'expected_uri_type' => null,
            'meta_keys'         => array(),
            'display_cb'        => null,
            'sanitation_cb'     => null
        );
        
        $this->raw_custom_fields = array_values( $args );
        $this->meta_names = array_keys( $args );
        $this->predicate = $this->raw_custom_fields[0]['predicate'];
    }
    
    public function get_data(){
        // load data from DB
    }
    
    public function save_data( $values ){
        // sanitize and save data
        wl_write_log('piedo saves');
        wl_write_log( $values );
    }
    
    public function html(){
        
        // write NONCE
        wp_nonce_field( 'wordlift_' . $this->meta_names[0] . '_entity_box', 'wordlift_' . $this->meta_names[0] . '_entity_box_nonce' );
        
        echo '<h3>' . $this->predicate . '</h3>';
        print_r( $this->raw_custom_fields );
        echo '<input>';
    }
}

