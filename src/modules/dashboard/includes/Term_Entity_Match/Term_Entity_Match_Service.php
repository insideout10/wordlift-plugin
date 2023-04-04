<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Service;
use Wordlift\Object_Type_Enum;

class Term_Entity_Match_Service extends Match_Service {

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

		/**
		 * @var $sort Sort
		 */
		$sort = new Sort( $params['sort'] );

		$query_builder = new Query_Builder(
			$params['element'],
			$params['direction'],
			$params['position'],
			$sort
		);
		$query         = $query_builder
			->taxonomy( $params['taxonomy'] )
			->has_match( $params['has_match'] )
			->order_by( $params['direction'] )
			->limit( $params['limit'] )
			->build();

		$items = $wpdb->get_results(
		// Each function above is preparing `$sql` by using `$wpdb->prepare`.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->prepare( $query, Object_Type_Enum::TERM )
		);

		$sort_ascending     = $sort->is_ascending();
		$sort_property_name = $sort->property_name();
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

		return $this->map( $items );
	}

	private function map( array $items ) {
		return array_map(
			function ( $item ) {
				$data             = json_decode( $item->match_jsonld, true );
				$item->match_name = $data && is_array( $data ) && array_key_exists( 'name', $data ) ? $data['name'] : null;
				return $item;
			},
			$items
		);
	}

}
