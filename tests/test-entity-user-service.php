<?php

/**
 * Test the {@link Wordlift_User_Service}.
 *
 * @since 3.1.7
 * @group entity
 */
class UserServiceTest extends Wordlift_Unit_Test_Case {

	/**
	 * Test the User service.
	 *
	 * @since 3.1.7
	 */
	function test_user_service() {

		$users_ids = get_users( array( 'fields' => 'id' ) );

		foreach ( $users_ids as $user_id ) {

			// Get the URI.
			$user_uri = Wordlift_User_Service::get_instance()->get_uri( $user_id );

			// Check that the URI is valid.
			$this->assertNotEmpty( $user_uri );
			$this->assertTrue( false !== $user_uri );

			// Try to change the nicename and check that the URI doesn't change.
			wp_update_user( array(
				'ID' => $user_id,
				'user_nicename' => uniqid( 'nicename-' ),
			) );

			// Get the URI again and check that it didn't change.
			$user_uri_1 = Wordlift_User_Service::get_instance()->get_uri( $user_id );

			$this->assertEquals( $user_uri, $user_uri_1 );

		}

	}

	/**
	 * Test the deny_editor_entity_create function setting user meta correctly
	 * for editors and non editors.
	 *
	 * @since 3.14.0
	 */
	function test_deny_editor_entity_create() {
		$user_service = Wordlift_User_Service::get_instance();

		// Test editor.
		$user = $this->factory->user->create_and_get( array( 'user_login' => 'wluser' ) );
		$user->add_role( 'editor' );

		$user_service->deny_editor_entity_create( $user->ID );
		$meta = get_user_option( Wordlift_User_Service::DENY_ENTITY_CREATE_META_KEY, $user->ID );
		$this->assertNotEmpty( $meta );

		// Test non editor.
		$user = $this->factory->user->create_and_get( array( 'user_login' => 'wluser2' ) );
		$user->add_role( 'administrator' );

		$user_service->deny_editor_entity_create( $user->ID );
		$meta = get_user_option( Wordlift_User_Service::DENY_ENTITY_CREATE_META_KEY, $user->ID );
		$this->assertEmpty( $meta );
	}

	/**
	 * Test the allow_editor_entity_create function clearing user meta correctly
	 * for editors.
	 *
	 * @since 3.14.0
	 */
	function test_allow_editor_entity_create() {
		$user_service = Wordlift_User_Service::get_instance();

		// Test editor.
		$user = $this->factory->user->create_and_get( array( 'user_login' => 'wluser' ) );
		$user->add_role( 'editor' );

		$user_service->deny_editor_entity_create( $user->ID );
		$user_service->allow_editor_entity_create( $user->ID );
		$meta = get_user_option( Wordlift_User_Service::DENY_ENTITY_CREATE_META_KEY, $user->ID );
		$this->assertEmpty( $meta );
	}

	/**
	 * Test the editor_can_create_entities function returning correctly the entity
	 * editing state for the user
	 *
	 * @since 3.14.0
	 */
	function test_editor_can_create_entities() {
		$user_service = Wordlift_User_Service::get_instance();

		$user = $this->factory->user->create_and_get( array( 'user_login' => 'wluser' ) );

		// Test as non editor.
		$this->AssertTrue( $user_service->editor_can_create_entities( $user->ID ) );

		// Test as editor.
		$user->add_role( 'editor' );

		$user_service->deny_editor_entity_create( $user->ID );
		$this->AssertFalse( $user_service->editor_can_create_entities( $user->ID ) );

		$user_service->allow_editor_entity_create( $user->ID );
		$this->AssertTrue( $user_service->editor_can_create_entities( $user->ID ) );
	}

	/**
	 * Test the has_cap function setting correctly the capabilities
	 *
	 * @since 3.14.0
	 */
	function test_has_cap() {
		$caps_to_test = array(
			'edit_wordlift_entity',
			'edit_wordlift_entities',
			'edit_others_wordlift_entities',
			'publish_wordlift_entities',
			'read_private_wordlift_entities',
			'delete_wordlift_entity',
			'delete_wordlift_entities',
			'delete_others_wordlift_entities',
			'delete_published_wordlift_entities',
			'delete_private_wordlift_entities',
		);

		$user_service = Wordlift_User_Service::get_instance();
		$user         = $this->factory->user->create_and_get( array( 'user_login' => 'wluser' ) );

		// No capability change for non editors.
		foreach ( $caps_to_test as $cap ) {
			$allowed_cap = $user_service->has_cap( array(), array( $cap ), array(
				$cap,
				$user->ID,
			) );
			$this->assertEmpty( $allowed_cap );
		}

		// No capability change for editors which are not denied
		$user->add_role( 'editor' );
		foreach ( $caps_to_test as $cap ) {
			$allowed_cap = $user_service->has_cap( array(), array( $cap ), array(
				$cap,
				$user->ID,
			) );
			$this->assertEmpty( $allowed_cap );
		}

		// Denied capability for denied editors.
		$user_service->deny_editor_entity_create( $user->ID );
		foreach ( $caps_to_test as $cap ) {
			$allowed_cap = $user_service->has_cap( array(), array( $cap ), array(
				$cap,
				$user->ID,
			) );

			$this->assertFalse( $allowed_cap[ $cap ] );
		}
	}

	/**
	 * test user cap filter integration
	 *
	 * @since 3.14.0
	 *
	 */
	function test_user_cap_filter() {
		$user_service = Wordlift_User_Service::get_instance();
		$user         = $this->factory->user->create_and_get( array( 'user_login' => 'wluser' ) );
		$user->add_role( 'editor' );
		$user_service->deny_editor_entity_create( $user->ID );

		$this->assertFalse( user_can( $user->ID, 'edit_wordlift_entity' ) );
	}
}
