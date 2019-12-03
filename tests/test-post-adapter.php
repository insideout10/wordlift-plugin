<?php
/**
 * Test the {@link Wordlift_Post_Adapter} class.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Post_Adapter_Test.
 *
 * @since 3.23.0
 */
class Post_Adapter_Test extends Wordlift_Unit_Test_Case {

	public function test_entity_not_linked_to_a_term() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Entity not Linked to a Term',
		) );

		// Add the same as.
		$same_as = "http://example.org/$entity_id";
		add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a term.
		register_taxonomy( 'wltests_tax', 'post' );
		$term_id = $this->factory->term->create( array(
			'taxonomy' => 'wltests_tax',
		) );

		// We expect the entity permalink.
		$expected = get_permalink( $entity_id );

		// Get the permalink.
		$permalink = Wordlift_Post_Adapter::get_production_permalink( $entity_id );

		$this->assertEquals( $expected, $permalink, 'Permalink should be the entity permalink.' );

	}

	public function test_entity_linked_to_a_term_via_sameas() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Entity not Linked to a Term',
		) );

		// Add the same as.
		$same_as = "http://example.org/$entity_id";
		add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a term.
		register_taxonomy( 'wltests_tax', 'post' );
		$term_id = $this->factory->term->create( array(
			'taxonomy' => 'wltests_tax',
		) );

		// Add same as.
		add_term_meta( $term_id, '_wl_entity_id', $same_as );

		// Get the permalink.
		$permalink = Wordlift_Post_Adapter::get_production_permalink( $entity_id );

		global $wp_version;
		if ( version_compare( $wp_version, '4.5', '>=' ) ) {
			// We expect the term link.
			$expected = get_term_link( $term_id );
			$this->assertEquals( $expected, $permalink, 'Permalink should be the term permalink.' );
		} else {
			// We expect the entity link.
			$expected = get_permalink( $entity_id );
			$this->assertEquals( $expected, $permalink, 'Permalink should be the entity permalink.' );
		}

	}

	public function test_get_labels__no_data() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array() );

		$this->assertEquals( array(), $result, 'Expect an empty array since we provided no data.' );
	}

	public function test_get_labels__label_only() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label' => 'Label',
		) );

		$this->assertEquals( array( 'Label' ), $result, 'Expect an array with `Label`.' );

	}

	public function test_get_labels__label_and_synonyms() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'    => 'Label 1',
			'synonyms' => array( 'Synonym 1', 'Synonym 2' ),
		) );

		$this->assertEquals( array( 'Label 1', 'Synonym 1', 'Synonym 2' ), $result, 'Expect an array with 3 labels.' );

	}

	public function test_get_labels__label_and_synonyms_and_annotations() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'       => 'Label 1',
			'synonyms'    => array( 'Synonym 1', 'Synonym 2' ),
			'annotations' => array(
				'annotation_1' => array( 'text' => 'Annotation 1' ),
				'annotation_2' => array( 'text' => 'Annotation 2' ),
				'annotation_3' => array(),
			),
			'occurrences' => array( 'annotation_1', 'annotation_3', 'annotation_4' ),
		) );

		$this->assertEquals( array(
			'Label 1',
			'Synonym 1',
			'Synonym 2',
			'Annotation 1',
		), $result, 'Expect an array with 4 labels.' );

	}

	public function test_get_labels__no_synonyms() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'       => 'Label 1',
			'annotations' => array(
				'annotation_1' => array( 'text' => 'Annotation 1' ),
				'annotation_2' => array( 'text' => 'Annotation 2' ),
				'annotation_3' => array(),
			),
			'occurrences' => array( 'annotation_1', 'annotation_3', 'annotation_4' ),
		) );

		$this->assertEquals( array(
			'Label 1',
			'Annotation 1',
		), $result, 'Expect an array with 2 labels.' );

	}

}
