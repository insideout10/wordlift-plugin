<?php
/**
 * Services: Google Analytics Export Service.
 *
 * This service exports a CSV that can be imported into Google Analytics.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Google_Analytics_Export_Service} class.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Google_Analytics_Export_Service {

	/**
	 * Export the site data that could be imported in Google Analytics.
	 * It will works only when permalink structure is set to "Postname".
	 *
	 * @since 3.16.0
	 *
	 * @return void
	 */
	public function export() {
		// Bail if the permalink structure is different from "Post name".
		if ( ! $this->is_postname_permalink_structure() ) {
			wp_die( 'The current permalink structure do not allow to export your data. Please change the permalink structure to "Post name".' );
		}

		// Output the file data. @codingStandardsIgnoreLine
		@ob_end_clean();

		// Generate unique filename using current timestamp.
		$filename = 'wl-ga-export-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';

		// Add proper file headers.
		header( "Content-Disposition: attachment; filename=$filename" );
		header( 'Content-Type: text/csv; charset=' . get_bloginfo( 'charset' ) );

		// Do not cache the file.
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Build the CSV file.
		$this->create_csv();

		wp_die();
	}

	/**
	 * Return site path. Some installations are in subdirectories
	 * and we need add them to expported permalinks.
	 *
	 * @since 3.16.0
	 *
	 * @return string The site path.
	 */
	public function get_site_path() {
		// Get home url from database.
		$home_url = home_url( '/' );

		// Parse the url.
		$parsed = wp_parse_url( $home_url );

		// Return the path.
		return $parsed['path'];
	}

	/**
	 * Check if the current permalink structure is set to "Post name".
	 *
	 * @since 3.16.0
	 *
	 * @return bool whether the structure is "Post name" or not.
	 */
	public static function is_postname_permalink_structure() {
		// Get current permalink structure.
		$structure = get_option( 'permalink_structure' );

		// The regular expression. It will check if the site structure contains postname.
		$regex = '~^/\%postname\%/$~';

		// Check if the site structure match the rquired one.
		preg_match( $regex, $structure, $matches );

		// Bail if the site have different structure.
		if ( empty( $matches ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Generate array data, that should be exported as csv.
	 * The data contains the post/page title, entity name and type.
	 *
	 * @since 3.16.0
	 *
	 * @return array $items Content data.
	 */
	public function get_content_data() {
		// Get the global $wpdb.
		global $wpdb;

		// Site path (optional).
		$path = $this->get_site_path();

		// Get the data.
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					CONCAT( %s, p.post_name, '/' ) AS 'post_name',
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

		return $items;
	}

	/**
	 * Create the CSV file that will be downloaded.
	 *
	 * @since 3.16.0
	 *
	 * @return void
	 */
	public function create_csv() {
		// Create a file pointer connected to the output stream.
		// Ignoring linter notices below that complain about file output which do not actually happen.
		$file = fopen( 'php://output', 'w' );

		// Add the column headers. @codingStandardsIgnoreLine
		fputcsv(
			$file,
			array(
				'ga:pagePath',
				'ga:dimension1',
				'ga:dimension2',
			)
		);

		// Cycle through items and add each item data to the file.
		foreach ( $this->get_content_data() as $row ) {
			// Add new line in the file. @codingStandardsIgnoreLine
			fputcsv(
				$file,
				(array) $row // convert the object to array.
			);
		}
	}

}
