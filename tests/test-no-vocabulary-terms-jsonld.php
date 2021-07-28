<?php

use Wordlift\Analysis\Response\Analysis_Response_Ops_Factory;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Term\Uri_Service;

/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocabulary_Terms_Jsonld extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {


	public function test_when_term_saved_should_generate_entity_uri() {
		$term_id    = wp_insert_term( 'vocabulary_term_test_1', 'no_vocabulary_terms' );
		$term_id    = $term_id['term_id'];
		$entity_uri = get_term_meta( $term_id, 'entity_url', true );
		$this->assertNotEmpty( $entity_uri, 'Entity uri should be set upon term save' );
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


	public function test_when_post_is_annotated_with_term_without_dataset_uri_should_not_add_it_to_mentions() {

		$term_data = wp_insert_term( 'vocabulary_term_test_3', 'no_vocabulary_terms' );
		$term_id   = $term_data['term_id'];
		// Add it to a post.
		$post_id = $this->factory()->post->create();
		// set the terms
		wp_set_object_terms( $post_id, array( $term_id ), 'no_vocabulary_terms' );

		// We manually remove entity_url
		delete_term_meta( $term_id, 'entity_url' );

		// Check the jsonld of the post
		$jsonld = Wordlift_Jsonld_Service::get_instance()
		                                 ->get_jsonld( false, $post_id, Jsonld_Context_Enum::PAGE );
		$this->assertCount( 1, $jsonld, 'We should have no term references' );

		$this->assertFalse( array_key_exists( 'mentions', $jsonld[0] ), 'Shouldnt be added to mentions since it doesnt have dataset uri' );
	}


	public function test_when_post_is_annotated_with_term_with_dataset_uri_should_be_added_to_mentions() {

		$term_data = wp_insert_term( 'vocabulary_term_test_4', 'no_vocabulary_terms' );
		$term_id   = $term_data['term_id'];
		// Add it to a post.
		$post_id = $this->factory()->post->create();
		// set the terms
		wp_set_object_terms( $post_id, array( $term_id ), 'no_vocabulary_terms' );

		// Check the jsonld of the post
		$jsonld      = Wordlift_Jsonld_Service::get_instance()
		                                      ->get_jsonld( false, $post_id, Jsonld_Context_Enum::PAGE );
		$post_jsonld = $jsonld[0];
		$this->assertTrue( array_key_exists( 'mentions', $post_jsonld ), 'Mentions should have the terms' );
		$this->assertCount( 1, $post_jsonld['mentions'], 'The term mention should be present' );

	}


}