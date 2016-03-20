<?php
require_once( 'functions.php' );

/**
 * Test the {@link TopicTaxonomyServiceTest}.
 *
 * @since 3.6.0
 */
class TopicTaxonomyServiceTest extends WP_UnitTestCase {

	/**
	 * The Log service.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Set up the test.
	 */
	function setUp() {

		parent::setUp();
		
		$this->log_service = Wordlift_Log_Service::get_logger( 'TopicTaxonomyServiceTest' );
		wl_configure_wordpress_test();
		wl_empty_blog();

	}

	/**
	 * Test the {@link get_or_create_term_from_topic_entity} function 
	 *
	 * @since 3.6.0
	 */
	function test_get_or_create_term_from_topic_entity() {

		// Create a fake topic
		$topic = uniqid( 'topic', true );
		// Create a fake topic entity
		$topic_id = wl_create_post( 'foo', sanitize_title( $topic ), $topic, 'draft', 'entity' );
		$topic_entity = get_post( $topic_id );
		// Create a reference to the service
		$topic_taxonomy_service = Wordlift_Topic_Taxonomy_Service::get_instance();
		// Check topic taxonomy term coresponding to $topic_id does not exist yet
		$this->assertFalse( get_term_by( 
			'slug', sanitize_title( $topic ), $topic_taxonomy_service::TAXONOMY_NAME
			) );
		// Going to create the term
		$term_id = $topic_taxonomy_service->get_or_create_term_from_topic_entity( $topic_entity );
	    // Check if the results is a numeric, meaning the term was properly created
	    $this->assertInternalType( 'int', $term_id ); 
	    // Retrieve the term obj and check its properties
	    $term = get_term_by( 'ID', $term_id, $topic_taxonomy_service::TAXONOMY_NAME );
	    // Check term name against topic entity post title
	    $this->assertEquals( $term->name, $topic_entity->post_title );
	    // Check term description against topic entity post content
	    $this->assertEquals( $term->description, $topic_entity->post_content );
		// Double check here
		$this->assertEquals( $term->description, 'foo' );
		// Check term slug
	    $this->assertEquals( $term->slug, sanitize_title( $topic ) );

	    // Call again the method with the same topic
		$new_term_id = $topic_taxonomy_service->get_or_create_term_from_topic_entity( $topic_entity );
	    // Ensure a new term was not created here
		$this->assertEquals( $term_id, $new_term_id );

	}



}
