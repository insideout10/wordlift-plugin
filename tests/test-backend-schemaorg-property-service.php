<?php
/**
 * Tests: Schemaorg Property Service Test.
 *
 * Test the {@link Wordlift_Schemaorg_Property_Service} class.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Schemaorg_Property_Service_Test class.
 *
 * @since 3.20.0
 * @group backend
 */
class Wordlift_Schemaorg_Property_Service_Test extends Wordlift_Unit_Test_Case {

	/**
	 * Test the `get_all` function.
	 *
	 * @since 3.20.0
	 */
	public function test_get_all() {

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );

		$prefix = Wordlift_Schemaorg_Property_Service::PREFIX;

		add_post_meta( $post_id, "{$prefix}propA_1_type", 'Text' );
		add_post_meta( $post_id, "{$prefix}propA_1_language", 'en' );
		add_post_meta( $post_id, "{$prefix}propA_1_value", 'Value 1' );
		add_post_meta( $post_id, "{$prefix}propA_2_type", 'Text' );
		add_post_meta( $post_id, "{$prefix}propA_2_language", 'en' );
		add_post_meta( $post_id, "{$prefix}propA_2_value", 'Value 2' );
		add_post_meta( $post_id, "{$prefix}propB_1_type", 'URL' );
		add_post_meta( $post_id, "{$prefix}propB_1_value", 'http://example.org/' );

		$props = Wordlift_Schemaorg_Property_Service::get_instance()
		                                            ->get_all( $post_id );

		$this->assertCount( 2, $props, 'There must be 2 properties.' );

		$this->assertArrayHasKey( 'propA', $props, 'The properties must contain `propA`.' );
		$this->assertCount( 2, $props['propA'], 'There must be 2 property instances.' );
		$this->assertEquals( array(
			'type'     => 'Text',
			'language' => 'en',
			'value'    => 'Value 1',
		), $props['propA'][0], 'The value must match.' );
		$this->assertEquals( array(
			'type'     => 'Text',
			'language' => 'en',
			'value'    => 'Value 2',
		), $props['propA'][1], 'The value must match.' );

		$this->assertArrayHasKey( 'propB', $props, 'The properties must contain `propB`.' );
		$this->assertCount( 1, $props['propB'], 'There must be 1 property instance.' );
		$this->assertEquals( array(
			'type'  => 'URL',
			'value' => 'http://example.org/',
		), $props['propB'][0], 'The value must match.' );

	}

	/**
	 * Test the `get_keys` function.
	 *
	 * @since 3.20.0
	 */
	public function test_get_keys() {

		$post_id = $this->factory()->post->create( array(
			'post_type' => 'entity',
		) );

		$prefix = Wordlift_Schemaorg_Property_Service::PREFIX;

		add_post_meta( $post_id, "{$prefix}propA_1_type", 'Text' );
		add_post_meta( $post_id, "{$prefix}propA_1_language", 'en' );
		add_post_meta( $post_id, "{$prefix}propA_1_value", 'Value 1' );
		add_post_meta( $post_id, "{$prefix}propA_2_type", 'Text' );
		add_post_meta( $post_id, "{$prefix}propA_2_language", 'en' );
		add_post_meta( $post_id, "{$prefix}propA_2_value", 'Value 2' );
		add_post_meta( $post_id, "{$prefix}propB_1_type", 'URL' );
		add_post_meta( $post_id, "{$prefix}propB_1_value", 'http://example.org/' );

		$keys = Wordlift_Schemaorg_Property_Service::get_instance()
		                                           ->get_keys( $post_id );

		$this->assertCount( 8, $keys, 'There must be 8 keys.' );

		$this->assertContains( "{$prefix}propA_1_type", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propA_1_language", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propA_1_value", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propA_2_type", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propA_2_language", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propA_2_value", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propB_1_type", $keys, 'The key must exist.' );
		$this->assertContains( "{$prefix}propB_1_value", $keys, 'The key must exist.' );

	}

}
