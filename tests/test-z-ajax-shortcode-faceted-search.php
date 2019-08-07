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
		$this->setExpectedException( 'WPAjaxDieStopException', 'No post_id given' );
		$this->_handleAjax( 'wl_faceted_search' );
	}

	public function testDataSelectionForAMissingEntity() {
		$_GET['post_id'] = 1000000;
		$this->setExpectedException( 'WPAjaxDieStopException', 'No valid post_id given' );
		$this->_handleAjax( 'wl_faceted_search' );
	}

	public function testDataSelectionForAPostWithoutRelatedEntities() {
		$post_1_id       = wl_create_post( '', 'post1', 'A post', 'publish', 'post' );
		$_GET['post_id'] = $post_1_id;
		$this->setExpectedException( 'WPAjaxDieStopException', 'No entities available' );
		$this->_handleAjax( 'wl_faceted_search' );
	}

	public function testPostsSelectionWithoutFilters() {

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
		$_GET['type']    = 'posts';

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		$this->assertCount( 2, $response );
		$this->assertEquals( 'post', $response[0]->post_type );
		$this->assertEquals( 'post', $response[1]->post_type );
		$this->assertEquals( get_post_permalink( $response[0]->ID ), $response[0]->permalink );
		$this->assertEquals( get_post_permalink( $response[1]->ID ), $response[1]->permalink );

		$post_ids = array( $response[0]->ID, $response[1]->ID );
		$this->assertContains( $post_1_id, $post_ids );
		$this->assertContains( $post_2_id, $post_ids );

	}

	public function testPostsSelectionWithoutFiltersForAStandardPost() {

		// Create 2 posts and 1 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		$post_1_id   = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post', 'publish' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $post_1_id;
		$_GET['type']    = 'posts';

		Ttl_Cache::flush_all();

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		// I Expect one post because $post_1_id should be not included in the results.
		$this->assertCount( 1, $response, "The response isn't right: " . var_export( $response, true ) );
		$this->assertEquals( 'post', $response[0]->post_type );

		$post_ids = array( $response[0]->ID );
		$this->assertContains( $post_2_id, $post_ids );

	}

	public function testPostsSelectionWithoutFiltersOnPostDrafts() {

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
		$_GET['type']    = 'posts';

		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		$this->assertCount( 0, $response, "The response doesn't match: " . var_export( $response, true ) );
	}

	public function testPostsSelectionWithFilters() {

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
		$_GET['type']    = 'posts';
		// Mock php://input
		$mock_http_raw_data = json_encode(
			array( wl_get_entity_uri( $entity_2_id ) )
		);

		try {
			$this->_handleAjax( 'wl_faceted_search', $mock_http_raw_data );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'array', $response );
		$this->assertCount( 1, $response );
		$this->assertEquals( 'post', $response[0]->post_type );
		$this->assertEquals( get_post_permalink( $response[0]->ID ), $response[0]->permalink );
		$post_ids = array( $response[0]->ID );
		$this->assertContains( $post_2_id, $post_ids );
		$this->assertNotContains( $post_1_id, $post_ids );

	}

	public function testFacetsSelection() {

		// Create 3 posts and 3 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		$entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'publish', 'entity' );
		$entity_3_id = wl_create_post( '', 'entity2', 'A third Entity', 'draft', 'entity' );

		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );
		$post_3_id = wl_create_post( '', 'post3', 'A post', 'publish' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_3_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $entity_1_id;
		$_GET['type']    = 'facets';

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		// $entity_1_id itself is not included, only published entity are returned
		$this->assertCount( 1, $response );
		$entity_uris = array( $response[0]->id );
		$this->assertNotContains( wl_get_entity_uri( $entity_1_id ), $entity_uris );
		$this->assertContains( wl_get_entity_uri( $entity_2_id ), $entity_uris );

	}

	public function testFacetsSelectionForStandardPost() {

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
		$_GET['type']    = 'facets';

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		$this->assertCount( 2, $response );
		$entity_uris = array( $response[0]->id, $response[1]->id );
		$this->assertContains( wl_get_entity_uri( $entity_1_id ), $entity_uris );
		$this->assertContains( wl_get_entity_uri( $entity_2_id ), $entity_uris );

	}

	public function testFacetsSelectionLimit() {

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
		$_GET['type']    = 'facets';
		$_GET['limit']   = 1;

		try {
			$this->_handleAjax( 'wl_faceted_search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		$this->assertCount( 1, $response );

	}

}
