<?php
/**
 * Services: Linked Data Service.
 *
 * A service to handle data to be pushed to the remote Linked Data store.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Linked_Data_Service} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Linked_Data_Service {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The {@link Wordlift_Linked_Data_Service} singleton instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Linked_Data_Service $instance The {@link Wordlift_Linked_Data_Service} singleton instance.
	 */
	private static $instance;

	/**
	 * Create a {@link Wordlift_Linked_Data_Service} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Entity_Service      $entity_service      The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service} instance.
	 */
	public function __construct( $entity_service, $entity_type_service ) {

		$this->entity_service      = $entity_service;
		$this->entity_type_service = $entity_type_service;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of {@link Wordlift_Linked_Data_Service}.
	 *
	 * @since 3.15.0
	 *
	 * @return Wordlift_Linked_Data_Service The singleton instance of <a href='psi_element://Wordlift_Linked_Data_Service'>Wordlift_Linked_Data_Service</a>.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Push a {@link WP_Post} to the Linked Data store.
	 *
	 * If the {@link WP_Post} is an entity and it's not of the `Article` type,
	 * then it is pushed to the remote Linked Data store.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 */
	public function push( $post_id ) {

		// Bail out if it's not an entity: we do NOT publish non entities or
		// entities of type `Article`s.
		if ( ! $this->entity_service->is_entity( $post_id ) ) {
			return;
		}

		// Bail out if the entity type is `Article`.
		if ( ! $this->entity_type_service->has_entity_type( $post_id, 'http://schema.org/Article' ) ) {
			return;
		}

		// Get the post and push it to the Linked Data store.
		$this->do_push( $post_id );

		// Reindex the triple store if buffering is turned off.
		if ( false === WL_ENABLE_SPARQL_UPDATE_QUERIES_BUFFERING ) {
			wordlift_reindex_triple_store();
		}

	}

	/**
	 * Push an entity to the Linked Data store.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 */
	private function do_push( $post_id ) {

		$post = get_post( $post_id );

		// Bail out if the post isn't published.
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		// get the entity URI and the SPARQL escaped version.
		$uri   = wl_get_entity_uri( $post->ID );
		$uri_e = wl_sparql_escape_uri( $uri );

		// If the URI ends with a trailing slash, then we have a problem.
		if ( '/' === substr( $uri, - 1, 1 ) ) {

			wl_write_log( "wl_push_entity_post_to_redlink : the URI is invalid [ post ID :: $post->ID ][ URI :: $uri ]" );

			return;
		}

		$configuration_service = Wordlift_Configuration_Service::get_instance();

		// Get the site language in order to define the literals language.
		$site_language = $configuration_service->get_language_code();

		// get the title and content as label and description.
		$label     = wordlift_esc_sparql( $post->post_title );
		$descr     = wordlift_esc_sparql( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
		$permalink = wl_sparql_escape_uri( get_permalink( $post->ID ) );

		// wl_write_log( "wl_push_entity_post_to_redlink [ entity post id :: $post->ID ][ uri :: $uri ][ label :: $label ]" );

		// create a new empty statement.
		$delete_stmt = '';
		$sparql      = '';

		// delete on RL all statements regarding properties set from WL (necessary when changing entity type)
		$all_custom_fields        = wl_entity_taxonomy_get_custom_fields();
		$predicates_to_be_deleted = array();
		foreach ( $all_custom_fields as $type => $fields ) {
			foreach ( $fields as $cf ) {
				$predicate = $cf['predicate'];
				if ( ! in_array( $predicate, $predicates_to_be_deleted ) ) {
					$predicates_to_be_deleted[] = $predicate;
					$delete_stmt                .= "DELETE { <$uri_e> <$predicate> ?o } WHERE  { <$uri_e> <$predicate> ?o };\n";
				}
			}
		}

		// Set the same as.
		if ( null !== $same_as = wl_schema_get_value( $post->ID, 'sameAs' ) ) {
			foreach ( $same_as as $same_as_uri ) {
				$same_as_uri_esc = wl_sparql_escape_uri( $same_as_uri );
				$sparql          .= "<$uri_e> owl:sameAs <$same_as_uri_esc> . \n";
			}
		}

		// set the label
		$sparql .= "<$uri_e> dct:title \"$label\"@$site_language . \n";
		$sparql .= "<$uri_e> rdfs:label \"$label\"@$site_language . \n";

		// Set the alternative labels.
		$alt_labels = $this->entity_service->get_alternative_labels( $post->ID );
		foreach ( $alt_labels as $alt_label ) {
			$sparql .= sprintf( '<%s> rdfs:label "%s"@%s . ', $uri_e, Wordlift_Sparql_Service::escape( $alt_label ), $site_language );
		}

		// set the description.
		if ( ! empty( $descr ) ) {
			$sparql .= "<$uri_e> schema:description \"$descr\"@$site_language . \n";
		}

		$main_type = wl_entity_type_taxonomy_get_type( $post->ID );

		if ( null != $main_type ) {
			$main_type_uri = wl_sparql_escape_uri( $main_type['uri'] );
			$sparql        .= " <$uri_e> a <$main_type_uri> . \n";

			// The type define custom fields that hold additional data about the entity.
			// For example Events may have start/end dates, Places may have coordinates.
			// The value in the export fields must be rewritten as triple predicates, this
			// is what we're going to do here.

//		wl_write_log( 'wl_push_entity_post_to_redlink : checking if entity has export fields [ type :: ' . var_export( $main_type, true ) . ' ]' );

			if ( isset( $main_type['custom_fields'] ) ) {
				foreach ( $main_type['custom_fields'] as $field => $settings ) {

					// schema:url uses the new Property Service. Hopefully all others will follow suit.
					if ( Wordlift_Schema_Url_Property_Service::META_KEY === $field ) {
						continue;
					}

					// wl_write_log( "wl_push_entity_post_to_redlink : entity has export fields" );

					$predicate = wordlift_esc_sparql( $settings['predicate'] );
					if ( ! isset( $settings['export_type'] ) || empty( $settings['export_type'] ) ) {
						$type = null;
					} else {
						$type = $settings['export_type'];
					}

					foreach ( get_post_meta( $post->ID, $field ) as $value ) {
						$sparql .= " <$uri_e> <$predicate> ";

						if ( ! is_null( $type ) && ( substr( $type, 0, 4 ) == 'http' ) ) {
							// Type is defined by a raw uri (es. http://schema.org/PostalAddress)

							// Extract uri if the value is numeric
							if ( is_numeric( $value ) ) {
								$value = wl_get_entity_uri( $value );
							}

							$sparql .= '<' . wl_sparql_escape_uri( $value ) . '>';
						} else {
							// Type is defined in another way (es. xsd:double)
							$sparql .= '"' . wordlift_esc_sparql( $value ) . '"^^' . wordlift_esc_sparql( $type );
						}

						$sparql .= " . \n";
					}
				}
			}
		}

		// Get the entity types.
		$type_uris = wl_get_entity_rdf_types( $post->ID );

		// Support type are only schema.org ones: it could be null
		foreach ( $type_uris as $type_uri ) {
			$type_uri = wl_sparql_escape_uri( $type_uri );
			$sparql   .= "<$uri_e> a <$type_uri> . \n";
		}

		// get related entities.
		$related_entities_ids = wl_core_get_related_entity_ids( $post->ID );

		if ( is_array( $related_entities_ids ) ) {
			foreach ( $related_entities_ids as $post_id ) {
				$related_entity_uri = wl_sparql_escape_uri( wl_get_entity_uri( $post_id ) );
				// create a two-way relationship.
				$sparql .= " <$uri_e> dct:relation <$related_entity_uri> . \n";
				$sparql .= " <$related_entity_uri> dct:relation <$uri_e> . \n";
			}
		}

		// Add SPARQL stmts to write the schema:image.
		$sparql .= wl_get_sparql_images( $uri, $post->ID );

		$query = rl_sparql_prefixes() . <<<EOF
    $delete_stmt
    DELETE { <$uri_e> rdfs:label ?o } WHERE  { <$uri_e> rdfs:label ?o };
    DELETE { <$uri_e> dct:title ?o } WHERE  { <$uri_e> dct:title ?o };
    DELETE { <$uri_e> owl:sameAs ?o . } WHERE  { <$uri_e> owl:sameAs ?o . };
    DELETE { <$uri_e> schema:description ?o . } WHERE  { <$uri_e> schema:description ?o . };
    DELETE { <$uri_e> schema:url ?o . } WHERE  { <$uri_e> schema:url ?o . };
    DELETE { <$uri_e> a ?o . } WHERE  { <$uri_e> a ?o . };
    DELETE { <$uri_e> dct:relation ?o . } WHERE  { <$uri_e> dct:relation ?o . };
    DELETE { <$uri_e> schema:image ?o . } WHERE  { <$uri_e> schema:image ?o . };
    INSERT DATA { $sparql };
EOF;

		// Add schema:url.
		$query .= Wordlift_Schema_Url_Property_Service::get_instance()
		                                              ->get_insert_query( $uri, $post->ID );

		rl_execute_sparql_update_query( $query );
	}

}
