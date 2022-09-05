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
 * @group ajax
 *
 * @since   3.0.0
 * @package Wordlift
 */
class FacetedSearchShortcodeTest extends Wordlift_Ajax_Unit_Test_Case {

	function setUp() {
		parent::setUp();

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}

	function _mock_api( $response, $request, $url ) {

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/queries$@', $url )
		     && in_array( md5( $request['body'] ), array(
				'8403eaa0ad0bb481eb8d7125e993c9d4',
				'1a60c9c70e2e1412e0ad63c1133bb4fb',
				'39532c50ab98078124e454ff35a4f26b',
				'52d70a03ffdfbb6466a48fa5d89694e2',
				'e06701d2651d8d43aaae08e2f9a374fd',
				'2d86db0762849be4190354010364faa0',
				'b67216fe8f49c180968a66c174e599b9',
				'8930a0271bd866734666bf5175a3aa17',
				'3e961742a1f0e78d04c57a275568f57f',
				'793c73084b6bc3ed67b83ca5d6d56df0',
				'be4b118e57e2755c550b8f619e0241da',
				'7cc7b2c484912026d36941bf15905300',
				'6659be6e2d973010651239c180889f7c',
				'd6d95adcfc4bc578c988c43b49f49214',
				'7e30de216d5e9f28f2a2bcff7f3253c0',
				'f356ce0f7d694544642303dbe782fa2f',
				'0fe0052d361335a573612a6e2b37827e',
				'93d19e07e8ed018701b7bf3d54c3f647',
				'8e6eec3723a38252aafec9544931ecfc',
				'1a28a9ebcf1bc8f6e86f3e1260157293',
				'490070779a1d73bf9eb8bb7203d8a2b1'
			) )
		     || preg_match( '~^INSERT DATA { <https://data\.localdomain\.localhost/(.*?)> <http://schema\.org/headline> "A post"@en \. 
<https://data\.localdomain\.localhost/\\1> <http://schema\.org/url> <http://example\.org/\?p=\d+> \. 
<https://data\.localdomain\.localhost/\\1> <http://www\.w3\.org/1999/02/22-rdf-syntax-ns#type> <http://schema\.org/Article> \.  };$~', $request['body'] ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/index$@', $url ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => ''
			);
		}

		return $response;
	}

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
		$_GET['uniqid']  = 'uniqid-123';
		$this->setExpectedException( 'WPAjaxDieStopException', 'No valid post_id given' );
		$this->_handleRest( '/wordlift/v1/faceted-search' );
	}

	public function testDataSelectionForAPostWithoutRelatedEntities() {
		$cache = new Ttl_Cache( 'faceted-search' );
		$cache->flush();

		$post_1_id       = wl_create_post( '', 'post1', 'A post', 'publish', 'post' );
		$_GET['post_id'] = $post_1_id;
		$_GET['uniqid']  = 'uniqid-123';
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
		$_GET['uniqid']  = 'uniqid-123';

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
		$this->assertEquals( get_permalink( $response->posts[0]->ID ), $response->posts[0]->permalink );
		$this->assertEquals( get_permalink( $response->posts[1]->ID ), $response->posts[1]->permalink );

		$post_ids = array( $response->posts[0]->ID, $response->posts[1]->ID );
		$this->assertContains( $post_1_id, $post_ids );
		$this->assertContains( $post_2_id, $post_ids );

	}

	public static function set_post_modified_to_one_year_after( $post_id ) {

		global $wpdb;

		$time = time() + DAY_IN_SECONDS * 365;

		$mysql_time_format = "Y-m-d H:i:s";

		$post_modified = gmdate( $mysql_time_format, $time );

		$post_modified_gmt = gmdate( $mysql_time_format, ( $time + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		$wpdb->query( "UPDATE $wpdb->posts SET post_modified = '{$post_modified}', post_modified_gmt = '{$post_modified_gmt}'  WHERE ID = {$post_id}" );
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
		$_GET['uniqid']  = 'uniqid-123';

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
		$_GET['uniqid']  = 'uniqid-123';

		try {
			$this->_handleRest( '/wordlift/v1/faceted-search' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );
		$this->assertInternalType( 'object', $response );
		$this->assertCount( 1, $response->posts );

	}

	/**
	 * @see https://github.com/insideout10/wordlift-plugin/issues/1181
	 * When the sort parameter isn't provided,
	 * Navigator and Faceted Search results should be sorted by date (modified) descending.
	 */
	public function test_by_default_faceted_search_uses_date_desc() {
		$request = array();

		// Create 2 posts and 2 entities
		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );
		$post_3_id = wl_create_post( '', 'post3', 'A post', 'publish' );
		self::set_post_modified_to_one_year_after( $post_3_id );
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_1_id );

		// we have post_1, post_2, $post_2 are related to entity_1
		/**
		 * Now when a faceted search queries the results then it should
		 * return the date in the descending order.
		 */
		$_GET['post_id'] = $post_1_id;
		$_GET['uniqid']  = 'uniqid-123';
		$data            = wl_shortcode_faceted_search_origin( $_GET );
		$this->assertArrayHasKey( 'posts', $data );
		$posts = $data['posts'];
		// the first should be $post_3
		// the second should be $post_2
		$this->assertEquals( $posts[0]->ID, $post_3_id );
		$this->assertEquals( $posts[1]->ID, $post_2_id );
	}


	public function test_when_sort_param_is_provided_it_should_order_correctly() {
		$request = array();

		// Create 2 posts and 2 entities
		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );
		$post_3_id = wl_create_post( '', 'post3', 'A post', 'publish' );
		self::set_post_modified_to_one_year_after( $post_3_id );
		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_1_id );

		// we have post_1, post_2, $post_2 are related to entity_1
		/**
		 * Now when a faceted search queries the results then it should
		 * return the date in the asc order.
		 */
		$_GET['post_id'] = $post_1_id;
		$_GET['sort']    = 'ASC';
		$_GET['uniqid']  = 'uniqid-123';
		$data            = wl_shortcode_faceted_search_origin( $_GET );
		$this->assertArrayHasKey( 'posts', $data );
		$posts = $data['posts'];
		// the first should be $post_3
		// the second should be $post_2
		$this->assertEquals( $posts[0]->ID, $post_2_id );
		$this->assertEquals( $posts[1]->ID, $post_3_id );
	}

	public function test_when_invalid_data_type_provided_for_sort_then_should_sort_by_desc() {
		$request = array();

		// Create 2 posts and 2 entities
		$post_1_id = wl_create_post( '', 'post1', 'A post', 'publish' );
		$post_2_id = wl_create_post( '', 'post2', 'A post', 'publish' );
		$post_3_id = wl_create_post( '', 'post3', 'A post', 'publish' );
		self::set_post_modified_to_one_year_after( $post_3_id );

		$entity_1_id = wl_create_post( '', 'entity0', 'An Entity', 'publish', 'entity' );
		// Insert relations
		wl_core_add_relation_instance( $post_1_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_2_id, WL_WHAT_RELATION, $entity_1_id );
		wl_core_add_relation_instance( $post_3_id, WL_WHAT_RELATION, $entity_1_id );

		// we have post_1, post_2, $post_2 are related to entity_1
		/**
		 * Now when a faceted search queries the results then it should
		 * return the date in the descending order.
		 */
		$_GET['post_id'] = $post_1_id;
		$_GET['sort']    = array( 'some-dangerous-data' );
		$_GET['uniqid']  = 'uniqid-123';
		$data            = wl_shortcode_faceted_search_origin( $_GET );
		$this->assertArrayHasKey( 'posts', $data );
		$posts = $data['posts'];
		// the first should be $post_3
		// the second should be $post_2
		$this->assertEquals( $posts[0]->ID, $post_3_id );
		$this->assertEquals( $posts[1]->ID, $post_2_id );
	}
}
