<?php

use Wordlift\Post\Post_Adapter;

/**
 * Define the Wordlift_Entity_Post_Adapter_Test class.
 *
 * @since 3.29.0
 * @group entity
 */
class Wordlift_Entity_Block_Editor_Post_Adapter extends Wordlift_Unit_Test_Case {

	/**
	 * @var Post_Adapter
	 */
	private $post_adapter;

	function setUp() {
		parent::setUp();
		$this->post_adapter = new Post_Adapter();
	}


	public function test_when_post_status_is_set_to_auto_draft_or_inherit_should_not_process_data() {
		$this->assertCount(
			0,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'Before tests no entity should be present'
		);
		$this->post_adapter->wp_insert_post_data(
			array( 'post_status' => 'auto-draft' ),
			array()
		);
		$this->post_adapter->wp_insert_post_data(
			array( 'post_status' => 'inherit' ),
			array()
		);
		$this->assertCount(
			0,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'0 entities should be created since the post status is set to auto draft or inherit'
		);
	}


}