<?php

use Wordlift\Dataset\Sync_Service;
use Wordlift\Jsonld\Jsonld_Service;

/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Dataset_Sync_Service_Test extends Wordlift_Unit_Test_Case {

	public function test_on_save_post_sync_should_be_done() {
		global $wp_filter;
		$wp_filter = array();
		// create a instance of sync service, now we should have save_post registered.
		new Sync_Service( null, Jsonld_Service::get_instance() );
		$this->assertArrayHasKey( 'save_post', $wp_filter );
		$this->assertTrue( (bool) has_action( 'save_post', array( Sync_Service::get_instance(), 'sync_item' ) ) );
	}


	public function test_when_post_meta_updated_should_sync_item() {
		global $wp_filter;
		$wp_filter = array();
		// create a instance of sync service, now we should have updated_post_meta registered.
		new Sync_Service( null, Jsonld_Service::get_instance() );
		$this->assertArrayHasKey( 'updated_post_meta', $wp_filter );
		$this->assertTrue( (bool) has_action( 'updated_post_meta', array( Sync_Service::get_instance(), 'sync_item_on_meta_change' ) ) );
	}


}