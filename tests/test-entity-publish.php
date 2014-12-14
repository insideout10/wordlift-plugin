<?php

/**
 * This file covers tests related to the entity display as setting.
 */

require_once 'functions.php';

class EntityPublishTest extends WP_UnitTestCase {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		wl_configure_wordpress_test();
		rl_empty_dataset();
	}

	function testDisplayAsIndex() {

		global $wp_query, $post;

		$post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_display_as( $post_id, 'index' );

		$wp_query = new WP_Query( 'post_in=' . $post_id );
		$post     = get_post( $post_id );

		// Check that the template is the index.
		$template_name = '/themes/twentyfourteen/index.php';
		$this->assertTrue( substr( get_single_template(), - strlen( $template_name ) ) === $template_name );

	}

	function testDisplayAsPage() {

		global $wp_query, $post;

		$post_id = wl_create_post( '', 'entity-1', 'Entity 1', 'publish', 'entity' );
		wl_set_entity_display_as( $post_id, 'page' );

		$wp_query = new WP_Query( 'post_in=' . $post_id );
		$post     = get_post( $post_id );

		// Check that the template is the single.
		$template_name = '/themes/twentyfourteen/single.php';
		$this->assertTrue( substr( get_single_template(), - strlen( $template_name ) ) === $template_name );

	}

	/**
	 * Test the default *display as* assignment and subsequent assignments.
	 */
	function testDisplayAsDefault() {

		$this->setDisplayAsDefault( 'page' );
		$entity_1 = wl_save_entity( 'http://example.org/entity_1', 'Entity 1', 'http://schema.org/Thing', 'Sample Entity 1' );
		$this->assertEquals( 'page', wl_get_entity_display_as( $entity_1->ID ) );

		wl_set_entity_display_as( $entity_1->ID, 'index' );
		$this->assertEquals( 'index', wl_get_entity_display_as( $entity_1->ID ) );

		$this->setDisplayAsDefault( 'index' );
		$entity_2 = wl_save_entity( 'http://example.org/entity_2', 'Entity 2', 'http://schema.org/Thing', 'Sample Entity 2' );
		$this->assertEquals( 'index', wl_get_entity_display_as( $entity_2->ID ) );

		wl_set_entity_display_as( $entity_1->ID, 'page' );
		$this->assertEquals( 'page', wl_get_entity_display_as( $entity_1->ID ) );

	}

	function setDisplayAsDefault( $value ) {

		wl_configuration_set_entity_display_as( $value );
	}

}