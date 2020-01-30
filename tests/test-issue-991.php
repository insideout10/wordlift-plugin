<?php
/**
 * Tests: Issue 991.
 *
 * @since      3.23.4
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Issue_991} class.
 *
 * @since      3.23.4
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Issue_991 extends Wordlift_Unit_Test_Case {

	/**
	 * This test checks that WLP is setting `thing` as entity type when the provided entity type URI doesn't match
	 * any entity type.
	 *
	 * @since 3.23.4
	 */
	public function test_991() {

		// Create an entity.
		$post_id = $this->factory->post->create( array(
			'post_title'  => 'Test Issue 991',
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		// The thing entity type is assigned automatically.
		$types_1 = wp_get_post_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertCount( 1, $types_1, 'Expect 1 term.' );
		$this->assertEquals( $types_1[0]->slug, 'thing', 'Expect the term to be thing.' );

		// Remove the entity types.
		wp_set_post_terms( $post_id, null, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$types_2 = wp_get_post_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		/*
		 * Since 3.23.6 we always set 'thing' or 'article' if not terms are set.
		 *
		 * @since 3.23.6
		 */
		$this->assertCount( 1, $types_2, "Expect one term set." );

		// Try to add the entity type using `wl-other` as type uri as the JS client would do.
		$this->entity_type_service->set( $post_id, 'wl-other' );

		$types_3 = wp_get_post_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Check that thing has been added (because wl-other doesn't match any entity type).
		$this->assertCount( 1, $types_3, 'Expect 1 term.' );
		$this->assertEquals( $types_3[0]->slug, 'thing', 'Expect the term to be thing.' );

	}

}
