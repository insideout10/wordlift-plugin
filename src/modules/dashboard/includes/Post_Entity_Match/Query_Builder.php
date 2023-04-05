<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Escape;
use Wordlift\Modules\Dashboard\Match\Match_Query_Builder;

/**
 * This class builds the query to extract the following parameters from
 * the various table by applying the criteria on the post_type. The following columns are
 * extracted in the resulting query
 * 'about_jsonld' => Maps to the matched jsonld
 * 'id' => The post id
 * 'name' => The post title,
 * 'match_id' => The unique id on the wl_entities table,
 */
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
		SELECT p.ID as id, e.about_jsonld as match_jsonld,
		       parent.post_title as name, p.post_title as recipe_name, e.id AS match_id FROM {$wpdb->prefix}posts p
			INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
			INNER JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
			LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id
			WHERE e.content_type = %d
		";
		$this->cursor()
		->post_type()
		->has_match()
		->order_by()
		->limit();
	}

	public function post_type() {
		$post_types = $this->params['post_types'];

		if ( ! isset( $post_types ) ) {
			return $this;
		}
		$post_types_sql = Escape::sql_array( $post_types );
		$this->sql     .= " AND p.post_type IN ({$post_types_sql}) ";

		return $this;
	}

}
