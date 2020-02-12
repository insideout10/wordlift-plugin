<?php
/**
 * This file provides the Linked Data autocomplete service.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.24.2
 * @package Wordlift\Autocomplete
 */

namespace Wordlift\Autocomplete;

use Wordlift\Entity\Entity_Helper;
use Wordlift_Log_Service;

class Linked_Data_Autocomplete_Service implements Autocomplete_Service {

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

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
	 * The {@link Class_Wordlift_Autocomplete_Service} instance.
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 * @param Entity_Helper $entity_helper
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service
	 *
	 * @since 3.15.0
	 */
	public function __construct( $configuration_service, $entity_helper, $entity_uri_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Autocomplete_Service' );

		$this->configuration_service = $configuration_service;
		$this->entity_helper         = $entity_helper;
		$this->entity_uri_service    = $entity_uri_service;

	}

	/**
	 * Make request to external API and return the response.
	 *
	 * @param string $query The search string.
	 * @param string $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                      in Wikipedia. By default is "cloud".
	 * @param array|string $exclude The exclude parameter string.
	 *
	 * @return array $response The API response.
	 * @since 3.15.0
	 *
	 */
	public function query( $query, $scope = 'cloud', $exclude = '' ) {

		$results = $this->do_query( $query, $scope, $exclude );

		$uris = array_reduce( $results, function ( $carry, $result ) {

			$carry[] = $result['id'];

			return array_merge( $carry, $result['sameAss'] );
		}, array() );

		$mappings = $this->entity_helper->map_many_to_local( $uris );

		$that           = $this;
		$mapped_results = array_map( function ( $result ) use ( $that, $mappings ) {

			if ( $that->entity_uri_service->is_internal( $result['id'] ) ) {
				return $result;
			}

			$uris = array_merge( $result['id'], $result['sameAss'] );

			foreach ( $uris as $uri ) {
				if ( isset( $mappings[ $uri ] ) ) {
					return $that->entity_uri_service->get_entity( $mappings[ $uri ] );
				}
			}

			return $result;
		}, $results );

		return $mapped_results;
	}

	private function do_query( $query, $scope = 'cloud', $exclude = '' ) {
		$url = $this->build_request_url( $query, $exclude, $scope );

		// Return the response.
		$response = wp_remote_get( $url, array(
			'timeout' => 30
		) );

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
	 * @param string $query The search string.
	 * @param array|string $exclude The exclude parameter.
	 * @param string $scope The search scope: "local" will search only in the local dataset; "cloud" will search also
	 *                      in Wikipedia. By default is "cloud".
	 *
	 * @return string Built url.
	 * @since 3.15.0
	 *
	 */
	private function build_request_url( $query, $exclude, $scope ) {
		$args = array(
			'key'      => $this->configuration_service->get_key(),
			'language' => $this->configuration_service->get_language_code(),
			'query'    => $query,
			'scope'    => $scope,
			'limit'    => 10,
		);

		// Add args to URL.
		$request_url = add_query_arg(
			urlencode_deep( $args ),
			$this->configuration_service->get_autocomplete_url()
		);

		// Add the exclude parameter.
		if ( ! empty( $exclude ) ) {
			foreach ( (array) $exclude as $item ) {
				$request_url .= "&exclude=" . urlencode( $item );
			}
		}

		// return the built url.
		return $request_url;
	}

}
