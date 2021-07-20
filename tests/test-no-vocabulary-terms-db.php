<?php

use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Term_Relation_Service;
use Wordlift\Relation\Types\Term_Relation;


/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocbulary_Terms_Db_Test extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {


	public function test_post_save_should_term_references() {
		$post_id          = $this->create_post_with_term_reference( 'post_save_term_1' );
		$relation_service = Object_Relation_Service::get_instance();
		$references       = $relation_service->get_references( $post_id, Object_Type_Enum::POST );
		// We should have term references.
		$this->assertCount( 1, $references, 'Term_Reference should be saved' );
		$this->assertTrue( $references[0] instanceof Term_Reference, 'We should have term relation' );
	}


}