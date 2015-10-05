<?php

class WL_Metabox {
    
    protected $fields;
    
    public function __construct( $args ) {
        
        // TODO: default args
        
        // Add hooks to print metaboxes and save submitted data.
        add_action( 'add_meta_boxes', array( &$this, 'add_main_metabox' ));
        add_action( 'wl_linked_data_save_post', array( &$this, 'save_form_data' ) );
        
        // TODO: enqueue JS and CSS for the fields
        $this->enqueue_scripts_and_styles();
        
    }
    
    // Note: the first function that calls this method will instantiate the fields.
    public function instantiate_fields( $entity_id ){
        
        // This function must be called only once. Not called from the constructor because WP hooks have an unpredicatable order
        if( isset( $this->fields ) ) {
            return;
        }
        
        $entity_id = get_the_ID();
        $entity_type = wl_entity_taxonomy_get_custom_fields( $entity_id );
        if ( isset( $entity_id ) && is_numeric( $entity_id ) && isset( $entity_type ) ) {

            // In some special case, properties must be grouped in one metabox (e.g. coordinates) or dealed with custom methods.
                    // We divide metaboxes in two groups:
                    // - simple: accept values for one property
                    // - grouped: accept values for more properties, or for one property that needs a specific metabox.
            $metaboxes         = wl_entities_metaboxes_group_properties_by_input_field( $entity_type );
            $simple_metaboxes  = $metaboxes[0];
            $grouped_metaboxes = $metaboxes[1];

            // Loop over simple entity properties
            foreach ( $simple_metaboxes as $key => $property ) {

                // Don't present to the user the full schema name, just the slug
                $property_slug_name = explode( '/', $property['predicate'] );
                $property_slug_name = end( $property_slug_name );

                // Metabox title
                $title = __( 'Edit', 'wordlift' ) . ' ' . get_the_title() . ' ' . __( $property_slug_name, 'wordlift' );

                // Info passed to the metabox
                $info         = array();
                $info[ $key ] = $property;

                $unique_metabox_name = uniqid( 'wl_metabox_' );

                //add_meta_box(
                //	$unique_metabox_name, $title, 'wl_entities_' . $property['type'] . '_box_content', $post_type, 'normal', 'high', $info
                //);

                // COOOOOOOOOOOOOOOL //////
                $this->add_field( $info );
            }

            // Loop over grouped properties
            foreach ( $grouped_metaboxes as $key => $property ) {

                // Metabox title
                $title = __( 'Edit', 'wordlift' ) . ' ' . get_the_title() . ' ' . __( $key, 'wordlift' );

                $unique_metabox_name = uniqid( 'wl_metabox_' );

                //add_meta_box(
                //    $unique_metabox_name, $title, 'wl_entities_' . $key . '_box_content', WL_ENTITY_TYPE_NAME, 'normal', 'high'
                //);
                
                // TODO: coordinates xD
                //$this->add_field();

            }
            
        }
    }
    
    public function add_main_metabox(){
        
        // Add main metabox (will print also the inner fields)
        add_meta_box( 'uniqueMetaboxId', 'WL_METABOXXX!', array( $this, 'html'), WL_ENTITY_TYPE_NAME, 'normal', 'high' );        
    }
    
    public function add_field( $args ){
        
        // TODO default args
        
        // Which field?
        $meta = key( $args );
        $field_class = 'WL_Metabox_Field_' . $args[$meta]['type'];
        
        // Call apropriate constructor
        $this->fields[] = new $field_class( $args );
    }
    
    public function html( $entity ){
        
        $this->instantiate_fields( $entity->ID );
        
        foreach( $this->fields as $field ) {
            
            $field->print_nonce( 'TODOOOOOOOOOOOOOOO' );
            
            // write custom HTML
            $field->html();
            echo '<hr>';
        }
    }
    
    public function save_form_data( $post_id ){

        $this->instantiate_fields( $post_id );
        
        foreach( $this->fields as $field ) {
            
            // TODO: verify NONCE
            $field->verify_nonce();
        
            // TODO: pass only pertinent data            
            $field->save_data( array('CIAOOOOOOOOOO') );
        }
    }
    
    // print on page all the js and css the fields will need
    public function enqueue_scripts_and_styles(){
        
        // dateTimePicker
        wp_enqueue_style( 'datetimepickercss', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.css', __FILE__ ) );
        wp_enqueue_script( 'datetimepickerjs', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.js', __FILE__ ) );
        
        // Add AJAX autocomplete to facilitate metabox editing
        wp_enqueue_script('wl-entity-metabox-utility', plugins_url( 'js-client/wl_entity_metabox_utilities.js', __FILE__ ) );
        wp_localize_script( 'wl-entity-metabox-utility', 'wlEntityMetaboxParams', array(
                'ajax_url'          => admin_url('admin-ajax.php'),
                'action'            => 'entity_by_title'
            )
        );
    }
}