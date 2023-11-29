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
 * @group admin
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

		$this->input_element = new Wordlift_Admin_Input_Element();
	}

	/**
	 * Helper function to capture the result of "rendering"
	 * done by the tested object.
	 *
	 * @param array    The parameters to pass to the renderer.
	 *
	 * @return    string    The rendered HTML output
	 * @since 3.11.0
	 *
	 */
	function get_rendered_output( $args ) {

		// Capture the output.
		ob_start();

		// Render the input element.
		$this->input_element->render( $args );

		// Get the output.
		$output = ob_get_contents();

		// Close the buffer.
		ob_get_clean();

		return $output;
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

		// Render the input element.
		$output = $this->get_rendered_output( array(
			'css_class' => $css_class,
			'value'     => $value,
			'name'      => 'test',
		) );

		// Check that the css class has been set.
		$this->assertTrue( - 1 < strpos( $output, 'class="' . $css_class . '"' ) );
		$this->assertTrue( - 1 < strpos( $output, 'value="' . $value . '"' ) );

	}

	/**
	 * Test readonly.
	 *
	 * @since 3.11.0
	 */
	public function test_readonly() {

		// Render the input element.
		$output = $this->get_rendered_output( array( 'name' => 'test' ) );

		// Check that the css class has been set.
		$this->assertFalse( strpos( $output, ' readonly="' ) );

		// Render the input element.
		$output = $this->get_rendered_output( array(
			'readonly' => true,
			'name'     => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, ' readonly="' ), "`readonly` expected, instead got `$output`" );

	}

	function test_description() {

		// Test no description by default.
		$output = $this->get_rendered_output( array( 'name' => 'test' ) );

		$this->assertFalse( strpos( $output, '<p>' ) );

		// Test no description with empty string.
		$output = $this->get_rendered_output( array(
			'description' => '',
			'name'        => 'test',
		) );

		$this->assertFalse( strpos( $output, '<p>' ) );

		// Test simple text description.
		$output = $this->get_rendered_output( array(
			'description' => 'simple test',
			'name'        => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, 'simple test' ) );

		// Test description requiring html escaping.
		$output = $this->get_rendered_output( array(
			'description' => 'simple & test',
			'name'        => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, 'simple &amp; test' ) );

		// Test description with a link.
		$output = $this->get_rendered_output( array(
			'description' => 'some <a href="">text</a> and more',
			'name'        => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, 'some <a href="">text</a> and more' ) );

		// Test non allowed html elements removed from description.
		$output = $this->get_rendered_output( array(
			'description' => 'some <a href="">text<span>oops</span></a> and more',
			'name'        => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, 'some <a href="">textoops</a> and more' ) );

		// Test non allowed attributes removed from description.
		$output = $this->get_rendered_output( array(
			'description' => 'some <a href="" onclick="">text</a> and more',
			'name'        => 'test',
		) );

		$this->assertTrue( - 1 < strpos( $output, 'some <a href="">text</a> and more' ) );

	}

}
