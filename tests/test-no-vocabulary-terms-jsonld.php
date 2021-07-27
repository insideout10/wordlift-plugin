<?php

use Wordlift\Analysis\Response\Analysis_Response_Ops_Factory;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Term\Type_Service;
use Wordlift\Term\Uri_Service;

/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocabulary_Terms_Jsonld extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {


	public function test_when_term_saved_should_generate_entity_uri() {
		$term_id    = $this->create_and_get_term();
		$entity_uri = get_term_meta( $term_id, 'entity_url', true );
		$this->assertNotEmpty( $entity_uri, 'Entity uri should be set upon term save' );
	}

	public function test_when_the_dataset_uri_not_present_dont_add_it_to_jsonld() {
		$term_id = $this->create_and_get_term();
		delete_term_meta( $term_id, 'entity_url' );
		// Try to get the jsonld for this term.
		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::UNKNOWN );
		$this->assertCount( 0, $jsonld );
	}

	public function test_when_the_property_reference_gets_added_to_the_term_should_print_correctly() {
		$term_id = $this->create_and_get_term();
		// Set the Entity type to Person.
		$term_type_service = Type_Service::get_instance();
		$term_type_service->set_entity_types( $term_id, array( 'person' ) );
		// Set  the birthPlace property to refer to another entity.
		$birth_place_entity_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		update_term_meta( $term_id, 'wl_birth_place', $birth_place_entity_id );
		// We should have this property on jsonld.
		$jsonld             = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::UNKNOWN );
		$term_entity_jsonld = $jsonld[0];
		$this->assertArrayHasKey( 'birthPlace', $term_entity_jsonld );
		$this->assertCount( 2, $jsonld, 'Term and the birth place reference should be expanded' );
	}


	public function test_should_generate_id_for_term_correctly() {

		$term_id = wp_insert_term( 'vocabulary_term_test_2', 'no_vocabulary_terms' );
		$term_id = $term_id['term_id'];
		// UNKNOWN is the context used when pushing to KG.
		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::UNKNOWN );
		// Since there are no types set, we should default it to Thing,
		$this->assertSame( $jsonld[0]['@type'], array( 'Thing' ) );
		$this->assertArrayHasKey( '@id', $jsonld[0] );
		$this->assertSame( wl_get_term_entity_uri( $term_id ), $jsonld[0]['@id'] );
	}


	public function test_when_save_post_with_term_references_should_generate_jsonld_correctly() {
		$post_id = $this->create_post_with_term_reference( 'post_save_term_2' );
		// generate the jsonld, we should have two items in the jsonld.
		$jsonld = Jsonld_Service::get_instance()->get(
			Object_Type_Enum::POST,
			$post_id
		);
		$this->assertCount( 2, $jsonld );
		// get the term references for this post.
		$references = Object_Relation_Service::get_instance()->get_references( $post_id, Object_Type_Enum::POST );
		$term_uri   = Uri_Service::get_instance()->get_uri_by_term( $references[0]->get_id() );
		$this->assertSame( $term_uri, $jsonld[1]['@id'], 'The term @id should be present in jsonld' );
		$this->assertSame( array( 'Thing' ), $jsonld[1]['@type'], 'The term @type should be present in jsonld' );
	}


	public function test_should_get_annotation_also_for_terms() {

		$term_data        = wp_insert_term( 'term_analysis_test_1', 'category' );
		$term             = get_term( $term_data['term_id'] );
		$term_uri_service = Uri_Service::get_instance();
		$term_uri_service->set_entity_uri( $term->term_id, "http://example.org/content_analysis_test_2" );

		$request_body = file_get_contents( dirname( __FILE__ ) . '/assets/content-term-analysis-request.json' );
		$request_json = json_decode( $request_body, true );

		$response_json = Analysis_Response_Ops_Factory
			::get_instance()
			->create( json_decode( '{ "entities": {}, "annotations": {}, "topics": {} }' ) )
			->make_entities_local()
			->add_occurrences( $request_json['content'] )
			->to_string();

		$response_json = json_decode( $response_json, true );

		$this->assertCount( 1, array_values( $response_json['entities'] ), 'The term entity should be present in response' );
		$this->assertCount( 1, array_values( $response_json['annotations'] ), 'The term annotation should be present in response' );
	}

	/**
	 * @return int|mixed
	 */
	private function create_and_get_term() {
		$term_id = wp_insert_term( 'vocabulary_term_test_1', 'no_vocabulary_terms' );
		$term_id = $term_id['term_id'];

		return $term_id;
	}

}