<?php
/**
 * Services: Google Analytics Export Service.
 *
 * This service exports a CSV that can be imported into Google Analytics.
 *
 * @since      3.13.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Google_Analytics_Export_Service} class.
 *
 * @since      3.13.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Google_Analytics_Export_Service {

	/**
	 * Export the CSV.
	 *
	 * @since 3.13.0
	 */
	public function export() {

		global $wpdb;

		$items = $wpdb->get_results(
			"SELECT p.post_name AS post_name, p1.post_name AS entity_name, t.slug AS entity_type" .
			" FROM $wpdb->posts p" .
			" INNER JOIN {$wpdb->prefix}wl_relation_instances ri" .
			"   ON ri.subject_id = p.id" .
			" INNER JOIN $wpdb->posts p1" .
			"   ON p1.id = ri.object_id AND p1.post_type = 'entity'" .
			" INNER JOIN  $wpdb->term_relationships tr" .
			"   ON tr.object_id = p1.id" .
			" INNER JOIN $wpdb->term_taxonomy tt" .
			"   ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'wl_entity_type'" .
			" INNER JOIN $wpdb->terms t" .
			"   ON t.term_id = tt.term_id" .
			" WHERE p.post_type IN ('page', 'post')"
		);

		ob_end_clean();

		header( 'Content-Disposition: attachment; filename=wl-ga-export.csv' );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );

		// Echo the CSV header.
		echo( "ga:pagePath,ga:dimension1,ga:dimension2\n" );

		foreach ( $items as $item ) {
			echo( "$item->post_name,$item->entity_name,$item->entity_type\n" );
		}
		exit;

	}

}
