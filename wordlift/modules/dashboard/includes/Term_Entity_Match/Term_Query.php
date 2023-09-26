<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Common\Cursor;
use Wordlift\Modules\Dashboard\Common\Cursor_Sort;
use WP_REST_Request;

class Term_Query {
	/**
	 * @var WP_REST_Request
	 */
	private $request;
	/**
	 * @var mixed
	 */
	private $position;
	/**
	 * @var mixed
	 */
	private $element;
	/**
	 * @var mixed
	 */
	private $direction;

	private $sort;

	private $sortby;
	/**
	 * @var mixed
	 */
	private $limit;

	/** @var Cursor_Sort $cursor_sort */
	private $cursor_sort;

	/**
	 * @param WP_REST_Request $request
	 * @param Cursor          $cursor
	 */
	public function __construct( $request, $cursor, $cursor_sort, $limit ) {
		global $wpdb;

		$this->request     = $request;
		$this->position    = $cursor->get_position();
		$this->element     = $cursor->get_element();
		$this->direction   = $cursor->get_direction();
		$this->limit       = $limit;
		$this->cursor_sort = $cursor_sort;

		$this->set_sort();

		// the `term_name` is required for sort.
		$this->sql = "
			SELECT t.term_id as id,
				e.about_jsonld as match_jsonld,
				t.name,
				t.name as term_name,
				e.id AS match_id
			FROM {$wpdb->prefix}terms t
			INNER JOIN {$wpdb->prefix}term_taxonomy tt
			    ON t.term_id = tt.term_id
			LEFT JOIN {$wpdb->prefix}wl_entities e
			    ON t.term_id = e.content_id AND e.content_type = 1
			WHERE 1=1
		";

		$this->cursor();
		$this->has_match();
		$this->term_contains();
		$this->taxonomies();
		$this->sort();
		$this->limit();

	}

	public function get_results() {
		global $wpdb;

		// The `sql` is prepared in each delegated function in this class.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$items = $wpdb->get_results( $this->sql );

		$sort = ( $this->sort === 'ASC' ? SORT_ASC : SORT_DESC );
		array_multisort( array_column( $items, $this->cursor_sort->get_sort_property() ), $sort, $items );
		$items = array_map( array( $this, 'map_item' ), $items );

		return $items;
	}

	public function map_item( $item ) {
		$item->match_name = $this->get_match_name( $item->match_jsonld );

		return $item;
	}

	private function get_match_name( $jsonld ) {
		$data = json_decode( $jsonld, true );
		if ( ! $data || ! array_key_exists( 'name', $data ) ) {
			return null;
		}

		return $data['name'];
	}

	private function post_types() {
		$post_types = $this->request->has_param( 'post_types' )
			? (array) $this->request->get_param( 'post_types' )
			: array( 'post', 'page' );
		$value      = array_map( 'esc_sql', $post_types );
		$this->sql .= " AND p.post_type IN ( '" . implode( "', '", $value ) . "' )";
	}

	private function limit() {
		$value      = is_numeric( $this->limit ) ? $this->limit : 10;
		$this->sql .= ' LIMIT ' . esc_sql( $value );
	}

	private function has_match() {
		if ( ! $this->request->has_param( 'has_match' ) ) {
			return;
		}

		$value = (bool) $this->request->get_param( 'has_match' );

		if ( $value ) {
			$this->sql .= ' AND e.about_jsonld IS NOT NULL';
		} else {
			$this->sql .= ' AND e.about_jsonld IS NULL';
		}
	}

	private function sort() {
		switch ( $this->direction . '$' . $this->sort ) {
			case 'ASCENDING$ASC':
			case 'DESCENDING$DESC':
				$sort = 'ASC';
				break;
			case 'ASCENDING$DESC':
			case 'DESCENDING$ASC':
				$sort = 'DESC';
				break;
		}

		$this->sql .= ' ORDER BY t.' . $this->sortby . ' ' . $sort;
	}

	private function cursor() {
		if ( ! isset( $this->position ) ) {
			return;
		}

		switch ( $this->direction . '$' . $this->sort ) {
			case 'ASCENDING$ASC':
			case 'DESCENDING$DESC':
				$condition = '>';
				break;
			case 'ASCENDING$DESC':
			case 'DESCENDING$ASC':
				$condition = '<';
				break;
		}

		$condition .= ( $this->element === 'INCLUDED' ? '=' : '' );
		global $wpdb;
		// We control the vars in this method.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$this->sql .= $wpdb->prepare( ' AND t.' . esc_sql( $this->sortby ) . ' ' . $condition . ' %s', $this->position );
	}

	private function set_sort() {
		$sortby_to_col = array(
			// sort param  col
			'term_name' => 'name',
		);

		$value = $this->request->has_param( 'sort' )
			? $this->request->get_param( 'sort' )
			: '+term_name';

		$sortby       = substr( $value, 1 );
		$this->sortby = isset( $sortby_to_col[ $sortby ] ) ? $sortby_to_col[ $sortby ] : $sortby;
		$this->sort   = substr( $value, 0, 1 ) === '+' ? 'ASC' : 'DESC';
	}

	private function term_contains() {
		if ( ! $this->request->has_param( 'term_contains' ) ) {
			return;
		}

		global $wpdb;
		$value      = $this->request->get_param( 'term_contains' );
		$this->sql .= $wpdb->prepare( ' and t.name LIKE %s', '%' . esc_sql( $value ) . '%' );
	}

	private function taxonomies() {
		$taxonomies = $this->request->has_param( 'taxonomies' )
			? (array) $this->request->get_param( 'taxonomies' )
			: array( 'post_tag', 'category' );
		$value      = array_map( 'esc_sql', $taxonomies );
		$this->sql .= " AND tt.taxonomy IN ( '" . implode( "', '", $value ) . "' )";
	}

}
