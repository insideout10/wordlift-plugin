<?php
/**
 * Tests: Ajax Related Posts Test.
 *
 * @since   3.0.0
 * @package Wordlift
 */

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
class AjaxRelatedPostsTest extends Wordlift_Ajax_Unit_Test_Case {

	public function test_dataselectionwithoutaninvalidentityid() {

		$_GET['post_id'] = 'foo';
		$this->setExpectedException( 'WPAjaxDieStopException', 'Post id missing or invalid!' );
		$this->_handleAjax( 'wordlift_related_posts' );

	}

	public function test_postsselectionwithfilters() {

		// Create 2 posts and 2 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		Wordlift_Entity_Type_Service::get_instance()->set( $entity_1_id, 'http://schema.org/Event' );
		$terms = wp_get_post_terms( $entity_1_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		$this->assertCount( 1, $terms, 'The entity must have 1 term set.' );

		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );

		$this->assertTrue( 0 < $entity_1_id );
		$this->assertTrue( 0 < $post_1_id );
		$this->assertTrue( 0 < $post_2_id );

		// Insert relations
		$this->assertTrue( 0 < wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id ) );
		$this->assertTrue( 0 < wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id ) );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $post_1_id;

		// Mock php://input
		$mock_http_raw_data = json_encode(
			array( wl_get_entity_uri( $entity_1_id ) )
		);


		try {
			$this->_handleAjax( 'wordlift_related_posts', $mock_http_raw_data );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'array', $response );
		$this->assertCount( 1, $response );
		$this->assertEquals( 'post', $response[0]->post_type );
		$this->assertEquals( $post_2_id, $response[0]->ID );
		$this->assertEquals( get_edit_post_link( $post_2_id, 'none' ), $response[0]->link );
		$this->assertEquals( get_post_permalink( $post_2_id ), $response[0]->permalink );


	}

	public function test_postsselectionwithoutfilters() {

		// Create 2 posts and 2 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		$post_1_id   = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id   = wl_create_post( '', 'post2', 'A post', 'publish' );
		// Notice that
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $post_1_id;
		// Mock php://input
		$mock_http_raw_data = json_encode(
			array()
		);

		try {
			$this->_handleAjax( 'wordlift_related_posts', $mock_http_raw_data );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'array', $response );
		// Here there will be no results
		$this->assertCount( 0, $response );

	}

	public function test_postsselectionstartingfromanentity() {

		// Create 2 posts and 2 entities
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
		$post_1_id   = wl_create_post( '', 'post1', 'A post', 'publish' );

		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );

		// Set $_GET variable: this means we will perform data selection for $entity_1_id
		$_GET['post_id'] = $entity_1_id;
		// Mock php://input
		$mock_http_raw_data = json_encode( array() );

		try {
			$this->_handleAjax( 'wordlift_related_posts', $mock_http_raw_data );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertInternalType( 'array', $response );
		$this->assertCount( 1, $response );
		$this->assertEquals( 'post', $response[0]->post_type );
		$this->assertEquals( $post_1_id, $response[0]->ID );

	}

}