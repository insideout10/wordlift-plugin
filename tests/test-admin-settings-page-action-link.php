<?php
/**
 * Tests: Settings Page Action Link.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_Settings_Page_Action_Link_Test} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group admin
 */
class Wordlift_Admin_Settings_Page_Action_Link_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Settings_Page_Action_Link} under testing.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Settings_Page_Action_Link $settings_page_action_link The {@link Wordlift_Admin_Settings_Page_Action_Link} instance.
	 */
	private $settings_page_action_link;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->settings_page_action_link = $this->get_wordlift_test()->get_settings_page_action_link();

	}

	/**
	 * Test that the {@link Wordlift_Admin_Settings_Page_Action_Link} adds a link.
	 *
	 * @since 3.11.0
	 */
	public function test() {

		$admin_settings_page = $this->getMockBuilder( 'Wordlift_Admin_Settings_Page' )
		                            ->disableOriginalConstructor()
		                            ->setMethods( array( 'get_menu_slug' ) )
		                            ->getMock();
		$admin_settings_page->method( 'get_menu_slug' )->willReturn( 'menu_slug' );

		$settings_page_action_link = new Wordlift_Admin_Settings_Page_Action_Link( $admin_settings_page );
		$links                     = $settings_page_action_link->action_links( array() );

		$this->assertCount( 1, $links );

	}

}
