<?php
/**
 * Tests: Posts Test.
 *
 * @since   3.0.0
 * @package Wordlift
 */

/**
 * We're going to perform a full-blown test here:
 *  - create a post,
 *  - analyse a post,
 *  - save the entities,
 *  - check that the entities have been created:
 *    -- locally
 *    -- in the cloud
 *  - delete the entities (check deletion)
 *  - delete the post (check deletion)
 *
 * @since   3.0.0
 * @package Wordlift
 * @group backend
 */
class Wordlift_Post_Test extends Wordlift_Unit_Test_Case {

	// The filename pointing to the test contents.
	const FILENAME = 'post.txt';
	const SLUG = 'tests-post';
	const TITLE = 'Test Post';

	// The number of expected entities (as available in the mock response).
	const EXPECTED_ENTITIES = 8;

	// When true, the remote response is saved locally and kept as a mock-up (be aware that the previous mockup is
	// overwritten).
	const SAVE_REMOTE_RESPONSE = false;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		add_filter( 'pre_http_request', array( $this, '_mock_api' ), 10, 3 );

		// Get the count of triples.
		$counts = rl_count_triples();
		$this->assertNotNull( $counts );
		$this->assertFalse( is_wp_error( $counts ) );

		$this->turn_on_entity_push();
	}

	function tearDown() {

		remove_filter( 'pre_http_request', array( $this, '_mock_api' ) );

		parent::tearDown();
	}


	function _mock_api( $response, $request, $url ) {

		if ( 'GET' === $request['method'] && 0 <= strpos( $url, '/datasets/key=key123/queries?q=PREFIX+geo%3A+%3Chttp%3A%2F%2Fwww.w3.org%2F2003%2F01%2Fgeo%2Fwgs84_pos%23%3E%0APREFIX+dct%3A+%3Chttp%3A%2F%2Fpurl.org%2Fdc%2Fterms%2F%3E%0APREFIX+rdfs%3A+%3Chttp%3A%2F%2Fwww.w3.org%2F2000%2F01%2Frdf-schema%23%3E%0APREFIX+owl%3A+%3Chttp%3A%2F%2Fwww.w3.org%2F2002%2F07%2Fowl%23%3E%0APREFIX+schema%3A+%3Chttp%3A%2F%2Fschema.org%2F%3E%0APREFIX+xsd%3A+%3Chttp%3A%2F%2Fwww.w3.org%2F2001%2FXMLSchema%23%3E%0A%0ASELECT+%28COUNT%28DISTINCT+%3Fs%29+AS+%3Fsubjects%29+%28COUNT%28DISTINCT+%3Fp%29+AS+%3Fpredicates%29+%28COUNT%28DISTINCT+%3Fo%29+AS+%3Fobjects%29+WHERE+%7B+%3Fs+%3Fp+%3Fo+%7D' ) ) {
			return array(
				'response' => array( 'code' => 200 ),
				'body'     => "subjects,predicates,objects
                               31090,17,34093
                               "
			);
		}

		return $response;
	}

	/**
	 * Test the plugin configuration.
	 */
	function test_configuration() {

		// #43: https://github.com/insideout10/wordlift-plugin/issues/43
		// We're using WordLift Server, we do not require a Redlink Key nor a Dataset Name to be set:
		// we now require a WordLift key to be set. In turn, setting WordLift key should set the dataset URI,
		// that we'll continue to check.
		// $this->assertNotNull(wl_configuration_get_redlink_key());
		// $this->assertNotNull( wl_configuration_get_redlink_dataset_name() );
		// $this->assertNotNull( wl_configuration_get_redlink_user_id() );
		$this->assertNotNull( wl_configuration_get_key() );
		$this->assertNotNull( wl_configuration_get_redlink_dataset_uri() );

		// $this->assertEquals( WL_CONFIG_DEFAULT_SITE_LANGUAGE, wl_configuration_get_site_language() );
	}

	/**
	 * Test the method to count the number of triples in the remote datastore.
	 *
	 * @group redlink
	 */
	function test_count_triples() {

		// Get the count of triples.
		$counts = rl_count_triples();

		$this->assertNotNull( $counts );
		$this->assertTrue( is_array( $counts ) );
		$this->assertEquals( 3, count( $counts ) );
		$this->assertTrue( isset( $counts['subjects'] ) );
		$this->assertTrue( isset( $counts['predicates'] ) );
		$this->assertTrue( isset( $counts['objects'] ) );

	}

	/**
	 * Test saving entities passed via a metabox.
	 *
	 * @group redlink
	 */
	public function test_entities_via_array() {

		// Check that SPARQL queries buffering is disabled.
		$this->assertFalse( wl_is_sparql_update_queries_buffering_enabled() );

		// Check that entity push is enabled.
		$this->assertFalse( apply_filters( 'wl_disable_entity_push', get_transient( 'DISABLE_ENTITY_PUSH' ) ) );

		// Create a post.
		$post_id = $this->create_post();
		$this->assertTrue( is_numeric( $post_id ) );

		$post = get_post( $post_id );
		$this->assertNotNull( $post );

		// Read the entities from the mock-up analysis.
		$analysis_results = wl_parse_file( dirname( __FILE__ ) . '/' . self::FILENAME . '.json' );
		$this->assertTrue( is_array( $analysis_results ) );

		// For each entity get the label, type, description and thumbnails.
		$this->assertTrue( isset( $analysis_results['entities'] ) );

		// Get a reference to the entities.
		$text_annotations = $analysis_results['text_annotations'];
		$best_entities    = array();
		foreach ( $text_annotations as $id => $text_annotation ) {
			$entity_annotation = wl_get_entity_annotation_best_match( $text_annotation['entities'] );
			$entity            = $entity_annotation['entity'];
			$entity_id         = $entity->{'@id'};

			if ( ! array_key_exists( $entity_id, $best_entities ) ) {
				$best_entities[ $entity_id ] = $entity;
			}
		}

		// Accumulate the entities in an array.
		$entities = array();
		foreach ( $best_entities as $uri => $entity ) {

			// Label
			if ( ! isset( $entity->{'http://www.w3.org/2000/01/rdf-schema#label'}->{'@value'} ) ) {
				var_dump( $entity );
			}
			$this->assertTrue( isset( $entity->{'http://www.w3.org/2000/01/rdf-schema#label'}->{'@value'} ) );
			$label = $entity->{'http://www.w3.org/2000/01/rdf-schema#label'}->{'@value'};
			$this->assertFalse( empty( $label ) );

			// Description
			$description = wl_get_entity_description( $entity );
			$this->assertNotNull( $description );

			// Images
			$images = wl_get_entity_thumbnails( $entity );
			$this->assertTrue( is_array( $images ) );

			// Save the entity to the entities array.
			$entities = array_merge_recursive( $entities, array(
				$uri => array(
					'uri'         => $uri,
					'label'       => $label,
					'main_type'   => 'http://schema.org/Thing',
					'type'        => array(),
					'description' => $description,
					'images'      => $images,
				),
			) );
		}

		// Save the entities in the array.
		$entity_posts = array();
		foreach ( $entities as $uri => $entity ) {
			$entity_posts[] = wl_save_entity( $entity );

		}
		// Publish
		$entity_post_ids = array_map( function ( $item ) {
			return $item->ID;
		}, $entity_posts );

		foreach ( $entity_post_ids as $entity_id ) {
			wp_publish_post( $entity_id );
		}

		// TODO: need to bind entities with posts.
		wl_core_add_relation_instances( $post_id, WL_WHAT_RELATION, $entity_post_ids );

		$this->assertCount( sizeof( $entity_post_ids ), wl_core_get_related_entity_ids( $post_id ) );

		// TODO: synchronize data.
		// NOTICE: this requires a published post!
		Wordlift_Linked_Data_Service::get_instance()->push( $post_id );

		// Check that the entities are created in WordPress.
		$this->assertCount( count( $entities ), $entity_posts );

		// Check that each entity is bound to the post.
		$entity_ids = array();
		foreach ( $entity_posts as $post ) {
			// Store the entity IDs for future checks.
			array_push( $entity_ids, $post->ID );

			// Get the related posts IDs.
			$rel_posts = wl_core_get_related_post_ids( $post->ID );

			$this->assertCount( 1, $rel_posts );
			// The post must be the one the test created.
			$this->assertEquals( $post_id, $rel_posts[0] );
		}

		// Check that the post references the entities.
		$rel_entities = wl_core_get_related_entity_ids( $post_id );
		$this->assertEquals( count( $entity_ids ), count( $rel_entities ) );
		foreach ( $entity_ids as $id ) {
			$this->assertTrue( in_array( $id, $rel_entities ) );
		}

		// Check that the locally saved entities and the remotely saved ones match.
		$this->check_entities( $entity_posts );

		// Delete the post.
		$this->deletePost( $post_id );

	}

	/**
	 * Create a test post.
	 * @return int
	 */
	public function create_post() {

		// Get the post contents.
		$input   = dirname( __FILE__ ) . '/' . self::FILENAME;
		$content = file_get_contents( $input );
		$this->assertTrue( false != $content );

		// Create the post.
		$post_id = wl_create_post( $content, self::SLUG, self::TITLE, 'publish' );
		$this->assertTrue( is_numeric( $post_id ) );

		return $post_id;
	}

	/**
	 * Delete a post.
	 *
	 * @param $post_id
	 */
	function deletePost( $post_id ) {

		// Delete the post.
		$result = wl_delete_post( $post_id );
		$this->assertTrue( false != $result );
	}

	/**
	 * Check the provided entity posts against the remote Redlink datastore.
	 *
	 * @param array $posts The array of entity posts.
	 */
	private function check_entities( $posts ) {

		foreach ( $posts as $post ) {
			$this->check_entity( $post );
		}
	}

	/**
	 * Check the provided entity post against the remote Redlink datastore.
	 *
	 * @param WP_Post $post The post to check.
	 */
	private function check_entity( $post ) {

		// Get the entity URI.
		$uri = Wordlift_Sparql_Service::escape( wl_get_entity_uri( $post->ID ) );

		wl_write_log( "checkEntity [ post id :: $post->ID ][ uri :: $uri ]" );

		// Prepare the SPARQL query to select label and URL.
		$sparql = <<<EOF
SELECT DISTINCT ?label ?url ?type
WHERE {
    <$uri> rdfs:label ?label ;
           schema:url ?url ;
           a ?type .
}
EOF;

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );

		if ( is_wp_error( $response ) ) {
			$this->fail( "An error occurred while contacting the remote end-point: " . $response->get_error_message() );
		}

		$this->assertFalse( is_wp_error( $response ) );

		$body = $response['body'];

		$matches = array();
		$count   = preg_match_all( '/^(?P<label>.*),(?P<url>.*),(?P<type>[^\r]*)/im', $body, $matches, PREG_SET_ORDER );
		$this->assertTrue( is_numeric( $count ) );

		// Expect only one match (headers + one row).
		if ( 2 !== $count ) {
			wl_write_log( "checkEntity [ post id :: $post->ID ][ uri :: $uri ][ body :: $body ][ count :: $count ][ count (expected) :: 2 ]" );
		}
		$this->assertEquals( 2, $count, "checkEntity [ post id :: $post->ID ][ uri :: $uri ]"
		                                . "[ body :: $body ][ count :: $count ][ count (expected) :: 2 ]"
		                                . "[ post data :: " . var_export( $post, true ) . "]"
		                                . "[ post entity type :: " . var_export( wp_get_object_terms( $post->ID, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ), true ) . "]" );

		// Focus on the first row.
		$match = $matches[1];

		// Get the label and URL from the remote answer.
		$label = $match['label'];
		$url   = $match['url'];
		$type  = $match['type'];

		// Get the post title and permalink.
		$post_title = $post->post_title;
		$permalink  = get_permalink( $post->ID );

		// Check for equality.
		$this->assertEquals( $post_title, $label );

		$this->assertEquals( $permalink, $url );
		$this->assertFalse( empty( $type ) );
	}

}
