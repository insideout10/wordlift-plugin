<?php
require_once 'functions.php';
require_once 'wordlift_ajax_unit_test_case.php';

/**
 * Class FacetedSearchShortcodeTest 
 * Extend WP_Ajax_UnitTestCase
 * @see https://codesymphony.co/wp-ajax-plugin-unit-testing/
 */
class AjaxRelatedPostsTest extends WL_Ajax_UnitTestCase
{
	/**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();
        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();
        
    }

    public function testDataSelectionWithoutAnEntityId() {
        $this->_setRole( "administrator" );
        $this->assertTrue( is_admin() );
    	$this->setExpectedException( 'WPAjaxDieStopException', 'Post id missing or invalid!' );
    	$this->_handleAjax( 'wordlift_related_posts' );
    }

    public function testDataSelectionWithoutAnInvalidEntityId() {
        $_GET['post_id'] = 'foo';
        $this->setExpectedException( 'WPAjaxDieStopException', 'Post id missing or invalid!' );
        $this->_handleAjax( 'wordlift_related_posts' );
    }

    public function testPostsSelectionWithFilters() {

        // Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish');
        $post_2_id = wl_create_post( '', 'post2', 'A post', 'publish');
            
        // Insert relations
        wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );

        // Set $_GET variable: this means we will perform data selection for $entity_1_id
        $_GET['post_id'] = $post_1_id;
        // Mock php://input
        $mock_http_raw_data = json_encode( 
            array( wl_get_entity_uri( $entity_1_id ) ) 
        );

        try {
            $this->_handleAjax( 'wordlift_related_posts', $mock_http_raw_data );
        } catch ( WPAjaxDieContinueException $e ) { }

        $response = json_decode( $this->_last_response );
        
        $this->assertInternalType( 'array', $response );
        $this->assertCount( 1, $response );
        $this->assertEquals( 'post', $response[0]->post_type );
        $this->assertEquals( $post_2_id, $response[0]->ID );
        $this->assertEquals( get_edit_post_link( $post_2_id, 'none' ), $response[0]->link );
        $this->assertEquals( get_post_permalink( $post_2_id ), $response[0]->permalink );
                 

    }

    public function testPostsSelectionWithoutFilters() {

        // Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish');
        $post_2_id = wl_create_post( '', 'post2', 'A post', 'publish');
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
        } catch ( WPAjaxDieContinueException $e ) { }

        $response = json_decode( $this->_last_response );
        
        $this->assertInternalType( 'array', $response );
        // Here there will be no results
        $this->assertCount( 0, $response );
                 
    }
    
}