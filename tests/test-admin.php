<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 29.08.18
 * Time: 15:03
 */


class Wordlift_Admin_Test extends Wordlift_Unit_Test_Case {

	/**
	 * {@inheritdoc}
	 */
	function tearDown() {
		global $wp_scripts;

		// Remove the registered localize script.
		unset( $wp_scripts->registered['wordlift'] );

		parent::tearDown();
	}


	public function test_enqueue_scripts_editor() {

		$this->do_test( 'editor', 'cloud' );

	}

	public function test_enqueue_scripts_subscriber() {

		$this->do_test( 'subscriber', 'local' );

	}

	private function do_test( $role, $autocomplete_scope ) {

		global $wp_scripts;

		$user_id = $this->factory()->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		set_current_screen( 'edit.php' );

		$post = $this->factory()->post->create_and_get( array( 'post_type' => 'entity' ) );
		setup_postdata( $GLOBALS['post'] = $post );

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
		$this->assertEquals( 1, $json['entityBeingEdited'], '`entityBeingEdited` must be 1 since current post is an entity.' );
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
