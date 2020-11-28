<?php
/**
 * Tests: SEO service.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Seo_Service} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group backend
 */
class Test_Wordlift_Seo_Service extends Wordlift_Unit_Test_Case {

	/**
	 * Test that when there is no SEO configuration, the terms
	 * is exposed by wordpress API as it is initially configured
	 * when the plugin was activated.
	 *
	 * @since 3.11.0
	 */
	function test_no_configuration() {

		// Test with no SEO settings ate all.
		// Use event as representative sample.
		$term = get_term_by( 'name', 'event', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertEquals( $term->name, 'Event' );
		$this->assertEquals( $term->description, 'An event.' );

		// Test with empty settings.
		// Use person as representative sample.
		update_option( 'wl_entity_type_settings', array() );
		$term = get_term_by( 'name', 'person', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertEquals( $term->name, 'Person' );
		$this->assertEquals( $term->description, 'A person (or a music artist).' );

	}

	/**
	 * Test that the SEO configuration is overwriting the name and description
	 * of the entity type for which they were configured.
	 *
	 * @since 3.11.0
	 */
	function test_with_configuration() {

		$term = get_term_by( 'name', 'event', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$test = array(
			'title'       => 'test name',
			'description' => 'test description',
		);

		// set the configuration to apply $test setting to the event type.
		update_option( 'wl_entity_type_settings', array( $term->term_id => $test ) );

		// Now get the term again and compare.
		$term = get_term_by( 'name', 'event', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertEquals( $term->name, 'test name' );
		$this->assertEquals( $term->description, 'test description' );

	}

	/**
	 * Test that the SEO configuration is not overwriting the name and description
	 * of the entity type for which they were configured when in admin context
	 *
	 * he test has to be last as it uses global defines
	 *
	 * @since 3.11.0
	 */
	function test_in_admin_context() {

		set_current_screen( 'edit.php' );

		$term = get_term_by( 'name', 'event', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$test = array(
			'title'       => 'test name',
			'description' => 'test description',
		);

		// set the configuration to apply $test setting to the event type.
		update_option( 'wl_entity_type_settings', array( $term->term_id => $test ) );

		// Now get the term again and compare.
		$term_2 = get_term_by( 'name', 'event', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertEquals( $term->term_id, $term_2->term_id );
		$this->assertEquals( 'Event', $term_2->name );
		$this->assertEquals( 'An event.', $term_2->description );

	}

}
