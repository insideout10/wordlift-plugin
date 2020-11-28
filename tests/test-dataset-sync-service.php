<?php

use Wordlift\Dataset\Sync_Service;
use Wordlift\Jsonld\Jsonld_Service;

/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group dataset
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
		$this->assertTrue( (bool) has_action( 'updated_post_meta', array(
			Sync_Service::get_instance(),
			'sync_item_on_meta_change'
		) ) );
		$this->assertArrayHasKey( 'deleted_post_meta', $wp_filter );
		$this->assertTrue( (bool) has_action( 'deleted_post_meta', array(
			Sync_Service::get_instance(),
			'sync_item_on_meta_change'
		) ) );
		$this->assertArrayHasKey( 'added_post_meta', $wp_filter );
		$this->assertTrue( (bool) has_action( 'added_post_meta', array(
			Sync_Service::get_instance(),
			'sync_item_on_meta_change'
		) ) );
	}


	public function test_when_post_deleted_should_delete_item() {
		global $wp_filter;
		$wp_filter = array();
		// create a instance of sync service, now we should have updated_post_meta registered.
		new Sync_Service( null, Jsonld_Service::get_instance() );
		$this->assertArrayHasKey( 'delete_post', $wp_filter );
		$this->assertTrue( (bool) has_action( 'delete_post', array( Sync_Service::get_instance(), 'delete_item' ) ) );
	}


}