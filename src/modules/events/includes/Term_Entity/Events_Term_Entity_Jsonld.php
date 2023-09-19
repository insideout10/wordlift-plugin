<?php

namespace Wordlift\Modules\Events\Term_Entity;

use Wordlift\Api\Api_Service;
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
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * @param Api_Service $api_service
	 */
	public function __construct( Api_Service $api_service ) {
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
	 * @param $data array An array containing jsonld for term and entities.
	 * @param $term_id int The term id for which the jsonld is generated.
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 *
	 * @return array
	 */
	public function set_events_request( $data, $term_id, $context ) {
		$jsonld_arr = $data['jsonld'];
		if ( $this->should_return_early( $context ) ) {
			return $data;
		}

		// Bail out if we don't have a term URL.
		$term_url = $this->get_term_url( $term_id );
		if ( ! is_string( $term_url ) ) {
			return $data;
		}

		$counts = $this->get_initial_counts( $term_id );

		$change_status = $this->update_counts_if_necessary( $jsonld_arr, $counts, $term_id );

		if ( $change_status ) {
			$this->send_api_request( $counts, $term_url );
		}

		return $data;
	}

	/**
	 * If context is not PAGE, return early.
	 *
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 *
	 * @return bool
	 */
	private function should_return_early( $context ) {
		return Jsonld_Context_Enum::PAGE !== $context;
	}

	/**
	 * Fetch the initial 'about' and 'mentions' counts from term meta.
	 *
	 * @param $term_id int The term id for which the jsonld is generated.
	 *
	 * @return int[]
	 */
	private function get_initial_counts( $term_id ) {
		return array(
			'about'    => get_term_meta( $term_id, 'wl_about_count', true ) ? (int) get_term_meta( $term_id, 'wl_about_count', true ) : 0,
			'mentions' => get_term_meta( $term_id, 'wl_mentions_count', true )
				? (int) get_term_meta( $term_id, 'wl_mentions_count', true )
				: 0,
		);
	}

	/**
	 * Update counts if necessary.
	 *
	 * @param $jsonld_arr
	 * @param $counts
	 * @param $term_id
	 *
	 * @return bool
	 */
	private function update_counts_if_necessary( $jsonld_arr, &$counts, $term_id ) {
		// Flag to indicate if we should make an API request.
		$change_status = false;

		// If the $jsonld_arr is empty but the counts were previously more than 0.
		if ( empty( $jsonld_arr[0] ) ) {
			return $this->reset_counts_if_non_zero( $counts, $term_id );
		}

		// Get data from the array.
		$data_arr = $jsonld_arr[0];

		// Iterate over the counts array.
		foreach ( $counts as $type => $type_count ) {
			// Check if data has 'about' or 'mentions' and the count is different from the existing meta value.
			if ( isset( $data_arr[ $type ] ) ) {
				$new_count = count( $data_arr[ $type ] );
				if ( $type_count !== $new_count ) {
					// Set flag to true if counts have changed.
					$change_status = true;

					// Update the counts array with new count.
					$counts[ $type ] = $new_count;

					// Update term meta with new count.
					update_term_meta( $term_id, 'wl_' . $type . '_count', $new_count );
					continue;
				}
			}

			if ( ! isset( $data_arr[ $type ] ) && $type_count > 0 ) {
				// If the 'about' or 'mentions' has become empty, set it to 0.
				$change_status   = true;
				$counts[ $type ] = 0;
				update_term_meta( $term_id, 'wl_' . $type . '_count', 0 );
			}
		}

		return $change_status;
	}

	/**
	 * Reset counts if non zero.
	 *
	 * @param $counts
	 * @param $term_id
	 *
	 * @return bool
	 */
	private function reset_counts_if_non_zero( &$counts, $term_id ) {
		$change_status = false;

		foreach ( $counts as $type => $type_count ) {
			if ( $type_count > 0 ) {
				$change_status   = true;
				$counts[ $type ] = 0;
				update_term_meta( $term_id, 'wl_' . $type . '_count', 0 );
			}
		}

		return $change_status;
	}

	/**
	 * Send api request.
	 *
	 * @param $counts
	 * @param $term_url string The term URL.
	 */
	private function send_api_request( $counts, $term_url ) {
		// If the count has changed, make the API request.
		$this->api_service->request(
			'POST',
			'/plugin/events',
			array( 'Content-Type' => 'application/json' ),
			wp_json_encode(
				array(
					'source' => 'jsonld',
					'args'   => array(
						array( 'about_count' => $counts['about'] ),
						array( 'mentions_count' => $counts['mentions'] ),
					),
					'url'    => $term_url,
				)
			),
			0.001,
			null,
			array( 'blocking' => false )
		);
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
