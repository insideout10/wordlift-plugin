<?php
/**
 * Service: Ingredients Service.
 *
 * @package WordLift
 */

namespace Wordlift\Modules\Food_Kg\Services;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

class Ingredients {

	/**
	 * Get Ingredients Data.
	 *
	 * @param int $limit The number of items to return.
	 * @param int $offset The offset.
	 */
	public function get_data( $limit = 20, $offset = 0 ) {
		global $wpdb;

		$ingredients = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p1.ID AS recipe_ID,
						p1.post_title AS recipe_name,
						p2.ID AS post_ID,
						p2.post_title
						FROM {$wpdb->prefix}wl_entities pm1
							INNER JOIN $wpdb->posts p1
								ON p1.ID = pm1.content_id AND p1.post_type = 'wprm_recipe'
							INNER JOIN $wpdb->postmeta pm2
								ON pm2.post_ID = pm1.content_id AND pm2.meta_key = 'wprm_parent_post_id'
							INNER JOIN $wpdb->posts p2"
				// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
				. " ON p2.post_status = 'publish' AND p2.ID = pm2.meta_value
							WHERE pm1.content_type = 0 AND pm1.about_jsonld IS NOT NULL
					LIMIT %d
					OFFSET %d",
				$limit,
				$offset
			)
		);

		if ( empty( $ingredients ) ) {
			return new \WP_Error( 'no_ingredients', __( 'No ingredients found.', 'wordlift' ), array( 'status' => 404 ) );
		}

		$data            = array();
		$content_service = Wordpress_Content_Service::get_instance();
		foreach ( $ingredients as $ingredient ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$recipe_id      = $ingredient->recipe_ID;
			$content_id     = Wordpress_Content_Id::create_post( $recipe_id );
			$recipe_json_ld = $content_service->get_about_jsonld( $content_id );
			$recipe         = json_decode( $recipe_json_ld, true );

			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$data[] = array(
				'main_ingredient_item_id' => $recipe ? $recipe['@id'] : null,
				'main_ingredient_name'    => $recipe ? $recipe['name'] : null,
				'recipe_id'               => (int) $recipe_id,
				'recipe_name'             => htmlspecialchars_decode( $ingredient->recipe_name ),
				'post_id'                 => (int) $ingredient->post_ID,
				'post_name'               => $ingredient->post_title,
				'post_url'                => get_the_permalink( $ingredient->post_ID ),
			);
			// phpcs:enable
		}

		return $data;
	}
}
