<?php

use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Term_Relation;
use Wordlift\Relation\Term_Relation_Service;
use Wordlift\Term\Uri_Service;

/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocbulary_Terms_Db_Test extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {


	public function test_post_save_should_term_references() {
		$post_id = $this->create_post_with_term_reference('post_save_term_1');
		$relations = Term_Relation_Service::get_instance();
		$relations = $relations->get_relations( $post_id );
		// We should have term relations.
		$this->assertCount( 1, $relations, 'Term relation should be saved' );
		$this->assertTrue( $relations[0] instanceof Term_Relation, 'We should have term relation' );
	}



}