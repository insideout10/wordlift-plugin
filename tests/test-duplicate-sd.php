<?php

/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @group duplicate_sd
 * Class Duplicate_Markup_Test
 */
class Duplicate_Sd_Test {

	public function test_given_post_with_faq_markup_should_not_allow_other_faq_on_page() {

		$post_1 = $this->factory()->post->create();
		Wordlift_Entity_Type_Service::get_instance()
		                            ->set( $post_1, 'http://schema.org/FAQPage', true );
		$post_2 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		Wordlift_Entity_Type_Service::get_instance()
		                            ->set( $post_2, 'http://schema.org/FAQPage', true );
		// Link post_2 to post_1.
		wl_core_add_relation_instance( $post_1, WL_WHAT_RELATION, $post_2 );
		// Now get the json ld, there should be only one item.
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $post_1 );
		$this->assertCount( 1, $jsonld );
	}


}
