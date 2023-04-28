<?php

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Wordlift_Install_3_32_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.32.0';

	public static function is_column_exists( $column_name ) {
		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='{$wpdb->prefix}wl_relation_instances' AND column_name = %s",
				$column_name
			)
		);

	}

	public function install() {
		global $wpdb;
		// Allocate only 8 bytes, represent 2 ^ 8 values in signed form ( -128 to 127 )
		// we default to 0 here, because we are going to represent Object_Type_Enum in
		// this field
		// const POST = 0;
		// const TERM = 1;
		// const HOMEPAGE = 2;
		// const USER = 3;
		// we add 0 as default since we want to add compat between old and new values.

		if ( ! self::is_column_exists( 'object_type' ) ) {
			// Add object_type column
			$wpdb->query(
				"ALTER TABLE {$wpdb->prefix}wl_relation_instances
					ADD object_type TINYINT DEFAULT 0;"
			);
		}

		if ( ! self::is_column_exists( 'subject_type' ) ) {
			// Add subject_type column.
			$wpdb->query(
				"ALTER TABLE {$wpdb->prefix}wl_relation_instances
					ADD subject_type TINYINT DEFAULT 0;"
			);
		}
	}

}
