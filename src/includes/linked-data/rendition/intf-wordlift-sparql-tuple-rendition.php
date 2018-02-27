<?php
/**
 * Abstract: Wordlift Sparql Tuple Rendition
 *
 * @since      3.18.0
 * @package    Wordlift
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
	// @@todo: rename to `get_insert_triples`.
	public function get( $post_id );

	/**
	 * Get delete statement for current post uri.
	 *
	 * @since 3.18.0
	 *
	 * @param $post_id
	 *
	 * @return array An array of delete tuples (`<...> <...> ?o` and `?s <...> <...>`).
	 */
	// @@todo: rename to `get_delete_triples`.
	public function get_delete_statement( $post_id );
}
