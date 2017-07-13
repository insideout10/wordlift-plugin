<?php
/**
 * Tests: Admin User Profile Page Test.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Admin_User_Profile_Page_Test} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Admin_User_Profile_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var \Wordlift_Admin_Person_Element $admin_person_element
	 */
	private $admin_person_element;

	/**
	 * @var \Wordlift_Admin_User_Profile_Page $user_profile_page
	 */
	private $user_profile_page;

	function setUp() {
		parent::setUp();

		$this->admin_person_element = $this->getMockBuilder( Wordlift_Admin_Person_Element::class )
		                                   ->disableOriginalConstructor()
		                                   ->setMethods( array() )
		                                   ->getMock();

		$this->user_profile_page = new Wordlift_Admin_User_Profile_Page( $this->admin_person_element );

	}

	function test_hooks() {

		$this->assertEquals( 10, has_filter( 'show_user_profile', array(
			$this->user_profile_page,
			'edit_user_profile',
		) ) );
		$this->assertEquals( 10, has_filter( 'edit_user_profile', array(
			$this->user_profile_page,
			'edit_user_profile',
		) ) );
		$this->assertEquals( 10, has_filter( 'edit_user_profile_update', array(
			$this->user_profile_page,
			'edit_user_profile_update',
		) ) );

	}

	function test_edit_user_profile_cannot_edit_users() {

		$user_id = $this->factory->user->create( array(
			'role' => 'editor',
		) );
		wp_set_current_user( $user_id );

		ob_clean();
		$this->user_profile_page->edit_user_profile()
		$result = ob_get_clean();

	}

}
