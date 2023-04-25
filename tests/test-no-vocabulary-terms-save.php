<?php

use Wordlift\Relation\Post_Terms_Relation_Service;
use Wordlift\Term\Type_Service;

/**
 * @since 3.31.7
 * @group no-vocabulary-terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocabulary_Terms_Save extends Wordlift_Vocabulary_Terms_Unit_Test_Case {

	public function test_when_entity_url_already_present_shouldnt_change() {

		$term_data  = wp_insert_term( 'no_vocabulary_term_3', self::NO_VOCABULARY_TERM_TAXONOMY );
		$term_id    = $term_data['term_id'];
		$entity_url = get_term_meta( $term_id, 'entity_url', true );
		// now update the term, we should have the same uri.
		wp_update_term( $term_id, self::NO_VOCABULARY_TERM_TAXONOMY, array(
			'description' => 'some changed description'
		) );
		$current_entity_url = get_term_meta( $term_id, 'entity_url', true );
		$this->assertSame( $entity_url, $current_entity_url, 'Entity URL should be the same' );

	}

	public function test_when_entity_type_is_not_assigned_should_return_thing() {
		$term_data    = wp_insert_term( 'no_vocabulary_term_3', self::NO_VOCABULARY_TERM_TAXONOMY );
		$term_id      = $term_data['term_id'];
		$entity_types = Type_Service::get_instance()->get_entity_types( $term_id );
		$this->assertCount( 1, $entity_types, 'Default entity type should be set to thing' );
		$this->assertTrue( $entity_types[0] instanceof WP_Term );
		$this->assertSame( 'Thing', $entity_types[0]->name );
	}

	public function test_should_return_relation_type_correctly_for_terms() {
		$term_data     = wp_insert_term( 'no_vocabulary_term_3', self::NO_VOCABULARY_TERM_TAXONOMY );
		$term_id       = $term_data['term_id'];
		$term_relation = Post_Terms_Relation_Service::get_instance();
		$this->assertSame( WL_WHAT_RELATION, $term_relation->get_relation_type( $term_id ), 'For Thing we should get WL_WHAT_RELATION' );
	}

}
