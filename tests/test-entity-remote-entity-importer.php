<?php
/**
 * @since 3.35.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Content\WordPress\Wordpress_Content_Service;
use Wordlift\Entity\Remote_Entity\Invalid_Remote_Entity;
use Wordlift\Entity\Remote_Entity\Valid_Remote_Entity;
use Wordlift\Entity\Remote_Entity_Importer\Invalid_Remote_Entity_Importer;
use Wordlift\Entity\Remote_Entity_Importer\Remote_Entity_Importer_Factory;
use Wordlift\Entity\Remote_Entity_Importer\Valid_Remote_Entity_Importer;

/**
 * Class Entity_Remote_Entity_Importer_Test
 * @group entity
 */
class Entity_Remote_Entity_Importer_Test extends Wordlift_Unit_Test_Case {


	public function test_given_invalid_entity_should_return_invalid_importer() {
		$importer = Remote_Entity_Importer_Factory::from_entity(
			new Invalid_Remote_Entity()
		);
		$this->assertTrue( $importer instanceof Invalid_Remote_Entity_Importer );
		$this->assertFalse( $importer->import(), 'Cant import invalid entities, So it should return false' );
	}


	public function test_given_valid_entity_should_return_valid_importer() {
		$importer = Remote_Entity_Importer_Factory::from_entity(
			new Valid_Remote_Entity(
				array( 'Thing' ),
				'name',
				'description',
				array( 'https://schema.org' )
			)
		);
		$this->assertTrue( $importer instanceof Valid_Remote_Entity_Importer );
	}

	public function test_given_valid_entity_should_import_to_db_correctly() {
		$item_id             = 'https://example.org/entity_1';
		$valid_remote_entity = new Valid_Remote_Entity(
			array( 'Thing', 'Recipe' ),
			'name',
			'description',
			array( $item_id, 'https://example.org/entity_2' )
		);
		$importer            = Remote_Entity_Importer_Factory::from_entity( $valid_remote_entity );
		$importer->import();

		$content_service = Wordpress_Content_Service::get_instance();
		$content         = $content_service->get_by_entity_id_or_same_as( $item_id );
		$this->assertNotNull( $content, 'Post should be created.' );
		/**
		 * @var $post  WP_Post
		 */
		$post = $content->get_bag();
		$this->assertEquals( 'draft', $post->post_status, 'Imported entity should not be published' );
		// check if entity types are set correctly
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();
		$types               = $entity_type_service->get_names( $post->ID );
		$this->assertTrue( count( array_diff( $types, $valid_remote_entity->get_types() ) ) === 0, 'All the types should be imported' );
		$this->assertTrue(
			count( array_diff( get_post_meta( $post->ID, 'entity_same_as' ), $valid_remote_entity->get_same_as() ) ) === 0,
			'All the sameAs URIs should be imported'
		);
	}


}
