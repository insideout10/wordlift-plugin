<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Download_Ingredients_Data {

	public function register_hooks() {
		add_action( 'wp_ajax_wl_download_ingredients_data', array( $this, 'wl_download_ingredients_data' ) );
	}

	public function wl_download_ingredients_data() {

		check_ajax_referer( 'wl-dl-ingredients-data-nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wordlift' ) );
		}

		global $wpdb;

		$items = $wpdb->get_results(
			"SELECT p1.ID AS recipe_ID,
					    p1.post_title AS recipe_name,
					    p2.ID AS post_ID,
					    p2.post_title
						FROM $wpdb->postmeta pm1
						    INNER JOIN $wpdb->posts p1
						        ON p1.ID = pm1.post_ID AND p1.post_type = 'wprm_recipe'
							INNER JOIN $wpdb->postmeta pm2
								ON pm2.post_ID = pm1.post_ID AND pm2.meta_key = 'wprm_parent_post_id'
						    INNER JOIN $wpdb->posts p2"
			// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
			. " ON p2.post_status = 'publish' AND p2.ID = pm2.meta_value
							WHERE pm1.meta_key = '_wl_main_ingredient_jsonld'"
		);

		if ( ! $items ) {
			wp_send_json_error( __( 'No main ingredients found.', 'wordlift' ) );
		}

		// Generate unique filename using current timestamp.
		$filename = 'wl-main-ingredients-data-' . gmdate( 'Y-m-d-H-i-s' ) . '.tsv';

		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/text/tab-separated-values; charset=' . get_bloginfo( 'charset' ) );

		// Do not cache the file.
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$output = fopen( 'php://output', 'w' );

		// Insert Header.
		fputcsv(
			$output,
			array(
				__( 'Ingredient Name', 'wordlift' ),
				__( 'Recipe Name', 'wordlift' ),
				__( 'Recipe ID', 'wordlift' ),
				__( 'Post Name', 'wordlift' ),
				__( 'Post ID', 'wordlift' ),
				__( 'Post URL', 'wordlift' ),
			),
			"\t"
		);

		// Insert Data.
		foreach ( $items as $item ) {
			$recipe_json_ld = get_post_meta( $item->recipe_ID, '_wl_main_ingredient_jsonld', true ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$recipe         = json_decode( $recipe_json_ld, true );
			fputcsv(
				$output,
				array(
					$recipe ? $recipe['name'] : 'null',
					$item->recipe_name,
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$item->recipe_ID,
					$item->post_title,
					$item->post_ID,
					esc_url( get_the_permalink( $item->post_ID ) ),
				),
				"\t"
			);
			ob_flush();
		}

		wp_die();
	}
}
