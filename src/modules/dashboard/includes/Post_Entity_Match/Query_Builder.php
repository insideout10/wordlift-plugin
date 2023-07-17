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
		 *
		 *  Returns an array of rows where each row contains
		 * 'post_title' => The title of the post
		 * 'id'   => The id of the post
		 * 'parent_post_title' => The title of the post linked to this post via wprm_parent_post_id property
		 * ( this is only applicable when the post is wprm_recipe, returns null if not present )
		 * 'parent_post_id'  => The id of the linked parent post.
		 * 'match_jsonld' => The matched `about_jsonld` column from wl_entities.
		 * 'match_id' => This id points to id column of wl_entities table.
		 */
		$this->sql = "
		SELECT p.ID as id,
		       p.post_title as post_title,
		       p.post_status as post_status,
		       p.post_modified_gmt as date_modified_gmt,
		       parent.post_title as parent_post_title,
		       parent.ID as parent_post_id,
		       e.about_jsonld as match_jsonld,
		       e.id AS match_id
			FROM {$wpdb->prefix}posts p
			LEFT JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
			LEFT JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
			LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id AND e.content_type = %d
			WHERE 1=1 
		";

		$this->cursor()
			 ->post_type()
			 ->post_status()
			 ->has_match()
			 ->order_by()
			 ->limit();
	}

	private function post_status() {
		global $wpdb;

		// If a value has been provided and it's either 'draft' or 'publish', we add the related filter.
		if ( is_string( $this->params['post_status'] ) && in_array(
			$this->params['post_status'],
			array(
				'publish',
				'draft',
			),
			true
		) ) {

			$this->sql .= $wpdb->prepare( ' AND p.post_status = %s', $this->params['post_status'] );

			return $this;
		}

		// By default we filter on 'draft' and 'publish'.
		$this->sql .= " AND p.post_status IN ( 'draft', 'publish' )";

		return $this;
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
