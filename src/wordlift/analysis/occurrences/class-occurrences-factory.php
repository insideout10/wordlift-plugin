<?php
/**
 * This file provides a factory to choose a Occurrences strategy based on feature active.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */
namespace Wordlift\Analysis\Occurrences;

class Occurrences_Factory {

	public function get_instance( $post_id ) {

		return Default_Strategy::get_instance();
	}


}