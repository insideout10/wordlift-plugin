<?php
require_once 'functions.php';
require_once 'wordlift_ajax_unit_test_case.php';

/**
 * Class FacetedSearchShortcodeTest 
 * Extend WP_Ajax_UnitTestCase
 * @see https://codesymphony.co/wp-ajax-plugin-unit-testing/
 */
class FacetedSearchShortcodeTest extends WL_Ajax_UnitTestCase
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
    	$this->setExpectedException( 'WPAjaxDieStopException', 'No post_id given' );
    	$this->_handleAjax( 'wl_faceted_search' );
    }

    // From 3.4.0 faceted search is available also for standard posts
    public function testDataSelectionForANotEntity() {
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish', 'post');
        $_GET[ 'post_id' ] = $post_1_id;
        $this->setExpectedException( 'WPAjaxDieContinueException', '' );
        $this->_handleAjax( 'wl_faceted_search' );
    }

    public function testPostsSelectionWithoutFilters() {

    	// Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish');
        $post_2_id = wl_create_post( '', 'post2', 'A post', 'publish');
            
        // Insert relations
        wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

        // Set $_GET variable: this means we will perform data selection for $entity_1_id
        $_GET[ 'post_id' ] = $entity_1_id;
		$_GET[ 'type' ] = 'posts';

        try {
 	   	    $this->_handleAjax( 'wl_faceted_search' );
    	} catch ( WPAjaxDieContinueException $e ) { }
    	
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

    public function testPostsSelectionWithoutFiltersOnPostDrafts() {

        // Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post' );
        $post_2_id = wl_create_post( '', 'post2', 'A post' );
            
        // Insert relations
        wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

        // Set $_GET variable: this means we will perform data selection for $entity_1_id
        $_GET[ 'post_id' ] = $entity_1_id;
        $_GET[ 'type' ] = 'posts';

        try {
            $this->_handleAjax( 'wl_faceted_search' );
        } catch ( WPAjaxDieContinueException $e ) { }
        
        $response = json_decode( $this->_last_response );
        $this->assertInternalType( 'array', $response );
        $this->assertCount( 0, $response );
    }

    public function testPostsSelectionWithFilters() {

        // Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish');
        $post_2_id = wl_create_post( '', 'post2', 'A post', 'publish');
            
        // Insert relations
        wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

        // Set $_GET variable: this means we will perform data selection for $entity_1_id
        $_GET[ 'post_id' ] = $entity_1_id;
        $_GET[ 'type' ] = 'posts';
        // Mock php://input
        $mock_http_raw_data = json_encode( 
            array( wl_get_entity_uri( $entity_2_id ) ) 
        );

        try {
            $this->_handleAjax( 'wl_faceted_search', $mock_http_raw_data );
        } catch ( WPAjaxDieContinueException $e ) { }

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

    	// Create 2 posts and 2 entities
        $entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'draft', 'entity' );
        $entity_2_id = wl_create_post( '', 'entity1', 'Another Entity', 'draft', 'entity' );
        $post_1_id = wl_create_post( '', 'post1', 'A post', 'publish');
        $post_2_id = wl_create_post( '', 'post2', 'A post', 'publish');
            
        // Insert relations
        wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
        wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_2_id );

        // Set $_GET variable: this means we will perform data selection for $entity_1_id
        $_GET[ 'post_id' ] = $entity_1_id;
		$_GET[ 'type' ] = 'facets';

        try {
 	   	    $this->_handleAjax( 'wl_faceted_search' );
    	} catch ( WPAjaxDieContinueException $e ) { }
    	
    	$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'array', $response );
		$this->assertCount( 1, $response );
		$entity_uris = array( $response[0]->id );
		$this->assertNotContains( wl_get_entity_uri( $entity_1_id ), $entity_uris );
		$this->assertContains( wl_get_entity_uri( $entity_2_id ), $entity_uris );		

    }
    
}