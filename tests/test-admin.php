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
 * Define the {@link Wordlift_Admin_Test} class.
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
	 * @since 3.19.4
	 *
	 * @param string $default The default editor id.
	 *
	 * @return string
	 */
	public function _wl_default_editor_id( $default ) {

		$this->assertEquals( 'content', $default, 'The default editor id must be `content`.' );

		return 'a_custom_id';
	}

	/**
	 * Check that the editor id in `wlSettings` matches the provided editor id.
	 *
	 * @since 3.19.4
	 *
	 * @param string $editor_id The expected editor id.
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

}
