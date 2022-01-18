<?php

use WordLift\Cleanup\Entity_Annotation_Cleanup_Post_Handler;

class Cleanup_Replace_Entity_Annotations_Test extends Wordlift_Unit_Test_Case {

	function test_replace_relative_with_absolute_url_if_local_entity_exists() {
		$content          = <<<EOF
<span id="urn:enhancement-61" class="textannotation disambiguated wl-thing" itemid="/entity/electronic_mail">email</span>
EOF;
		$expected_content = <<<EOF
 <span id="urn:enhancement-61" class="textannotation disambiguated wl-thing" itemid="https://wordlift.localhost/vocabulary/entity/electronic_mail">email</span>
 EOF;
		$post_id =
		$data = array( '$post_content' => addslashes( $content ) );
		$entity_annotation_cleanup_post_handler = Entity_Annotation_Cleanup_Post_Handler::get_instance();
		$output = $entity_annotation_cleanup_post_handler->process_post( $data );

		$this->assertEquals( addslashes( $expected_content ), $output['post_content'] );

	}

	function test_cleanup_entity_annotation_from_posts_content() {

	}
}
