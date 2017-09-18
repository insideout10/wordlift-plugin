<?php
/**
 * Services: Google Analytics Export Service.
 *
 * This service exports a CSV that can be imported into Google Analytics.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Google_Analytics_Export_Service} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Google_Analytics_Export_Service {

	/**
	 * Export the CSV.
	 *
	 * @since 3.15.0
	 */
	public function export() {

		if ( ! wl_check_permalink_structure() ) {
			return;
		}

		// Get the global $wpdb.
		global $wpdb;

		// Site path (optional).
		$path = $this->get_site_prefix();

		// First, let's see if we have the data in the cache already.
		$items = get_transient( 'google_content_data' );

		if ( false === $items ) {
			// Build sql query.
			$items = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
						CONCAT( '%s', p.post_name, '/' ) AS 'post_name',
						p1.post_name AS 'entity_name',
						t.slug AS 'entity_type'
					FROM {$wpdb->prefix}posts p
						INNER JOIN {$wpdb->prefix}wl_relation_instances ri
							ON ri.subject_id = p.id
						INNER JOIN {$wpdb->prefix}posts p1
							ON p1.id = ri.object_id AND p1.post_type = 'entity'
						INNER JOIN {$wpdb->prefix}term_relationships tr
							ON tr.object_id = p1.id
						INNER JOIN {$wpdb->prefix}term_taxonomy tt
							ON tt.term_taxonomy_id = tr.term_taxonomy_id
							AND tt.taxonomy = 'wl_entity_type'
						INNER JOIN {$wpdb->prefix}terms t
							ON t.term_id = tt.term_id
						WHERE p.post_type IN ( 'page', 'post' );",
					$path
				)
			); // db call ok; no-cache ok.

			// Set the transient, so nex time we will use it, instead creating new db request.
			set_transient( 'google_content_data', $items, 300 );
		}

		// Output the file data.
		ob_end_clean();

		// Add proper file headers.
		header( 'Content-Disposition: attachment; filename=wl-ga-export.csv' );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );

		// Echo the CSV header.
		echo( "ga:pagePath,ga:dimension1,ga:dimension2\n" );

		// Cycle through items and add each item data to the file.
		foreach ( $items as $item ) {
			// Add new line in the file.
			echo esc_html( "$item->post_name,$item->entity_name,$item->entity_type\n" );
		}

		// Finally exit.
		exit;
	}

	/**
	 * Check and return site prefix if there is such.
	 *
	 * @since 3.15.0
	 *
	 * @return string The site prefix or empty string.
	 */
	public function get_site_prefix() {
		// Get current permalink structure.
		$structure = get_option( 'permalink_structure' );

		// Regular expression that will check for both prefix and %postname%.
		// The first group in the expression will return the prefix.
		$regex = '~(\/?.*)\%postname\%\/~';

		// Do the magic and collect the matches if any.
		preg_match( $regex , $structure, $matches );

		// Return the prefix if there is such.
		if ( ! empty( $matches[1] ) ) {
			return $matches[1];
		}

		// There is no prefix.
		return '/';
	}
}
