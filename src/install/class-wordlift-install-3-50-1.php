<?php

use Wordlift\Jsonld\Jsonld_Utils;

/**
 * @since 3.49.1
 */
class Wordlift_Install_3_50_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.50.1';

	/**
	 * Is column exists
	 *
	 * @param $column_name
	 *
	 * @return mixed
	 */
	public static function is_column_exists( $column_name ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='{$wpdb->prefix}wl_entities' AND column_name = %s",
				$column_name
			)
		);
	}

	/**
	 * Install
	 *
	 * @return void
	 */
	public function install() {
		global $wpdb;

		// Check if 'match_name' column exists
		if ( self::is_column_exists( 'match_name' ) ) {
			return;
		}

		// Add new 'match_name' column
		dbDelta(
			"ALTER TABLE {$wpdb->prefix}wl_entities
					ADD match_name VARCHAR(255) AFTER about_jsonld;"
		);

		// Get all rows with 'about_jsonld'
		$results = $wpdb->get_results(
			"SELECT id, about_jsonld FROM {$wpdb->prefix}wl_entities WHERE about_jsonld IS NOT NULL",
			ARRAY_A
		);

		// Update 'match_name' for each row
		foreach ( $results as $row ) {
			$match_name = Jsonld_Utils::get_about_match_name( $row['about_jsonld'] );

			if ( is_null( $match_name ) ) {
				continue;
			}

			$wpdb->update(
				"{$wpdb->prefix}wl_entities",
				array( 'match_name' => $match_name ),
				array( 'id' => $row['id'] )
			);
		}
	}
}
