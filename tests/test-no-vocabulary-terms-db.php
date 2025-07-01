<?php

use Wordlift\Jsonld\Term_Reference;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Object_Relation_Service;


/**
 * @since 3.31.7
 * @group no-vocabulary-terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocbulary_Terms_Db_Test extends Wordlift_Vocabulary_Terms_Unit_Test_Case {

	public function test_post_save_should_term_references() {
		$post_id          = $this->create_post_with_term_reference( 'post_save_term_1' );
		$relation_service = \Wordlift\Relation\Relation_Service::get_instance();
		/**
		 * @var $relations array<\Wordlift\Relation\Relation>
		 */
		$relations       = $relation_service->get_relations( \Wordlift\Content\WordPress\Wordpress_Content_Id::create_post($post_id) )->toArray();
		// We should have term relations.
		$this->assertCount( 1, $relations, 'Term_Reference should be saved' );
		$this->assertEquals( Object_Type_Enum::TERM,  $relations[0]->get_object()->get_type(), 'We should have term relation' );
	}

}
