<?php
/**
 * Tests: Tab element render
 *
 * Test the {@link Wordlift_Admin_Tabs_Element} element renderer.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Tabs_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Tabs_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Tabs_Element} to test.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Tabs_Element $tab_element
	 */
	private $tab_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->tab_element = new Wordlift_Admin_Tabs_Element();
	}

	/**
	* Helper function to capture the result of "rendering"
	* done by the tested object.
	*
	* @since 3.11.0
	*
	* @param 	array 	The parameters to pass to the renderer.
	* @return 	string 	The rendered HTML output
	*/
	function get_rendered_output( $args ) {
		// Capture the output.
		ob_start();

		// Render the input element.
		$this->tab_element->render( $args );

		// Get the output.
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Test that the tabs JS is being enqueued.
	 *
	 * @since 3.11.0
	 *
	 */
	function test_js_enqueue() {

		// Render something minimally valid to test just the enqueue.
		$this->get_rendered_output( array() );

		$this->assertTrue( wp_script_is( 'jquery-ui-tabs' ) );
	}

	/**
	 * Test the rendering of the active attribute.
	 *
	 * @since 3.11.0
	 *
	 */
	function test_active_attribute() {

		// Test default.
		$output = $this->get_rendered_output( array() );
		$this->assertTrue( - 1 < strpos( $output, 'data-active="0"' ) );

		// Test value.
		$output = $this->get_rendered_output( array( 'active' => 2 ) );
		$this->assertTrue( - 1 < strpos( $output, 'data-active="2"' ) );

	}

	/**
	 * Helper function to test the callbak functionality, jsut dumps
	 * the number of elements in the $args parameters.
	 *
	 * @param array $args	The arguments registered to be passed to the callback
	 *						panel.
	 *
	 * @since 3.11.0
	 */
	function panel_callback( $args ) {
		echo 'there are ' . count( $args );
	}

	/**
	 * Test the execution of the panel callback with the proper parameters
	 *
	 * @since 3.11.0
	 *
	 */
	function test_callbacks() {

		$output = $this->get_rendered_output( array(
			'tabs' => array(
						array(
						'label' => 'first',
						'callback' => array( $this, 'panel_callback' ),
						'args' => array( 0 => 'bla' ),
					),
						array(
						'label' => 'second',
						'callback' => array( $this, 'panel_callback' ),
						'args' => array( 0 => 'bla', 1 => 'blabla' ),
					),
				),
			)
		);

		// Check first panel.
		$this->assertTrue( - 1 < strpos( $output, 'there are 1' ) );

		// Check second panel.
		$this->assertTrue( - 1 < strpos( $output, 'there are 2' ) );

	}

	/**
	 * Test the proper rendering of tab labels
	 *
	 * @since 3.11.0
	 *
	 */
	function test_labels() {

		$output = $this->get_rendered_output( array(
			'tabs' => array(
						array(
						'label' => 'first',
						'callback' => array( $this, 'panel_callback' ),
						'args' => array( 0 => 'bla' ),
					),
						array(
						'label' => 'html > escaping',
						'callback' => array( $this, 'panel_callback' ),
						'args' => array( 0 => 'bla', 1 => 'blabla' ),
					),
				),
			)
		);

		// Check first label.
		$this->assertTrue( - 1 < strpos( $output, 'first' ) );

		// Check html escaping with second label.
		$this->assertTrue( - 1 < strpos( $output, 'html &gt; escaping' ) );

	}

}
