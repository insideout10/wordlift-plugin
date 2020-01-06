<?php
/**
 * Test: Admin.
 *
 * Test the {@link Wordlift_Admin} class.
 *
 * @since 3.19.4
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Admin_Test class.
 *
 * @since 3.19.4
 */
class Wordlift_Admin_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Admin} to test.
	 *
	 * @since 3.19.4
	 * @access private
	 * @var \Wordlift_Admin $admin The {@link Wordlift_Admin} to test.
	 */
	private $admin;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();

		$this->admin = Wordlift_Admin::get_instance();

	}

	/**
	 * {@inheritdoc}
	 */
	public function tearDown() {
		global $wp_scripts;

		unset( $wp_scripts->registered['wordlift'] );

		parent::tearDown();
	}

	/**
	 * Test the default editor id.
	 *
	 * @since 3.19.4
	 */
	public function test_default_editor_id() {

		$this->admin->enqueue_scripts();

		$this->check_editor_id( 'content' );

	}

	/**
	 * Test changing the default editor id.
	 *
	 * @since 3.19.4
	 */
	public function test_default_editor_id_set_to_a_custom_id() {

		add_filter( 'wl_default_editor_id', array( $this, '_wl_default_editor_id' ) );

		$this->admin->enqueue_scripts();

		remove_filter( 'wl_default_editor_id', array( $this, '_wl_default_editor_id' ) );

		$this->check_editor_id( 'a_custom_id' );

	}

	/**
	 * Filter for the `wl_default_editor_id` filter.
	 *
	 * @param string $default The default editor id.
	 *
	 * @return string
	 * @since 3.19.4
	 *
	 */
	public function _wl_default_editor_id( $default ) {

		$this->assertEquals( 'content', $default, 'The default editor id must be `content`.' );

		return 'a_custom_id';
	}

	/**
	 * Check that the editor id in `wlSettings` matches the provided editor id.
	 *
	 * @param string $editor_id The expected editor id.
	 *
	 * @since 3.19.4
	 *
	 */
	private function check_editor_id( $editor_id ) {
		global $wp_scripts;

		$this->assertObjectHasAttribute( 'registered', $wp_scripts, '`registered` must exist in `$wp_scripts`.' );
		$this->assertArrayHasKey( 'wordlift', $wp_scripts->registered, '`wordlift` must exist in `registered`.' );
		$this->assertObjectHasAttribute( 'extra', $wp_scripts->registered['wordlift'], '`extra` must exist in `wordlift`.' );
		$this->assertArrayHasKey( 'data', $wp_scripts->registered['wordlift']->extra, '`data` must exist in `extra`.' );

		$data = $wp_scripts->registered['wordlift']->extra['data'];

		$matches = array();
		preg_match( '/^var wlSettings = (.*);$/', $data, $matches );

		$this->assertCount( 2, $matches, 'The `wlSettings` content must be found, got ' . var_export( $matches, true ) . ' for data ' . $data );

		$json = json_decode( $matches[1], true );

		$this->assertArrayHasKey( 'default_editor_id', $json, '`default_editor_id` must exist.' );
		$this->assertEquals( $editor_id, $json['default_editor_id'], "Editor id doesn't match." );

	}

	/**
	 * Test the `enqueue_scripts` function as an editor in the edit.php screen.
	 *
	 * @since 3.20.0
	 */
	public function test_enqueue_scripts_editor() {

		$this->do_test( 'editor', 'cloud' );

	}

	/**
	 * Test the `enqueue_scripts` function as a subscriber in the edit.php screen.
	 *
	 * @since 3.20.0
	 */
	public function test_enqueue_scripts_subscriber() {

		$this->do_test( 'subscriber', 'local' );

	}

	/**
	 * Function used to perform the tests.
	 *
	 * @param string $role The user's role.
	 * @param string $autocomplete_scope The expected autocomplete scope (`local` or `cloud`).
	 *
	 * @since 3.20.0
	 *
	 */
	private function do_test( $role, $autocomplete_scope ) {

		global $wp_scripts;

		$user_id = $this->factory()->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		set_current_screen( 'edit.php' );

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'entity' ) );
		setup_postdata( $GLOBALS['post'] = $post );

		$this->assertEquals( 'entity', $post->post_type, 'The post type must be entity. It was: ' . $post->post_type );
		$this->assertTrue( Wordlift_Entity_Service::get_instance()->is_entity( $post->ID ), 'Post is not an entity.' );
		$this->assertTrue( is_numeric( get_the_ID() ), 'ID is not numeric.' );

		// Add Schema.org props.
		add_post_meta( $post->ID, Wordlift_Schemaorg_Property_Service::PREFIX . 'testProp_1_type', 'Text' );
		add_post_meta( $post->ID, Wordlift_Schemaorg_Property_Service::PREFIX . 'testProp_1_language', 'en' );
		add_post_meta( $post->ID, Wordlift_Schemaorg_Property_Service::PREFIX . 'testProp_1_value', 'Value' );

		Wordlift_Admin::get_instance()->enqueue_scripts();

		$this->assertObjectHasAttribute( 'registered', $wp_scripts, 'The `registered` attribute must exist.' );
		$this->assertArrayHasKey( 'wordlift', $wp_scripts->registered, 'The `wordlift` key must exist.' );
		$this->assertObjectHasAttribute( 'extra', $wp_scripts->registered['wordlift'], 'The `extra` attribute must exist.' );
		$this->assertArrayHasKey( 'data', $wp_scripts->registered['wordlift']->extra, 'The `data` key must exist.' );

		$data = $wp_scripts->registered['wordlift']->extra['data'];

		$matches = array();
		preg_match( '/^var wlSettings = (.*);$/', $data, $matches );

		$this->assertArrayHasKey( 1, $matches, '`wlSettings = ...;` must exist.' );

		$json = json_decode( $matches[1], true );

		$this->assertArrayHasKey( 'wl_autocomplete_nonce', $json, 'The `wl_autocomplete_nonce` key must exist.' );
		$this->assertArrayHasKey( 'autocomplete_scope', $json, 'The `autocomplete_scope` key must exist.' );
		$this->assertEquals( $autocomplete_scope, $json['autocomplete_scope'], "The default `autocomplete_scope` must be `$autocomplete_scope` when user role `$role`." );
		$this->assertArrayHasKey( 'post_id', $json, 'The `post_id` key must exist.' );
		$this->assertEquals( $post->ID, $json['post_id'], 'Post id must match current post.' );
		$this->assertArrayHasKey( 'entityBeingEdited', $json, 'The `post_id` key must exist.' );
		$this->assertEquals( 1, $json['entityBeingEdited'], '`entityBeingEdited` must be 1 since current post is an entity. Received: ' . var_export( $json['entityBeingEdited'], true ) );
		$this->assertArrayHasKey( 'wl_schemaorg_property_nonce', $json, 'The `wl_schemaorg_property_nonce` key must exist.' );
		$this->assertArrayHasKey( 'properties', $json, 'The `properties` key must exist.' );
		$this->assertCount( 1, $json['properties'], 'There must be one Schema.org property.' );
		$this->assertArrayHasKey( 'testProp', $json['properties'], 'The `testProp` key must exist.' );
		$this->assertCount( 1, $json['properties']['testProp'], 'There must be one Schema.org property instance.' );
		$this->assertArrayHasKey( 'type', $json['properties']['testProp'][0], 'The `type` key must exist.' );
		$this->assertArrayHasKey( 'language', $json['properties']['testProp'][0], 'The `language` key must exist.' );
		$this->assertArrayHasKey( 'value', $json['properties']['testProp'][0], 'The `value` key must exist.' );
		$this->assertEquals( 'Text', $json['properties']['testProp'][0]['type'], 'The `type` value must match.' );
		$this->assertEquals( 'en', $json['properties']['testProp'][0]['language'], 'The `language` value must match.' );
		$this->assertEquals( 'Value', $json['properties']['testProp'][0]['value'], 'The `value` value must match.' );


		unset( $GLOBALS['post'] );
		set_current_screen( 'front' );
		setup_userdata( null );

	}

}
