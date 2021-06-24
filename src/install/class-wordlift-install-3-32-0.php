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

	public function must_install() {
		// This db column alter should happen.
		return true;
	}

	public function install() {
		global $wpdb;
		// Allocate only 8 bytes, represent 2 ^ 8 values in signed form ( -128 to 127 )
		// we default to 0 here, because we are going to represent Object_Type_Enum in
		// this field
		//		const POST = 0;
		//		const TERM = 1;
		//		const HOMEPAGE = 2;
		//		const USER = 3;
		// we add 0 as default since we want to add compat between old and new values.
		$query_template = <<<EOF
ALTER TABLE %s
ADD %s TINYINT DEFAULT 0; 
EOF;
		$query = sprintf( $query_template, $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME, 'object_type' );

		$wpdb->query( $query );

	}
}