<?php
/**
 * Tests: Deactivator Feedback Test.
 *
 * Define the test for the {@link Wordlift_Deactivator_Feedback}.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Test the {@link Wordlift_Deactivator_Feedback} class.
 *
 * @since      3.19.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group backend
 */
class Wordlift_Deactivator_Feedback_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Deactivator_Feedback} instance to test.
	 *
	 * @since  3.19.0
	 * @access private
	 * @var Wordlift_Deactivator_Feedback $deactivator_feedback A {@link Wordlift_Deactivator_Feedback} instance.
	 */
	private $deactivator_feedback;


	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.19.0
	 * @access protected
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	protected $configuration_service;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->configuration_service = $this->get_wordlift_test()->get_configuration_service();
		$this->deactivator_feedback  = new Wordlift_Deactivator_Feedback( $this->configuration_service );

	}

	/**
	 * Test that the popup will not be displayed by default.
	 *
	 * @return  void
	 * @version 3.19.0
	 *
	 */
	public function test_default_popup_markup() {
		// Get the popup markup
		ob_start();
		$this->deactivator_feedback->render_feedback_popup();
		$markup = ob_get_clean();

		$this->assertEmpty( $markup );
	}

	/**
	 * Test that the popup will not be displayed
	 * when there are no permissions for that.
	 *
	 * @return  void
	 * @version 3.19.0
	 *
	 */
	public function test_default_popup_markup_on_plugins_page() {
		// Change the currnet page to plugins.
		global $pagenow;
		$pagenow = 'plugins.php';

		// Get the popup markup
		ob_start();
		$this->deactivator_feedback->render_feedback_popup();
		$markup = ob_get_clean();

		$this->assertEmpty( $markup );
	}

	/**
	 * Test that the popup will not be displayed
	 * when it's not plugins.php page, even when we have permissions.
	 *
	 * @return  void
	 * @version 3.19.0
	 *
	 */
	public function test_default_popup_markup_with_added_user_preferences() {
		// Set the preference to yes, so we can test.
		$this->configuration_service->set_diagnostic_preferences( 'yes' );

		// Reset the pagenow.
		global $pagenow;
		$pagenow = 'index.php';

		// Get the popup markup
		ob_start();
		$this->deactivator_feedback->render_feedback_popup();
		$markup = ob_get_clean();

		$this->assertEmpty( $markup );
	}


	/**
	 * Test the feedback popup rendering
	 *
	 * @since 3.19.0
	 */
	public function test_render_feedback_popup_markup() {
		// Add user preferences and change pagenow to allow testing.
		$this->add_preferences();

		$reason_ids = array(
			'TOO_COMPLICATED',
			'NOT_ENOUGH_FEATURES',
			'COSTS_TOO_MUCH',
			'FOUND_ANOTHER_TOOL',
			'SOMETHING_DIDNT_WORK',
			'ANOTHER_REASON',
		);

		// Get the popup markup
		ob_start();
		$this->deactivator_feedback->render_feedback_popup();
		$markup = ob_get_clean();

		foreach ( $reason_ids as $id ) {
			$this->assertRegExp(
				'/\<input\s+type="radio"\s+name="wl-code"\s+class="wl-code"\s+(checked=\'checked\')?\s+value="' . esc_attr( $id ) . '"\s+\/>/',
				$markup
			);
		}

	}

	/**
	 * Add required preferences to show the popup.
	 *
	 * @version 3.19.0
	 */
	public function add_preferences() {
		// Change the currnet page to plugins to allow testing.
		global $pagenow;
		$pagenow = 'plugins.php';

		// Set the preference to yes, so we can test.
		$this->configuration_service->set_diagnostic_preferences( 'yes' );
	}
}
