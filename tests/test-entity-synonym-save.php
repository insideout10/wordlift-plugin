<?php

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Object_Type_Enum;

/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Entity_Synonym_Save_Test extends Wordlift_Unit_Test_Case {


	public function test_when_synonym_saved_for_entity_should_not_be_replaced_while_saving_the_post_where_it_is_referenced() {

		if ( ! function_exists('parse_blocks') ) {
			$this->markTestSkipped('Skipped because WP < 5.0 doesnt have parse_blocks function');
		}

		$entity_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		Wordlift_Entity_Service::get_instance()->set_alternative_labels( $entity_id,
			array( 's1', 's2', 's3' )
		);

		// create a post referencing this entity.
		$entity_uri = Wordpress_Content_Service::get_instance()->get_entity_id(
			new Wordpress_Content_Id( $entity_id, Object_Type_Enum::POST )
		);

		$post_content = <<<EOF
<span id="urn:enhancement-98208d90-b62e-476c-a05a-242bcd90788d" class="textannotation disambiguated wl-thing" itemid="$entity_uri">s4</span>
EOF;


		$post_id = $this->factory()->post->create( array(
			'post_content' => $post_content,
			'post_status'  => 'publish'
		) );

		$synonyms = Wordlift_Entity_Service::get_instance()->get_alternative_labels( $entity_id );

		$this->assertCount( 4, $synonyms );

		$this->assertEquals( $synonyms, array( 's1', 's2', 's3', 's4' ) );
	}

}