<?php

namespace Wordlift\Modules\Events\Term_Entity;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift_Url_Property_Service;

/**
 * Class Events_Term_Entity_Jsonld
 *
 * @package Wordlift\Modules\Events\Term_Entity
 */
class Events_Term_Entity_Jsonld {

	/**
	 * The {@link Api_Service} used to communicate with the remote APIs.
	 *
	 * @access private
	 * @var Default_Api_Service
	 */
	private $api_service;

	/**
	 * @param Default_Api_Service $api_service
	 */
	public function __construct( Default_Api_Service $api_service ) {
		$this->api_service = $api_service;
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_filter( 'wl_term_jsonld_array', array( $this, 'set_events_request' ), 90, 3 );
	}

	/**
	 * Set term jsonld array event.
	 *
	 * @param $jsonld_arr array The final jsonld before outputting to page.
	 * @param $term_id int The term id for which the jsonld is generated.
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 */
	public function set_events_request( $jsonld_arr, $term_id, $context ) {
		// If context is not PAGE or the array is empty, return early.
		if ( Jsonld_Context_Enum::PAGE !== $context || empty( $jsonld_arr[0] ) ) {
			return;
		}

		// Flag to indicate if we should make an API request.
		$change_status = false;

		// Get data from the array.
		$data = $jsonld_arr[0];

		// Fetch the initial 'about' and 'mentions' counts from term meta.
		$counts = [
			'about'    => get_term_meta( $term_id, 'wl_about_count', true ) ? : 0,
			'mentions' => get_term_meta( $term_id, 'wl_mentions_count', true ) ? : 0,
		];

		// Iterate over the counts array.
		foreach ( $counts as $key => $count ) {
			// Check if data has 'about' or 'mentions' and the count is different from the existing meta value.
			if ( ! empty( $data[ $key ] ) ) {
				$new_count = count( $data[ $key ] );
				if ( $count !== $new_count ) {
					// Set flag to true if counts have changed.
					$change_status = true;

					// Update the counts array with new count.
					$counts[ $key ] = $new_count;

					// Update term meta with new count.
					update_term_meta( $term_id, 'wl_' . $key . '_count', $new_count );
				}
			}
		}

		// If the count has changed, make the API request.
		if ( $change_status ) {
			$this->api_service->request(
				'POST',
				'/plugin/events',
				[ 'Content-Type' => 'application/json' ],
				wp_json_encode( [
					'source' => 'jsonld',
					'args'   => [
						[ 'about_count' => $counts['about'] ],
						[ 'mentions_count' => $counts['mentions'] ],
					],
					'url'    => $this->get_term_url( $term_id ),
				] ),
				0.001,
				null,
				[ 'blocking' => false ]
			);
		}
	}

	/**
	 * Get term url.
	 *
	 * @param $id
	 *
	 * @return array|false|int|mixed|string|\WP_Error|\WP_Term|null
	 */
	private function get_term_url( $id ) {
		if ( null === $id ) {
			return isset( $_SERVER['REQUEST_URI'] ) ? filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_URL ) : '';
		}

		$maybe_url = get_term_meta( $id, Wordlift_Url_Property_Service::META_KEY, true );
		if ( ! empty( $maybe_url ) ) {
			return $maybe_url;
		}

		return get_term_link( $id );
	}
}
