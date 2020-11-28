<?php
/**
 * This file provides the tests for the {@link Wordlift\Entity\Entity_Factory} class.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/944
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 */

use Wordlift\Entity\Entity_Store;

/**
 * Class Entity_Factory_Test
 * @group entity
 */
class Entity_Factory_Test extends \Wordlift_Unit_Test_Case {

	/**
	 * Test creating an entity providing only one label as string.
	 *
	 * @throws Exception
	 */
	public function test_create__only_label_as_string() {

		$entity_factory = Entity_Store::get_instance();

		$post_id = $entity_factory->create( array(
			'labels' => 'Label 1',
		) );

		$this->assertEquals( 'Label 1', get_the_title( $post_id ), 'The title must match the provided label.' );

		$alt_labels = get_post_meta( $post_id, Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY );
		$this->assertCount( 0, $alt_labels, 'We don`t expect alternate labels because we didn`t provide any.' );

	}

	/**
	 * Test creating an entity providing multiple labels, a description and multiple same as.
	 *
	 * @throws Exception
	 */
	public function test_create__many_labels() {

		$entity_factory = Entity_Store::get_instance();

		$post_id = $entity_factory->create( array(
			'labels'      => array( 'Label 1', 'Label 2', 'Label 3' ),
			'description' => 'Lorem Ipsum',
			'same_as'     => array( 'http://example.org/1', 'http://example.org/2' ),
		) );

		$post = get_post( $post_id );
		$this->assertEquals( 'Label 1', $post->post_title, 'The title must match the provided label.' );

		$this->assertEquals( 'Lorem Ipsum', $post->post_content, 'The post`s content must match the title.' );

		$alt_labels = get_post_meta( $post_id, \Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY );
		$this->assertCount( 2, $alt_labels, 'We expect 2 alternate labels.' );
		$this->assertContains( 'Label 2', $alt_labels, 'Alternative labels must contain `Label 2`.' );
		$this->assertContains( 'Label 3', $alt_labels, 'Alternative labels must contain `Label 3`.' );

		$same_as = get_post_meta( $post_id, \Wordlift_Schema_Service::FIELD_SAME_AS );
		$this->assertCount( 2, $same_as, 'We expect 2 alternate labels.' );
		$this->assertContains( 'http://example.org/1', $same_as, '`same_as` must contain `http://example.org/1`.' );
		$this->assertContains( 'http://example.org/2', $same_as, '`same_as` must contain `http://example.org/2`.' );
	}

}
