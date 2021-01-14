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

		$post_meta_table_name = $wpdb->postmeta;
		$meta_key = Wordlift_Schema_Service::FIELD_SAME_AS;
		$wpdb->query("DELETE  FROM $post_meta_table_name WHERE meta_key='$meta_key'");

	}


}
