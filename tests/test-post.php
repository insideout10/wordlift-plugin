<?php
/**
 * Tests: Posts Test.
 *
 * @since   3.0.0
 * @package Wordlift
 */

require_once 'functions.php';

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
 */
class PostTest extends Wordlift_Unit_Test_Case {

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

		wl_empty_blog();

		$this->assertEquals( 0, count( get_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => 'post',
			'post_status'    => 'any',
		) ) ) );
		$this->assertEquals( 0, count( get_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => 'entity',
			'post_status'    => 'any',
		) ) ) );

		// Get the count of triples.
		$counts = rl_count_triples();
		$this->assertNotNull( $counts );
		$this->assertFalse( is_wp_error( $counts ) );
//		$this->assertEquals( 0, $counts['subjects'] );
//		$this->assertEquals( 0, $counts['predicates'] );
//		$this->assertEquals( 0, $counts['objects'] );
	}

	/**
	 * Test the plugin configuration.
	 */
	function testConfiguration() {

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
	 */
	function testCountTriples() {

		// Get the count of triples.
		$counts = rl_count_triples();

		$this->assertNotNull( $counts );
		$this->assertTrue( is_array( $counts ) );
		$this->assertEquals( 3, count( $counts ) );
		$this->assertTrue( isset( $counts['subjects'] ) );
		$this->assertTrue( isset( $counts['predicates'] ) );
		$this->assertTrue( isset( $counts['objects'] ) );
	}

//	/**
//	 * Test create a post and submit it to Redlink for analysis.
//	 */
//	function testRedlinkAPI() {
//
//		// Create the test post.
//		$post_id = $this->createPost();
//
//		// Send the post for analysis.
//		$body = wl_analyze_post( $post_id );
//
//		// Save the results to a file.
//		if ( self::SAVE_REMOTE_RESPONSE ) {
//			$output = dirname( __FILE__ ) . '/' . self::FILENAME . '.json';
//			$result = file_put_contents( $output, $body );
//			$this->assertFalse( false === $result );
//		}
//
//		// Delete the test post.
//		$this->deletePost( $post_id );
//	}

	/**
	 * Test saving entities passed via a metabox.
	 */
	function testEntitiesViaArray() {

		self::turn_on_entity_push();

		// Create a post.
		$post_id = $this->createPost();
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

			// Type
//            $type = wl_get_entity_type($entity);
//            $this->assertFalse(empty($type));
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
		$this->checkEntities( $entity_posts );

		// Delete the post.
		$this->deletePost( $post_id );

		self::turn_off_entity_push();
	}

	function testSaveImage() {


		wl_save_image( 'http://upload.wikimedia.org/wikipedia/commons/a/a6/Flag_of_Rome.svg' );

		wl_save_image( 'https://usercontent.googleapis.com/freebase/v1/image/m/04js6kc?maxwidth=4096&maxheight=4096' );
	}

	/**
	 * Create a test post.
	 * @return int
	 */
	function createPost() {

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
	function checkEntities( $posts ) {

		foreach ( $posts as $post ) {
			$this->checkEntity( $post );
		}
	}

	/**
	 * Check the provided entity post against the remote Redlink datastore.
	 *
	 * @param WP_Post $post The post to check.
	 */
	function checkEntity( $post ) {

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
		$this->assertFalse( is_wp_error( $response ) );

		$body = $response['body'];

		$matches = array();
		$count   = preg_match_all( '/^(?P<label>.*),(?P<url>.*),(?P<type>[^\r]*)/im', $body, $matches, PREG_SET_ORDER );
		$this->assertTrue( is_numeric( $count ) );

		// Expect only one match (headers + one row).
		if ( 2 !== $count ) {
			wl_write_log( "checkEntity [ post id :: $post->ID ][ uri :: $uri ][ body :: $body ][ count :: $count ][ count (expected) :: 2 ]" );
		}
		$this->assertEquals( 2, $count );

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

	/**
	 * Check the provided entity post against the remote Redlink datastore.
	 *
	 * @param string $uri       The entity URI.
	 * @param string $title     The entity title.
	 * @param string $permalink The entity permalink.
	 */
	function checkEntityWithData( $uri, $title, $permalink ) {

		wl_write_log( "checkEntityWithData [ uri :: $uri ]" );

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
		$this->assertFalse( is_wp_error( $response ) );

		$body = $response['body'];

		$matches = array();
		$count   = preg_match_all( '/^(?P<label>.*),(?P<url>.*),(?P<type>[^\r]*)/im', $body, $matches, PREG_SET_ORDER );
		$this->assertTrue( is_numeric( $count ) );

		// Expect only one match (headers + one row).
		$this->assertEquals( 2, $count );

		// Focus on the first row.
		$match = $matches[1];

		// Get the label and URL from the remote answer.
		$label = $match['label'];
		$url   = $match['url'];
		$type  = $match['type'];

		// Check for equality.
		$this->assertEquals( $title, $label );
		$this->assertEquals( $permalink, $url );
		$this->assertFalse( empty( $type ) );
	}

}

function getDatasetName() {
	$dataset_name = "$this->dataset_name_prefix-php-" . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "-wp-$this->wp_version-ms-$this->wp_multisite";

	return str_replace( '.', '-', $dataset_name );
}
