<?php
/**
 * Tests: Issue 711.
 *
 * @since      3.16.3
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Issue_711} class.
 *
 * @since      3.16.3
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group issue
 */
class Wordlift_Issue_711 extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance under test.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance under test.
	 */
	private $entity_uri_service;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->entity_uri_service = Wordlift_Entity_Uri_Service::get_instance();

	}

	/**
	 * Test.
	 */
	public function test() {

		$uri     = Wordlift_Configuration_Service::get_instance()->get_dataset_uri()
		           . 'dataset/entity_uri';
		$same_as = 'https://data.example.org/dataset/entity_same_as';
		$this->_test_meta_key( WL_ENTITY_URL_META_NAME, $uri );
		$this->_test_meta_key( Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

	}

	/**
	 * Test each meta key/uri.
	 *
	 * @param string $meta_key The meta key.
	 * @param string $uri_1 The URI.
	 *
	 * @since 3.16.3
	 *
	 */
	private function _test_meta_key( $meta_key, $uri_1 ) {
		global $wpdb;

		// Create an entity.
		$post_id_1 = $this->factory->post->create( array(
			'post_type' => 'entity',
		) );
		Wordlift_Entity_Type_Service::get_instance()->set( $post_id_1, 'http://schema.org/Person' );

		// Add an entity URI.
		update_post_meta( $post_id_1, $meta_key, $uri_1 );

		$num_queries_1 = $wpdb->num_queries;

		// Get the entity by entity URI.
		$post_1_1 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNotNull( $post_1_1 );

		$num_queries_2 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_1, $num_queries_2 );

		// Get again the entity by entity URI, check no queries.
		$post_1_2 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNotNull( $post_1_1 );
		$this->assertEquals( $post_1_1, $post_1_2 );

		$num_queries_3 = $wpdb->num_queries;
		$this->assertEquals( $num_queries_2, $num_queries_3 );

		// Delete a post meta.
		delete_post_meta( $post_id_1, $meta_key, $uri_1 );

		$num_queries_4 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_3, $num_queries_4 );

		// Check post now null, with queries.
		$post_1_3 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNull( $post_1_3 );

		$num_queries_5 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_4, $num_queries_5 );

		// Check post now null, without queries (cached).
		$post_1_4 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNull( $post_1_4 );

		$num_queries_6 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_5, $num_queries_6 );

		// Add again the entity URI.
		add_post_meta( $post_id_1, $meta_key, $uri_1 );

		$num_queries_7 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_6, $num_queries_7 );

		// Get the entity by entity URI.
		$post_1_5 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNotNull( $post_1_5 );

		$num_queries_8 = $wpdb->num_queries;
		$this->assertGreaterThan( $num_queries_7, $num_queries_8 );

		// Get the entity by entity URI.
		$post_1_6 = $this->entity_uri_service->get_entity( $uri_1 );
		$this->assertNotNull( $post_1_6 );

		$num_queries_9 = $wpdb->num_queries;
		$this->assertEquals( $num_queries_8, $num_queries_9 );

	}

}
