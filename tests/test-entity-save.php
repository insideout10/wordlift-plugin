<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Entity\Entity_No_Index_Flag;

/**
 * Class Entity_Save_Test
 * @group entity
 */
class Entity_Save_Test extends Wordlift_Unit_Test_Case {

	public function setUp() {
		parent::setUp();
	}

	public function test_when_entity_is_saved_for_first_time_should_set_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( 1, $result, 'Value should be 1 to denote the flag set by yoast' );

	}


	public function test_when_other_post_type_is_saved_for_first_time_should_not_set_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create();

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( '', $result, 'The flag should not be set for other post types' );

	}


	public function test_when_other_post_type_is_updated_should_not_remove_noindex_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create();

		// Emulate the post has no index flag, it should not be removed on update
		update_post_meta( $entity, Entity_No_Index_Flag::YOAST_POST_NO_INDEX_FLAG, 1);

		// update the entity
		wp_update_post( array( 'ID' => $entity, 'post_content' => 'test' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( 1, $result, 'The flag should be present for other post type' );

	}



	public function test_when_entity_is_updated_should_remove_yoast_no_index_flag() {

		new Entity_No_Index_Flag();

		$entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// update the entity
		wp_update_post( array( 'ID' => $entity, 'post_content' => 'test' ) );

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', true );

		$this->assertEquals( '', $result, 'The no index flag should not be present' );

	}


}
