<?php

use Wordlift\Content\WordPress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Store;
use Wordlift\Object_Type_Enum;

/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Synonym_Save_Test extends Wordlift_Unit_Test_Case {


	public function test_when_synonym_saved_for_entity_should_not_be_replaced_while_saving_the_post_where_it_is_referenced() {

		$entity_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		Wordlift_Entity_Service::get_instance()->set_alternative_labels( $entity_id,
			array( 's1', 's2', 's3' )
		);

		Entity_Store::get_instance()->update( array(
			'ID'     => $entity_id,
			'labels' => array( 's4' ),
			'same_as' => array()
		));

		$synonyms = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $entity_id );

		$this->assertCount( 4, $synonyms, 's1, s2, s3, s4 synonyms should be saved.' );

		$this->assertEquals( $synonyms, array( 's1', 's2', 's3', 's4' ), 'New synonym should be added only at the end' );
	}

}