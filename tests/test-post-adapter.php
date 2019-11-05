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

		// We expect the term link.
		$expected = get_term_link( $term_id );

		// Get the permalink.
		$permalink = Wordlift_Post_Adapter::get_production_permalink( $entity_id );

		$this->assertEquals( $expected, $permalink, 'Permalink should be the term permalink.' );

	}

}
