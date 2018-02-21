<?php
/**
 * Interfaces: Sparql Tuple Rendition
 *
 * @since 3.18.0
 * @package Wordlift
 * @subpackage Wordlift/includes/linked_data/rendition
 */

/**
 * Define the {@link Wordlift_Sparql_Tuple_Rendition} interface.
 */
interface Wordlift_Sparql_Tuple_Rendition {

	/**
	 * Get tuple representations for the specified {@link WP_Post}.
	 *
	 * @since 3.18.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of tuples.
	 */
	 function get( $post_id );

}
