<?php

namespace Wordlift\Modules\Food_Kg\Term_Entity;

use Wordlift\Modules\Common\Synchronization\Store;

class Food_Kg_Ingredients_Term_Store implements Store {

	public function list_items( $id_greater_than, $batch_size ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"
					SELECT t.term_id, t.name FROM $wpdb->terms t
	                INNER JOIN $wpdb->term_taxonomy tt
	                 ON tt.term_id = t.term_id AND tt.taxonomy = 'wprm_ingredient'
					WHERE t.term_id > %d
					ORDER BY t.term_id
					LIMIT %d
	                ",
				$id_greater_than,
				$batch_size
			)
		);
	}

	/**
	 * Get the total number of posts to process.
	 *
	 * We only count published posts.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		return $wpdb->get_var(
			"
				SELECT COUNT(1) FROM $wpdb->terms t
	            INNER JOIN $wpdb->term_taxonomy tt
	             ON tt.term_id = t.term_id AND tt.taxonomy = 'wprm_ingredient'
			"
		);
	}

}
