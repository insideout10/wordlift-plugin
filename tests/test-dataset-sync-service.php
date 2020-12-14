<?php
/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Dataset\Sync_Post_Hooks;
use Wordlift\Dataset\Sync_Service;

/**
 * Class Api_Sync_Service
 * @group api
 */
class Test_Dataset_Sync_Service extends Wordlift_Unit_Test_Case {

	/**
	 * This test asserts we batch the sync request.
	 */
	public function test_on_save_post_to_sync_service_should_have_post_id() {
		$post_id              = $this->factory()->post->create();
		$post                 = get_post( $post_id );
		$update               = false;
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		// we call on save_post, updated_post_meta, deleted_post_meta
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// Fire a save_post.
		do_action( 'save_post', $post_id, $post, $update );
		$this->assertNotNull( $sync_post_hooks->post_id );
		$this->assertEquals( $post_id, $sync_post_hooks->post_id );
	}


	public function test_on_updated_post_meta_to_sync_service_should_have_post_id() {
		$post_id              = $this->factory()->post->create();
		$post                 = get_post( $post_id );
		$update               = false;
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		// we call on save_post, updated_post_meta, deleted_post_meta
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// Fire a save_post.
		do_action( 'updated_post_meta', '', $post_id, '', '' );
		$this->assertNotNull( $sync_post_hooks->post_id );
		$this->assertEquals( $post_id, $sync_post_hooks->post_id );
	}

	public function test_on_added_post_meta_to_sync_service_should_have_post_id() {
		$post_id              = $this->factory()->post->create();
		$post                 = get_post( $post_id );
		$update               = false;
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		// we call on save_post, updated_post_meta, deleted_post_meta
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// Fire a save_post.
		do_action( 'added_post_meta', '', $post_id, '', '' );
		$this->assertNotNull( $sync_post_hooks->post_id );
		$this->assertEquals( $post_id, $sync_post_hooks->post_id );
	}

	public function test_on_deleted_post_meta_to_sync_service_should_have_post_id() {
		$post_id              = $this->factory()->post->create();
		$post                 = get_post( $post_id );
		$update               = false;
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		// we call on save_post, updated_post_meta, deleted_post_meta
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// Fire a save_post.
		do_action( 'deleted_post_meta', '', $post_id, '', '' );
		$this->assertNotNull( $sync_post_hooks->post_id );
		$this->assertEquals( $post_id, $sync_post_hooks->post_id );
	}

	public function test_should_have_a_function_on_shutdown_call() {
		global $wp_filter;
		$wp_filter            = array();
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		// we call on save_post, updated_post_meta, deleted_post_meta
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// we should have a function on shutdown hook
		$this->assertArrayHasKey( 'shutdown', $wp_filter );
		$this->assertCount( 1, $wp_filter['shutdown']->callbacks );
	}


	public function test_on_shutdown_method_sync_should_be_called() {
		$sync_service         = Sync_Service::get_instance();
		$sync_adapter_factory = new \Wordlift\Dataset\Sync_Object_Adapter_Factory();
		$sync_post_hooks = new Sync_Post_Hooks( $sync_service, $sync_adapter_factory );
		// do a save_post
		$post_id              = $this->factory()->post->create();
		$post                 = get_post( $post_id );
		$update               = false;
		do_action( 'save_post', $post_id, $post, $update );
		// now try to call the shutdown hook.
		$this->assertTrue( $sync_post_hooks->shutdown() );

	}
}
