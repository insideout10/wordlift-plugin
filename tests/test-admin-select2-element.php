<?php
/**
 * Tests: Select2 Element Renderer Test.
 *
 * Test the {@link Wordlift_Admin_Select2_Element} element renderer.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Select2_Element_Test} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Select2_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Select2_Element} element renderer under test.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Select2_Element $select2_element The {@link Wordlift_Admin_Select2_Element} element renderer.
	 */
	private $select2_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$select2_element = new Wordlift_Admin_Select2_Element();
		$select2_element = $this->get_wordlift_test()->get_select2_element();

	}

	/**
	 * Test options output.
	 *
	 * @since 3.11.0
	 */
	public function test_options() {

		// Generate a random number of options.
		$count   = rand( 1, 10 );
		$options = array();
		for ( $i = 1; $i <= $count; $i ++ ) {
			$options[] = "value-$i";
		}

		// Render Select2.
		ob_start();
		$select2_element = new Wordlift_Admin_Select2_Element();
		$select2_element->render( array(
			'value'   => 0,
			'options' => $options,
		) );
		$output = ob_get_clean();

		// Check that there is a matching number of `option` tags.
		$matches = array();
		preg_match_all( '/<option\s+/', $output, $matches );

		$this->assertCount( $count, $matches[0] );

		// Check that the first option is selected.
		$this->assertEquals( 1, preg_match( '/<option\s+value="0"\s+selected=\'selected\'\s+>/', $output ) );

	}

	/**
	 * Test data output.
	 *
	 * @since 3.11.0
	 */
	public function test_data() {

		// Generate a random number of options.
		$count = rand( 1, 10 );
		$data  = array();
		for ( $i = 1; $i <= $count; $i ++ ) {
			$data[] = "value-$i";
		}

		// Render Select2.
		ob_start();
		$select2_element = new Wordlift_Admin_Select2_Element();
		$select2_element->render( array(
			'data' => array(
				'wl-select2-data' => json_encode( $data ),
			),
		) );
		$output = ob_get_clean();

		// Check that each option is present in the `data-wl-select2-data` attribute.
		foreach ( $data as $item ) {
			$matches = array();
			preg_match_all( '/data-wl-select2-data="[^"]+' . $item . '/', $output, $matches );

			$this->assertCount( 1, $matches[0] );
		}

	}

	/**
	 * Test templates output.
	 *
	 * @since 3.11.0
	 */
	public function test_templates() {

		$template_result    = uniqid( 'template-result-' );
		$template_selection = uniqid( 'template-selection-' );

		// Render Select2.
		ob_start();
		$select2_element = new Wordlift_Admin_Select2_Element();
		$select2_element->render( array(
			'data' => array(
				'wl-select2-template-result'    => $template_result,
				'wl-select2-template-selection' => $template_selection,
			)
		) );
		$output = ob_get_clean();

		// Check that the templates have been printed.
		$this->assertTrue( - 1 < strpos( $output, "data-wl-select2-template-result=\"$template_result\"" ) );
		$this->assertTrue( - 1 < strpos( $output, "data-wl-select2-template-selection=\"$template_selection\"" ) );

	}

}
