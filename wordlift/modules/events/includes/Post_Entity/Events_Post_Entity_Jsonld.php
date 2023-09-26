<?php

namespace Wordlift\Modules\Events\Post_Entity;

use Wordlift\Api\Api_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use WPRM_Recipe;

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
		add_filter( 'wl_after_get_jsonld', array( $this, 'handle_after_get_jsonld' ), 90, 3 );
		add_filter( 'wprm_recipe_metadata', array( $this, 'handle_recipe_metadata' ), PHP_INT_MAX - 100, 2 );
	}

	public function handle_after_get_jsonld( $jsonld_arr, $post_id, $context ) {

		if (
			// We're not in a page context.
			Jsonld_Context_Enum::PAGE !== $context ||
			// It's a Recipe post.
			$this->is_a_recipe_post( $post_id )
		) {
			return $jsonld_arr;
		}

		return $this->set_events_request( $jsonld_arr, $post_id );
	}

	private function is_a_recipe_post( $post_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT COUNT(1) 
			FROM $wpdb->postmeta 
			WHERE meta_key = 'wprm_parent_post_id' 
			AND meta_value = %d
		",
				$post_id
			)
		);
	}

	/**
	 * @param $value
	 * @param WPRM_Recipe $recipe
	 *
	 * @return mixed
	 */
	public function handle_recipe_metadata( $value, $recipe ) {
		// We only handle the parent post.
		$parent_post_id = $recipe->parent_post_id();
		if ( is_numeric( $parent_post_id ) && 0 < $parent_post_id ) {
			$this->set_events_request( array( $value ), $parent_post_id, Jsonld_Context_Enum::PAGE );
		}

		return $value;
	}

	/**
	 * Set events request.
	 *
	 * @param $jsonld_arr array The final jsonld before outputting to page.
	 * @param $post_id int The post id for which the jsonld is generated.
	 *
	 * @return array
	 */
	private function set_events_request( $jsonld_arr, $post_id ) {

		// Bail out if we can't get a permalink.
		$permalink = get_permalink( $post_id );
		if ( false === $permalink ) {
			return $jsonld_arr;
		}

		$counts = $this->get_initial_counts_post( $post_id );

		$change_status = $this->update_counts_if_necessary_post( $jsonld_arr, $counts, $post_id );

		if ( $change_status ) {
			$this->send_api_request_post( $counts, $permalink );
		}

		return $jsonld_arr;
	}

	/**
	 * Fetch the initial 'about' and 'mentions' counts from post meta.
	 *
	 * @param $post_id int The post id for which the jsonld is generated.
	 *
	 * @return int[]
	 */
	private function get_initial_counts_post( $post_id ) {
		return array(
			'about'    => get_post_meta( $post_id, 'wl_about_count', true ) ? (int) get_post_meta( $post_id, 'wl_about_count', true ) : 0,
			'mentions' => get_post_meta( $post_id, 'wl_mentions_count', true )
				? (int) get_post_meta( $post_id, 'wl_mentions_count', true )
				: 0,
		);
	}

	/**
	 * Update counts if necessary post.
	 *
	 * @param $jsonld_arr
	 * @param $counts
	 * @param $post_id
	 *
	 * @return bool
	 */
	private function update_counts_if_necessary_post( $jsonld_arr, &$counts, $post_id ) {
		// Flag to indicate if we should make an API request.
		$change_status = false;

		// If the $jsonld_arr is empty but the counts were previously more than 0.
		if ( empty( $jsonld_arr[0] ) ) {
			return $this->reset_counts_if_non_zero_post( $counts, $post_id );
		}

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
					continue;
				}
			}

			if ( ! isset( $data[ $type ] ) && $type_count > 0 ) {
				// If the 'about' or 'mentions' has become empty, set it to 0.
				$change_status   = true;
				$counts[ $type ] = 0;
				update_post_meta( $post_id, 'wl_' . $type . '_count', 0 );
			}
		}

		return $change_status;
	}

	/**
	 * Reset counts if non zero post.
	 *
	 * @param $counts
	 * @param $post_id
	 *
	 * @return bool
	 */
	private function reset_counts_if_non_zero_post( &$counts, $post_id ) {
		$change_status = false;

		foreach ( $counts as $type => $type_count ) {
			if ( $type_count > 0 ) {
				$change_status   = true;
				$counts[ $type ] = 0;
				update_post_meta( $post_id, 'wl_' . $type . '_count', 0 );
			}
		}

		return $change_status;
	}

	/**
	 * Send api request post.
	 *
	 * @param $counts
	 * @param $permalink string The web page URL
	 */
	private function send_api_request_post( $counts, $permalink ) {
		// If the count has changed, make the API request.
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$blocking = apply_filters( 'wl_feature__enable__sync-blocking', false );

		// Apply the filter to the request args
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
					'url'    => $permalink,
				)
			),
			$blocking ? 60 : 0.001,
			null,
			array( 'blocking' => $blocking )
		);
	}
}
