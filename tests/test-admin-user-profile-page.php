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

	/**
	 * Test that the hooks are set.
	 *
	 * @since 3.14.0
	 */
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

	/**
	 * Test that the element is not printed when the user doesn't have `edit users`
	 * capabilities.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_cannot_edit_users() {

		$target_user = $this->factory->user->create_and_get( array(
			'role' => 'editor',
		) );

		$current_user_id = $this->factory->user->create( array(
			'role' => 'editor',
		) );
		wp_set_current_user( $current_user_id );

		ob_clean();
		$this->user_profile_page->edit_user_profile( $target_user );
		$result = ob_get_clean();

		$this->assertEmpty( $result, 'There must be no output.' );

	}

	/**
	 * Test that the element is not printed when the user doesn't have `edit users`
	 * capabilities.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_can_edit_users() {

		$target_user = $this->factory->user->create_and_get( array(
			'role' => 'editor',
		) );

		$current_user_id = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $current_user_id );

		ob_clean();
		$this->user_profile_page->edit_user_profile( $target_user );
		$result = ob_get_clean();

		$this->assertNotEmpty( $result, 'There must be some output.' );

	}

	/**
	 * Test that an editor cannot configure the user profile.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_update_cannot_edit_users() {

		// Target user.
		$target_user_id = $this->factory->user->create( array(
			'role' => 'editor',
		) );

		// Current user.
		$current_user_id = $this->factory->user->create( array(
			'role' => 'editor',
		) );
		wp_set_current_user( $current_user_id );

		// Set the requested entity id.
		$_POST['wl_person'] = 1;

		// Update the user profile.
		$this->user_profile_page->edit_user_profile_update( $target_user_id );

		// Check that the value hasn't been set.
		$meta_value = get_user_meta( $target_user_id, 'wl_person', true );
		$this->assertEmpty( $meta_value, "The `wl_person` meta isn't set when the current user cannot edit users." );

	}

	/**
	 * Test that an administrator can configure the user profile.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_update_can_edit_users() {

		// Target user.
		$target_user_id = $this->factory->user->create( array(
			'role' => 'editor',
		) );

		// Current user.
		$current_user_id = $this->factory->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $current_user_id );

		// Set the requested entity id.
		$_POST['wl_person'] = 1;

		// Update the user profile.
		$this->user_profile_page->edit_user_profile_update( $target_user_id );

		// Check that the value hasn't been set.
		$meta_value = get_user_meta( $target_user_id, 'wl_person', true );
		$this->assertEquals( 1, $meta_value, "The `wl_person` meta is set when the current user can edit users." );

	}

}
