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
	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;


	public function setUp() {
		parent::setUp();
		$this->jsonld_service = Wordlift_Jsonld_Service::get_instance();
		$this->entity_service = Wordlift_Entity_Service::get_instance();
	}

	public function test_when_entity_jsonld_has_references_should_add_additional_context() {
		$post_1       = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$post_2       = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		$expected_uri = $this->entity_service->get_uri( $post_2 );
		wl_core_add_relation_instances( $post_1, WL_WHAT_RELATION, array(
			$post_2
		) );
		$jsonld = $this->jsonld_service->get_jsonld( false, $post_1 );
		$item   = $jsonld[0];
		// should have the entity urls in the relation property.
		$this->assertArrayHasKey( 'http://purl.org/dc/terms/relation', $item );
		$this->assertCount( 1, $item['http://purl.org/dc/terms/relation'] );
		$this->assertEquals( $item['http://purl.org/dc/terms/relation'][0]['@id'], $expected_uri );
	}


	public function test_when_no_references_for_entity_present_should_not_add_relation_property() {
		$post_1 = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		$jsonld = $this->jsonld_service->get_jsonld( false, $post_1 );
		$item   = $jsonld[0];
		// should have the entity urls in the relation property.
		$this->assertFalse( array_key_exists( 'http://purl.org/dc/terms/relation', $item ) );
	}


}