<?php

class WL_Metabox {
    
    protected $fields = array();
    
    public function __construct( $args ) {
        
        // TODO: default args        
        
        // register metabox
        add_meta_box(
            'uniqueMetaboxId', 'WL_METABOXXX!', array( $this, 'html'), WL_ENTITY_TYPE_NAME, 'normal', 'high', array(1,2,3,4)
        );
    }
    
    public function add_field( $args ){
        
        // TODO default args
        
        $field_class = 'WL_Metabox_Field';
        
        $this->fields[] = new $field_class( $args );
    }
    
    public function html( $params ){
        
        foreach( $this->fields as $field ) {
            
            $field->html();
        }
    }
    
}

class WL_Metabox_Field {
    
    protected $cardinality;
    protected $expected_type;
    protected $expected_uri_type;
    protected $meta_keys;
    
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
    }
    
    public function get_data(){
        // load data from DB
    }
    
    public function save_data(){
        // sanitize and save data
    }
    
    public function html(){
        echo '<h3>my field</h3>';
    }
}

