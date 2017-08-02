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
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

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
	 * The {@link Wordlift_Schema_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Schema_Service $schema_service The {@link Wordlift_Schema_Service} instance.
	 */
	private $schema_service;

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
	 * @param \Wordlift_Schema_Service      $schema_service      The {@link Wordlift_Schema_Service} instance.
	 */
	public function __construct( $entity_service, $entity_type_service, $schema_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Linked_Data_Service' );

		$this->entity_service      = $entity_service;
		$this->entity_type_service = $entity_type_service;
		$this->schema_service      = $schema_service;

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

		$this->log->debug( "Pushing post $post_id..." );

		// Bail out if it's not an entity: we do NOT publish non entities or
		// entities of type `Article`s.
		if ( ! $this->entity_service->is_entity( $post_id ) ) {
			$this->log->debug( "Post $post_id is not an entity." );

			return;
		}

		// Bail out if the entity type is `Article`.
		if ( $this->entity_type_service->has_entity_type( $post_id, 'http://schema.org/Article' ) ) {
			$this->log->debug( "Post $post_id is an `Article`." );

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
		$this->log->debug( "Pushing post $post_id..." );

		// Get the post.
		$post = get_post( $post_id );

		// Bail out if the post isn't found.
		if ( null === $post ) {
			$this->log->debug( "Post $post_id not found." );

			return;
		}

		// Bail out if the post isn't published.
		if ( 'publish' !== $post->post_status ) {
			$this->log->debug( "Post $post_id not published." );

			return;
		}

		// Bail out if the URI isn't valid.
		if ( ! $this->has_valid_uri( $post_id ) ) {
			$this->log->debug( "Post $post_id URI invalid." );

			return;
		}

		// Get the delete statements.
		$deletes = $this->get_delete_statements( $post_id );

//		var_dump( $deletes );

		// Run the delete queries.
		rl_execute_sparql_update_query( implode( "\n", $deletes ) );

		$type        = $this->entity_type_service->get( $post_id );
		$linked_data = $type['linked_data'];

		/** @var \Wordlift_Sparql_Tuple_Rendition $item */
		foreach ( $linked_data as $item ) {
			var_dump( $item->get( $post_id ) );
		}

//		wp_die();

		// get the entity URI and the SPARQL escaped version.
		$uri   = wl_get_entity_uri( $post->ID );
		$uri_e = wl_sparql_escape_uri( $uri );

		$configuration_service = Wordlift_Configuration_Service::get_instance();

		// Get the site language in order to define the literals language.
		$site_language = $configuration_service->get_language_code();

		// get the title and content as label and description.
		$label     = wordlift_esc_sparql( $post->post_title );
		$descr     = wordlift_esc_sparql( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
		$permalink = wl_sparql_escape_uri( get_permalink( $post->ID ) );

		// wl_write_log( "wl_push_entity_post_to_redlink [ entity post id :: $post->ID ][ uri :: $uri ][ label :: $label ]" );

		// create a new empty statement.
		$sparql = '';

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

		$query = rl_sparql_prefixes() . "\nINSERT DATA { $sparql };";

		// Add schema:url.
		$query .= Wordlift_Schema_Url_Property_Service::get_instance()
		                                              ->get_insert_query( $uri, $post->ID );

//		wp_die( '<pre>' . htmlentities( $query ) . '</pre>' );
		rl_execute_sparql_update_query( $query );
	}

	/**
	 * Check if an entity's {@link WP_Post} has a valid URI.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The entity's {@link WP_Post}'s id.
	 *
	 * @return bool True if the URI is valid otherwise false.
	 */
	private function has_valid_uri( $post_id ) {

		// Get the entity's URI.
		$uri = $this->entity_service->get_uri( $post_id );

		// If the URI isn't found, return false.
		if ( null === $uri ) {
			return false;
		}

		// If the URI ends with a trailing slash, return false.
		if ( '/' === substr( $uri, - 1 ) ) {
			return false;
		}

		// URI is valid.
		return true;
	}

	/**
	 * Get the delete statements.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of delete statements.
	 */
	private function get_delete_statements( $post_id ) {

		// Get the entity URI.
		$uri = $this->entity_service->get_uri( $post_id );

		// Prepare the delete statements with the entity as subject.
		$as_subject = array_map( function ( $item ) use ( $uri ) {
			return Wordlift_Query_Builder::new_instance()
			                             ->delete()
			                             ->statement( $uri, $item, '?o' )
			                             ->build();
		}, $this->schema_service->get_all_predicates() );

		// Prepare the delete statements with the entity as object.
		$as_object = array_map( function ( $item ) use ( $uri ) {
			return Wordlift_Query_Builder::new_instance()
			                             ->delete()
			                             ->statement( '?s', $item, $uri, Wordlift_Query_Builder::OBJECT_URI )
			                             ->build();
		}, $this->schema_service->get_all_predicates() );

		// Merge the delete statements and return them.
		return array_merge( $as_subject, $as_object );
	}

}
