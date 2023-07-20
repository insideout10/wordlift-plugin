<?php

namespace Wordlift\Modules\Events\Post_Entity;

use Wordlift\Api\Api_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Class Events_Post_Entity_Jsonld
 *
 * @package Wordlift\Modules\Events\Post_Entity
 */
class Events_Post_Entity_Jsonld {

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
		add_filter( 'wl_after_get_jsonld', array( $this, 'set_events_request' ), 90, 3 );
	}

	/**
	 * Set events request.
	 *
	 * @param $jsonld_arr array The final jsonld before outputting to page.
	 * @param $post_id int The post id for which the jsonld is generated.
	 * @param $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
	 *
	 * @return array
	 */
	public function set_events_request( $jsonld_arr, $post_id, $context ) {
		// If context is not PAGE, return early.
		if ( Jsonld_Context_Enum::PAGE !== $context ) {
			return $jsonld_arr;
		}

		// Flag to indicate if we should make an API request.
		$change_status = false;

		// Fetch the initial 'about' and 'mentions' counts from post meta.
		$counts = array(
			'about'    => get_post_meta( $post_id, 'wl_about_count', true ) ? (int) get_post_meta( $post_id, 'wl_about_count', true ) : 0,
			'mentions' => get_post_meta( $post_id, 'wl_mentions_count', true )
				? (int) get_post_meta( $post_id, 'wl_mentions_count', true )
				: 0,
		);

		// Check if $jsonld_arr is not empty
		if ( ! empty( $jsonld_arr[0] ) ) {
			// Get data from the array.
			$data = $jsonld_arr[0];

			// Iterate over the counts array.
			foreach ( $counts as $type => $type_count ) {
				// Check if data has 'about' or 'mentions' and the count is different from the existing meta value.
				if ( isset( $data[ $type ] ) ) {
					$new_count = count( $data[ $type ] );

					if ( $type_count !== $new_count ) {
						// Set flag to true if counts have changed.
						$change_status = true;

						// Update the counts array with new count.
						$counts[ $type ] = $new_count;

						// Update post meta with new count.
						update_post_meta( $post_id, 'wl_' . $type . '_count', $new_count );
					}
				} elseif ( $type_count > 0 ) {
					// If the 'about' or 'mentions' has become empty, set it to 0.
					$change_status   = true;
					$counts[ $type ] = 0;
					update_post_meta( $post_id, 'wl_' . $type . '_count', 0 );
				}
			}
		} else {
			// If the $jsonld_arr is empty but the counts were previously more than 0.
			foreach ( $counts as $type => $type_count ) {
				if ( $type_count > 0 ) {
					$change_status   = true;
					$counts[ $type ] = 0;
					update_post_meta( $post_id, 'wl_' . $type . '_count', 0 );
				}
			}
		}

		// If the count has changed, make the API request.
		if ( $change_status ) {
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
						'url'    => get_permalink( $post_id ),
					)
				),
				0.001,
				null,
				array( 'blocking' => false )
			);
		}

		return $jsonld_arr;
	}
}
