<?php
/**
 * Tests: Related Entities Cloud Widget class.
 *
 * @since   3.11.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Related_Entities_Cloud_Widget} class.
 *
 * @since   3.11.0
 * @package Wordlift
 */
class Wordlift_Related_Entities_Cloud_Widget_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test widget title being properly sanitized when update happens
	 *
	 * @since 3.11.0
	 *
	 */
	function test_title_sanitization() {

		$widget = new Wordlift_Related_Entities_Cloud_Widget();

		// Just a simple string
		$updated = $widget->update( array( 'title' => 'simple' ), array() );
		$this->assertEquals( 'simple', $updated['title'] );

		// Some html now.
		$updated = $widget->update( array( 'title' => 'si<b>mp</b>le' ), array() );
		$this->assertEquals( 'simple', $updated['title'] );

	}

	/**
	 * Test that the get_related_entities_tags method of the widget
	 * returns the correct tags when in various contexts.
	 *
	 * @since 3.11.0
	 */
	function test_get_related_entities_tags() {

		// Create posts to test against
		$post_id   = wl_create_post( '', 'post1', 'A post with no entities', 'publish', 'post' );
		$post_id1   = wl_create_post( '', 'post2', 'A post with an entity', 'publish', 'post' );
		$post_id2   = wl_create_post( '', 'post3', 'A post with two entities', 'publish', 'post' );

		$entity_id = wl_create_post( '', 'entity1', 'An Entity', 'publish', 'entity' );
		$entity_id2 = wl_create_post( '', 'entity2', 'Another Entity', 'publish', 'entity' );
		$entity_id3 = wl_create_post( '', 'entity3', 'Entity to Entity', 'publish', 'entity' );

		wl_core_add_relation_instance( $post_id1, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id2, WL_WHAT_RELATION, $entity_id2 );
		wl_core_add_relation_instance( $entity_id2, WL_WHAT_RELATION, $entity_id3 );

		$widget = new Wordlift_Related_Entities_Cloud_Widget();

		// test post with no connected entities at all
		$this->go_to( '?p=' . $post_id );
		$tags = $widget->get_related_entities_tags();
		$this->assertEquals( 0, count( $tags ) );

		// test post with one connected entity with weight 2
		$this->go_to( '?p=' . $post_id1 );
		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$tags = $widget->get_related_entities_tags();
		$this->assertEquals( 1, count( $tags ) );
		$this->assertEquals( 'http://example.org/?entity=entity1', $tags[0]->link );
		$this->assertEquals( 'An Entity', $tags[0]->slug );
		$this->assertEquals( 'An Entity', $tags[0]->name );
		$this->assertEquals( $entity_id, $tags[0]->id );
		$this->assertEquals( 2, $tags[0]->count );

		// Test post with two connected entities.
		$this->go_to( '?p=' . $post_id2 );
		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$tags = $widget->get_related_entities_tags();
		$this->assertEquals( 2, count( $tags ) );

		// Assume order is order of creation of entities.
		$this->assertEquals( 'http://example.org/?entity=entity1', $tags[0]->link );
		$this->assertEquals( 'An Entity', $tags[0]->slug );
		$this->assertEquals( 'An Entity', $tags[0]->name );
		$this->assertEquals( $entity_id, $tags[0]->id );
		$this->assertEquals( 2, $tags[0]->count );

		$this->assertEquals( 'http://example.org/?entity=entity2', $tags[1]->link );
		$this->assertEquals( 'Another Entity', $tags[1]->slug );
		$this->assertEquals( 'Another Entity', $tags[1]->name );
		$this->assertEquals( $entity_id2, $tags[1]->id );
		$this->assertEquals( 2, $tags[1]->count );

		// Test entity page.
		$this->go_to( '?entity=entity2' );
		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$tags = $widget->get_related_entities_tags();
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
		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$tags = $widget->get_related_entities_tags();
		$this->assertEquals( 0, count( $tags ) );

	}

	/**
	 * Test that the widget output the core tag cloud class.
	 *
	 * @since 3.11.0
	 */
	function test_widget_class() {
		// Create posts to test against
		$post_id   = wl_create_post( '', 'post1', 'A post with no entities', 'publish', 'post' );

		$entity_id = wl_create_post( '', 'entity1', 'An Entity', 'publish', 'entity' );
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );

		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$this->go_to( '?p=' . $post_id );
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',	// Test before widget setting.
				'after_widget' => 'aw',		// Test after widget setting.
				'before_title' => 'bt',	// Test before title setting.
				'after_title' => 'at',		// Test after title setting.
				),
			array( 'title' => '' )
		);
		$output = ob_get_clean();

		// test before and after widget.
		$this->assertEquals( 'bw', substr( $output, 0, 2 ) );
		$this->assertEquals( 'aw', substr( $output, -2, 2 ) );

		// Test default title with before and after
		$this->assertTrue( false !== strpos( $output, 'btRelated Entitiesat' ) );

		// Test class generation
		$this->assertTrue( false !== strpos( $output, '<div class="tagcloud wl-related-entities-cloud">' ) );

		// test with configured title
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',	// Test before widget setting.
				'after_widget' => 'aw',		// Test after widget setting.
				'before_title' => 'bt',	// Test before title setting.
				'after_title' => 'at',		// Test after title setting.
				),
			array( 'title' => 'bla' )
		);
		$output = ob_get_clean();
		$this->assertTrue( false !== strpos( $output, 'btblaat' ) );

		// Test the widget_title filter is applied to the title
		add_filter( 'widget_title', function ( $t, $i, $idb ) {return 'passed';}, 10, 3 );
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',	// Test before widget setting.
				'after_widget' => 'aw',		// Test after widget setting.
				'before_title' => 'bt',	// Test before title setting.
				'after_title' => 'at',		// Test after title setting.
				),
			array( 'title' => 'bla' )
		);
		$output = ob_get_clean();
		$this->assertTrue( false !== strpos( $output, 'btpassedat' ) );
	}
}
