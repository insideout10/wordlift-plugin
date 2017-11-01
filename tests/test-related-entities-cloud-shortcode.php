<?php
/**
 * Tests: Related Entities Cloud Widget class.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Related_Entities_Cloud_Shortcode} class.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Related_Entities_Cloud_Shortcode_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test that the get_related_entities_tags method of the shortcode
	 * returns the correct tags when in various contexts.
	 *
	 * @since 3.12.0
	 */
	function test_get_related_entities_tags() {

		// Create posts to test against
		$post_id  = wl_create_post( '', 'post1', 'A post with no entities', 'publish', 'post' );
		$post_id1 = wl_create_post( '', 'post2', 'A post with an entity', 'publish', 'post' );
		$post_id2 = wl_create_post( '', 'post3', 'A post with two entities', 'publish', 'post' );

		$entity_id  = wl_create_post( '', 'entity1', 'An Entity', 'publish', 'entity' );
		$entity_id2 = wl_create_post( '', 'entity2', 'Another Entity', 'publish', 'entity' );
		$entity_id3 = wl_create_post( '', 'entity3', 'Entity to Entity', 'publish', 'entity' );

		wl_core_add_relation_instance( $post_id1, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id2 );
		wl_core_add_relation_instance( $entity_id2, WL_WHAT_RELATION, $entity_id3 );

		$shortcode = new Wordlift_Related_Entities_Cloud_Shortcode( Wordlift_Relation_Service::get_instance() );

		// test post with no connected entities at all
		$this->go_to( '?p=' . $post_id );
		$tags = $shortcode->get_related_entities_tags();
		$this->assertEquals( 0, count( $tags ) );

		// test post with one connected entity with weight 2
		$this->go_to( '?p=' . $post_id1 );
		$widget = new Wordlift_Related_Entities_Cloud_Shortcode( Wordlift_Relation_Service::get_instance() );
		$tags   = $shortcode->get_related_entities_tags();
		$this->assertEquals( 1, count( $tags ) );
		$this->assertEquals( 'http://example.org/?entity=entity1', $tags[0]->link );
		$this->assertEquals( 'An Entity', $tags[0]->slug );
		$this->assertEquals( 'An Entity', $tags[0]->name );
		$this->assertEquals( $entity_id, $tags[0]->id );
		$this->assertEquals( 2, $tags[0]->count );

		// Test post with two connected entities.
		$this->go_to( '?p=' . $post_id2 );
		$shortcode = new Wordlift_Related_Entities_Cloud_Shortcode( Wordlift_Relation_Service::get_instance() );
		$tags      = $shortcode->get_related_entities_tags();
		$this->assertEquals( 2, count( $tags ) );

		// Can't trust order, so have to compare results to expectations in a harder way.
		$matched_all = true;
		$results = array( 0 => array(
				'slug' => 'An Entity',
				'name' => 'An Entity',
				'url' => 'http://example.org/?entity=entity1',
				'count' => 2,
			),
			1 => array(
				'slug' => 'Another Entity',
				'name' => 'Another Entity',
				'url' => 'http://example.org/?entity=entity2',
				'count' => 2,
			),
		);

		foreach ( array( 0 => $entity_id, 1 => $entity_id2 ) as $res_key=>$id ) {
			// Weak compare because the entity ids are proper ints while from the API
			// we might get strings.
			if ( $id == $tags[0]->id ) {
				$key = 0;
			} elseif ( $id == $tags[1]->id ) {
				$key = 1;
			} else {
				$matched_all = false;
				break;
			}

			$this->assertEquals( $results[ $res_key ]['url'], $tags[ $key ]->link );
			$this->assertEquals( $results[ $res_key ]['slug'], $tags[ $key ]->slug );
			$this->assertEquals( $results[ $res_key ]['name'], $tags[ $key ]->name );
			$this->assertEquals( $results[ $res_key ]['count'], $tags[ $key ]->count );
		}

		$this->assertTrue( $matched_all );

		// Test entity page.
		$this->go_to( '?entity=entity2' );
		$shortcode = new Wordlift_Related_Entities_Cloud_Shortcode( Wordlift_Relation_Service::get_instance() );
		$tags      = $shortcode->get_related_entities_tags();
		$this->assertEquals( 1, count( $tags ) );

		// Assume order is order of creation of entities.
		$this->assertEquals( 'http://example.org/?entity=entity3', $tags[0]->link );
		$this->assertEquals( 'Entity to Entity', $tags[0]->slug );
		$this->assertEquals( 'Entity to Entity', $tags[0]->name );
		$this->assertEquals( $entity_id3, $tags[0]->id );
		// The entity3 has no related entities.
		$this->assertEquals( 0, $tags[0]->count );

		// Test in home page context.
		$this->go_to( '/' );
		$shortcode = new Wordlift_Related_Entities_Cloud_Shortcode( Wordlift_Relation_Service::get_instance() );
		$tags      = $shortcode->get_related_entities_tags();
		$this->assertEquals( 0, count( $tags ) );

	}

	/**
	 * Test that the shortcode is registered and basic sanity
	 *
	 * @since 3.12.0
	 */
	function test_shortcode() {
		// Create posts to test against
		$post_id  = wl_create_post( '', 'post1', 'A post with no entities', 'publish', 'post' );
		$post_id1 = wl_create_post( '', 'post2', 'A post with an entity', 'publish', 'post' );
		$post_id2 = wl_create_post( '', 'post3', 'A post with two entities', 'publish', 'post' );

		$entity_id  = wl_create_post( '', 'entity1', 'An Entity', 'publish', 'entity' );
		$entity_id2 = wl_create_post( '', 'entity2', 'Another Entity', 'publish', 'entity' );
		$entity_id3 = wl_create_post( '', 'entity3', 'Entity to Entity', 'publish', 'entity' );

		wl_core_add_relation_instance( $post_id1, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id2 );
		wl_core_add_relation_instance( $entity_id2, WL_WHAT_RELATION, $entity_id3 );

		// test post with no connected entities at all
		$this->go_to( '?p=' . $post_id );
		$output = do_shortcode( '[wl_cloud]' );
		$this->assertEquals( '', $output );

		// test post with a connected entity
		$this->go_to( '?p=' . $post_id1 );
		$output = do_shortcode( '[wl_cloud]' );
		$this->assertNotEquals( '', $output );
	}

}
