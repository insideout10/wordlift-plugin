<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Escape;
use Wordlift\Modules\Dashboard\Match\Match_Query_Builder;

/**
 * Class Query_Builder
 *
 * @package Wordlift\Modules\Dashboard\Term_Entity_Match
 */
class Query_Builder extends Match_Query_Builder {

	/**
	 * Build.
	 *
	 * @throws \Exception If there was a problem with SQL query.
	 */
	public function build() {

		global $wpdb;
		/**
		 * Why not use JSON_EXTRACT() to extract the match_name ?
		 * As of now the min wp compatibility is 5.3 which requires min mysql version
		 * 5.6, The JSON_* functions are introduced on 5.7 which will break the
		 * compatibility.
		 */
		$this->sql = "
		SELECT t.term_id as id, e.about_jsonld as match_jsonld,
		       t.name, e.id AS match_id FROM {$wpdb->prefix}terms t
			INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
			LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
			AND e.content_type = %d WHERE 1=1
		";

		$this->cursor()
			 ->term_contains()
			 ->taxonomy()
			 ->has_match()
			 ->order_by()
			 ->limit();

	}

	/**
	 * Taxonomy.
	 *
	 * @return $this
	 *
	 * @throws \Exception When supplied argument is not an array, throw exception.
	 */
	public function taxonomy() {
		$taxonomies = $this->params['taxonomies'];

		if ( ! isset( $taxonomies ) ) {
			return $this;
		}
		$sql        = Escape::sql_array( $taxonomies );
		$this->sql .= " AND tt.taxonomy IN ($sql)";

		return $this;
	}

	/**
	 * Ingredient name contains.
	 *
	 * @return $this
	 */
	public function term_contains() {
		global $wpdb;

		// If the term_contains value is a non-empty string, add the filter
		if ( is_string( $this->params['term_contains'] ) && ! empty( $this->params['term_contains'] ) ) {
			$term_contains = $this->params['term_contains'];
			$this->sql    .= $wpdb->prepare( ' AND t.name LIKE %s', '%' . $term_contains . '%' );
		}

		return $this;
	}
}
