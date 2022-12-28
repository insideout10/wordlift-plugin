<?php

namespace Wordlift\Vocabulary;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Vocabulary\Cache\Cache;
use Wordlift\Vocabulary\Data\Entity_List\Default_Entity_List;

/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Analysis_Service {

	/**
	 * @var Default_Api_Service
	 */
	private $api_service;
	/**
	 * @var Cache
	 */
	private $cache_service;
	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * Tag_Rest_Endpoint constructor.
	 *
	 * @param Default_Api_Service $api_service
	 * @param Cache               $cache_service
	 */
	public function __construct( $api_service, $cache_service ) {

		$this->api_service = $api_service;

		$this->cache_service = $cache_service;

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

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
		if ( false !== $cache_result ) {
			return $cache_result;
		}

		// send the request.
		$tag_name = $tag->name;

		$entities = $this->get_entities_by_search_query( $tag_name );

		if ( ! $entities ) {
			return false;
		}

		$this->cache_service->put( $cache_key, $entities );

		return $entities;

	}

	/**
	 * @param $entity_url string
	 * Formats the entity url from https://foo.com/some/path to
	 * https/foo.com/some/path
	 *
	 * @return bool|string
	 */
	public static function format_entity_url( $entity_url ) {
		$result = wp_parse_url( $entity_url );
		if ( ! $result ) {
			return false;
		}
		if ( ! array_key_exists( 'scheme', $result )
			 || ! array_key_exists( 'host', $result )
			 || ! array_key_exists( 'path', $result ) ) {
			return false;
		}

		return $result['scheme'] . '/' . $result['host'] . $result['path'];
	}

	private function get_meta( $entity_url ) {

		$cache_results = $this->cache_service->get( $entity_url );

		if ( false !== $cache_results ) {
			return $cache_results;
		}

		$formatted_url = self::format_entity_url( $entity_url );

		if ( ! $formatted_url ) {
			return array();
		}

		$meta_url = 'https://api.wordlift.io/id/' . $formatted_url;

		$response = wp_remote_get( $meta_url );

		if ( is_wp_error( $response )
			 || ! isset( $response['response']['code'] )
			 || 2 !== (int) $response['response']['code'] / 100 ) {
			return false;
		}

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
			if ( $meta ) {
				$meta                = Default_Entity_List::compact_jsonld( $meta );
				$entity['meta']      = $meta;
				$filtered_entities[] = $entity;
			}
		}

		return $filtered_entities;

	}

	/**
	 * @param $tag_name
	 *
	 * @return array
	 */
	public function get_entities_by_search_query( $tag_name ) {
		$response = $this->api_service->request(
			'POST',
			'/analysis/single',
			array( 'Content-Type' => 'application/json' ),
			wp_json_encode(
				array(
					'content'         => $tag_name,
					'contentType'     => 'text/plain',
					'version'         => '1.0.0',
					'contentLanguage' => 'en',
					'scope'           => $this->get_scope(),
				)
			)
		);

		if ( ! $response->is_success() ) {
			return false;
		}

		$response = json_decode( $response->get_body(), true );

		if ( ! array_key_exists( 'entities', $response ) ) {
			return false;
		}

		$entities = $this->get_meta_for_entities( $response['entities'] );

		return $entities;
	}

	public function get_scope() {
		$service = \Wordlift_Configuration_Service::get_instance();

		return count( $service->get_network_dataset_ids() ) > 0 ? 'network-only' : 'cloud-only';
	}

}
