<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Sort;

class Query_Builder {

	private $sql;

	/**
	 * @var Match_Sort
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
		$this->sql = "
		SELECT t.term_id as id, e.about_jsonld as match_jsonld,
		       t.name, e.id AS match_id FROM {$wpdb->prefix}terms t
			INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
			LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
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

	public function taxonomy( $taxonomy ) {
		global $wpdb;
		if ( ! isset( $taxonomy ) ) {
			return $this;
		}

		$this->sql .= $wpdb->prepare( ' AND tt.taxonomy = %s', $taxonomy );
		return $this;
	}

	public function order_by( $direction ) {
		$this->sql .= $this->sort->get_orderby_clause( $direction );
		return $this;
	}

	public function build() {
		return $this->sql;
	}

}
