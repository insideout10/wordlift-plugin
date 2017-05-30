<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 17/05/2017
 * Time: 14:53
 */
class Wordlift_Linked_Data_Service {

	/**
	 *
	 * @var Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * @var Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @var Wordlift_User_Service
	 */
	private $user_service;

	/**
	 * @var \Wordlift_Linked_Data_Service
	 */
	private static $instance;

	/**
	 * Wordlift_Linked_Data_Service constructor.
	 *
	 * @param Wordlift_Entity_Service        $entity_service
	 * @param Wordlift_Configuration_Service $configuration_service
	 * @param Wordlift_User_Service          $user_service
	 */
	public function __construct( $entity_service, $configuration_service, $user_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Linked_Data_Service' );

		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
		$this->user_service          = $user_service;

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}

	private function is_entity_push_disabled() {

		return get_transient( 'DISABLE_ENTITY_PUSH' );
	}

	private function is_valid_post( $post ) {

		return ( 'post' === $post->post_type && 'publish' === $post->post_status );
	}

	private function is_valid_uri( $uri ) {

		return '/' !== substr( $uri, - 1, 1 );
	}

	/**
	 * Push the provided post to Redlink (not suitable for entities).
	 *
	 * @param WP_Post $post A post instance.
	 */
	function wl_push_post_to_redlink( $post ) {

		// Bail out if entity push is disabled.
		if ( $this->is_entity_push_disabled() ) {
			$this->log->info( "Linked Data publishing is disabled." );

			return;
		}

		// Bail out if the post is not valid.
		if ( $this->is_valid_post( $post ) ) {
			$this->log->info( "The post isn't valid for publication to the Linked Data Store [ post id :: {$post->ID } ]" );

			return;
		}

		// Get the post URI.
		$uri = $this->entity_service->get_uri( $post->ID );

		// Bail out if the URI is not valid.
		if ( $this->is_valid_uri( $uri ) ) {
			$this->log->warn( "The post URI isn't valid [ post id :: {$post->ID } ][ uri :: $uri ]" );

			return;
		}

		// Get the site language in order to define the literals language.
		$site_language = $this->configuration_service->get_language_code();

		// save the author and get the author URI.
		$author_uri = $this->user_service->get_uri( $post->post_author );

		// Get other post properties.
		$date_published      = Wordlift_Sparql_Service::fix_tz( get_the_time( 'c', $post ) );
		$date_modified       = Wordlift_Sparql_Service::fix_tz( wl_get_post_modified_time( $post ) );
		$title               = $post->post_title;
		$user_comments_count = $post->comment_count;

		// Add Location Created
		$location_created_entity_id = get_post_meta( $post->ID, Wordlift_Schema_Service::FIELD_LOCATION_CREATED, true );

		$topic_entity_id = get_post_meta( $post->ID, Wordlift_Schema_Service::FIELD_TOPIC, true );

		$this->create_sparql_query( $uri, $title, $site_language, $author_uri, $date_published, $date_modified, $post, $user_comments_count, $location_created_entity_id, $topic_entity_id );

	}

	private function create_sparql_query( $uri, $title, $site_language, $author_uri, $date_published, $date_modified, $post, $user_comments_count, $location_created_entity_id, $topic_entity_id ) {

		$insert = Wordlift_Query_Builder::new_instance()
		                                ->insert();


		// Create the SPARQL query.
		$sparql = '';
		if ( ! empty( $title ) ) {
//			$sparql .= "<$uri> rdfs:label '$title'@$site_language . \n";

			$insert->statement( $uri, Wordlift_Query_Builder::RDFS_LABEL_URI, $title, null, null, $site_language );
		}

//		$sparql .= "<$uri> a <http://schema.org/BlogPosting> . \n";
////	$sparql .= "<$uri> schema:url <$permalink> . \n";
//		$sparql .= "<$uri> schema:datePublished $date_published . \n";
//		$sparql .= "<$uri> schema:dateModified $date_modified . \n";
//		$sparql .= "<$uri> schema:interactionCount 'UserComments:$user_comments_count' . \n";

		$insert->statement( $uri, Wordlift_Query_Builder::RDFS_TYPE_URI, 'http://schema.org/BlogPosting' )
		       ->statement( $uri, 'http://schema.org/datePublished', $date_published, Wordlift_Query_Builder::OBJECT_AUTO, '<http://www.w3.org/2001/XMLSchema#dateTime>' )
		       ->statement( $uri, 'http://schema.org/dateModified', $date_modified, Wordlift_Query_Builder::OBJECT_AUTO, '<http://www.w3.org/2001/XMLSchema#dateTime>' )
		       ->statement( $uri, Wordlift_Query_Builder::SCHEMA_INTERACTION_COUNT_URI, "UserComments:$user_comments_count" );

		if ( $location_created_entity_id ) {
//			$escaped_uri = wl_sparql_escape_uri( wl_get_entity_uri( $location_created_entity_id ) );
//			wl_write_log( "wl_push_post_to_redlink [ post_id :: $post->ID ][ locationCreated :: $escaped_uri ]" );
//			$sparql .= "<$uri> schema:locationCreated <$escaped_uri> . \n";

			$location_uri = $this->entity_service->get_uri( $location_created_entity_id );
			$insert->statement( $uri, Wordlift_Query_Builder::SCHEMA_LOCATION_CREATED_URI, $location_uri );
		}
		// Add Topic
//		$topic_entity_id = get_post_meta(
//			$post->ID, Wordlift_Schema_Service::FIELD_TOPIC, true
//		);
//		wl_write_log( "wl_push_post_to_redlink [ entity_id :: $topic_entity_id ]" );

		if ( $topic_entity_id ) {
//			$escaped_uri = wl_sparql_escape_uri( wl_get_entity_uri( $topic_entity_id ) );
//			wl_write_log( "wl_push_post_to_redlink [ post_id :: $post->ID ][ topic :: $escaped_uri ]" );
//			$sparql .= "<$uri> dct:subject <$escaped_uri> . \n";

			$topic_uri = $this->entity_service->get_uri( $topic_entity_id );
			$insert->statement( $uri, Wordlift_Query_Builder::DCTERMS_SUBJECT_URI, $topic_uri );
		}

		if ( ! empty( $author_uri ) ) {
//			$sparql .= "<$uri> schema:author <$author_uri> . \n";

			$insert->statement( $uri, Wordlift_Query_Builder::SCHEMA_AUTHOR_URI, $author_uri );
		}


//		// Add SPARQL stmts to write the schema:image.
//		$sparql .= wl_get_sparql_images( $uri, $post->ID );

		$this->add_images( $insert, $uri, $post->ID );

//		// Get the SPARQL fragment with the dcterms:references statement.
//		$sparql .= wl_get_sparql_post_references( $post->ID );

		$this->add_references( $insert, $uri, $post->ID );

		// create the query:
		//  - remove existing references to entities.
		//  - set the new post information (including references).
		$query = rl_sparql_prefixes() . <<<EOF
            DELETE { <$uri> dct:references ?o . }
            WHERE  { <$uri> dct:references ?o . };
            DELETE { <$uri> dct:subject ?o . }
            WHERE  { <$uri> dct:subject ?o . };
            DELETE { <$uri> schema:url ?o . }
            WHERE  { <$uri> schema:url ?o . };
            DELETE { <$uri> schema:datePublished ?o . }
            WHERE  { <$uri> schema:datePublished ?o . };
            DELETE { <$uri> schema:dateModified ?o . }
            WHERE  { <$uri> schema:dateModified ?o . };
            DELETE { <$uri> schema:locationCreated ?o . }
            WHERE  { <$uri> schema:locationCreated ?o . };
            DELETE { <$uri> a ?o . }
            WHERE  { <$uri> a ?o . };
            DELETE { <$uri> rdfs:label ?o . }
            WHERE  { <$uri> rdfs:label ?o . };
            DELETE { <$uri> schema:image ?o . }
            WHERE  { <$uri> schema:image ?o . };
            DELETE { <$uri> schema:interactionCount ?o . }
            WHERE  { <$uri> schema:interactionCount ?o . };
            DELETE { <$uri> schema:author ?o . }
            WHERE  { <$uri> schema:author ?o . };
EOF;

		//             INSERT DATA { $sparql };

		// Add schema:url.
		$query .= Wordlift_Schema_Url_Property_Service::get_instance()
		                                              ->add_query_statement( $insert, $uri, $post->ID );

		$query .= $insert->build();

		// execute the query.
		rl_execute_sparql_update_query( $query );

	}

	/**
	 * Get a SPARQL fragment with schema:image predicates.
	 *
	 * @since 3.14.0
	 *
	 * @param Wordlift_Query_Builder $query
	 * @param string                 $uri     The URI subject of the statements.
	 * @param int                    $post_id The post ID.
	 *
	 * @return string The SPARQL fragment.
	 */
	private function add_images( $query, $uri, $post_id ) {

		foreach ( wl_get_image_urls( $post_id ) as $image_uri ) {
			$query->statement( $uri, Wordlift_Query_Builder::SCHEMA_IMAGE_URI, $image_uri );
		}

		return $query;

//		$sparql = '';
//
//		// Get the escaped URI.
//		$uri_e = esc_html( $uri );
//
//		// Add SPARQL stmts to write the schema:image.
//		$image_urls = wl_get_image_urls( $post_id );
//		foreach ( $image_urls as $image_url ) {
//			$image_url_esc = wl_sparql_escape_uri( $image_url );
//			$sparql        .= " <$uri_e> schema:image <$image_url_esc> . \n";
//		}

//		return $sparql;
	}

	/**
	 * @param \Wordlift_Query_Builder $query
	 * @param                         $uri
	 * @param                         $post_id
	 *
	 * @return string
	 */
	private function add_references( $query, $uri, $post_id ) {

		foreach ( wl_core_get_related_entity_ids( $post_id ) as $id ) {
			$target_uri = $this->entity_service->get_uri( $id );
			$query->statement( $uri, Wordlift_Query_Builder::DCTERMS_REFERENCES_URI, $target_uri );
		}

		return $query;

//		// Get the post URI.
//		$post_uri = wordlift_esc_sparql( wl_get_entity_uri( $post_id ) );
//
//		// Get the related entities IDs.
//		$related = wl_core_get_related_entity_ids( $post_id );
//
//		// Build the SPARQL fragment.
//		$sparql = '';
//		foreach ( $related as $id ) {
//			$uri    = wordlift_esc_sparql( wl_get_entity_uri( $id ) );
//			$sparql .= "<$post_uri> dct:references <$uri> . ";
//		}
//
//		return $sparql;
	}

}
