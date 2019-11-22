<?php


namespace Wordlift\Analysis\Response;

class Analysis_Response_Ops {

	/**
	 * The analysis response json.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var mixed $json Holds the analysis response json.
	 */
	private $json;

	/**
	 * Holds the {@link Wordlift_Entity_Uri_Service}.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	private $entity_service;

	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;
	/**
	 * @var \Wordlift_Post_Image_Storage
	 */
	private $post_image_storage;

	/**
	 * Analysis_Response_Ops constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service  $entity_uri_service The {@link Wordlift_Entity_Uri_Service}.
	 * @param \Wordlift_Entity_Service      $entity_service The {@link Wordlift_Entity_Service}.
	 * @param \Wordlift_Entity_Type_Service $entity_type_service The {@link Wordlift_Entity_Type_Service}.
	 * @param \Wordlift_Post_Image_Storage  $post_image_storage A {@link Wordlift_Post_Image_Storage} instance.
	 * @param mixed                         $json The analysis response json.
	 *
	 * @since 3.21.5
	 */
	public function __construct( $entity_uri_service, $entity_service, $entity_type_service, $post_image_storage, $json ) {

		$this->json                = $json;
		$this->entity_uri_service  = $entity_uri_service;
		$this->entity_service      = $entity_service;
		$this->entity_type_service = $entity_type_service;
		$this->post_image_storage  = $post_image_storage;

	}

	/**
	 * Switches remote entities, i.e. entities with id outside the local dataset, to local entities.
	 *
	 * The function takes all the entities that have an id which is not local. For each remote entity, a list of URIs
	 * is built comprising the entity id and the sameAs. Then a query is issued in the local database to find potential
	 * matches from the local vocabulary.
	 *
	 * If found, the entity id is swapped with the local id and the remote id is added to the sameAs.
	 *
	 * @return Analysis_Response_Ops The current Analysis_Response_Ops instance.
	 */
	public function make_entities_local() {

		if ( ! isset( $this->json->entities ) ) {
			return $this;
		}

		// Get the URIs.
		$uris = array_keys( get_object_vars( $this->json->entities ) );

		// Filter only the external URIs.
		$entity_uri_service = $this->entity_uri_service;
		$external_uris      = array_filter( $uris, function ( $item ) use ( $entity_uri_service ) {
			return ! $entity_uri_service->is_internal( $item );
		} );

		// Preload the URIs.
		$entity_uri_service->preload_uris( $external_uris );

		$mappings = array();
		foreach ( $external_uris as $external_uri ) {
			$entity = $entity_uri_service->get_entity( $external_uri );
			if ( null !== $entity ) {

				// Get the internal URI.
				$internal_uri              = $this->entity_service->get_uri( $entity->ID );
				$mappings[ $external_uri ] = $internal_uri;
			}
		}

		foreach ( $mappings as $external_uri => $internal_uri ) {

			// Move the data from the external URI to the internal URI.
			if ( ! isset( $this->json->entities->{$internal_uri} ) ) {
				$this->json->entities->{$internal_uri} = $this->json->entities->{$external_uri};
			}

			// Ensure sameAs is an array.
			if ( ! isset( $this->json->entities->{$internal_uri}->sameAs )
			     || ! is_array( $this->json->entities->{$internal_uri}->sameAs ) ) {
				$this->json->entities->{$internal_uri}->sameAs = array();
			}

			// Add the external URI as sameAs.
			$this->json->entities->{$internal_uri}->sameAs[] = $external_uri;

			// Finally remove the external URI.
			unset( $this->json->entities->{$external_uri} );
		}

		if ( isset( $this->json->annotations ) ) {
			foreach ( $this->json->annotations as $key => $annotation ) {
				if ( isset( $annotation->entityMatches ) ) {
					foreach ( $annotation->entityMatches as $match ) {
						if ( isset( $match->entityId ) && isset( $mappings[ $match->entityId ] ) ) {
							$match->entityId = $mappings[ $match->entityId ];
						}
					}
				}
			}
		}

		return $this;
	}

	public function add_occurrences( $content ) {

		// Try to get all the disambiguated annotations and bail out if an error occurs.
		if ( false === preg_match_all(
				'|<span\s+id="([^"]+)"\s+class="textannotation\s+disambiguated(?=[\s"])[^"]*"\s+itemid="([^"]*)">|',
				$content,
				$matches,
				PREG_SET_ORDER
			) ) {
			return $this;
		}

		// Get the annotations' ids indexed by entity ids.
		$occurrences = array_reduce( $matches, function ( $carry, $item ) {
			$annotation_id = $item[1];
			$item_id       = $item[2];
			if ( ! isset( $carry[ $item_id ] ) ) {
				$carry[ $item_id ] = array();
			}

			$carry[ $item_id ][] = $annotation_id;

			return $carry;
		}, array() );

		foreach ( array_keys( $occurrences ) as $id ) {

			// If the entity isn't there, add it.
			if ( ! isset( $this->json->entities->{$id} ) ) {
				$entity = $this->get_local_entity( $id );

				// Entity not found in the local vocabulary, continue to the next one.
				if ( false === $entity ) {
					continue;
				}

				$this->json->entities->{$id} = $entity;
			}
		}

		// Here we're adding back some data structures required by the client-side code.
		//
		// We're adding:
		//  1. the .entities[entity_id].occurrences array with the annotations' ids.
		//  2. the .entities[entity_id].annotations[annotation_id] = { id: annotation_id } map.
		//
		// Before 3.23.0 this was done by the client-side code located in src/coffee/editpost-widget/app.services.AnalysisService.coffee
		// function `preselect`, which was called by src/coffee/editpost-widget/app.services.EditorService.coffee in
		// `embedAnalysis`.
		foreach ( $this->json->entities as $id => $entity ) {
			$this->json->entities->{$id}->occurrences = isset( $occurrences[ $id ] ) ? $occurrences[ $id ] : array();;

			foreach ( $this->json->entities->{$id}->occurrences as $annotation_id ) {
				$this->json->entities->{$id}->annotations[ $annotation_id ] = array(
					'id' => $annotation_id,
				);
			}
		}

		return $this;
	}

	private function get_local_entity( $uri ) {

		$entity = $this->entity_uri_service->get_entity( $uri );

		if ( null === $entity ) {
			return false;
		}

		$type   = $this->entity_type_service->get( $entity->ID );
		$images = $this->post_image_storage->get( $entity->ID );

		return (object) array(
			'id'          => $uri,
			'label'       => $entity->post_title,
			'description' => $entity->post_content,
			'sameAs'      => wl_schema_get_value( $entity->ID, 'sameAs' ),
			'mainType'    => str_replace( 'wl-', '', $type['css_class'] ),
			'types'       => wl_get_entity_rdf_types( $entity->ID ),
			'images'      => $images,
		);
	}

	/**
	 * Get the string representation of the JSON.
	 *
	 * @return false|string The string representation or false in case of error.
	 */
	public function to_string() {

		// Add the `JSON_UNESCAPED_UNICODE` only for PHP 5.4+.
		$options = ( version_compare( PHP_VERSION, '5.4', '>=' )
			? JSON_UNESCAPED_UNICODE : 0 );

		return wp_json_encode( $this->json, $options );

	}

	/**
	 * Create an Analysis_Response_Ops instance given the provided JSON structure.
	 *
	 * @param mixed $json The JSON structure.
	 *
	 * @return Analysis_Response_Ops A new Analysis_Response_Ops instance.
	 */
	public static function create( $json ) {

		return new static(
			\Wordlift_Entity_Uri_Service::get_instance(),
			\Wordlift_Entity_Service::get_instance(),
			\Wordlift_Entity_Type_Service::get_instance(),
			\Wordlift_Storage_Factory::get_instance()->post_images(),
			$json );
	}

	/**
	 * Create an Analysis_Response_Ops instance given the provided http response.
	 *
	 * @param array $response {
	 *
	 * @type string $body The response body.
	 * }
	 *
	 * @return Analysis_Response_Ops A new Analysis_Response_Ops instance.
	 * @throws \Exception if the provided response doesn't contain a `body` element.
	 */
	public static function create_with_response( $response ) {

		if ( ! isset( $response['body'] ) ) {
			throw new \Exception( "`body` is required in response." );
		}

		return static::create( json_decode( $response['body'] ) );
	}

}
