<?php

/**
 *
 * API Data Hooks
 * @author Navdeep Singh <navdeep@wordlift.io>
 * @package Wordlift\Api_Data
 */

namespace Wordlift\Api_Data;

class Api_Data_Hooks {

	const META_KEY = 'wl_schema_url';

	protected $cache_requests = array();

	private $configuration_service;

	public function __construct( $configuration_service ) {

		$this->configuration_service = $configuration_service;
		/**
		 * Hook for Post Save
		 */
		add_action( 'save_post', array( $this, 'post_save_request_delete_cache' ) );
		/**
		 * Check for Meta Key change on Post Save
		 */
		add_action( 'updated_post_meta', array( $this, 'post_meta_request_delete_cache' ), 10, 4 );

		/**
		 * Initiate the purge cache requests
		 */
		add_action( 'shutdown', array( $this, 'init_purge_cache_requests' ) );
	}

	public function post_save_request_delete_cache( $post_id ) {
		$this->delete_cache_for_meta_values( $post_id );
	}

	public function post_meta_request_delete_cache( $meta_id, $post_id, $meta_key, $_meta_value ) {
		$this->cache_requests[] = $post_id;
	}

	public function init_purge_cache_requests() {
		// Bail early.
		if( empty( $this->cache_requests) ) {
			return;
		}

		// Avoid duplicate cache requests.
		$unique_requests = array_unique( $this->cache_requests );

		foreach ( $unique_requests as $key => $post_id ) {
			$this->delete_cache_for_meta_values( $post_id );
		}
	}

	/**
	 * @param integer $post_id
	 *
	 * @return
	 *
	 */
	private function delete_cache_for_meta_values( $post_id ) {

		/**
		 * Get Post Meta Values
		 */
		$values = get_post_meta( $post_id, self::META_KEY, false );

		/**
		 * Iterate over $values array
		 */
		if ( ! empty( $values ) ) {
			foreach ( $values as $link ) {

				/**
				 * Skip the <permalink>
				 */
				if ( $link === '<permalink>' ) {
					$link = get_permalink();
				}

				/**
				 * Sanitize the link
				 */
				$link = $this->sanitize( $link );
				/**
				 * Make actual API DELETE cache request
				 */
				$this->api_call_delete_cache( $link );
			}
		}
	}

	/**
	 * @desc Sanitize the $link
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	private function sanitize( $link ) {
		return preg_replace( '/:\//i', '', $link );
	}

	/**
	 * @desc Do a DELETE request with WP request function
	 *
	 * @param string $path path that goes after the URL eg. "/user/login"
	 *
	 * @return bool  True if successful otherwise false
	 */
	private function api_call_delete_cache( $path ) {

		// Bailout if path is empty
		if ( empty( $path ) ) {
			return false;
		}

		$log = \Wordlift_Log_Service::get_logger( 'api_call_delete_cache' );

		$log->debug( "Delete cache request started:: '$path'" );

		$url  = $this->configuration_service->get_api_url() . 'data/' . $path;
		$args = array(
			'method' => 'DELETE'
		);

		// Execute the request
		$api_response = wp_remote_request( $url, $args );

		// If an error occured, return false
		if ( is_wp_error( $api_response ) || 200 !== (int) $api_response['response']['code'] ) {

			$log->warn( var_export( $api_response, true ) );

			return false;

		}

		$log->debug( "Request executed successfully" );

		return true;

	}
}
