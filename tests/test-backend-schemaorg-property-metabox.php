<?php
/**
 * Tests: Schema.org Property Metabox.
 *
 * Test the {@link Wordlift_Admin_Schemaorg_Property_Metabox} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

require_once( dirname( __FILE__ ) . '/../src/admin/class-wordlift-admin-schemaorg-property-metabox.php' );

/**
 * Define the Wordlift_Admin_Schemaorg_Property_Metabox_Test class.
 *
 * @since 3.20.0
 * @group backend
 */
class Wordlift_Backend_Schemaorg_Property_Metabox_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin_Schemaorg_Property_Metabox} instance to test.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Admin_Schemaorg_Property_Metabox $schemaorg_property_metabox The {@link Wordlift_Admin_Schemaorg_Property_Metabox} instance.
	 */
	private $schemaorg_property_metabox;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {

		// This needs to happen before `setUp` otherwise the plugin is already loaded.
		set_current_screen( 'edit.php' );

		// Ensure `is_admin` is true.
		$this->assertTrue( is_admin() );

		// Setup the environment.
		parent::setUp();

		// Finally get the class instance to test.
		$this->schemaorg_property_metabox = new Wordlift_Admin_Schemaorg_Property_Metabox( Wordlift_Schemaorg_Property_Service::get_instance() );

	}

	/**
	 * {@inheritdoc}
	 */
	function tearDown() {

		// Reset the current screen.
		set_current_screen( 'front' );

		parent::tearDown();
	}

	//region ## TEST `add_meta_boxes`.

	/**
	 * Test that a metabox isn't added for unsupported custom post types.
	 *
	 * @since 3.20.0
	 */
	public function test_add_meta_boxes_expect_no_metabox() {

		global $wp_meta_boxes;

		$this->schemaorg_property_metabox->add_meta_boxes( 'a_custom_post_type' );

		$this->assertFalse( isset( $wp_meta_boxes['a_custom_post_type']['normal']['default']['wl-schemaorg-property'] ), 'Expect a metabox not to be set.' );

	}

	/**
	 * Test that a metabox isn't added for unsupported custom post types.
	 *
	 * @since 3.20.0
	 */
	public function test_add_meta_boxes_expect_metabox() {

		global $wp_meta_boxes;

		$this->schemaorg_property_metabox->add_meta_boxes( 'entity' );

		$this->assertNotNull( $wp_meta_boxes['entity']['normal']['default']['wl-schemaorg-property'], 'Expect a metabox to be set.' );

	}

	/**
	 * Test that a metabox isn't added for unsupported custom post types.
	 *
	 * @since 3.20.0
	 */
	public function test_add_meta_boxes_expect_metabox_with_custom_post_type() {

		global $wp_meta_boxes;

		add_filter( 'wl_valid_entity_post_types', array( $this, 'add_entity_post_types' ) );

		$this->schemaorg_property_metabox->add_meta_boxes( 'a_custom_post_type' );

		remove_filter( 'wl_valid_entity_post_types', array( $this, 'add_entity_post_types' ) );

		$this->assertNotNull( $wp_meta_boxes['a_custom_post_type']['normal']['default']['wl-schemaorg-property'], 'Expect a metabox to be set.' );

	}

	/**
	 * Hook to `wl_valid_entity_post_types` to add custom post type to the entity types.
	 *
	 * @param array $types An array of custom post types.
	 *
	 * @return array The extended array of custom post types.
	 * @since 3.20.0
	 *
	 */
	public function add_entity_post_types( $types ) {

		return array_merge(
			$types,
			array( 'a_custom_post_type' )
		);
	}

	//endregion

	//region ## TEST `save_post`.
	/**
	 * Test saving a post.
	 *
	 * @since 3.20.0
	 */
	public function test_save_post() {

		// Set up the current user.
		$user_id = $this->factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$this->assertTrue( current_user_can( 'edit_wordlift_entities' ) );

		// Create a post.
		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// Set the nonce and the properties.
		$_POST = array(
			Wordlift_Admin_Schemaorg_Property_Metabox::NONCE_NAME => wp_create_nonce( Wordlift_Admin_Schemaorg_Property_Metabox::ACTION_NAME ),
			// `_wl_prop` is *not* Wordlift_Schemaorg_Property_Service::PREFIX.
			'_wl_prop'                                            => array(
				'testPropertyA' => array(
					'1' => array(
						'type'     => 'Text',
						'language' => 'en',
						'value'    => 'Sample Value',
					),
				),
				'testPropertyB' => array(
					'1' => array(
						'type'     => 'URL',
						'language' => '',
						'value'    => 'http://example.org',
					),
				),
			),
		);

		$this->schemaorg_property_metabox->save_post( $post_id );

		// Reset the user id.
		setup_userdata( null );

		// Check the post meta.
		$post_meta = get_post_meta( $post_id );

		$this->assertCount( 1, $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_type' ], 'Expecting only one value for this meta.' );
		$this->assertEquals( 'Text', $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_type' ][0] );

		$this->assertCount( 1, $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_language' ], 'Expecting only one value for this meta.' );
		$this->assertEquals( 'en', $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_language' ][0] );

		$this->assertCount( 1, $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_value' ], 'Expecting only one value for this meta.' );
		$this->assertEquals( 'Sample Value', $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_value' ][0] );

		$this->assertCount( 1, $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_type' ], 'Expecting only one value for this meta.' );
		$this->assertEquals( 'URL', $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_type' ][0] );

		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_language', $post_meta, 'Expecting meta not to be set.' );

		$this->assertCount( 1, $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_value' ], 'Expecting only one value for this meta.' );
		$this->assertEquals( 'http://example.org', $post_meta[ Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_value' ][0] );

	}

	/**
	 * Check that data is not saved w/o a nonce.
	 *
	 * @since 3.20.0
	 */
	public function test_save_post_without_nonce() {

		// Set up the current user.
		$user_id = $this->factory()->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $user_id );
		$this->assertTrue( current_user_can( 'edit_posts' ) );

		// Create a post.
		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// Set the nonce and the properties.
		$_POST = array(
			// `_wl_prop` is *not* Wordlift_Schemaorg_Property_Service::PREFIX.
			'_wl_prop' => array(
				'testPropertyA' => array(
					'1' => array(
						'type'     => 'Text',
						'language' => 'en',
						'value'    => 'Sample Value',
					),
				),
				'testPropertyB' => array(
					'1' => array(
						'type'     => 'URL',
						'language' => '',
						'value'    => 'http://example.org',
					),
				),
			),
		);

		$this->schemaorg_property_metabox->save_post( $post_id );

		// Reset the user id.
		setup_userdata( null );

		// Check the post meta.
		$post_meta = get_post_meta( $post_id );

		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_type', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_language', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_value', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_type', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_language', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_value', $post_meta, 'Expecting meta not to be set.' );

	}

	/**
	 * Check that data is not saved w/o `can_edit_posts` capability.
	 *
	 * @since 3.20.0
	 */
	public function test_save_post_with_user_cannot_edit_posts() {

		// Set up the current user.
		$user_id = $this->factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		$this->assertFalse( current_user_can( 'edit_posts' ) );

		// Create a post.
		$post_id = $this->factory()->post->create( array( 'post_type' => 'entity' ) );

		// Set the nonce and the properties.
		$_POST = array(
			Wordlift_Admin_Schemaorg_Property_Metabox::NONCE_NAME => wp_create_nonce( Wordlift_Admin_Schemaorg_Property_Metabox::ACTION_NAME ),
			// `_wl_prop` is *not* Wordlift_Schemaorg_Property_Service::PREFIX.
			'_wl_prop'                                            => array(
				'testPropertyA' => array(
					'1' => array(
						'type'     => 'Text',
						'language' => 'en',
						'value'    => 'Sample Value',
					),
				),
				'testPropertyB' => array(
					'1' => array(
						'type'     => 'URL',
						'language' => '',
						'value'    => 'http://example.org',
					),
				),
			),
		);

		$this->schemaorg_property_metabox->save_post( $post_id );

		// Reset the user id.
		setup_userdata( null );

		// Check the post meta.
		$post_meta = get_post_meta( $post_id );

		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_type', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_language', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyA_1_value', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_type', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_language', $post_meta, 'Expecting meta not to be set.' );
		$this->assertArrayNotHasKey( Wordlift_Schemaorg_Property_Service::PREFIX . 'testPropertyB_1_value', $post_meta, 'Expecting meta not to be set.' );

	}
	//endregion

}
