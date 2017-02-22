<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 22/02/2017
 * Time: 11:59
 */
class Wordlift_Admin_Publisher_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Publisher_Element} class under testing.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Publisher_Element $publisher_element The {@link Wordlift_Admin_Publisher_Element} class.
	 */
	private $publisher_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->publisher_element = $this->get_wordlift_test()->get_publisher_element();

	}

	public function test_without_publisher() {

		// Generate a random name.
		$name = uniqid( 'name-' );

		// Call the render and catch the output.
		ob_start();
		$this->publisher_element->render( array( 'name' => $name ) );
		$output = ob_get_clean();

		// Check that the name is there.
		$this->assertTrue( - 1 < strpos( $output, 'name="' . $name . '"' ) );

	}

}
