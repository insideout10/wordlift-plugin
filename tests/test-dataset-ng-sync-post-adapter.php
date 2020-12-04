<?php

use Wordlift\Dataset\Sync_Post_Adapter;

/**
 * Class Test_Dataset_Ng_Sync_Post_Adapter
 *
 * @group dataset-ng
 */
class Test_Dataset_Ng_Sync_Post_Adapter extends Wordlift_Unit_Test_Case {

	function setUp() {
		parent::setUp();

		// These tests make sense only if `WL_FEATURES__DATASET_NG` is enabled.
		if ( ! wp_validate_boolean( getenv( 'WL_FEATURES__DATASET_NG' ) ) ) {
			$this->markTestSkipped( '`WL_FEATURES__DATASET_NG` not enabled.' );
		}
	}

	function test_update_meta() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		$adapter->update_meta( '_tmp_test_key', 'value123' );
		$this->assertEquals( 'value123', get_post_meta( $post_id, '_tmp_test_key', true ) );
	}

	function test_get_meta() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123'
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		update_post_meta( $post_id, '_tmp_test_key_2', 'value456' );
		$this->assertEquals( 'value456', $adapter->get_meta( '_tmp_test_key_2', true ) );
	}

	function test_is_not_published() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
			'post_status'  => 'draft',
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		$this->assertFalse( $adapter->is_published() );
	}

	function test_is_published() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
			'post_status'  => 'publish',
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		$this->assertTrue( $adapter->is_published() );
	}

	function test_is_public() {
		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
			'post_status'  => 'draft',
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		$this->assertTrue( $adapter->is_public() );
	}

	function test_is_not_public() {
		register_post_type( 'tmp_post_type', array( 'public' => false ) );

		$post_id = $this->factory()->post->create( array(
			'post_title'   => 'Title 123',
			'post_content' => 'Content 123',
			'post_status'  => 'draft',
			'post_type'    => 'tmp_post_type',
		) );

		$adapter = new Sync_Post_Adapter( $post_id );
		$this->assertFalse( $adapter->is_public() );
	}

}
