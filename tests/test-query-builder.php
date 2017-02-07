<?php
require_once( 'functions.php' );

/**
 * Test the {@link Wordlift_Query_Builder}.
 *
 * @since 3.1.7
 */
class QueryBuilderTest extends Wordlift_Unit_Test_Case {

	/**
	 * The Log service.
	 *
	 * @since  3.1.7
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// We don't need to check the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();;

		$this->log_service = Wordlift_Log_Service::get_logger( 'QueryBuilderTest' );

		wl_configure_wordpress_test();
		// wl_empty_blog();

	}

	/**
	 * Test the Query builder.
	 *
	 * @since 3.1.7
	 */
	function test_insert() {

		$users_ids = get_users( array( 'fields' => 'id' ) );

		foreach ( $users_ids as $user_id ) {

			// Get the URI.
			$user_uri = Wordlift_User_Service::get_instance()->get_uri( $user_id );
			$user     = get_userdata( $user_id );

			$query = Wordlift_Query_Builder::new_instance()
			                               ->insert()
			                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_TYPE_URI, Wordlift_Query_Builder::SCHEMA_PERSON_URI )
			                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_LABEL_URI, $user->display_name )
			                               ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_GIVEN_NAME_URI, $user->user_firstname )
			                               ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_FAMILY_NAME_URI, $user->user_lastname )
			                               ->build();

			$this->log_service->info( $query );

		}

	}

	function test_delete() {

		$users_ids = get_users( array( 'fields' => 'id' ) );

		foreach ( $users_ids as $user_id ) {

			// Get the URI.
			$user_uri = Wordlift_User_Service::get_instance()->get_uri( $user_id );

			$query = Wordlift_Query_Builder::new_instance()->delete()
			                               ->statement( $user_uri, Wordlift_Query_Builder::RDFS_TYPE_URI, '?o' )
			                               ->build()
			         . Wordlift_Query_Builder::new_instance()->delete()
			                                 ->statement( $user_uri, Wordlift_Query_Builder::RDFS_LABEL_URI, '?o' )
			                                 ->build()
			         . Wordlift_Query_Builder::new_instance()->delete()
			                                 ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_GIVEN_NAME_URI, '?o' )
			                                 ->build()
			         . Wordlift_Query_Builder::new_instance()->delete()
			                                 ->statement( $user_uri, Wordlift_Query_Builder::SCHEMA_FAMILY_NAME_URI, '?o' )
			                                 ->build();

			$this->log_service->info( $query );

		}
	}

}
