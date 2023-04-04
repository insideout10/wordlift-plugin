<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

/**
 * This class builds the query to extract the following parameters from
 * the various table by applying the criteria on the post_type. The following columns are
 * extracted in the resulting query
 * 'about_jsonld' => Maps to the matched jsonld
 * 'id' => The post id
 * 'name' => The post title,
 * 'match_id' => The unique id on the wl_entities table,
 */
class Query_Builder {

	private $sql;

	/**
	 * @var Sort
	 */
	private $sort;

	/**
	 * @param $cursor_field
	 * @param $element
	 * @param $direction
	 * @param $position
	 * @param $sort Sort
	 */
	public function __construct( $element, $direction, $position, $sort ) {

		$this->sort = $sort;

		global $wpdb;
		/**
		 * Why not use JSON_EXTRACT() to extract the match_name ?
		 * As of now the min wp compatibility is 5.3 which requires min mysql version
		 * 5.6, The JSON_* functions are introduced on 5.7 which will break the
		 * compatibility.
		 */
		$this->sql           = "
		SELECT p.ID as id, e.about_jsonld as match_jsonld,
		       parent.post_title as name, p.post_title as recipe_name, e.id AS match_id FROM {$wpdb->prefix}posts p
			INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
			INNER JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
			LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id
			WHERE e.content_type = %d
		";
		$tmp_sql             = " AND {$this->sort->get_field_name()} ";
		$is_included         = ( $element !== 'EXCLUDED' );
		$is_ascending        = ( $direction !== 'DESCENDING' );
		$is_sorted_ascending = $sort->is_ascending();
		switch ( array( $is_ascending, $is_sorted_ascending ) ) {
			case array( true, true ):   // Forward & Ascending Order
			case array( false, false ): // Backward & Descending Order
				$tmp_sql .= ' >';
				break;
			case array( true, false ):  // Forward & Ascending Order
			case array( false, true ):  // Backward & Descending Order
				$tmp_sql .= ' <';
				break;
		}
		if ( $is_included ) {
			$tmp_sql .= '=';
		}
		$tmp_sql .= ' %s';

		// `$tmp_sql` is built dynamically in this function
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$this->sql .= $wpdb->prepare( $tmp_sql, $position );

	}

	public function has_match( $value ) {
		switch ( $value ) {
			case true:
				$this->sql .= ' AND e.about_jsonld IS NOT NULL ';
				break;
			case false:
				$this->sql .= ' AND e.about_jsonld IS NULL ';
				break;
			default:
		}
		return $this;
	}

	public function limit( $limit ) {
		global $wpdb;
		$this->sql .= $wpdb->prepare( ' LIMIT %d', $limit );
		return $this;
	}

	public function post_type( $post_type ) {
		global $wpdb;
		if ( ! isset( $post_type ) ) {
			return $this;
		}

		$this->sql .= $wpdb->prepare( ' AND p.post_type = %s', $post_type );
		return $this;
	}

	public function order_by( $direction ) {
		$sort_order = $this->sort->get_sort_order( $direction, $this->sort->is_ascending() );
		$this->sql .= " ORDER BY {$this->sort->get_field_name()} $sort_order";
		return $this;
	}

	public function build() {
		return $this->sql;
	}

}
