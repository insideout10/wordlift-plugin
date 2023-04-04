<?php

namespace Wordlift\Modules\Food_Kg\Api;

use Wordlift\Object_Type_Enum;

// phpcs:disable
class Match_Service {

	/**
	 * @param $taxonomy string The taxonomy name
	 * @param $position int The position of the item, usually id used for pagination.
	 * @param $limit int The number of results to return
	 * @param $direction string @enum { 'FORWARD', 'BACKWARD'}
	 * @param $sort string @enum { 'ASC', 'DESC'}
	 * @param $filter string @enum { 'MATCHED', 'UNMATCHED', 'ALL'}
	 *
	 * @return array|array[]
	 * @throws \Exception
	 */

	public function get_term_matches( $taxonomy, $position, $limit, $direction, $sort, $filter ) {

		$operator = Page::FORWARD === $direction ? '>=' : '<=';

		$this->validate_args( $sort );

		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT t.term_id as id, e.about_jsonld as match_jsonld,  t.name,  e.id AS match_id FROM {$wpdb->prefix}terms t
     INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
     LEFT JOIN {$wpdb->prefix}wl_entities e ON t.term_id = e.content_id
     WHERE e.content_type = %d AND tt.taxonomy = %s AND t.term_id {$operator} %d {{filter}} ORDER BY t.term_id {$sort} LIMIT %d",
			Object_Type_Enum::TERM,
			$taxonomy,
			$position,
			$limit
		);

		$query = $this->filters( $filter, $query );

		return array_map(
			function ( $e ) {
				return Match_Entry::from( $e )->serialize();
			},
			$wpdb->get_results(
				$query,
				ARRAY_A
			)
		);
	}

	/**
	 * @param $post_type string The post type
	 * @param $position int The position of the item, usually id used for pagination.
	 * @param $limit int The number of results to return
	 * @param $direction string @enum { 'FORWARD', 'BACKWARD'}
	 * @param $sort string @enum { 'ASC', 'DESC'}
	 * @param $filter string @enum { 'MATCHED', 'UNMATCHED', 'ALL'}
	 *
	 * @return array|array[]
	 * @throws \Exception
	 */
	public function get_post_matches( $post_type, $position, $limit, $direction, $sort, $filter ) {
		global $wpdb;

		$operator = Page::FORWARD === $direction ? '>=' : '<=';

		$this->validate_args( $sort );

		$query = $wpdb->prepare(
			"SELECT e.content_id as id, e.about_jsonld as match_jsonld, parent.post_title as name, p.post_title as recipe_name, e.id AS match_id 
FROM {$wpdb->prefix}posts p 
INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'wprm_parent_post_id' 
INNER JOIN {$wpdb->prefix}posts parent ON pm.meta_value = parent.ID 
LEFT JOIN {$wpdb->prefix}wl_entities e ON p.ID = e.content_id 
WHERE e.content_type = %d AND p.post_type = %s AND p.ID {$operator} %d AND pm.meta_value IS NOT NULL {{filter}} 
ORDER BY p.ID {$sort} LIMIT %d;",
			Object_Type_Enum::POST,
			$post_type,
			$position,
			$limit
		);

		$query = $this->filters( $filter, $query );

		return array_map(
			function ( $e ) {
				return array_merge(
					Match_Entry::from( $e )->serialize(),
					array(
						'recipe_name'    => $e['recipe_name'],
						'post_permalink' => get_permalink( $e['id'] ),
					)
				);
			},
			$wpdb->get_results(
				$query,
				ARRAY_A
			)
		);
	}



	/**
	 * @param $sort
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function validate_args( $sort ) {
		if ( ! in_array( $sort, array( Page::SORT_ASC, Page::SORT_DESC ) ) ) {
			throw new \Exception( 'Invalid sort order specified' );
		}
	}

	/**
	 * @param $filter
	 * @param $query
	 *
	 * @return array|string|string[]
	 */
	public function filters( $filter, $query ) {
		switch ( $filter ) {
			case 'MATCHED':
				$query = str_replace( '{{filter}}', ' AND ( e.about_jsonld IS NOT NULL AND  e.about_jsonld != "{}" ) ', $query );
				break;
			case 'UNMATCHED':
				$query = str_replace( '{{filter}}', ' AND ( e.about_jsonld IS NULL OR e.about_jsonld="{}" ) ', $query );
				break;
			default:
				$query = str_replace( '{{filter}}', '', $query );
		}

		return $query;
	}

}
