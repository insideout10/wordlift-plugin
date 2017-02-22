<?php
/**
 * Tests: Admin Input Element Test.
 *
 * Test the {@link Wordlift_Admin_Input_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Input_Element_Test} test class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Admin_Input_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Input_Element} to test.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Input_Element $input_element
	 */
	private $input_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->input_element = $this->get_wordlift_test()->get_input_element();
	}

	/**
	 * Test a random css class and value.
	 *
	 * @since 3.11.0
	 */
	public function test_custom_values() {

		// A random css class.
		$css_class = uniqid( 'css-class-' );
		$value     = uniqid( 'value-' );

		// Capture the output.
		ob_start();

		// Render the input element.
		$this->input_element->render( array(
			'css_class' => $css_class,
			'value'     => $value,
		) );

		// Get the output.
		$output = ob_get_clean();

		// Check that the css class has been set.
		$this->assertTrue( - 1 < strpos( $output, ' class="' . $css_class . '"' ) );
		$this->assertTrue( - 1 < strpos( $output, ' value="' . $value . '"' ) );

	}

	/**
	 * Test readonly.
	 *
	 * @since 3.11.0
	 */
	public function test_readonly() {

		// Capture the output.
		ob_start();

		// Render the input element.
		$this->input_element->render( array() );

		// Get the output.
		$output = ob_get_clean();

		// Check that the css class has been set.
		$this->assertFalse( strpos( $output, ' readonly="' ) );

		// Capture the output.
		ob_start();

		// Render the input element.
		$this->input_element->render( array( 'readonly' => true ) );

		// Get the output.
		$output = ob_get_clean();

		$this->assertTrue( - 1 < strpos( $output, ' readonly="' ) );

	}

}
