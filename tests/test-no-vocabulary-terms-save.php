<?php

/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocabulary_Terms_Save extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {

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


}