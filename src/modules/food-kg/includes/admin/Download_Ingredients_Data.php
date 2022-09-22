<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Download_Ingredients_Data {

	public function register_hooks() {
		add_action( 'wp_ajax_wl_download_ingredients_data', array( $this, 'wl_download_ingredients_data' ) );
	}

	public function wl_download_ingredients_data() {
		global $wpdb;

		// @@todo add the admin capability check + nonce.

		$items = $wpdb->get_results(
			"SELECT p1.ID AS recipe_ID,
					    p1.post_title AS recipe_name,
					    p2.ID AS post_ID,
					    p2.post_title,
					    p2.post_status
					FROM {$wpdb->posts} p1
					    INNER JOIN wp_postmeta pm1 ON pm1.post_ID = p1.ID
					        AND pm1.meta_key = '_wl_main_ingredient_jsonld'
					    INNER JOIN {$wpdb->posts} p2
					        ON p2.post_content LIKE CONCAT( '%<!--WPRM Recipe ', p1.ID,'-->%' )
					            AND p2.post_status = 'publish'
					WHERE p1.post_type = 'wprm_recipe'"
		);

		if ( ! $items ) {
			wp_send_json_error( __( 'No main ingredients found.', 'wordlift' ) );
		}

		// Generate unique filename using current timestamp.
		$filename = 'wl-main-ingredients-data-' . gmdate( 'Y-m-d-H-i-s' ) . '.tsv';

		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/tsv; charset=' . get_bloginfo( 'charset' ) );

		// Do not cache the file.
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$output = fopen( 'php://output', 'w' );

		// Insert Header.
		fputcsv(
			$output,
			array(
				__( 'Name', 'wordlift' ),
				__( 'URL', 'wordlift' ),
				__( 'Ingredient Name', 'wordlift' ),
				__( 'Post ID', 'wordlift' ),
				__( 'Recipe ID', 'wordlift' ),
			),
			"\t"
		);

		// Insert Data.
		foreach ( $items as $item ) {
			$row = array(
				$item->post_title,
				esc_url( get_the_permalink( $item->post_ID ) ),
				'',
				$item->post_ID,
				$item->recipe_ID,
			);
			fputcsv( $output, $row );
		}

		wp_die();
	}
}
