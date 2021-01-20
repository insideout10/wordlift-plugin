<?php
/**
 * @since 3.28.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

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

		$result = get_post_meta( $entity, '_yoast_wpseo_meta-robots-noindex', false );

		$this->assertEquals( 1, $result, 'Value should be 1 to denote the flag set by yoast' );

	}


}
