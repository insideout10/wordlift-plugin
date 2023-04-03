<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Sort;

class Query_Builder {

	private $sql;

	/**
	 * @var Sort
	 */
	private $sort;

	public function __construct( $cursor_field, $element, $direction, $position, $sort ) {
		global $wpdb;
		$this->sql           = "
		SELECT t.term_id as id, e.about_jsonld as match_jsonld, t.name, e.id AS match_id FROM {$wpdb->prefix}terms t
			INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
			LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
			WHERE e.content_type = %d
		";
		$tmp_sql             = " AND $cursor_field ";
		$is_included         = ( $element !== 'EXCLUDED' );
		$is_ascending        = ( $direction !== 'DESCENDING' );
		$is_sorted_ascending = ( strpos( $sort, '-' ) !== 0 );
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

		$this->sort = new Sort( $sort );
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

	public function order_by( $direction, $sort_ascending, $sort_field_name ) {
		$sort_order = $this->sort->get_sort_order( $direction, $sort_ascending );
		$this->sql .= " ORDER BY $sort_field_name $sort_order";
		return $this;
	}

	public function build() {
		return $this->sql;
	}

}
