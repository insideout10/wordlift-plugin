<?php

use Wordlift\Dataset\Sync_Service;

/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Dataset_Sync_Service_Test extends Wordlift_Unit_Test_Case {

	public function test_on_save_post_sync_should_be_done() {
		global $wp_filter;
		$this->assertArrayHasKey( 'save_post', $wp_filter );
		$this->assertTrue( has_action( 'save_post', array( Sync_Service::get_instance(), 'sync_item' ) ) );
	}


}