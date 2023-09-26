<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Object_Type_Enum;
use WP_REST_Request;

class Recipe_Query {
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

		$this->sql = "
			SELECT p.ID as id,
				p.post_title,
				p.post_status,
				p.post_modified_gmt as date_modified_gmt,
				e.about_jsonld as match_jsonld,
				e.id AS match_id,
				IFNULL( parent.post_title, '(no post)' ) as parent_post_title,
				IFNULL( parent.ID, p.ID ) as parent_post_id
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->prefix}wl_entities e 
			    ON p.ID = e.content_id AND e.content_type = 0
			INNER JOIN {$wpdb->prefix}postmeta pm
			    ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
			INNER JOIN {$wpdb->prefix}posts parent
			    ON pm.meta_value = parent.ID 
			WHERE 1=1 
		";

		$this->cursor();
		$this->has_match();
		$this->post_types();
		$this->post_status();
		$this->sort();
		$this->limit();

		// p.post_modified_gmt $condition %s
		// AND p.post_types IN ( '" . implode( "','", $post_types ) . "' )
		// AND p.post_status = %s
		// ORDER BY p.post_modified_gmt $sort
		// LIMIT %d
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
		if ( $item->id ) {
			$item->post_link    = get_edit_post_link( $item->id, 'ui' );
			$item->view_link    = get_permalink( $item->id );
			$item->preview_link = get_preview_post_link( $item->id );
		}

		if ( $item->parent_post_id ) {
			$item->parent_post_link = get_edit_post_link( $item->parent_post_id, 'ui' );
		}

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

		$this->sql .= ' ORDER BY p.' . $this->sortby . ' ' . $sort;
	}

	private function post_status() {
		if ( ! $this->request->has_param( 'post_status' ) ) {
			$this->sql .= " AND p.post_status IN ( 'draft', 'publish' ) ";

			return;
		}

		global $wpdb;
		$value      = $this->request->get_param( 'post_status' );
		$this->sql .= $wpdb->prepare( ' AND p.post_status = %s', $value );
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
		$this->sql .= $wpdb->prepare( ' AND p.' . esc_sql( $this->sortby ) . ' ' . $condition . ' %s', $this->position );
	}

	private function set_sort() {
		$sortby_to_col = array(
			'date_modified_gmt' => 'post_modified_gmt',
		);

		$value = $this->request->has_param( 'sort' )
			? $this->request->get_param( 'sort' )
			: '-date_modified_gmt';

		$sortby       = substr( $value, 1 );
		$this->sortby = isset( $sortby_to_col[ $sortby ] ) ? $sortby_to_col[ $sortby ] : $sortby;
		$this->sort   = substr( $value, 0, 1 ) === '+' ? 'ASC' : 'DESC';
	}

	public static function get_data() {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT COUNT(1) AS total, COUNT(e.about_jsonld) as lifted
				FROM {$wpdb->prefix}posts p
				LEFT JOIN {$wpdb->prefix}wl_entities e
					ON e.content_id = p.ID
						AND e.content_type = %d 
				INNER JOIN {$wpdb->prefix}postmeta pm
					ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
				INNER JOIN {$wpdb->prefix}posts parent
					ON pm.meta_value = parent.ID 
				WHERE p.post_type = %s
				",
				Object_Type_Enum::POST,
				'wprm_recipe'
			)
		);
	}

	/**
	 * SELECT p.ID as id,
	p.post_title,
	p.post_status,
	p.post_modified_gmt as date_modified_gmt,
	e.about_jsonld as match_jsonld,
	e.id AS match_id,
	IFNULL( parent.post_title, '(no post)' ) as parent_post_title,
	IFNULL( parent.ID, p.ID ) as parent_post_id
	FROM {$wpdb->posts} p
	LEFT JOIN {$wpdb->prefix}wl_entities e
	ON p.ID = e.content_id AND e.content_type = 0
	INNER JOIN {$wpdb->prefix}postmeta pm
	ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id'
	INNER JOIN {$wpdb->prefix}posts parent
	ON pm.meta_value = parent.ID
	WHERE 1=1
	 */
}
