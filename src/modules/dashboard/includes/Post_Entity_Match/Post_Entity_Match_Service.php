<?php

namespace Wordlift\Modules\Dashboard\Post_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Service;

use Wordlift\Object_Type_Enum;

class Post_Entity_Match_Service extends Match_Service {

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
				'post_type' => null,
				'has_match' => null,
			)
		);

		/**
		 * @var $sort Sort
		 */
		$sort = new Sort( $params['sort'] );

		$query_builder = new Query_Builder(
			$params,
			$sort
		);

		$items = $wpdb->get_results(
			// Each function above is preparing `$sql` by using `$wpdb->prepare`.
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->prepare( $query_builder->get(), Object_Type_Enum::POST )
		);

		$sort->apply( $items );

		return $this->map( $items );
	}

	private function map( array $items ) {
		return array_map(
			function ( $item ) {
				$data             = json_decode( $item->match_jsonld, true );
				$item->match_name = $data && is_array( $data ) && array_key_exists( 'name', $data ) ? $data['name'] : null;
				// @TODO: review which link needs to be shown.
				$item->post_permalink = get_edit_post_link( $item->parent_post_id, 'ui' );
				return $item;
			},
			$items
		);
	}

}
