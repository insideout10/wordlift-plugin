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
		$query_template = $this->get_query_template();

		// Add object_type column
		$object_type_column_query = sprintf( $query_template, $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME, 'object_type' );
		$wpdb->query( $object_type_column_query );

		// Add subject_type column.
		$subject_type_column_query = sprintf( $query_template, $wpdb->prefix . WL_DB_RELATION_INSTANCES_TABLE_NAME, 'object_type' );
		$wpdb->query( $subject_type_column_query );

	}

	/**
	 * @return string
	 */
	protected function get_query_template() {
		return <<<EOF
ALTER TABLE %s
ADD %s TINYINT DEFAULT 0; 
EOF;
	}
}