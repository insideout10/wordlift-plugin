<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Query_Builder;

class Query_Builder extends Match_Query_Builder {

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
			WHERE e.content_type = %d
		";

		$this->cursor();
			$this->taxonomy();
			$this->has_match();
			$this->order_by();
			$this->limit();

	}

	public function taxonomy() {
		$taxonomy = $this->params['taxonomy'];
		global $wpdb;
		if ( ! isset( $taxonomy ) ) {
			return $this;
		}

		$this->sql .= $wpdb->prepare( ' AND tt.taxonomy = %s', $taxonomy );
		return $this;
	}

}
