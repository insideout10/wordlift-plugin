<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Object_Type_Enum;

class Term_Entity_Match_Service {

	public function list_items( $args ) {
		global $wpdb;

		$params = wp_parse_args(
			$args,
			array(
				'position'  => null,
				'element'   => 'INCLUDED',
				'direction' => 'ASCENDING',
				'limit'     => 20,
				'sort'      => '+id',
				// Query.
				'taxonomy'  => null,
				'has_match' => null,
			)
		);

		$sql = "
		SELECT t.term_id as id, e.about_jsonld as match_jsonld, t.name, e.id AS match_id FROM {$wpdb->prefix}terms t
			INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
			LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
			WHERE e.content_type = %d
		";

		$sort_ascending      = $this->is_sort_ascending( $params['sort'] );
		$sort_sql_field_name = $this->get_sort_sql_field_name( $params['sort'] );
		$sort_property_name  = $this->get_sort_property_name( $params['sort'] );

		$query_builder = new Query_Builder(
			$sort_sql_field_name,
			$params['element'],
			$params['direction'],
			$params['position'],
			$params['sort']
		);
		$query         = $query_builder
			->taxonomy( $params['taxonomy'] )
			->has_match( $params['has_match'] )
			->order_by( $params['direction'], $sort_ascending, $sort_sql_field_name )
			->limit( $params['limit'] )
			->build();

		$this->build_sql_cursor( $sql, $sort_sql_field_name, $params['element'], $params['direction'], $params['position'], $params['sort'] );
		$this->build_sql_query_taxonomy( $sql, $params['taxonomy'] );
		$this->build_sql_query_has_match( $sql, $params['has_match'] );
		$this->build_sql_order_by( $sql, $params['direction'], $sort_ascending, $sort_sql_field_name );
		$this->build_sql_limit( $sql, $params['limit'] );

		$items = $wpdb->get_results(
		// Each function above is preparing `$sql` by using `$wpdb->prepare`.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->prepare( $query, Object_Type_Enum::TERM )
		);

		usort(
			$items,
			function ( $a, $b ) use ( $sort_ascending, $sort_property_name ) {
				if ( $a->{$sort_property_name} === $b->{$sort_property_name} ) {
					return 0;
				}

				switch ( array(
					$sort_ascending,
					$a->{$sort_property_name} > $b->{$sort_property_name},
				) ) {
					case array( true, true ):
					case array( false, false ):
						return 1;
					case array( true, false ):
					case array( false, true ):
						return - 1;
				}

				return 0;
			}
		);

		return $items;
	}

	private function build_sql_cursor( &$sql, $cursor_field, $element, $direction, $position, $sort ) {
		if ( ! isset( $position ) ) {
			return;
		}

		global $wpdb;

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
		$sql .= $wpdb->prepare( $tmp_sql, $position );
	}

	private function build_sql_query_has_match( &$sql, $value ) {
		switch ( $value ) {
			case true:
				$sql .= ' AND e.about_jsonld IS NOT NULL ';
				break;
			case false:
				$sql .= ' AND e.about_jsonld IS NULL ';
				break;
			default:
		}

	}

	private function build_sql_limit( &$sql, $limit ) {
		global $wpdb;
		$sql .= $wpdb->prepare( ' LIMIT %d', $limit );
	}

	private function build_sql_query_taxonomy( &$sql, $taxonomy ) {
		if ( ! isset( $taxonomy ) ) {
			return;
		}
		global $wpdb;
		$sql .= $wpdb->prepare( ' AND tt.taxonomy = %s', $taxonomy );
	}

	private function build_sql_order_by( &$sql, $direction, $sort_ascending, $sort_field_name ) {
		$sort_order = $this->get_sort_order( $direction, $sort_ascending );
		$sql       .= " ORDER BY $sort_field_name $sort_order";
	}

	private function get_sort_sql_field_name( $sort ) {
		$tmp_sort_field_name = substr( $sort, 1 );
		if ( $tmp_sort_field_name === 'id' ) {
			return 't.term_id';
		} else {
			return 't.name';
		}
	}

	private function get_sort_property_name( $sort ) {
		$tmp_sort_field_name = substr( $sort, 1 );
		if ( $tmp_sort_field_name === 'id' ) {
			return 'id';
		} else {
			return 'name';
		}
	}

	private function is_sort_ascending( $sort ) {
		return strpos( $sort, '-' ) !== 0;
	}

	private function get_sort_order( $direction, $sort_ascending ) {
		switch ( array( $sort_ascending, $direction ) ) {
			case array( true, 'ASCENDING' ):
			case array( false, 'DESCENDING' ):
				return 'ASC';
			case array( true, 'DESCENDING' ):
			case array( false, 'ASCENDING' ):
				return 'DESC';
		}

		return 'ASC';
	}

}
