<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Download_Ingredients_Data {

	public function register_hooks() {
		add_action( 'wp_ajax_wl_download_ingredients_data', array( $this, 'wl_download_ingredients_data' ) );
	}

	public function wl_download_ingredients_data() {
		global $wpdb;

		$items = $wpdb->get_results(
			"SELECT p.ID, p.post_title, pm.meta_value
			FROM $wpdb->posts p
			INNER JOIN $wpdb->postmeta pm
			    ON pm.post_ID = p.ID
					AND pm.meta_key = '_wl_main_ingredient_jsonld'"
		);

		if ( ! $items ) {
			wp_send_json_error( __( 'No main ingredients found.', 'wordlift' ) );
		}

		$tsv = array();

		foreach ( $items as $item ) {
			$tsv[] = array(
				$item->post_title,
				esc_url( get_the_permalink( $item->ID ) ),
			);
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
			)
		);

		// Insert Data.
		foreach ( $tsv as $row ) {
			fputcsv( $output, $row );
		}

		wp_die();
	}
}
