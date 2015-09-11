<?php

/**
 * This file provides methods and functions to generate entities meta-boxes in the admin UI.
 */

/**
 * Adds the entities meta box (called from *add_meta_boxes* hook).
 *
 * @param string $post_type The type of the current open post.
 */
function wl_admin_add_entities_meta_box( $post_type ) {
	wl_write_log( "wl_admin_add_entities_meta_box [ post type :: $post_type ]" );

	// Add main meta box for related entities and 4W
	add_meta_box(
		'wordlift_entities_box', __( 'Wordlift', 'wordlift' ), 'wl_entities_box_content', $post_type, 'side', 'high'
	);

	// Add meta box for specific type of entities
	$entity_id   = get_the_ID();
	$entity_type = wl_entity_type_taxonomy_get_type( $entity_id );
        
	if ( isset( $entity_id ) && is_numeric( $entity_id ) && isset( $entity_type['custom_fields'] ) ) {

		// In some special case, properties must be grouped in one metabox (e.g. coordinates) or dealed with custom methods.
                // We divide metaboxes in two groups:
                // - simple: accept values for one property
                // - grouped: accept values for more properties, or for one property that needs a specific metabox.
		$metaboxes         = wl_entities_metaboxes_group_properties_by_input_field( $entity_type['custom_fields'] );
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

			add_meta_box(
				$unique_metabox_name, $title, 'wl_entities_' . $property['type'] . '_box_content', $post_type, 'normal', 'high', $info
			);
		}
                
		// Loop over grouped properties
		foreach ( $grouped_metaboxes as $key => $property ) {

			// Metabox title
			$title = __( 'Edit', 'wordlift' ) . ' ' . get_the_title() . ' ' . __( $key, 'wordlift' );

			$unique_metabox_name = uniqid( 'wl_metabox_' );
                        
			add_meta_box(
				$unique_metabox_name, $title, 'wl_entities_' . $key . '_box_content', $post_type, 'normal', 'high'
			);

		}
                
                // Add AJAX autocomplete to facilitate metabox editing
                wp_enqueue_script('wl-entity-metabox-utility', plugins_url( 'js-client/wl_entity_metabox_utilities.js', __FILE__ ) );
                wp_localize_script( 'wl-entity-metabox-utility', 'wlEntityMetaboxParams', array(
                        'ajax_url'          => admin_url('admin-ajax.php'),
                        'action'            => 'entity_by_title'
                    )
                );
	}
}

function wl_echo_nonce( $meta_name ) {
	wp_nonce_field( 'wordlift_' . $meta_name . '_entity_box', 'wordlift_' . $meta_name . '_entity_box_nonce' );
}

/**
 * Separes metaboxes in simple and grouped (called from *wl_admin_add_entities_meta_box*).
 *
 * @param array $custom_fields Information on the entity type.
 */
function wl_entities_metaboxes_group_properties_by_input_field( $custom_fields ) {

	$simple_properties  = array();
	$grouped_properties = array();

	// Loop over possible entity properties
	foreach ( $custom_fields as $key => $property ) {

		// Check presence of predicate and type
		if ( isset( $property['predicate'] ) && isset( $property['type'] ) ) {

			// Check if input_field is defined
			if ( isset( $property['input_field'] ) && $property['input_field'] !== '' ) {
                                
				$grouped_key = $property['input_field'];

				// Update list of grouped properties
				$grouped_properties[ $grouped_key ][ $key ] = $property;

			} else {

				// input_field not defined, add simple metabox
				$simple_properties[ $key ] = $property;
			}
		}
	}

	return array( $simple_properties, $grouped_properties );
}

add_action( 'add_meta_boxes', 'wl_admin_add_entities_meta_box' );

/**
 * Displays the meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_box_content( $post ) {
    
	wl_write_log( "wl_entities_box_content [ post id :: $post->ID ]" );
	
        // Angularjs edit-post widget wrapper
	echo '<div id="wordlift-edit-post-outer-wrapper"></div>';
	
        // Angularjs edit-post widget classification boxes configuration
	$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );
        
        // Array to store all related entities ids
        $all_referenced_entities_ids = array();
	
        // Add selected entities to classification_boxes
	foreach ( $classification_boxes as $i => $box ) {
		// Build the proper relation name
		$relation_name = $box['id'];
		
		wl_write_log( "Going to related of $relation_name" );
    
		// Get entity ids related to the current post for the given relation name (both draft and published entities)
		$draft_entity_ids = wl_core_get_related_entity_ids( $post->ID, array(
                    'predicate' => $relation_name,
                    'status'    => 'draft'
                ) );
                $publish_entity_ids = wl_core_get_related_entity_ids( $post->ID, array(
                    'predicate' => $relation_name,
                    'status'    => 'publish'
                ) );
                $entity_ids = array_unique( array_merge( $draft_entity_ids, $publish_entity_ids ) );
                
                // Store the entity ids for all the 4W
                $all_referenced_entities_ids = array_merge( $all_referenced_entities_ids, $entity_ids );
	
		// Transform entity ids array in entity uris array
		array_walk($entity_ids, function(&$entity_id) {
                    // Retrieve the entity uri for the given entity id
                    $entity_id = wl_get_entity_uri( $entity_id );
		});
		
		// Enhance current box selected entities
		$classification_boxes[ $i ]['selectedEntities'] = $entity_ids;
	}
	// Json encoding for classification boxes structure
	$classification_boxes = json_encode( $classification_boxes );
        
        // Ensure there are no repetitions of the referenced entities
        $all_referenced_entities_ids = array_unique( $all_referenced_entities_ids );
	
    // Build the entity storage object
    $referenced_entities_obj = array();
    foreach ( $all_referenced_entities_ids as $referenced_entity ) {
        $entity = wl_serialize_entity( $referenced_entity );
        $referenced_entities_obj[ $entity['id'] ] = $entity;
    }

    $referenced_entities_obj = empty($referenced_entities_obj) ? 
        '{}' : json_encode( $referenced_entities_obj );
	
	$default_thumbnail_path = WL_DEFAULT_THUMBNAIL_PATH;

	echo <<<EOF
    <script type="text/javascript">
        jQuery( function() {
        	if ('undefined' == typeof window.wordlift) {
            	window.wordlift = {}
            	window.wordlift.entities = {}  		
        	}

        	window.wordlift.classificationBoxes = $classification_boxes;
        	window.wordlift.entities = $referenced_entities_obj;
        	window.wordlift.currentPostId = $post->ID;
			window.wordlift.defaultThumbnailPath = '$default_thumbnail_path';


        });
    </script>
EOF;
}

/**
 * Build the HTML template for metaboxes
 */
function wl_entities_metaboxes_build_template( $meta_name, $meta_values, $cardinality=1, $expected_types=null ) {
    
    // TODO: test this function and add parameters checks
    if( !is_array( $expected_types ) ){
        $expected_types = array( $expected_types );
    }
    
    // TODO: move nonce here! (may be risky)
    
    // Always add an empty <input> tag to allow insertion of new values.
    $meta_values[] = null;
    
    // The containing <div> contains info on cardinality and expected types
    $template = 'template:::::</br><div class="wl-metabox" data-cardinality="' . $cardinality . '"';
    if( count( $expected_types ) != 0 && !is_null( $expected_types[0] ) ){
        $template.= ' data-expected-types="' . implode($expected_types,',') . '"';
    }
    $template.= '>';
    
    // The insid <input> tags host the meta values.
    // Each hosts one human readable value (i.e. entity name or uri)
    // and is accompained by an hidden <input> tag has both the index of the value and its raw value (i.e. the uri or entity id)
    foreach( $meta_values as $index => $meta_value ){
        $template .= '<div data-wl-meta-index="' . $index . '">
                        <input type="text" class="' . $meta_name . ' wl-autocomplete" value="' . $meta_value . '" style="width:100%" />
                        <input type="hidden" class="' . $meta_name . '" name="wl_metaboxes[' . $meta_name . '][' . $index . ']" value="' . $meta_value . '" />
                    </div>';
    }
    $template .= '</div>';
    
    return $template;
}

/**
 * Displays the date meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 * @param $info Array The custom_field the method must manage.
 */
function wl_entities_date_box_content( $post, $info ) {

	// Which meta/custom_field are we managing?
	$custom_field = $info['args'];
	$meta_name    = ( array_keys( $custom_field ) );
	$meta_name    = $meta_name[0];

	// Include dateTimePicker on page
	wp_enqueue_style(
		'datetimepickercss', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.css', __FILE__ )
	);
	wp_enqueue_script(
		'datetimepickerjs', plugins_url( 'js-client/datetimepicker/jquery.datetimepicker.js', __FILE__ )
	);

	// Set nonce
	wl_echo_nonce( $meta_name );

	$date = get_post_meta( $post->ID, $meta_name, true );
	$date = esc_attr( $date );
        
        $pickerDate  = '';
	// Give the timepicker the date in its favourite format.
	if ( ! empty( $date ) ) {
		$pickerDate = date( 'Y/m/d H:i', strtotime( $date ) );
	}

	// Two input fields, one for the datetimepicker and another to store the time in the required format
	echo '<input type="text" id="' . $meta_name . '" value="' . $pickerDate . '" style="width:100%" />';
	echo '<input type="hidden" id="' . $meta_name . '_hidden" name="wl_metaboxes[' . $meta_name . ']" value="' . $date . '" style="width:100%" />';

	echo "<script type='text/javascript'>
    $ = jQuery;
    $(document).ready(function() {
    
        var lastDateTimePickerClicked;

        $('#" . $meta_name . "').datetimepicker({
            onChangeDateTime:function(dp, input){
                // format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
                var currentDate = input.val();
                currentDate = currentDate.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z')
                // store value to save in the hidden input field
                $('#" . $meta_name . "_hidden').val( currentDate );
            }
        });
    });
    </script>";
}

/**
 * Displays the string meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 * @param $info Array The custom_field the method must manage.
 */
function wl_entities_string_box_content( $post, $info ) {

	// Which meta/custom_field are we managing?
	$custom_field = $info['args'];
	$meta_name    = ( array_keys( $custom_field ) );
	$meta_name    = $meta_name[0];

	// Set nonce
	wl_echo_nonce( $meta_name );

	$default = get_post_meta( $post->ID, $meta_name, true );

	echo '<input type="text" id="' . $meta_name . '" name="wl_metaboxes[' . $meta_name . ']" value="' . $default . '" style="width:100%" />';
}

/**
 * Displays the coordinates meta box contents (called by *add_meta_box* callback).
 *
 * @param WP_Post $post The current post.
 */
function wl_entities_coordinates_box_content( $post ) {

	// Add leaflet css and library.
	wp_enqueue_style(
		'leaflet_css', plugins_url( 'bower_components/leaflet/dist/leaflet.css', __FILE__ )
	);
	wp_enqueue_script(
		'leaflet_js', plugins_url( 'bower_components/leaflet/dist/leaflet.js', __FILE__ )
	);

	// Set nonce for both meta (latitude and longitude)
	wl_echo_nonce( WL_CUSTOM_FIELD_GEO_LATITUDE );
	wl_echo_nonce( WL_CUSTOM_FIELD_GEO_LONGITUDE );

	// Get coordinates
	$coords = wl_get_coordinates( $post->ID );

	// Print input fields
	echo '<label for="wl_place_lat">' . __( 'Latitude', 'wordlift' ) . '</label>';
	echo '<input type="text" id="wl_place_lat" name="wl_metaboxes[' . WL_CUSTOM_FIELD_GEO_LATITUDE . ']" value="' . $coords['latitude'] . '" style="width:100%" />';

	echo '<label for="wl_place_lon">' . __( 'Longitude', 'wordlift' ) . '</label>';
	echo '<input type="text" id="wl_place_lon" name="wl_metaboxes[' . WL_CUSTOM_FIELD_GEO_LONGITUDE . ']" value="' . $coords['longitude'] . '" style="width:100%" />';

	// Show Leaflet map to pick coordinates
	echo "<div id='wl_place_coords_map'></div>";
	echo "<script type='text/javascript'>
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
}

/**
 * Displays jQuery autocomplete in a meta box, to assign an entity as property value (e.g. location of an Event).
 * The assigned entity can also be created on the fly.
 *
 * @param WP_Post $post The current post.
 * @param $info Array The custom_field the method must manage.
 */
function wl_entities_uri_box_content( $post, $info ) {
    
	// Which meta/custom_field are we managing?
	$custom_field = $info['args'];
	$meta_name    = ( array_keys( $custom_field ) );
	$meta_name    = $meta_name[0];
        
        // Which type of entity can we accept?
	if ( isset( $custom_field[ $meta_name ]['constraints']['uri_type'] ) ) {
		// Specific schema type (e.g. Place, Event, ecc.)
		$expected_types = $custom_field[ $meta_name ]['constraints']['uri_type'];
	} else {
		// Any entity
		$expected_types = null;
	}
        
        // How many values can we accept?
        if( isset( $custom_field[ $meta_name ]['constraints']['cardinality'] ) ){
            $cardinality = $custom_field[ $meta_name ]['constraints']['cardinality'];
        } else {
            $cardinality = 1;
        }
        
	// Set Nonce
	wl_echo_nonce( $meta_name );

	// Get already inserted values, if any
	$default_entities = get_post_meta( $post->ID, $meta_name );
	/*if ( is_array( $defaultEntities ) && !empty( $defaultEntities ) ) {
            foreach( $defaultEntities as $defaultEntityIdentifier ) {
		// If the value is an ID (local entity for sure), display the URI
		if ( is_numeric( $defaultEntityIdentifier ) ) {
			$defaultEntity      = get_post( $defaultEntityIdentifier );
			$defaultEntityIdentifier = wl_get_entity_uri( $defaultEntity->ID );
		}
            }
	}*/
        
        // Write already saved values in page
        echo wl_entities_metaboxes_build_template( $meta_name, $default_entities, $cardinality, $expected_types );
        
        // That's all. The script *wl_entity_metabox_utilities.js* will take care of the rest.
}

/**
 * Saves the values of wordlift metaboxes set in the entity editor page
 */
function wl_entity_metabox_save( $post_id ) {

	if ( ! isset( $_POST['wl_metaboxes'] ) ) {
		return;
	}

	// Loop over the wl_metaboxes array and save metaboxes values
	foreach ( $_POST['wl_metaboxes'] as $meta_name => $meta_values ) {
            
		// First, verify nonce is set for this meta
		$nonce_name   = 'wordlift_' . $meta_name . '_entity_box_nonce';
		$nonce_verify = 'wordlift_' . $meta_name . '_entity_box';
		if ( ! isset( $_POST[ $nonce_name ] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_verify ) ) {
			return $post_id;
		}
                
                // Delete values before updating
                delete_post_meta( $post_id, $meta_name );
                
		// Save the property value(s)
		if ( isset( $meta_name ) && isset( $meta_values ) && $meta_values !== '' ) {

                    // There can be one or more property values, so we force to array:
                    if( !is_array( $meta_values ) ) {
                        $meta_values = array( $meta_values );
                    }
                        
                    foreach( $meta_values as $meta_value ) { 
                        
                        wl_write_log( 'piedo about to evaluate ' . $meta_name );
                        wl_write_log( $meta_value );
                
			// If the meta expects an entity...
			$expecting_uri = ( wl_get_meta_type( $meta_name ) === WL_DATA_TYPE_URI );
			// ...and the user inputs an entity that is not present in the db...
			$absent_from_db = is_null( wl_get_entity_post_by_uri( $meta_value ) );
			// ...and that is not an external uri
			$name_is_uri = strpos( $meta_value, 'http' ) === 0;

			if ( $expecting_uri && $absent_from_db && ! $name_is_uri ) {

				// ...we create a new entity!
				$new_entity_id = wp_insert_post( array(
                                    'post_status'  => 'publish',
                                    'post_type'    => WL_ENTITY_TYPE_NAME,
                                    'post_title'   => $meta_value
                                ) );
                                $new_entity = get_post( $new_entity_id );
                                
				// Assign type
				$constraints = wl_get_meta_constraints( $meta_name );
                                if( isset( $constraints['uri_type'] ) ){
                                    if( !is_array($constraints['uri_type']) ){
                                        $type = $constraints['uri_type'];
                                    } else {
                                        $type = $constraints['uri_type'];
                                    }
                                } else {
                                    $type = 'Thing';
                                }
				$type        = 'http://schema.org/' . $type;
				wl_set_entity_main_type( $new_entity_id, $type );

                                // Build uri for this entity
                                $new_uri = wl_build_entity_uri( $new_entity_id );
                                wl_set_entity_uri( $new_entity_id, $new_uri );
                                
				// Update the value that will be saved as meta
				$meta_value = $new_uri;
                                
                                wl_push_entity_post_to_redlink( $new_entity );
			}
                        
                        wl_write_log( 'piedo about to insert ' . $meta_name );
                        wl_write_log( $meta_value );
                        
			add_post_meta( $post_id, $meta_name, $meta_value );
                    }
		}
	}
	// Push changes on RedLink
	wl_linked_data_push_to_redlink( $post_id );
}

add_action( 'wl_linked_data_save_post', 'wl_entity_metabox_save' );