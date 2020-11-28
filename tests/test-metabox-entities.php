<?php
/**
 * Tests: Metabox Entities Test.
 *
 * Test functions in src/admin/wordlift_admin_meta_box_entities.php.
 *
 * @since 3.19.4
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Metabox_Entities_Test class.
 *
 * @since 3.19.4
 * @group metabox
 */
class Wordlift_Metabox_Entities_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The last default flag from a `wl_post_type_supports_editor` filter call.
	 *
	 * @since 3.19.4
	 * @access private
	 * @var bool $last_default The last default flag from a `wl_post_type_supports_editor` filter call.
	 */
	private $last_default;

	/**
	 * The last `post_type` parameter from a `wl_post_type_supported_editor` filter call.
	 *
	 * @since 3.19.4
	 * @access private
	 * @var string $last_post_type The last `post_type` parameter from a `wl_post_type_supported_editor` filter call.
	 */
	private $last_post_type;

	/**
	 * Test the `wl_post_type_supports_editor` function and filter with the `post` post type.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/847.
	 */
	public function test_post_type_supports_editor() {

		$default_via_wp = post_type_supports( 'post', 'editor' );

		$this->assertTrue( $default_via_wp, 'Post type `post` must support `editor`.' );

		$default_via_wl = wl_post_type_supports_editor( 'post' );

		$this->assertTrue( $default_via_wl, 'Post type `post` must support `editor`.' );

		add_filter( 'wl_post_type_supports_editor', array( $this, '_post_type_supports_editor_return_false' ), 10, 2 );

		$filtered = wl_post_type_supports_editor( 'post' );

		$this->assertFalse( $filtered, 'Post type `post` must not support `editor`.' );
		$this->assertTrue( $this->last_default, 'By default post type `post` must not support `editor`.' );
		$this->assertEquals( 'post', $this->last_post_type, 'Last post type must be `post`.' );

		remove_filter( 'wl_post_type_supports_editor', array( $this, '_post_type_supports_editor_return_false' ) );

	}

	/**
	 * Test the `wl_post_type_supports_editor` function and filter with a custom post type.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/847.
	 */
	public function test_post_type_supports_editor_custom_post_type() {

		register_post_type( 'post_type_847', array(
			'supports' => array( 'editor' => false ),
		) );

		$default_via_wp = post_type_supports( 'post_type_847', 'editor' );

		$this->assertFalse( $default_via_wp, 'Post type `post_type_847` must not support `editor`.' );

		$default_via_wl = wl_post_type_supports_editor( 'post_type_847' );

		$this->assertFalse( $default_via_wl, 'Post type `post_type_847` must not support `editor`.' );

		add_filter( 'wl_post_type_supports_editor', array( $this, '_post_type_supports_editor_return_true' ), 10, 2 );

		$filtered = wl_post_type_supports_editor( 'post_type_847' );

		$this->assertTrue( $filtered, 'Post type `post_type_847` must not support `editor`.' );
		$this->assertFalse( $this->last_default, 'By default post type `post_type_847` must not support `editor`.' );
		$this->assertEquals( 'post_type_847', $this->last_post_type, 'Last post type must be `post_type_847`.' );

		remove_filter( 'wl_post_type_supports_editor', array( $this, '_post_type_supports_editor_return_true' ) );

	}

	/**
	 * Hook to `wl_post_type_supports_editor` filter.
	 *
	 * @since 3.19.4
	 *
	 * @param bool   $default The default flag.
	 * @param string $post_type The post type.
	 *
	 * @return bool The new flag, always false.
	 */
	public function _post_type_supports_editor_return_false( $default, $post_type ) {

		$this->last_default   = $default;
		$this->last_post_type = $post_type;

		return false;
	}

	/**
	 * Hook to `wl_post_type_supports_editor` filter.
	 *
	 * @since 3.19.4
	 *
	 * @param bool   $default The default flag.
	 * @param string $post_type The post type.
	 *
	 * @return bool The new flag, always true.
	 */
	public function _post_type_supports_editor_return_true( $default, $post_type ) {

		$this->last_default   = $default;
		$this->last_post_type = $post_type;

		return true;
	}

}
