<?php
/**
 * Tests: Download Google Data.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Download_Google_Content_Data_Test} class.
 *
 * @group   ajax
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Download_Google_Content_Data_Test extends Wordlift_Ajax_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Google_Analytics_Export_Service} instance.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var \Wordlift_Google_Analytics_Export_Service $export_service The {@link Wordlift_Google_Analytics_Export_Service} instance.
	 */
	protected $export_service;

	/**
	 * {@inheritdoc}
	 */
	function setUp() {
		parent::setUp();

		global $wp_rewrite;

		if ( $wp_rewrite->permalink_structure ) {
			$this->set_permalink_structure( '' );
		}

		$this->export_service = new Wordlift_Google_Analytics_Export_Service();

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );
	}

	function tearDown() {
		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}


	function _mock_api( $response, $request, $url ) {

		if ( 'POST' === $request['method'] && preg_match( '@/datasets/key=key123/queries$@', $url )
		     && ( in_array( md5( $request['body'] ),
					array(
						'45aa1b0696baa684e4d9f78fb84ea253',
						'40a67488480c5993a0905d6774ddc459',
						'f1368f426dcae5fc95567f069daff698',
						'4aec754e407f9a55ee34fd555c73784d',
						'8930a0271bd866734666bf5175a3aa17',
						'54e3d6564def2ef6345a88f2c6021ccd',
						'5e16ec9c6add6122e53e0ce4c0c5a2e2',
						'27669186597e15554b6a9b940276fb19',
						'77b824b9035dc122e7cf7884b47ca3ff',
						'c02e0cad0d0d5979b0d1061b49ec1745',
						'88d74184041489182765a54c2df580fa',
						'0da01417f8ec4517d7c228bb25346f75',
						'd928af2d4c1c73d02166d1c7628994ef',
						'40b82ef6c46e5619c3de074c6fcbda97'
					) )
		          || preg_match( '~^INSERT DATA { <https://data\.localdomain\.localhost/(.*?)> <http://schema\.org/headline> "A post with no entities"@en \. 
<https://data\.localdomain\.localhost/\\1> <http://schema\.org/url> <http://example\.org/\?p=\d+> \. 
<https://data\.localdomain\.localhost/\\1> <http://www\.w3\.org/1999/02/22-rdf-syntax-ns#type> <http://schema\.org/Article> \.  };$~', $request['body'] ) ) ) {
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

	/**
	 * Test that "is_postname_permalink_structure" is working properly.
	 *
	 * @since 3.16.0
	 */
	public function test_permalink_structure() {
		// Check if the permalink structure is set to "postname".
		$test_permalink_structure_1 = Wordlift_Google_Analytics_Export_Service::is_postname_permalink_structure();

		$this->assertFalse( $test_permalink_structure_1 );

		// Change the structure to "postname".
		$this->set_permalink_structure( '/%postname%/' );

		// Check if the permalink structure is set to "postname".
		$test_permalink_structure_2 = Wordlift_Google_Analytics_Export_Service::is_postname_permalink_structure();

		$this->assertTrue( $test_permalink_structure_2 );
	}

	/**
	 * Test that "get_content_data" is working properly
	 * and return the proper number of enities,
	 *
	 * @return void
	 * @since 3.16.0
	 *
	 */
	public function test_content_data() {
		// Create posts.
		$post_id = wl_create_post( '', 'data-post1', 'A post with no entities', 'publish', 'post' );

		$post_id_2 = wl_create_post( '', 'data-post2', 'A post with no entities', 'publish', 'post' );

		// Create entity.
		$entity_id = wl_create_post( '', 'data-entity1', 'An Entity', 'publish', 'entity' );

		// Add relation between our entity and post.
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );

		// Get content data.
		$data = $this->export_service->get_content_data();

		// Add relation to other post with the entity.
		wl_core_add_relation_instance( $post_id_2, WL_WHAT_RELATION, $entity_id );

		// Get the content data again.
		$data_2 = $this->export_service->get_content_data();

		// Check the number of returned items.
		$this->assertCount( 1, $data );
		$this->assertCount( 2, $data_2 );

		// Get the first item from response.
		$item   = $data[0];
		$item_2 = $data_2[1];

		// Compare the names.
		$this->assertEquals( 'data-entity1', $item->entity_name );
		$this->assertEquals( 'data-entity1', $item_2->entity_name );

		// Compare the type.
		$this->assertEquals( 'thing', $item->entity_type );
		$this->assertEquals( 'thing', $item_2->entity_type );

		// Compare the path.
		$this->assertEquals( '/data-post1/', $item->post_name );
		$this->assertEquals( '/data-post2/', $item_2->post_name );

		wp_delete_post( $post_id );
		wp_delete_post( $post_id_2 );
	}

	/**
	 * Test that "create_csv" is working properly and return the expected result.
	 *
	 * @return void
	 * @since 3.16.0
	 *
	 */
	public function test_csv_creation() {
		// Create posts.
		$post_id   = wl_create_post( '', 'csv-post1', 'A post with no entities', 'publish', 'post' );
		$post_id_2 = wl_create_post( '', 'csv-post2', 'A post with no entities', 'publish', 'post' );

		// Create entity.
		$entity_id = wl_create_post( '', 'csv-entity1', 'An Entity', 'publish', 'entity' );

		// Add relation between our entity and post.
		wl_core_add_relation_instance( $post_id, WL_WHAT_RELATION, $entity_id );
		wl_core_add_relation_instance( $post_id_2, WL_WHAT_RELATION, $entity_id );

		// Get the CSV content.
		ob_start();
		$this->export_service->create_csv();
		$response = ob_get_clean();

		// Turn the CSV back to array.
		$csv_object = str_getcsv( $response, "\n" );

		$this->assertCount( 3, $csv_object );

		$this->assertEquals( 'ga:pagePath,ga:dimension1,ga:dimension2', $csv_object[0] );
	}
}
