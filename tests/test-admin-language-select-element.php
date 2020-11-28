<?php
/**
 * Tests: Language Select Element Test.
 *
 * Test the {@link Wordlift_Admin_Language_Select_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Language_Select_Element_Test} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Language_Select_Element_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Language_Select_Element} element renderer under
	 * test.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Language_Select_Element $language_select_element The {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 */
	private $language_select_element;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->language_select_element = $this->get_wordlift_test()->get_language_select_element();

	}

	/**
	 * Test the default selected value (en) when no value is passed.
	 *
	 * @since 3.11.0
	 */
	public function test_default_value() {

		ob_start();
		$this->language_select_element->render( array() );
		$output = ob_get_clean();

		$matches = array();
		preg_match_all( '/<option\s+/', $output, $matches );

		$this->assertCount( count( Wordlift_Languages::get_languages() ), $matches[0] );

		$matches = array();
		preg_match_all( '/<option\s+value="en"\s+selected=\'selected\'/', $output, $matches );

		$this->assertCount( 1, $matches[0] );

	}

	/**
	 * Test a custom value (it).
	 *
	 * @since 3.11.0
	 */
	public function test_custom_value() {

		ob_start();
		$this->language_select_element->render( array( 'value' => 'it' ) );
		$output = ob_get_clean();

		$matches = array();
		preg_match_all( '/<option\s+/', $output, $matches );

		$this->assertCount( count( Wordlift_Languages::get_languages() ), $matches[0] );

		$matches = array();
		preg_match_all( '/<option\s+value="it"\s+selected=\'selected\'/', $output, $matches );

		$this->assertCount( 1, $matches[0] );

	}

	/**
	 * Test an invalid value (zzz).
	 *
	 * @since 3.11.0
	 */
	public function test_unknown_value() {

		ob_start();
		$this->language_select_element->render( array( 'value' => 'zzz' ) );
		$output = ob_get_clean();

		$matches = array();
		preg_match_all( '/<option\s+/', $output, $matches );

		$this->assertCount( count( Wordlift_Languages::get_languages() ), $matches[0] );

		$matches = array();
		preg_match_all( '/<option\s+value="en"\s+selected=\'selected\'/', $output, $matches );

		$this->assertCount( 1, $matches[0] );

	}

}
