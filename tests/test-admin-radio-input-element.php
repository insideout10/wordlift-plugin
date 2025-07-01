<?php

/**
 * Tests: Admin Input Element Test.
 *
 * Define the {@link Wordlift_Admin_Radio_Input_Element_Test} test class.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Radio_Input_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Radio_Input_Element} to test.
	 *
	 * @since  3.19.0
	 * @access private
	 * @var \Wordlift_Admin_Radio_Input_Element $radio_element
	 */
	private $radio_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->radio_element = new Wordlift_Admin_Radio_Input_Element();
	}

	/**
	 * Helper function to capture the result of "rendering"
	 * done by the tested object.
	 *
	 * @param array    The parameters to pass to the renderer.
	 *
	 * @return    string    The rendered HTML output
	 * @since 3.19.0
	 *
	 */
	function get_rendered_output( $args ) {
		// Capture the output.
		ob_start();

		// Render the input element.
		$this->radio_element->render( $args );

		// Get the output.
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Test a random css class and value.
	 *
	 * @since 3.19.0
	 */
	public function test_attributes() {

		// A random css class & id.
		$css_class = uniqid( 'css-class-' );
		$id        = uniqid( 'id-' );

		// Render the input element.
		$output = $this->get_rendered_output( array(
			'css_class' => $css_class,
			'id'        => $id,
			'name'      => 'test',
		) );

		// Check that the css class has been set.
		$this->assertTrue( - 1 < strpos( $output, 'class="' . $css_class . '"' ), "Can't find $css_class in \n$output" );
		$this->assertTrue( - 1 < strpos( $output, 'id="' . $id . '"' ) );
		$this->assertTrue( - 1 < strpos( $output, 'name="test"' ), 'Expecting name="test", got ' . $output );

	}

	/**
	 * Test that the value is properly set.
	 *
	 * @since 3.19.0
	 */
	public function test_values() {

		// Render the input element.
		$output = $this->get_rendered_output( array(
			'value' => 'yes',
			'name'  => 'test',
		) );

		$this->assertRegExp( '/value="yes"\s+checked=\'checked\'/', $output );
		$this->assertRegExp( '/value="no"\s+\/>/', $output );

	}

	/**
	 * Test the field description
	 *
	 * @since  3.19.0
	 */
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
