<?php
/**
 * @since ?.??.?
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

class Dct_Relations_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $jsonld_service;


	public function setUp() {
		parent::setUp();
		$this->jsonld_service = Wordlift_Jsonld_Service::get_instance();
	}

	public function test_when_entity_jsonld_has_references_should_add_additional_context() {

		$jsonld = array();
		$post_1 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_2 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		wl_core_add_relation_instances( $post_1, WL_WHAT_RELATION, array(
			$post_2
		) );
		$jsonld = $this->jsonld_service->get_jsonld( false, $post_1 );
		// the @context key should be array.
		$this->assertCount( 2, $jsonld );
		$item = $jsonld[0];
		$this->assertArrayHasKey( '@context', $item );
		$this->assertTrue( is_array( $item['@context'] ) );
		$context = $item['@context'];
		$this->assertEquals( $context, array( 'http://schema.org', 'http://purl.org' ) );
	}

}