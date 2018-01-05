<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05.01.18
 * Time: 12:43
 */

class Wordlift_Batch_Analysis_Sql_Helper {


	/**
	 * Get the base SQL statement to submit a post for Batch Analysis.
	 *
	 * Functions may use this base SQL and add their own filters.
	 *
	 * @since 3.14.2
	 *
	 * @param array $args An array of parameters, see {@link submit}.
	 *
	 * @return string The base SQL.
	 */
	public static function get_sql( $args ) {
		global $wpdb;

		// Get the link options.
		$link_options = serialize( array(
			'link'            => $args['link'],
			'min_occurrences' => $args['min_occurrences'],
		) );

		/*
		Prepare the statement:
			1. Insert into `postmeta` the meta keys and values:
				a) state meta, with value of SUBMIT (0),
				b) submit timestamp, with value of UTC timestamp,
				c) link meta, with the provided value.
			2. Join the current state value, can be used for filters by other functions.
			3. Filter by `post`/`page` types.
			4. Filter by `publish` status.
			5. Filter by `post_content` where autoselect includes/excludes posts with/without annotations.
			6. Filter by `post_date_gmt` where `post_date_gmt` is the date from where analysis will start.
			7. Filter by `post_date_gmt` where `post_date_gmt` is the date where analysis will end.
			8. Filter by `post_id` where `include` is the posts id to include.
			9. Filter by `post_id` where `exclude` is the posts id to exclude.
		*/

		// @codingStandardsIgnoreStart, Ignore phpcs sanitation errors.
		return $wpdb->prepare(
			"INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value )"
			// Populate 3 metas for the batch analysis request using a SQL
			// statement: each meta contains a state for the batch analysis
			// request.
			. ' SELECT p.ID, metas.* FROM ('
			. '  SELECT %s, 0 FROM dual'                // STATE_META_KEY.
			. '   UNION'
			. '	 SELECT %s, UTC_TIMESTAMP() FROM dual'  // SUBMIT_TIMESTAMP_META_KEY.
			. '	  UNION'
			. '	 SELECT %s, %s FROM dual'               // LINK_META_KEY.
			. '	) metas'
			. ", $wpdb->posts p"
			// This was for consumption by clients, removing to simplify.
			//			. " LEFT JOIN $wpdb->postmeta batch_analysis_state"
			//			. '	 ON batch_analysis_state.post_id = p.ID'
			//			. "   AND batch_analysis_state.meta_key = %s"
			. " WHERE p.post_type = IN ('" . join( "', '", array_map( 'esc_sql', $args['post_type'] ) ) . "')"
			. "  AND p.post_status = 'publish'"
			. Wordlift_Batch_Analysis_Sql_Helper::and_include_annotated( $args['include_annotated'] )
			. Wordlift_Batch_Analysis_Sql_Helper::and_post_date_from( $args['from'] )
			. Wordlift_Batch_Analysis_Sql_Helper::and_post_date_to( $args['to'] )
			. Wordlift_Batch_Analysis_Sql_Helper::and_include_posts( $args['include'] )
			. Wordlift_Batch_Analysis_Sql_Helper::and_exclude_posts( $args['exclude'] )
			,
			Wordlift_Batch_Analysis_Service::STATE_META_KEY,
			Wordlift_Batch_Analysis_Service::SUBMIT_TIMESTAMP_META_KEY,
			Wordlift_Batch_Analysis_Service::LINK_META_KEY,
			$link_options,
			Wordlift_Batch_Analysis_Service::STATE_META_KEY
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Add a clause to analyze all auto selected posts, i.e. non annotated posts.
	 *
	 * @param bool $include Whether to include annotated posts in selection.
	 *
	 * @since  3.17.0
	 *
	 * @return string The `post_content` clause.
	 */
	public static function and_include_annotated( $include ) {

		// Bail out with an empty string if we include all the posts.
		if ( $include ) {
			return '';
		}

		// Filter out already annotated posts.
		return " AND p.post_content NOT REGEXP '<[a-z]+ id=\"urn:[^\"]+\" class=\"[^\"]+\" itemid=\"[^\"]+\">';";
	}

	/**
	 * Add the start date clause.
	 *
	 * @param  DateTime|null $value The date where the analysis should start.
	 *
	 * @since  3.17.0
	 *
	 * @return string The start `post_date_gmt` clause
	 */
	public static function and_post_date_from( $value ) {

		// Bail out if the `from` isn't specified.
		if ( null === $value ) {
			return '';
		}

		// Try to convert the value to a date, GMT timezone.
		$date = self::get_mysql_date_string( $value );

		// Return the clause.
		return " AND p.post_date_gmt >= '$date'";
	}

	/**
	 * Add the `to` date clause.
	 *
	 * @param  DateTime|null $value The `to` date clause.
	 *
	 * @since  3.17.0
	 *
	 * @return string The end `post_date_gmt` clause
	 */
	public static function and_post_date_to( $value ) {

		// Bail out if the `from` isn't specified.
		if ( null === $value ) {
			return '';
		}

		// Try to convert the value to a date, GMT timezone.
		$date = self::get_mysql_date_string( $value );

		// Return the clause.
		return " AND p.post_date_gmt <= '$date'";
	}

	/**
	 * Include specific posts by their id in the analysis.
	 *
	 * @param array $include Array of post ids to include.
	 *
	 * @since  3.17.0
	 *
	 * @return string The posts IN clause.
	 */
	public static function and_include_posts( $include ) {

		// Bail if the param is not set.
		if ( null === $include ) {
			return '';
		}

		return ' AND p.ID IN ( ' . implode( ',', wp_parse_id_list( $include ) ) . ' )';
	}

	/**
	 * Exclude specific posts by ids.
	 *
	 * @param array $exclude Array of post ids to exclude.
	 *
	 * @since  3.17.0
	 *
	 * @return string The posts NOT IN clause.
	 */
	public static function and_exclude_posts( $exclude ) {

		// Bail if the param is not set.
		if ( null === $exclude ) {
			return '';
		}

		return ' AND p.ID NOT IN ( ' . implode( ',', wp_parse_id_list( $exclude ) ) . ' )';
	}


	/**
	 * Convert a `Y-m-d'T'H:i:sT` string representation of a date to a MySQL
	 * date string, taking into consideration the time zone.
	 *
	 * If the date string cannot be converted, the processing is stopped.
	 *
	 * @since 3.17.0
	 *
	 * @param string $value A date represented as `Y-m-d'T'H:i:sT`.
	 *
	 * @return string A MySQL string representation of the date.
	 */
	private static function get_mysql_date_string( $value ) {

		// Try to convert the value to a date, GMT timezone.
		$date = date_create_from_format( 'Y-m-d\TH:i:sT', $value, date_timezone_get( 'GMT' ) );

		// Stop if the conversion failed.
		if ( false === $date ) {
			wp_die( "Invalid date format, date must be in Y-m-d'T'H:i:sT format." );
		}

		// Return the clause.
		return $date->format( 'Y-m-d H:i:s' );
	}

}
