<?php
/**
 * Tests: Faceted Search Shortcode Test.
 *
 * @since   3.0.0
 * @package Wordlift
 */

use Wordlift\Cache\Ttl_Cache;

/**
 * Class FacetedSearchShortcodeTest
 * Extend WP_Ajax_UnitTestCase
 * See https://codesymphony.co/wp-ajax-plugin-unit-testing/
 *
 * @group   ajax
 *
 * @since   3.0.0
 * @package Wordlift
 */
class FacetedSearchShortcodeTest extends Wordlift_Ajax_Unit_Test_Case {

	public function testDataSelectionWithoutAnEntityId() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		$this->setExpectedException( 'WPAjaxDieStopException', 'No post_id given' );
		$this->_handleRest( '/wordlift/v1/faceted-search' );
	}

	public function testDataSelectionForAMissingEntity() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		$_GET['post_id'] = 1000000;
		$this->setExpectedException( 'WPAjaxDieStopException', 'No valid post_id given' );
		$this->_handleRest( '/wordlift/v1/faceted-search' );
	}

	public function testDataSelectionForAPostWithoutRelatedEntities() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		$post_1_id       = wl_create_post( '', 'post1', 'A post', 'publish', 'post' );
		$_GET['post_id'] = $post_1_id;
		$this->setExpectedException( 'WPAjaxDieStopException', 'No entities available' );
		$this->_handleRest( '/wordlift/v1/faceted-search' );
	}

	public function testPostsSelectionWithoutFilters() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		// Create 2 posts and 2 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
		$post_1_id   = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post', 'publish' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $entity_1_id;

		try {
			$this->_handleRest( '/wordlift/v1/faceted-search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'object', $response );
		$this->assertInternalType( 'array', $response->posts );
		$this->assertInternalType( 'array', $response->entities );
		$this->assertCount( 2, $response->posts );
		$this->assertEquals( 'post', $response->posts[0]->post_type );
		$this->assertEquals( 'post', $response->posts[1]->post_type );
		$this->assertEquals( get_post_permalink( $response->posts[0]->ID ), $response->posts[0]->permalink );
		$this->assertEquals( get_post_permalink( $response->posts[1]->ID ), $response->posts[1]->permalink );

		$post_ids = array( $response->posts[0]->ID, $response->posts[1]->ID );
		$this->assertContains( $post_1_id, $post_ids );
		$this->assertContains( $post_2_id, $post_ids );

	}

	public function testPostsSelectionWithoutFiltersOnPostDrafts() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		// Create 2 posts and 2 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
		$post_1_id   = wl_create_post( '', 'post1', 'A post' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $entity_1_id;

		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		try {
			$this->_handleRest( '/wordlift/v1/faceted-search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'object', $response );
		$this->assertCount( 0, $response->posts, "The response doesn't match: " . var_export( $response->posts, true ) );
	}

	public function testFacetsSelectionLimit() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		// Create 2 posts and 2 entities
		$post_1_id   = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post', 'publish' );
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'publish', 'entity' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $post_1_id;
		$_GET['limit']   = 1;

		try {
			$this->_handleRest( '/wordlift/v1/faceted-search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'object', $response );
		$this->assertCount( 1, $response->posts );

	}

}
