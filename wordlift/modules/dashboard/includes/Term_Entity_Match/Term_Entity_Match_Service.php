<?php

namespace Wordlift\Modules\Dashboard\Term_Entity_Match;

use Wordlift\Modules\Dashboard\Match\Match_Service;
use Wordlift\Object_Type_Enum;

/**
 * Class Term_Entity_Match_Service
 *
 * @package Wordlift\Modules\Dashboard\Term_Entity_Match
 */
class Term_Entity_Match_Service extends Match_Service {

	/**
	 * List items.
	 *
	 * @param $args
	 *
	 * @return array
	 *
	 * @throws \Exception If there was a problem generating the list items.
	 */
	public function list_items( $args ) {
		global $wpdb;

		$params = wp_parse_args(
			$args,
			array(
				'position'      => null,
				'element'       => 'INCLUDED',
				'direction'     => 'ASCENDING',
				'limit'         => 10,
				'sort'          => '+id',
				// Query.
				'taxonomy'      => null,
				'has_match'     => null,
				'term_contains' => null,
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
		$query         = $query_builder
			->get();

		$items = $wpdb->get_results(
		// Each function above is preparing `$sql` by using `$wpdb->prepare`.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->prepare( $query, Object_Type_Enum::TERM )
		);

		$sort->apply( $items );

		return $this->map( $items );
	}

	/**
	 * Map.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	private function map( array $items ) {
		return array_map(
			function ( $item ) {
				$data              = json_decode( $item->match_jsonld, true );
				$item->match_name  = $data && is_array( $data ) && array_key_exists( 'name', $data ) ? $data['name'] : null;
				$item->occurrences = $this->get_term_occurrences( $item->id );

				return $item;
			},
			$items
		);
	}

	/**
	 * Get term occurrences.
	 *
	 * @param $term_id
	 *
	 * @return int
	 */
	private function get_term_occurrences( $term_id ) {
		$term = get_term( $term_id );

		if ( ! is_wp_error( $term ) ) {
			return $term->count;
		}

		return 0;
	}
}
