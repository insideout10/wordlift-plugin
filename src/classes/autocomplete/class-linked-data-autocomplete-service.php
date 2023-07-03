<?php
/**
 * This file provides the Linked Data autocomplete service.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Entity\Entity_Helper;
use Wordlift_Configuration_Service;
use Wordlift_Log_Service;
use Wordlift_Post_Excerpt_Helper;
use Wordlift_Schema_Service;

class Linked_Data_Autocomplete_Service implements Autocomplete_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;
	private $entity_helper;
	private $entity_uri_service;
	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * The {@link Class_Wordlift_Autocomplete_Service} instance.
	 *
	 * @param Entity_Helper                $entity_helper
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 * @param \Wordlift_Entity_Service     $entity_service
	 *
	 * @since 3.15.0
	 */
	public function __construct( $entity_helper, $entity_uri_service, $entity_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Autocomplete_Service' );

		$this->entity_helper      = $entity_helper;
		$this->entity_uri_service = $entity_uri_service;
		$this->entity_service     = $entity_service;

	}

	/**
	 * Make request to external API and return the response.
	 *
	 * @param string       $query The search string.
	 * @param string       $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                            in Wikipedia. By default is "cloud".
	 * @param array|string $excludes The exclude parameter string.
	 *
	 * @return array $response The API response.
	 * @since 3.15.0
	 */
	public function query( $query, $scope = 'cloud', $excludes = array() ) {

		$results = $this->do_query( $query, $scope, $excludes );

		$uris = array_reduce(
			$results,
			function ( $carry, $result ) {

				$carry[] = $result['id'];

				return array_merge( $carry, $result['sameAss'] );
			},
			array()
		);

		$mappings = $this->entity_helper->map_many_to_local( $uris );

		$that           = $this;
		$mapped_results = array_map(
			function ( $result ) use ( $that, $mappings ) {

				if ( $that->entity_uri_service->is_internal( $result['id'] ) ) {
					  return $result;
				}

				$uris = array_merge( (array) $result['id'], $result['sameAss'] );

				foreach ( $uris as $uri ) {
					if ( isset( $mappings[ $uri ] ) ) {
						$local_entity = $that->entity_uri_service->get_entity( $mappings[ $uri ] );

						return $that->post_to_autocomplete_result( $mappings[ $uri ], $local_entity );
					}
				}

				return $result;
			},
			$results
		);

		return $mapped_results;
	}

	private function do_query( $query, $scope = 'cloud', $exclude = '' ) {
		$url = $this->build_request_url( $query, $exclude, $scope );

		// Return the response.
		$response = Default_Api_Service::get_instance()->get( $url )->get_response();

		// If the response is valid, then send the suggestions.
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			// Echo the response.
			return json_decode( wp_remote_retrieve_body( $response ), true );
		} else {
			// Default error message.
			$error_message = 'Something went wrong.';

			// Get the real error message if there is WP_Error.
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
			}

			$this->log->error( $error_message );

			return array();
		}
	}

	/**
	 * Build the autocomplete url.
	 *
	 * @param string       $query The search string.
	 * @param array|string $exclude The exclude parameter.
	 * @param string       $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                            in Wikipedia. By default is "cloud".
	 *
	 * @return string Built url.
	 * @since 3.15.0
	 */
	private function build_request_url( $query, $exclude, $scope ) {
		$configuration_service = Wordlift_Configuration_Service::get_instance();

		$args = array(
			'key'      => $configuration_service->get_key(),
			'language' => $configuration_service->get_language_code(),
			'query'    => $query,
			'scope'    => $scope,
			'limit'    => 10,
		);

		// Add args to URL.
		$request_url = add_query_arg( urlencode_deep( $args ), '/autocomplete' );

		// Add the exclude parameter.
		if ( ! empty( $exclude ) ) {
			$request_url .= '&exclude=' . implode( '&exclude=', array_map( 'urlencode', (array) $exclude ) );
		}

		// return the built url.
		return $request_url;
	}

	private function post_to_autocomplete_result( $uri, $post ) {

		return array(
			'id'           => $uri,
			'label'        => array( $post->post_title ),
			'labels'       => $this->entity_service->get_alternative_labels( $post->ID ),
			'descriptions' => array( Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post ) ),
			'scope'        => 'local',
			'sameAss'      => get_post_meta( $post->ID, Wordlift_Schema_Service::FIELD_SAME_AS ),
			// The following properties are less relevant because we're linking entities that exist already in the
			// vocabulary. That's why we don't make an effort to load the real data.
			'types'        => array( 'http://schema.org/Thing' ),
			'urls'         => array(),
			'images'       => array(),
		);
	}

}
