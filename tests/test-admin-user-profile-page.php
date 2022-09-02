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
 * @group admin
 */
class Wordlift_Admin_User_Profile_Page_Test extends Wordlift_Unit_Test_Case {

	/**
	 * A {@link Wordlift_Admin_Author_Element} instance mock.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_Author_Element $admin_person_element
	 */
	private $admin_person_element;

	/**
	 * A {@link Wordlift_Admin_User_Profile_Page} instance mock.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Admin_User_Profile_Page $user_profile_page
	 */
	private $user_profile_page;

	/**
	 * The {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service The {@link Wordlift_User_Service} instance.
	 */
	private $user_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->admin_person_element = $this->getMockBuilder( 'Wordlift_Admin_Author_Element' )
		                                   ->disableOriginalConstructor()
		                                   ->setMethods( array() )
		                                   ->getMock();

		$this->user_service = $this->getMockBuilder( 'Wordlift_Admin_Author_Element' )
		                           ->disableOriginalConstructor()
		                           ->setMethods( array(
			                           'allow_editor_entity_create',
			                           'deny_editor_entity_create',
			                           'editor_can_create_entities',
			                           'get_entity',
			                           'set_entity',
			                           'is_editor',
		                           ) )
		                           ->getMock();

		$this->user_profile_page = new Wordlift_Admin_User_Profile_Page( $this->admin_person_element, $this->user_service );

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

		$target_user = $this->factory()->user->create_and_get( array(
			'role' => 'editor',
		) );

		$current_user_id = $this->factory()->user->create( array(
			'role' => 'editor',
		) );
		wp_set_current_user( $current_user_id );

		$this->user_service->expects( $this->never() )
		                   ->method( 'get_entity' );

		ob_start();
		$this->user_profile_page->edit_user_profile( $target_user );
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEmpty( $result, 'There must be no output.' );

	}

	/**
	 * Test that the element is not printed when the user doesn't have `edit users`
	 * capabilities.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_can_edit_users() {

		$target_user = $this->factory()->user->create_and_get( array(
			'role' => 'editor',
		) );

		$current_user_id = $this->factory()->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $current_user_id );

		if ( function_exists( 'grant_super_admin' ) ) {
			grant_super_admin( $current_user_id );
		}

		$this->user_service->expects( $this->once() )
		                   ->method( 'get_entity' )
		                   ->with( $this->equalTo( $target_user->ID ) )
		                   ->willReturn( '' );

		$this->user_service->expects( $this->once() )
		                   ->method( 'is_editor' )
		                   ->with( $this->equalTo( $target_user->ID ) )
		                   ->willReturn( true );

		$this->user_service->expects( $this->once() )
		                   ->method( 'editor_can_create_entities' )
		                   ->with( $this->equalTo( $target_user->ID ) )
		                   ->willReturn( true );

		ob_start();
		$this->user_profile_page->edit_user_profile( $target_user );
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertNotEmpty( $result, 'There must be some output.' );

	}

	/**
	 * Test that an editor cannot configure the user profile.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_update_cannot_edit_users() {

		// Target user.
		$target_user_id = $this->factory()->user->create( array(
			'role' => 'editor',
		) );

		// Current user.
		$current_user_id = $this->factory()->user->create( array(
			'role' => 'editor',
		) );
		wp_set_current_user( $current_user_id );

		// Set the requested entity id.
		$_POST['wl_person'] = 1;

		$this->user_service->expects( $this->never() )
		                   ->method( 'set_entity' );

		// Update the user profile.
		$this->user_profile_page->edit_user_profile_update( $target_user_id );

	}

	/**
	 * Test that an administrator can configure the user profile.
	 *
	 * @since 3.14.0
	 */
	function test_edit_user_profile_update_can_edit_users() {

		// Target user.
		$target_user_id = $this->factory()->user->create( array(
			'role' => 'editor',
		) );

		// Current user.
		$current_user_id = $this->factory()->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $current_user_id );

		if ( function_exists( 'grant_super_admin' ) ) {
			grant_super_admin( $current_user_id );
		}

		// Set the requested entity id.
		$_POST['wl_person'] = 1;

		$this->user_service->expects( $this->exactly( 2 ) )
		                   ->method( 'set_entity' )
		                   ->with( $this->equalTo( $target_user_id ), $this->equalTo( 1 ) )
		                   ->willReturn( true );

		$this->user_service->expects( $this->once() )
		                   ->method( 'deny_editor_entity_create' )
		                   ->with( $this->equalTo( $target_user_id ) )
		                   ->willReturn( true );

		$_REQUEST['wordlift_user_save_nonce'] = wp_create_nonce( 'wordlift_user_save' );
		// Update the user profile.
		$this->user_profile_page->edit_user_profile_update( $target_user_id );

		$this->user_service->expects( $this->once() )
		                   ->method( 'allow_editor_entity_create' )
		                   ->with( $this->equalTo( $target_user_id ) )
		                   ->willReturn( true );

		$_POST['wl_can_create_entities'] = '1';

		// Update the user profile.
		$this->user_profile_page->edit_user_profile_update( $target_user_id );

	}

}
