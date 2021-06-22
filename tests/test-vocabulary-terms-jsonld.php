<?php

/**
 * @since 3.31.7
 * @group vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Terms_Jsonld extends \Wordlift_Unit_Test_Case {


	public function test_should_generate_id_for_term_correctly() {

		$term   = $this->factory()->term->create();
		$jsonld = Wordlift_Term_JsonLd_Adapter::get_instance()->get( $term );
		// Since there are no types set, we should default it to Thing,
		$this->assertSame( $jsonld[0]['@type'], array( 'Thing' ) );
		$this->assertArrayHasKey( '@id', $jsonld[0] );
	}


}