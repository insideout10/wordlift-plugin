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
				'limit'     => 10,
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

	/** Returns an array of rows where each row contains
	 * 'post_title' => The title of the post
	 * 'id'   => The id of the post
	 * 'post_link' => The edit post link
	 * 'parent_post_title' => The title of the post linked to this post via wprm_parent_post_id property
	 * ( this is only applicable when the post is wprm_recipe, returns null if not present )
	 * 'parent_post_id'  => The id of the linked parent post.
	 * 'parent_post_link' => The link to parent post.
	 * 'match_jsonld' => The matched `about_jsonld` column from wl_entities.
	 * 'match_id' => This id points to id column of wl_entities table.
	 */
	private function map( array $items ) {
		return array_map(
			function ( $item ) {
				$data             = json_decode( $item->match_jsonld, true );
				$item->match_name = $data && is_array( $data ) && array_key_exists( 'name', $data ) ? $data['name'] : null;

				if ( $item->id ) {
					$item->post_link = get_edit_post_link( $item->id, 'ui' );
				}

				if ( $item->parent_post_id ) {
					$item->parent_post_link = get_edit_post_link( $item->parent_post_id, 'ui' );
				}

				return $item;
			},
			$items
		);
	}

}
