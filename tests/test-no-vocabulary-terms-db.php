<?php

use Wordlift\Relation\Object_Relation_Service;
use Wordlift\Relation\Term_Relation;
use Wordlift\Term\Uri_Service;

/**
 * @since 3.31.7
 * @group no_vocabulary_terms
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class No_Vocbulary_Terms_Db_Test extends \Wordlift_Vocabulary_Terms_Unit_Test_Case {


	public function test_post_save_should_term_references() {


		$term_data        = wp_insert_term( 'post_save_term_1', self::NO_VOCABULARY_TERM_TAXONOMY );
		$term             = get_term( $term_data['term_id'] );
		$term_uri_service = Uri_Service::get_instance();
		$term_uri         = $term_uri_service->get_uri_by_term( $term->term_id );
		$post_content     = <<<EOF
		<span itemid="$term_uri">test</span>
EOF;
		$post_id          = wp_insert_post(array(
			'post_content' => $post_content
		));
		$relations = Object_Relation_Service::get_instance();
		$relations = $relations->get_relations( $post_id );
		// We should have term relations.
		$this->assertCount( 1, $relations, 'Term relation should be saved' );
		$this->assertTrue( $relations[0] instanceof Term_Relation, 'We should have term relation' );
	}

}