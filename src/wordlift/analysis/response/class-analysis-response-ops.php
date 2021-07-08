<?php
/**
 * This file provides a class to manipulate the analysis response.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.25.0
 * @package Wordlift\Analysis\Response
 */

namespace Wordlift\Analysis\Response;

use stdClass;
use Wordlift\Analysis\Entity_Provider\Entity_Provider_Registry;
use Wordlift\Entity\Entity_Helper;

class Analysis_Response_Ops  {

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


	/**
	 * @var Entity_Helper
	 */
	private $entity_helper;
	/**
	 * @var Entity_Provider_Registry
	 */
	private $entity_provider_registry;

	/**
	 * Analysis_Response_Ops constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service}.
	 * @param Entity_Helper $entity_helper The {@link Entity_Helper}.
	 * @param Entity_Provider_Registry $entity_provider_registry Entity Provider registry.
	 * @param mixed $json The analysis response json.
	 *
	 * @since 3.21.5
	 */
	public function __construct( $entity_uri_service, $entity_helper, $entity_provider_registry, $json ) {

		$this->json                = $json;
		$this->entity_uri_service  = $entity_uri_service;
		$this->entity_helper       = $entity_helper;
		$this->entity_provider_registry = $entity_provider_registry;
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
		$uris     = array_keys( get_object_vars( $this->json->entities ) );
		$mappings = $this->entity_helper->map_many_to_local( $uris );

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

	/**
	 * Add occurrences by parsing the provided html content.
	 *
	 * @param string $content The html content with annotations.
	 *
	 * @return Analysis_Response_Ops The {@link Analysis_Response_Ops} instance.
	 *
	 * @since 3.23.7 refactor the regex pattern to take into account that there might be css classes between textannotation
	 *  and disambiguated.
	 *
	 * @link https://github.com/insideout10/wordlift-plugin/issues/1001
	 */
	public function add_occurrences( $content ) {

		// Try to get all the disambiguated annotations and bail out if an error occurs.
		if ( false === preg_match_all(
				'|<span\s+id="([^"]+)"\s+class="textannotation\s+(?:\S+\s+)?disambiguated(?=[\s"])[^"]*"\s+itemid="([^"]*)">(.*?)</span>|',
				$content,
				$matches,
				PREG_OFFSET_CAPTURE
			) ) {
			return $this;
		}

		if ( empty( $matches ) ) {
			return $this;
		}

		$parse_data = array_reduce( range( 0, count( $matches[1] ) - 1 ), function ( $carry, $i ) use ( $matches ) {
			if ( empty( $matches[0] ) ) {
				return $carry;
			}

			$start         = $matches[0][ $i ][1];
			$end           = $start + strlen( $matches[0][ $i ][0] );
			$annotation_id = $matches[1][ $i ][0];
			$item_id       = $matches[2][ $i ][0];
			$text          = $matches[3][ $i ][0];

			$annotation               = new StdClass;
			$annotation->annotationId = $annotation_id;
			$annotation->start        = $start;
			$annotation->end          = $end;
			$annotation->text         = $text;

			$entity_match                = new StdClass;
			$entity_match->confidence    = 100;
			$entity_match->entityId      = $item_id;
			$annotation->entityMatches[] = $entity_match;

			$carry['annotations'][ $annotation_id ] = $annotation;
			$carry['occurrences'][ $item_id ][]     = $annotation_id;

			return $carry;
		}, array( 'annotations' => array(), 'occurrences' => array(), ) );

		$annotations = $parse_data['annotations'];
		$occurrences = $parse_data['occurrences'];

		foreach ( array_keys( $occurrences ) as $item_id ) {

			// If the entity isn't there, add it.
			if ( ! is_bool( $this->json ) && ! isset( $this->json->entities->{$item_id} ) ) {
				$entity = $this->entity_provider_registry->get_local_entity( $item_id );

				// Entity not found in the local vocabulary, continue to the next one.
				if ( false === $entity ) {
					continue;
				}

				$this->json->entities->{$item_id} = $entity;
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

		if ( ! is_bool( $this->json ) ) {
			foreach ( $this->json->entities as $id => $entity ) {
				$this->json->entities->{$id}->occurrences = isset( $occurrences[ $id ] ) ? $occurrences[ $id ] : array();;

				foreach ( $this->json->entities->{$id}->occurrences as $annotation_id ) {
					$this->json->entities->{$id}->annotations[ $annotation_id ] = array(
						'id' => $annotation_id,
					);
				}
			}
		}

		// Add the missing annotations. This allows the analysis response to work also if we didn't receive results
		// from the analysis API.
		foreach ( $annotations as $annotation_id => $annotation ) {

			if ( ! is_bool( $this->json ) && ! isset( $this->json->annotations->{$annotation_id} ) ) {
				$this->json->annotations->{$annotation_id} = $annotation;
			}

		}

		return $this;
	}

	/**
	 * Add local entities
	 *
	 * @return Analysis_Response_Ops The {@link Analysis_Response_Ops} instance.
	 *
	 * @since 3.27.6
	 *
	 * @link https://github.com/insideout10/wordlift-plugin/issues/1178
	 */
	public function add_local_entities() {

		// Populating the local entities object
		$entities = array();
		foreach ( $this->json->annotations as $annotation ) {
			foreach ( $annotation->entityMatches as $entity_matches ) {

				$entity_id         = $this->entity_uri_service->get_post_id_from_url( $entity_matches->entityId );
				$serialized_entity = wl_serialize_entity( $entity_id );

				if ( $serialized_entity ) {
					$serialized_entity['entityId'] = $serialized_entity['id'];
					unset( $serialized_entity['id'] );

					$entities[ $entity_matches->entityId ] = $serialized_entity;
				}

			}
		}

		// Adding occurrences and annotations data structures required by the client-side code.
		foreach ( $entities as $entity_id => $entity ) {
			foreach ( $this->json->annotations as $annotation ) {
				if ( $annotation->entityMatches[0]->entityId === $entity_id ) {
					$entities[ $entity_id ]['occurrences'][]                                  = $annotation->annotationId;
					$entities[ $entity_id ]['annotations'][ $annotation->annotationId ]['id'] = $annotation->annotationId;
				}
			}
		}

		$this->json->entities = $entities;

		return $this;

	}


	/**
	 * Return the JSON response.
	 *
	 * @return mixed The JSON response.
	 * @since 3.24.2
	 */
	public function get_json() {

		return $this->json;
	}

	/**
	 * Get the string representation of the JSON.
	 *
	 * @return false|string The string representation or false in case of error.
	 */
	public function to_string() {

		// Add the `JSON_UNESCAPED_UNICODE` only for PHP 5.4+.
		$options = ( version_compare( PHP_VERSION, '5.4', '>=' )
			? 256 : 0 );

		return wp_json_encode( $this->json, $options );
	}

}
