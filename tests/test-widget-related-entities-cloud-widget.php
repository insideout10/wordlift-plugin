<?php
/**
 * Tests: Related Entities Cloud Widget class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Related_Entities_Cloud_Widget} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group widget
 */
class Wordlift_Related_Entities_Cloud_Widget_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test widget title being properly sanitized when update happens
	 *
	 * @since 3.11.0
	 *
	 */
	function test_title_sanitize() {

		$widget = new Wordlift_Related_Entities_Cloud_Widget();

		// Just a simple string
		$updated = $widget->update( array( 'title' => 'simple' ), array() );
		$this->assertEquals( 'simple', $updated['title'] );

		// Some html now.
		$updated = $widget->update( array( 'title' => 'si<b>mp</b>le' ), array() );
		$this->assertEquals( 'simple', $updated['title'] );

	}

	/**
	 * Test that the widget output the core tag cloud class.
	 *
	 * @since 3.11.0
	 */
	function test_widget_class() {

		// Create posts to test against
		$post_id = wl_create_post( '', 'post1', 'A post with no entities', 'publish', 'post' );

		$entity_id = wl_create_post( '', 'entity1', 'An Entity', 'publish', 'entity' );
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );

		$widget = new Wordlift_Related_Entities_Cloud_Widget();
		$this->go_to( '?p=' . $post_id );
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',    // Test before widget setting.
				'after_widget'  => 'aw',        // Test after widget setting.
				'before_title'  => 'bt',    // Test before title setting.
				'after_title'   => 'at',        // Test after title setting.
			),
			array( 'title' => '' )
		);
		$output = ob_get_clean();

		// test before and after widget.
		$this->assertEquals( 'bw', substr( $output, 0, 2 ) );
		$this->assertEquals( 'aw', substr( $output, - 2, 2 ) );

		// Test default title with before and after
		$this->assertTrue( false !== strpos( $output, 'btRelated Entitiesat' ) );

		// Test class generation
		$this->assertTrue( false !== strpos( $output, '<div class="tagcloud wl-related-entities-cloud">' ) );

		// test with configured title
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',    // Test before widget setting.
				'after_widget'  => 'aw',        // Test after widget setting.
				'before_title'  => 'bt',    // Test before title setting.
				'after_title'   => 'at',        // Test after title setting.
			),
			array( 'title' => 'bla' )
		);
		$output = ob_get_clean();
		$this->assertTrue( false !== strpos( $output, 'btblaat' ) );

		// Test the widget_title filter is applied to the title
		add_filter( 'widget_title', function ( $t, $i, $idb ) {
			return 'passed';
		}, 10, 3 );
		ob_start();
		$widget->widget(
			array(
				'before_widget' => 'bw',    // Test before widget setting.
				'after_widget'  => 'aw',        // Test after widget setting.
				'before_title'  => 'bt',    // Test before title setting.
				'after_title'   => 'at',        // Test after title setting.
			),
			array( 'title' => 'bla' )
		);
		$output = ob_get_clean();
		$this->assertTrue( false !== strpos( $output, 'btpassedat' ) );

	}

}
