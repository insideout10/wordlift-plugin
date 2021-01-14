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

		$dataset_uri = Wordlift_Configuration_Service::get_instance()->get_dataset_uri();

		if ( ! $dataset_uri ) {
			// Dont run the query, running it would delete all the values.
			return;
		}
		$post_meta_table_name = $wpdb->postmeta;
		$meta_key = Wordlift_Schema_Service::FIELD_SAME_AS;

		$http        = $wpdb->esc_like( 'http://' ) . '%';
		$https       = $wpdb->esc_like( 'https://' ) . '%';
		$dataset_uri = $wpdb->esc_like( $dataset_uri ) . '%';
		$sql         = $wpdb->prepare( "DELETE FROM $post_meta_table_name WHERE meta_key='$meta_key' 
AND ( ( meta_value NOT LIKE %s AND meta_value NOT LIKE %s )
OR meta_value LIKE %s )", $http, $https, $dataset_uri );
		$wpdb->query( $sql );

	}


}
