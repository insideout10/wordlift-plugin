<?php

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
		$this->assertSame( array('Thing'), $jsonld[1]['@type'], 'The term @type should be present in jsonld' );
	}


}