<?php

use Wordlift\Jsonld\Post_Reference;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Wordlift_Object_Relation_Service;

/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group install
 */
class Install_3_32_0_Test extends Wordlift_Unit_Test_Case {

	public function test_should_default_to_post_for_relation_instance() {
		$post = $this->factory()->post->create( array( 'post_type' => 'post')  );
		$linked_entity = $this->factory()->post->create(array('post_type' => 'entity'));
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $linked_entity );
		wl_core_add_relation_instance( $post, WL_WHAT_RELATION, $linked_entity + 1, Object_Type_Enum::TERM );
		$references = Object_Relation_Service::get_instance()->get_references( $post );
		$this->assertCount( 2, $references );
		$this->assertTrue( $references[0] instanceof Post_Reference );
		$this->assertTrue( $references[1] instanceof Term_Reference );
	}

}
