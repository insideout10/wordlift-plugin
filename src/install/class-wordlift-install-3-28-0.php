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
			// Don't run the query, running it would delete all the values.
			return;
		}

		$http        = $wpdb->esc_like( 'http://' ) . '%';
		$https       = $wpdb->esc_like( 'https://' ) . '%';
		$dataset_uri = $wpdb->esc_like( $dataset_uri ) . '%';

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key=%s 
AND ( ( meta_value NOT LIKE %s AND meta_value NOT LIKE %s )
OR meta_value LIKE %s )",
				Wordlift_Schema_Service::FIELD_SAME_AS,
				$http,
				$https,
				$dataset_uri
			)
		);

	}

}
