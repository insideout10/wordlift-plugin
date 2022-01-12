<?php
/**
 * Tests: Publisher Element test.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Publisher_Element_Test } class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Publisher_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the Publisher element w/o any publisher configured or available in WP.
	 *
	 * @since 3.11.0
	 */
	public function test_without_publisher() {

		// Generate a random name.
		$name = uniqid( 'name-' );

		// Call the render and catch the output.
		ob_start();
		$publisher_element = new Wordlift_Admin_Publisher_Element( Wordlift_Publisher_Service::get_instance(), new Wordlift_Admin_Tabs_Element(), new Wordlift_Admin_Select2_Element() );
		$publisher_element->render( array( 'name' => $name ) );
		$output = ob_get_clean();

		// Check that the name is there.
		$this->assertTrue( - 1 < strpos( $output, 'name="' . $name . '"' ) );

		// Check that the data-active flag is set to 1, since there are no publishers
		// available in this WP install.
		$matches = array();
		preg_match_all( '/<div\s+class="wl-tabs-element"\s+data-active="1"/', $output, $matches );

		$this->assertCount( 1, $matches );

	}

	/**
	 * Test the Publisher element with a publisher available but not configured.
	 *
	 * @since 3.11.0
	 */
	public function test_with_a_potential_publisher() {

		// Create an entity for the publisher.
		$post_id = Wordlift_Entity_Service::get_instance()->create( 'John Smith', 'http://schema.org/Person', null, 'publish' );

		// Call the render and catch the output.
		ob_start();
		$publisher_element = new Wordlift_Admin_Publisher_Element( Wordlift_Publisher_Service::get_instance(), new Wordlift_Admin_Tabs_Element(), new Wordlift_Admin_Select2_Element() );
		$publisher_element->render( array() );
		$output = ob_get_clean();

		// Check that the data-active flag is set to 0, since there's one potential
		// publisher.
		$matches = array();
		preg_match_all( '/<div\s+class="wl-tabs-element"\s+data-active="0"/', $output, $matches );

		$this->assertCount( 1, $matches );

		// Check that John Smith is in the options.
		$matches = array();
		preg_match_all( '/data-wl-select2-data=".+id.+' . $post_id . '.+text.+John Smith"/', $output, $matches );

		$this->assertCount( 1, $matches );

	}

	/**
	 * Test the Publisher element with a publisher configured in WP.
	 *
	 * @since 3.11.0
	 */
	public function test_with_configured_publisher() {

		// Create an entity for the publisher.
		$post_id = Wordlift_Entity_Service::get_instance()->create( 'Jane Doe', 'http://schema.org/Person', null, 'publish' );

		// Set the publisher.
		Wordlift_Configuration_Service::get_instance()->set_publisher_id( $post_id );

		// Call the render and catch the output.
		ob_start();
		$publisher_element = new Wordlift_Admin_Publisher_Element( Wordlift_Publisher_Service::get_instance(), new Wordlift_Admin_Tabs_Element(), new Wordlift_Admin_Select2_Element() );
		$publisher_element->render( array() );
		$output = ob_get_clean();

		// Check that the data-active flag is set to 0, since there's one potential
		// publisher.
		$matches = array();
		preg_match_all( '/<div\s+class="wl-tabs-element"\s+data-active="0"/', $output, $matches );

		$this->assertCount( 1, $matches );

		// Check that John Smith is in the options.
		$matches = array();
		preg_match_all( '|<option\+value="' . $post_id . '"\+selected=\'selected\'>Jane Doe</option>|', $output, $matches );

		$this->assertCount( 1, $matches );

	}

}
