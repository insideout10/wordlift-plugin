<?php 
/**
 * Install known types in WordPress.
 */
function wl_core_install_entity_type_data() {

	// Ensure the custom type and the taxonomy are registered.
	wl_entity_type_register();
	wl_entity_type_taxonomy_register();

	// Set the taxonomy data.
        // Note: parent types must be defined before child types.
	$terms = array(
                'thing'         => array(
                        'label'              => 'Thing',
                        'description'        => 'A generic thing (something that doesn\'t fit in the previous definitions.',
                        'css'                => 'wl-thing',
                        'uri'                => 'http://schema.org/Thing',
                        'same_as'            => array( '*' ), // set as default.
                        'custom_fields'      => array(
				WL_CUSTOM_FIELD_SAME_AS => array(
					'predicate'   => 'http://schema.org/sameAs',
					'type'        => WL_DATA_TYPE_URI,
                                        'export_type' => 'http://schema.org/Thing',
					'constraints' => '',
                                        'input_field' => 'sameas'   // to build custom metabox
				)
			),
                        // {{sameAs}} not present in the microdata template,
                        // because it is treated separately in *wl_content_embed_item_microdata*
                        'microdata_template' => '',
                        'templates'          => array(
                                'subtitle' => '{{id}}'
                        )
		),
		'creative-work' => array(
			'label'              => 'CreativeWork',
			'description'        => 'A creative work (or a Music Album).',
                        'parents'             => array( 'thing' ),
			'css'                => 'wl-creative-work',
			'uri'                => 'http://schema.org/CreativeWork',
			'same_as'            => array(
				'http://schema.org/MusicAlbum',
				'http://schema.org/Product'
			),
			'custom_fields'      => array(),
			'microdata_template' => '',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		),
		'event'         => array(
			'label'              => 'Event',
			'description'        => 'An event.',
                        'parents'             => array( 'thing' ),
			'css'                => 'wl-event',
			'uri'                => 'http://schema.org/Event',
			'same_as'            => array( 'http://dbpedia.org/ontology/Event' ),
			'custom_fields'      => array(
				WL_CUSTOM_FIELD_CAL_DATE_START => array(
					'predicate'   => 'http://schema.org/startDate',
					'type'        => WL_DATA_TYPE_DATE,
                                        'export_type' => 'xsd:date',
					'constraints' => ''
				),
				WL_CUSTOM_FIELD_CAL_DATE_END   => array(
					'predicate'   => 'http://schema.org/endDate',
					'type'        => WL_DATA_TYPE_DATE,
                                        'export_type' => 'xsd:date',
					'constraints' => ''
				),
				WL_CUSTOM_FIELD_LOCATION       => array(
					'predicate'   => 'http://schema.org/location',
					'type'        => WL_DATA_TYPE_URI,
                                        'export_type' => 'http://schema.org/PostalAddress',
					'constraints' => array(
						'uri_type' => 'Place'
					)
				)
			),
			'microdata_template' =>
				'{{startDate}}
                                {{endDate}}
                                {{location}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		),
		'organization'  => array(
			'label'              => 'Organization',
			'description'        => 'An organization, including a government or a newspaper.',
                        'parents'             => array( 'thing' ),
			'css'                => 'wl-organization',
			'uri'                => 'http://schema.org/Organization',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/organization.organization',
				'http://rdf.freebase.com/ns/government.government',
				'http://schema.org/Newspaper'
			),
			'custom_fields'      => array(
                            WL_CUSTOM_FIELD_FOUNDER  => array(
                                        'predicate'        => 'http://schema.org/founder',
					'type'        => WL_DATA_TYPE_URI,
                                        'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type' => 'Person'
					)
				),
                        ),
			'microdata_template' => '{{founder}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		),
		'person'        => array(
			'label'              => 'Person',
			'description'        => 'A person (or a music artist).',
                        'parents'             => array( 'thing' ),
			'css'                => 'wl-person',
			'uri'                => 'http://schema.org/Person',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/people.person',
				'http://rdf.freebase.com/ns/music.artist',
				'http://dbpedia.org/class/yago/LivingPeople'
			),
			'custom_fields'      => array(),
			'microdata_template' => '',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		),
		'place'         => array(
			'label'              => 'Place',
			'description'        => 'A place.',
                        'parents'             => array( 'thing' ),
			'css'                => 'wl-place',
			'uri'                => 'http://schema.org/Place',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/location.location',
				'http://www.opengis.net/gml/_Feature'
			),
			'custom_fields'      => array(
				WL_CUSTOM_FIELD_GEO_LATITUDE  => array(
                                        'predicate'        => 'http://schema.org/latitude',
					'type'             => WL_DATA_TYPE_DOUBLE,
                                        'export_type'      => 'xsd:double',
					'constraints' => '',
					'input_field' => 'coordinates'   // to build custom metabox
				),
				WL_CUSTOM_FIELD_GEO_LONGITUDE => array(
					'predicate'   => 'http://schema.org/longitude',
					'type'        => WL_DATA_TYPE_DOUBLE,
                                        'export_type'      => 'xsd:double',
					'constraints' => '',
					'input_field' => 'coordinates'   // to build custom metabox
				),
				WL_CUSTOM_FIELD_ADDRESS       => array(
					'predicate' => 'http://schema.org/address',
					'type'        => WL_DATA_TYPE_STRING,
                                        'export_type'      => 'http://schema.org/PostalAddress',
					'constraints' => ''
				)
			),
			'microdata_template' =>
				'<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                                    {{latitude}}
                                    {{longitude}}
                                </span>
                                {{address}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		),
                'localbusiness'         => array(
                        'label'              => 'LocalBusiness',
                        'description'        => 'A local business.',
                        'parents'            => array( 'place', 'organization' ),
                        'css'                => 'wl-local-business',
                        'uri'                => 'http://schema.org/LocalBusiness',
                        'same_as'            => array(
                                'http://rdf.freebase.com/ns/business/business_location',
                                'https://schema.org/Store'
                        ),
                        'custom_fields'      => array(
                                
                        ),
                        'microdata_template' => '',
                        'templates'          => array(
                                'subtitle' => '{{id}}'
                        )
                ),
            
	);
        
	foreach ( $terms as $slug => $term ) {
		
            // Create the term if it does not exist, then get its ID
            $term_id = term_exists( $slug, WL_ENTITY_TYPE_TAXONOMY_NAME );

            if( $term_id == 0 || is_null( $term_id ) ) {
                $result = wp_insert_term( $slug, WL_ENTITY_TYPE_TAXONOMY_NAME );
            } else {
                $term_id = $term_id['term_id'];
                $result = get_term( $term_id, WL_ENTITY_TYPE_TAXONOMY_NAME, ARRAY_A );
            }

            // Check for errors.
            if ( is_wp_error( $result ) ) {
                    wl_write_log( 'wl_install_entity_type_data [ ' . $result->get_error_message() . ' ]' );
                    continue;
            }

            // Check if 'parent' corresponds to an actual term and get its ID.
            if( !isset( $term['parents'] ) ) {
                $term['parents'] = array();
            }

            $parent_ids = array();
            foreach( $term['parents'] as $parent_slug ) {
                $parent_id = get_term_by( 'slug', $parent_slug, WL_ENTITY_TYPE_TAXONOMY_NAME );
                $parent_ids[] = intval( $parent_id->term_id );  // Note: int casting is suggested by Codex: http://codex.wordpress.org/Function_Reference/get_term_by
            }

            // Define a parent in the WP taxonomy style (not important for WL)
            if( empty( $parent_ids ) ) {
                // No parent
                $parent_id = 0;
            } else {
                // Get first parent
                $parent_id = $parent_ids[0];
            }

            // Update term with description, slug and parent    
            wp_update_term( $result['term_id'], WL_ENTITY_TYPE_TAXONOMY_NAME, array(
                'name'         => $term['label'],
                'slug'          => $slug,
                'description'   => $term['description'],
                'parent'        => $parent_id   // We give to WP taxonomy just one parent. TODO: see if can give more than one
            ));

            // Inherit custom fields and microdata template from parent.
            $term = wl_entity_type_taxonomy_type_inheritage( $term, $parent_ids );

            // Add custom metadata to the term.
            wl_entity_type_taxonomy_update_term( $result['term_id'], $term['css'], $term['uri'], $term['same_as'], $term['custom_fields'], $term['templates'], $term['microdata_template'] );
        }
}

/**
 * Install known types in WordPress.
 */
function wl_core_install_create_relation_instance_table() {

	global $wpdb;
	// global $wl_db_version;
	$installed_version = get_option( "wl_db_version" );

	if ( $installed_version != WL_DB_VERSION ) {
		$table_name = $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		// Sql statement for the relation instances custom table
		$sql = <<<EOF
			CREATE TABLE $table_name (
  				id int(11) NOT NULL AUTO_INCREMENT,
  				subject_id int(11) NOT NULL,
  				predicate char(10) NOT NULL,
  				object_id int(11) NOT NULL,
  				UNIQUE KEY id (id),
  				KEY subject_id_index (subject_id),
  				KEY object_id_index (object_id)
			) $charset_collate;
EOF;
	
		// @see: https://codex.wordpress.org/Creating_Tables_with_Plugins 
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$results = dbDelta( $sql );
		
		wl_write_log( $results );

		update_option( "wl_db_version", WL_DB_VERSION );
	}

}

/**
 * Install Wordlift in WordPress.
 */
function wl_core_install() {

	// Create a blank application key if there is none
    if( empty( wl_configuration_get_key() ) ){
    	wl_configuration_set_key('');
    }
	
	wl_core_install_entity_type_data();
	wl_core_install_create_relation_instance_table();

}

// Installation Hook
add_action( 'activate_wordlift/wordlift.php', 'wl_core_install' );

// Check db status on automated plugins updates
function wl_core_update_db_check() {
    if ( get_site_option( 'wl_db_version' ) != WL_DB_VERSION ) {
        wl_core_install_create_relation_instance_table();
    }
}
add_action( 'plugins_loaded', 'wl_core_update_db_check' );
