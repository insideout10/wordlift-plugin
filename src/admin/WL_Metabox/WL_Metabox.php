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
    
    /**
     * Add a callback to print the metabox in page.
     * Wordpress will fire the $this->html() callback at the right time.
     */
    public function add_main_metabox(){
        
        // Add main metabox (will print also the inner fields)
        add_meta_box( 'uniqueMetaboxId', get_the_title() . ' properties', array( $this, 'html'), WL_ENTITY_TYPE_NAME, 'normal', 'high' );        
    }
    
    /**
     * Called from WP to print the metabox content in page.
     * 
     * @param WP_Post $entity
     */
    public function html( $entity ){
        
        // Build the fields we need to print.
        $this->instantiate_fields( $entity->ID );
        
        $html = '';
        
        // Loop over the fields
        foreach( $this->fields as $field ) {
            
            // load data from DB (values will be available in $field->data)
            $field->get_data();
            
            /*
             * opens the HTML tag that will contain the Field HTML.
             * By default, it's a <div> containing data- attributes on cardinality and expected types.
             * Overwrite if you want to put a custom wrapper.
             */
            $html .= $field->html_wrapper_open();
            
            // print nonce
            $html .= $field->print_nonce();
            
            // print field HTML
            $html .= $field->html();
            
            // Close the HTML wrapper
            $html .= $field->html_wrapper_close();
            
            $html .= '<hr>';
        }
        
        // Echo Fields in page.
        echo $html;
    }
    
    /**
     * Read the WL <-> Schema mapping and build the Fields for the entity being edited.
     * 
     * @param int $entity_id
     * 
     * Note: the first function that calls this method will instantiate the fields.
     * Why it isn't called from the constructor? Because we need to hook this process as late as possible.
     */
    public function instantiate_fields( $entity_id ){
        
        // This function must be called only once. Not called from the constructor because WP hooks have a rococo ordering
        if( isset( $this->fields ) ) {
            return;
        }
        
        $entity_type = wl_entity_taxonomy_get_custom_fields( $entity_id );
        
        if ( isset( $entity_type ) ) {

            /**
             * In some special case, properties must be grouped in one field (e.g. coordinates) or dealed with custom methods.
             * We must divide fields in two groups:
             * - simple: accept values for one property
             * - grouped: accept values for more properties, or for one property that needs a specific metabox.
             */
            $metaboxes         = wl_entities_metaboxes_group_properties_by_input_field( $entity_type );
            $simple_metaboxes  = $metaboxes[0];
            $grouped_metaboxes = $metaboxes[1];

            // Loop over simple entity properties
            foreach ( $simple_metaboxes as $key => $property ) {

                // Info passed to the metabox
                $info         = array();
                $info[ $key ] = $property;

                // Build the requested field as WL_Metabox_Field_ object
                $this->add_field( $info );
            }

            // Loop over grouped properties
            foreach ( $grouped_metaboxes as $key => $property ) {
                
                // Info passed to the metabox
                $info         = array();
                $info[ $key ] = $property;

                // Build the requested field as WL_Metabox_Field_ object
                $this->add_field( $info, true );
            }
            
        }
    }
    
    
    public function add_field( $args, $grouped=false ){
        
        if( $grouped ) {
            // Special fields (sameas, coordinates, etc.)
            
            // Build Field with a custom class (e.g. WL_Metabox_Field_date)
            $field_class = 'WL_Metabox_Field_' . key($args);           
            
        } else {
            // Simple fields (string, uri, boolean, etc.)
            
            // Which field? We want to use the class that is specific for the field.
            $meta = key( $args );
            if( !isset( $args[$meta]['type'] ) || ( $args[$meta]['type'] == WL_DATA_TYPE_STRING ) ){
                // Use default WL_Metabox_Field (manages strings)
                $field_class = 'WL_Metabox_Field';
            } else {
                // Build Field with a custom class (e.g. WL_Metabox_Field_date)
                $field_class = 'WL_Metabox_Field_' . $args[$meta]['type'];
            }
        }
        
        // Call apropriate constructor (e.g. WL_Metabox_Field_... )
        $this->fields[] = new $field_class( $args );
    }
    
    public function save_form_data( $post_id ){

        // Build Field objects
        $this->instantiate_fields( $post_id );
        
        // Check if WL metabox form was posted
        if( !isset( $_POST['wl_metaboxes'] ) ){
            return;
        }
        
        foreach( $this->fields as $field ) {
            
            $valid_nonce = $field->verify_nonce();
        
            if( $valid_nonce ){
            
                $posted_data = $_POST['wl_metaboxes'];
                $field_name = $field->meta_name;

                // Each Filed only deals with its values.
                if( isset( $posted_data[$field_name] ) ){

                    $values = $posted_data[$field_name];
                    if( !is_array($values) ){
                        $values = array( $values );
                    }
                    
                    $field->sanitize_data( $values );

                    $field->save_data();
                }
            }
        }
        
        wl_linked_data_push_to_redlink( $post_id );
    }
    
    // print on page all the js and css the fields will need
    public function enqueue_scripts_and_styles(){
        
        // dateTimePicker
        wp_enqueue_style( 'datetimepickercss', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.css', __FILE__ ) );
        wp_enqueue_script( 'datetimepickerjs', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.js', __FILE__ ) );
        
        // Leaflet.
        wp_enqueue_style( 'leaflet_css', plugins_url( 'bower_components/leaflet/dist/leaflet.css', __FILE__ ) );
        wp_enqueue_script( 'leaflet_js', plugins_url( 'bower_components/leaflet/dist/leaflet.js', __FILE__ ) );
        
        // Add AJAX autocomplete to facilitate metabox editing
        wp_enqueue_script('wl-entity-metabox-utility', plugins_url( 'js-client/wl_entity_metabox_utilities.js', __FILE__ ) );
        wp_localize_script( 'wl-entity-metabox-utility', 'wlEntityMetaboxParams', array(
                'ajax_url'          => admin_url('admin-ajax.php'),
                'action'            => 'entity_by_title'
            )
        );
    }
}