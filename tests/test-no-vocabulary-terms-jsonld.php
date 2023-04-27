<?php

use Wordlift\Analysis\Response\Analysis_Response_Ops_Factory;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Relation_Service;
use Wordlift\Term\Type_Service;

/**
 * @since 3.31.7
 * @group no-vocabulary-terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocabulary_Terms_Jsonld extends Wordlift_Vocabulary_Terms_Unit_Test_Case {

	public function test_when_term_saved_should_generate_entity_uri() {
		$term_id    = $this->create_and_get_term();
		$entity_uri = get_term_meta( $term_id, 'entity_url', true );
		$this->assertNotEmpty( $entity_uri, 'Entity uri should be set upon term save' );
	}

	public function test_when_the_dataset_uri_not_present_dont_add_it_to_jsonld() {
		$this->markTestSkipped( 'As of 3.33.9 we automatically generate IDs when missing.' );

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
		$this->assertCount( 1, $term_entity_jsonld['birthPlace'], 'Birth place ids should be present' );
		$birth_place_data = array( '@id' => wl_get_entity_uri( $birth_place_entity_id ) );
		$this->assertSame( $birth_place_data, $term_entity_jsonld['birthPlace'][0], 'Reference data should be expanded correctly.' );
		$this->assertSame( $jsonld[1]['@id'], wl_get_entity_uri( $birth_place_entity_id ), 'Entity URI should be present' );
	}

	public function test_when_term_has_post_entity_reference_and_linked_to_different_entity_should_generate_correct_jsonld() {
		$tag     = wp_create_tag( 'test_jsonld_tag' );
		$term_id = $tag['term_id'];
		// Set the Entity type to Person.
		$term_type_service = Type_Service::get_instance();
		$term_type_service->set_entity_types( $term_id, array( 'person' ) );
		// Set the birthPlace property to refer to another entity.
		$birth_place_entity_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		update_term_meta( $term_id, 'wl_birth_place', $birth_place_entity_id );
		// Link another entity to birthplace entity.
		$another_entity = $this->factory()->post->create( array( 'post_type' => 'entity' ) );
		wl_core_add_relation_instance( $birth_place_entity_id, WL_WHAT_RELATION, $another_entity );
		// Now generate the jsonld.
		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get(
			$term_id,
			Jsonld_Context_Enum::PAGE
		);
		$this->assertCount( 2, $jsonld, 'We should have term, as well as the post entity reference' );

		$this->assertSame( $jsonld[1]['@id'], wl_get_entity_uri( $birth_place_entity_id ) );
	}


	public function test_should_generate_id_for_term_correctly() {

		$term_id = wp_insert_term( 'vocabulary_term_test_2', 'no_vocabulary_terms' );
		$term_id = $term_id['term_id'];
		// UNKNOWN is the context used when pushing to KG.
		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::UNKNOWN );
		// Since there are no types set, we should default it to Thing,
		$this->assertSame( $jsonld[0]['@type'], array( 'Thing' ) );
		$this->assertArrayHasKey( '@id', $jsonld[0] );
		$this->assertSame( Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_term( $term_id ) ), $jsonld[0]['@id'] );
	}


	public function test_when_save_post_with_term_references_should_generate_jsonld_correctly() {
		$post_id = $this->create_post_with_term_reference( 'post_save_term_2' );
		// generate the jsonld, we should have two items in the jsonld.
		$jsonld = Jsonld_Service::get_instance()->get( Object_Type_Enum::POST, $post_id );

		$this->assertCount( 2, $jsonld );
		// get the term relations for this post.
		$relations = Relation_Service::get_instance()->get_relations(
			Wordpress_Content_Id::create_post( $post_id )
		)->toArray();
		$term_uri   = Wordpress_Content_Service::get_instance()
											->get_entity_id(
												current($relations)->get_object()
											);
		$this->assertSame( $term_uri, $jsonld[1]['@id'], 'The term @id should be present in jsonld' );
		$this->assertSame( array( 'Thing' ), $jsonld[1]['@type'], 'The term @type should be present in jsonld' );
	}


	public function test_should_get_annotation_also_for_terms() {

		$term_data = wp_insert_term( 'term_analysis_test_1', 'category' );
		$term      = get_term( $term_data['term_id'] );
		Wordpress_Content_Service::get_instance()
								->set_entity_id(
									Wordpress_Content_Id::create_term( $term->term_id ),
									'https://data.localdomain.localhost/dataset/content_analysis_test_2'
								);

		$request_body = file_get_contents( dirname( __FILE__ ) . '/assets/content-term-analysis-request.json' );
		$request_json = json_decode( $request_body, true );

		$response_json = Analysis_Response_Ops_Factory::get_instance()
			->create( json_decode( '{ "entities": {}, "annotations": {}, "topics": {} }' ), null )
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

		var_dump($jsonld);
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

	function test_when_the_post_has_term_which_is_converted_to_entity_should_be_added_to_mentions() {
		$post_id   = $this->factory()->post->create();
		$term_data = wp_insert_term( 'vocabulary_term_test_5', 'no_vocabulary_terms' );
		$term_id   = $term_data['term_id'];
		wp_set_object_terms( $post_id, array( $term_id ), 'no_vocabulary_terms' );

		$jsonld = Wordlift_Jsonld_Service::get_instance()
										 ->get_jsonld( false, $post_id, Jsonld_Context_Enum::PAGE );

		// we should have the term entity in mentions.
		$term_entity_uri = Wordpress_Content_Service::get_instance()->get_entity_id( Wordpress_Content_Id::create_term( $term_id ) );

		$this->assertSame( array( '@id' => $term_entity_uri ), $jsonld[0]['mentions'][0] );
		$this->assertSame( $term_entity_uri, $jsonld[1]['@id'] );

	}

	function test_when_the_term_reference_present_in_jsonld_should_be_expanded() {
		$term_data         = wp_insert_term( 'vocabulary_term_test_6', 'no_vocabulary_terms' );
		$term_id           = $term_data['term_id'];
		$term_type_service = Type_Service::get_instance();
		$term_type_service->set_entity_types( $term_id, array( 'person' ) );

		$linked_term_data = wp_insert_term( 'vocabulary_term_test_7', 'no_vocabulary_terms' );
		$linked_term_id   = $linked_term_data['term_id'];
		update_term_meta( $term_id, 'wl_birth_place', 'term_' . $linked_term_id );

		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::REST );
		$this->assertCount( 2, $jsonld, 'Term references must be expanded' );
		$this->assertSame( 'vocabulary_term_test_7', $jsonld[1]['name'], 'Term references must be expanded' );

	}

	function test_when_the_term_is_linked_to_itself_it_should_not_expand() {
		$term_data         = wp_insert_term( 'vocabulary_term_test_8', 'no_vocabulary_terms' );
		$term_id           = $term_data['term_id'];
		$term_type_service = Type_Service::get_instance();
		$term_type_service->set_entity_types( $term_id, array( 'person' ) );

		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term_id, Jsonld_Context_Enum::REST );
		$this->assertCount( 1, $jsonld, 'Term references must not be expanded since it would cause infinite loop' );
	}

}
