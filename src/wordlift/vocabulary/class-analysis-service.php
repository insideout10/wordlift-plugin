<?php

namespace Cafemedia_Knowledge_Graph;

use Wordlift\Api\Default_Api_Service;


/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Analysis_Service {

	/**
	 * @var Default_Api_Service
	 */
	private $analysis_service;
	/**
	 * @var Options_Cache
	 */
	private $cache_service;


	/**
	 * Tag_Rest_Endpoint constructor.
	 *
	 * @param Default_Api_Service $analysis_service
	 * @param Options_Cache $cache_service
	 */
	public function __construct( $analysis_service, $cache_service ) {

		$this->analysis_service = $analysis_service;

		$this->cache_service = $cache_service;

	}


	/**
	 * Check if entities are in cache, if not return the results from
	 * cache service.
	 *
	 * @param $tag \WP_Term
	 */
	public function get_entities( $tag ) {

		$cache_key    = $tag->term_id;
		$cache_result = $this->cache_service->get( $cache_key );
		if ( $cache_result !== false ) {
			return $cache_result;
		}

		// send the request.
		$response = $this->analysis_service->request(
			'POST',
			"/analysis/single",
			array( 'Content-Type' => 'application/json' ),
			wp_json_encode( array(
				"content"         => $tag->name,
				"contentType"     => "text/plain",
				"version"         => "1.0.0",
				"contentLanguage" => "en",
				"scope"           => "network",
			) )
		);


		if ( ! $response->is_success() ) {
			return false;
		}

		$response = json_decode( $response->get_body(), true );

		if ( ! array_key_exists( 'entities', $response ) ) {
			return false;
		}


		$entities = $this->get_meta_for_entities( $response['entities'] );

		$this->cache_service->put( $cache_key, $entities );

		return $entities;

	}


	private function get_meta( $entity_url ) {


		$cache_results = $this->cache_service->get( $entity_url );

		if ( $cache_results !== false ) {
			return $cache_results;
		}

		$response = wp_remote_get( "https://app.wordlift.io/knowledge-cafemedia-com-food/wp-json/wordlift/v1/jsonld/meta/entity_url?meta_value=" . urlencode($entity_url) );

		if ( ! is_wp_error( $response ) ) {
			$meta = json_decode( wp_remote_retrieve_body( $response ), true );
			$this->cache_service->put( $entity_url, $meta );
			return $meta;
		}

		return array();

	}

	private function get_meta_for_entities( $entities ) {

		$filtered_entities = array();
		foreach ( $entities as $entity ) {
			$entity['meta'] = array();
			$meta           = $this->get_meta( $entity['entityId'] );
			if ( $meta && count( $meta ) > 0 ) {
				$entity['meta'] = $meta[0];
			}
			$filtered_entities[] = $entity;
		}

		return $filtered_entities;

	}


}
