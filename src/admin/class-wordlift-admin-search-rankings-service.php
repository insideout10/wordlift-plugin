<?php
/**
 * Services: Search Rankings.
 *
 * Provides a service to retrieve entity rankings.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Search_Rankings_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Search_Rankings_Service {

	/**
	 * The {@link Wordlift_Api_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Api_Service $api_service The {@link Wordlift_Api_Service} instance.
	 */
	private $api_service;

	/**
	 * Wordlift_Admin_Search_Rankings_Service constructor.
	 *
	 * @since 3.20.0
	 *
	 * @param $api_service
	 */
	public function __construct( $api_service ) {

		$this->api_service = $api_service;

	}

	/**
	 * Get the entity rankings.
	 *
	 * @since 3.20.0
	 * @return string|object|WP_Error The response structure if successful, a plain text if the response isn't recognized
	 * or a {@see WP_Error} instance.
	 */
	public function get() {

		return $this->api_service->get( 'entityrank' );
	}

	/**
	 * Get the average position of the ranking keywords.
	 *
	 * It's highly suggested for consumers to cache the response.
	 *
	 * @since 3.20.0
	 *
	 * @return false|float|int
	 */
	public function get_average_position() {

		// Get the search rankings.
		$search_rankings = $this->get();

		// In case of error or invalid response, return false.
		if ( is_wp_error( $search_rankings )
		     || ! isset( $search_rankings->children ) ) {
			return false;
		}

		// Get the rankings.
		$ranks = array();
		foreach ( $search_rankings->children as $child ) {
			// Skip in case of non conforming response.
			if ( ! isset( $child->score->rankings ) ) {
				continue;
			}
			foreach ( $child->score->rankings as $ranking ) {
				// Skip in case of non confirming response.
				if ( ! isset( $ranking->rank )
				     || ! is_numeric( $ranking->rank ) ) {
					continue;
				}
				// Accumulate the ranks.
				$ranks[] = $ranking->rank;
			}
		}

		// We couldn't find any data.
		if ( empty( $ranks ) ) {
			return false;
		}

		// Finally return the average.
		return array_sum( $ranks ) / count( $ranks );
	}

}
