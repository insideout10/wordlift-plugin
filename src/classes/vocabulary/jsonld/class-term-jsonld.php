<?php
/**
 * @since 3.31.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary\Jsonld;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift_Log_Service;

/**
 * Class Term_Jsonld
 *
 * @package Wordlift\Vocabulary\Jsonld
 */
class Term_Jsonld {

	/**
	 * The {@link Api_Service} used to communicate with the remote APIs.
	 *
	 * @access private
	 * @var Default_Api_Service
	 */
	private $api_service;

	/**
	 * Init.
	 */
	public function init() {
		$this->api_service = Default_Api_Service::get_instance();

		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array' ), 10, 2 );
		add_filter( 'wl_term_jsonld_array', array( $this, 'wl_term_jsonld_array_event' ), 90, 3 );
	}

	/**
	 * Wl term jsonld array.
	 *
	 * @param $jsonld_array
	 * @param $term_id
	 *
	 * @return array|mixed
	 */
	public function wl_term_jsonld_array( $jsonld_array, $term_id ) {

		$entities = Jsonld_Utils::get_matched_entities_for_term( $term_id );

		if ( count( $entities ) > 0 ) {
			$entity             = array_shift( $entities );
			$entity['@context'] = 'http://schema.org';

			$term_link = get_term_link( $term_id );
			if ( is_wp_error( $term_link ) ) {
				Wordlift_Log_Service::get_logger( get_class() )
					->error( "Term $term_id returned an error: " . $term_link->get_error_message() );

				return $jsonld_array;
			}

			$entity['@id']              = $term_link . '/#id';
			$entity['url']              = $term_link;
			$entity['mainEntityOfPage'] = $term_link;
			$jsonld_array['jsonld'][]   = $entity;
		}

		return $jsonld_array;
	}

	/**
	 * Set term jsonld array event.
	 *
	 * @param $jsonld_arr array The final jsonld before outputting to page.
	 * @param $term_id int The term id for which the jsonld is generated.
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 */
	public function wl_term_jsonld_array_event( $jsonld_arr, $term_id, $context ) {
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
}
