<?php

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

