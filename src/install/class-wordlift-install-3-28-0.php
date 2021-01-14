<?php

/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Wordlift_Install_3_28_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.28.0';

	public function install() {

		global $wpdb;

		$dataset_uri          = Wordlift_Configuration_Service::get_instance()->get_dataset_uri();

		if ( ! $dataset_uri ) {
			// Dont run the query, running it would delete all the values.
			return;
		}

		$post_meta_table_name = $wpdb->postmeta;
		$dataset_uri          = $dataset_uri . '%';
		$meta_key             = Wordlift_Schema_Service::FIELD_SAME_AS;
		$sql                  = "DELETE FROM $post_meta_table_name WHERE meta_key='$meta_key' 
AND ( ( meta_value NOT LIKE 'https://%' AND meta_value NOT LIKE 'http://%' )
OR meta_value LIKE '$dataset_uri' )";
		$wpdb->query( $sql );

	}


}
