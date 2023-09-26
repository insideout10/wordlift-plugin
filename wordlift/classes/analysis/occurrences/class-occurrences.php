<?php
/**
 * This file provides a interface to process the local entities.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */

namespace Wordlift\Analysis\Occurrences;

interface Occurrences {

	/**
	 * @return array Return json data structure.
	 */
	public function add_occurrences_to_entities( $occurrences, $json, $post_id );

}
