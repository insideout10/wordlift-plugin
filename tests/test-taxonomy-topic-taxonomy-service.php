<?php
/**
 * Test the {@link TopicTaxonomyServiceTest}.
 *
 * @since 3.6.0
 * @group taxonomy
 */
class TopicTaxonomyServiceTest extends Wordlift_Unit_Test_Case {

	/**
	 * Test the {@link get_or_create_term_from_topic_entity} function
	 *
	 * @since 3.6.0
	 */
	function test_get_or_create_term_from_topic_entity() {

		// Create a fake topic
		$topic = uniqid( 'topic', true );
		// Create a fake topic entity
		$topic_id     = wl_create_post( 'foo', sanitize_title( $topic ), $topic, 'draft', 'entity' );
		$topic_entity = get_post( $topic_id );

		$this->assertNotNull( $topic_entity, 'The topic entity should exist.' );
		$this->assertNotNull( $topic_entity->post_title, 'Cannot read the post title.' );

		// Create a reference to the service
		$topic_taxonomy_service = Wordlift_Topic_Taxonomy_Service::get_instance();

		// Check topic taxonomy term corresponding to $topic_id does not exist yet
		$this->assertFalse( get_term_by(
			'slug', sanitize_title( $topic ), $topic_taxonomy_service::TAXONOMY_NAME
		) );

		// Going to create the term
		$term_id = $topic_taxonomy_service->get_or_create_term_from_topic_entity( $topic_entity );

		// Check if the results is a numeric, meaning the term was properly created
		$this->assertInternalType( 'int', $term_id );
		$this->assertGreaterThan( 0, $term_id );

		// Retrieve the term obj and check its properties
		$taxonomy = get_taxonomy( Wordlift_Topic_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertFalse( false === $taxonomy, 'The topic taxonomy must exist.' );

		// Since WP 4.8 we had to change 'ID' to 'id'.
		$term = get_term_by( 'id', $term_id, Wordlift_Topic_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertFalse( false === $term );
		$this->assertNotNull( $term, 'The term should exist.' );
		$this->assertNotNull( $term->name, 'Cannot read the term name.' );

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

	/**
	 * Test the {@link set_topic_for} function
	 *
	 * @since 3.6.0
	 */
	function test_set_topic_for() {

		// Create a fake topic
		$topic = uniqid( 'topic', true );
		// Create a post to be related to that topic entity
		$post_id = wl_create_post( 'foo', 'a-post', uniqid( 'post', true ), 'draft', 'post' );

		// Create a reference to the service
		$topic_taxonomy_service = Wordlift_Topic_Taxonomy_Service::get_instance();
		// Check that with a null topic_id false is returned
		$this->assertFalse( $topic_taxonomy_service->set_topic_for( $post_id, null ) );
		// Create a fake topic entity
		$topic_id = wl_create_post( 'foo', sanitize_title( $topic ), $topic, 'draft', 'entity' );
		// Ensure any terms is related to the current post yer
		$term_list = wp_get_post_terms(
			$post_id, $topic_taxonomy_service::TAXONOMY_NAME );
		$this->assertEmpty( $term_list );
		// Pass the proper topic id and and check true is returned
		$this->assertTrue( $topic_taxonomy_service->set_topic_for( $post_id, $topic_id ) );
		// Ensure a single term is now related
		$term_list = wp_get_post_terms(
			$post_id, $topic_taxonomy_service::TAXONOMY_NAME );
		$this->assertNotEmpty( $term_list );
		$this->assertCount( 1, $term_list );
		// Check related term is what we expect to be
		$this->assertEquals( $topic, $term_list[0]->name );
		$this->assertEquals( sanitize_title( $topic ), $term_list[0]->slug );

	}
}
