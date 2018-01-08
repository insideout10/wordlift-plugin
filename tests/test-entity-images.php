<?php
require_once 'functions.php';

/**
 * Class EntityImagesTest
 */
class EntityImagesTest extends Wordlift_Unit_Test_Case {

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		// Empty the blog.
		wl_empty_blog();

		// Check that entities and posts have been deleted.
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

	}

	function testSaveOneImage() {

		$entity_post = wl_save_entity( array(
			'uri'             => 'http://example.org/entity',
			'label'           => 'Entity',
			'main_type'       => 'http://schema.org/Thing',
			'description'     => 'An example entity.',
			'type_uris'       => array(),
			'related_post_id' => null,
			'image'           => array(
				'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
			),
			'same_as'         => array(),
		) );

		// Get all the attachments for the entity post.
		$attachments = wl_get_attachments( $entity_post->ID );

		// Check that there is one attachment.
		$this->assertEquals( 1, count( $attachments ) );

		// Check that the attachments are found by source URL.
		$image_post = wl_get_attachment_for_source_url( $entity_post->ID, 'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg' );
		$this->assertNotNull( $image_post );

		// Check that the no attachments are found if the source URL doesn't exist.
		$image_post = wl_get_attachment_for_source_url( $entity_post->ID, 'http://example.org/non-existing-image.png' );
		$this->assertNull( $image_post );
	}

	function testSaveMultipleImages() {

		$images = array(
			'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/c/c2/Tim_Berners-Lee_2012.jpg',
		);

		$entity_post = wl_save_entity( array(
			'uri'             => 'http://example.org/entity',
			'label'           => 'Entity',
			'main_type'       => 'http://schema.org/Thing',
			'description'     => 'An example entity.',
			'type_uris'       => array(),
			'related_post_id' => null,
			'image'           => $images,
			'same_as'         => array(),
		) );

		// Get all the attachments for the entity post.
		$attachments = wl_get_attachments( $entity_post->ID );

		// Check that there is one attachment.
		$this->assertEquals( 3, count( $attachments ) );

		// Check that the attachments are found by source URL.
		foreach ( $images as $image ) {
			$image_post = wl_get_attachment_for_source_url( $entity_post->ID, $image );
			$this->assertNotNull( $image_post );
		}
	}

	function testSaveExistingImages() {

		$images = array(
			'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/c/c2/Tim_Berners-Lee_2012.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
		);

		$entity_post = wl_save_entity( array(
			'uri'             => 'http://example.org/entity',
			'label'           => 'Entity',
			'main_type'       => 'http://schema.org/Thing',
			'description'     => 'An example entity.',
			'type_uris'       => array(),
			'related_post_id' => null,
			'image'           => $images,
			'same_as'         => array(),
		) );

		// Get all the attachments for the entity post.
		$attachments = wl_get_attachments( $entity_post->ID );

		// Check that there is one attachment.
		$this->assertEquals( 3, count( $attachments ) );

		// Check that the attachments are found by source URL.
		foreach ( $images as $image ) {
			$image_post = wl_get_attachment_for_source_url( $entity_post->ID, $image );
			$this->assertNotNull( $image_post );
		}
	}

	function testEntityImagesMetadataPublishingOnRedLink() {

		// We need to push entities to the Linked Data store, we'll turn this off.
		Wordlift_Unit_Test_Case::turn_on_entity_push();

		// Create a first entity, just to have
		// two attachments available in the media library
		$featured_images = array(
			'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
			'http://upload.wikimedia.org/wikipedia/commons/c/c2/Tim_Berners-Lee_2012.jpg',
		);

		$entity_post_name = uniqid( 'entity', true );
		$entity_post      = wl_save_entity( array(
			'uri'             => "http://example.org/$entity_post_name",
			'label'           => uniqid( 'entity', true ),
			'main_type'       => 'http://schema.org/Thing',
			'description'     => 'A first example entity.',
			'type_uris'       => array(),
			'related_post_id' => null,
			'image'           => $featured_images,
			'same_as'         => array(),
		) );

		// Retrieve the attachment to use as featured image.
		$attachments           = wl_get_attachments( $entity_post->ID );
		$first_featured_image  = $attachments[0];
		$second_featured_image = $attachments[1];

		// Create a second entity entity, with one of the
		// few attachment available in the media library
		$images = array(
			'http://upload.wikimedia.org/wikipedia/commons/3/3a/Tim_Berners-Lee_closeup.jpg',
		);

		$entity_post_name = uniqid( 'entity', true );
		$entity_post      = wl_save_entity( array(
			'uri'             => "http://example.org/$entity_post_name",
			'label'           => uniqid( 'entity', true ),
			'main_type'       => 'http://schema.org/Thing',
			'description'     => 'A second example entity.',
			'type_uris'       => array(),
			'related_post_id' => null,
			'image'           => $images,
			'same_as'         => array(),
		) );

		// Retrieve the attachment
		$attachments = wl_get_attachments( $entity_post->ID );

		$this->assertCount( 1, $attachments );

		// Ensure the entity post is in draft
		$this->assertEquals( 'draft', $entity_post->post_status );
		// Publish the entity post
		wl_update_post_status( $entity_post->ID, 'publish' );
		// Ensure one image - $attachment - is on RL
		$redlink_images = $this->getImageRLMetadata( $entity_post->ID );

		$log = Wordlift_Log_Service::get_logger( get_class() );

		$log->debug( 'Found ' . count( $redlink_images ) . ' image(s).' );

		$this->assertCount( 1, $redlink_images );
		$this->assertContains(
			wp_get_attachment_url( $attachments[0]->ID ),
			$redlink_images );
		// Un-publish the entity post
		wl_update_post_status( $entity_post->ID, 'draft' );
		// Set $first_featured_image is the entity post featured image
		set_post_thumbnail( $entity_post->ID, $first_featured_image->ID );
		// Publish the entity post
		wl_update_post_status( $entity_post->ID, 'publish' );
		// Ensure one image - $first_featured_image - is on RL
		$redlink_images = $this->getImageRLMetadata( $entity_post->ID );
		$this->assertCount( 2, $redlink_images );
		$this->assertContains(
			wp_get_attachment_url( $first_featured_image->ID ),
			$redlink_images );

		// Set $second_featured_image is the entity post featured image
		set_post_thumbnail( $entity_post->ID, $second_featured_image->ID );
		// Ensure one image - $second_featured_image - is on RL
		$redlink_images = $this->getImageRLMetadata( $entity_post->ID );
		$this->assertCount( 1, $redlink_images );
		$this->assertContains(
			wp_get_attachment_url( $second_featured_image->ID ),
			$redlink_images );

		// Remove any featured image
		delete_post_thumbnail( $entity_post->ID );
		// Ensure no images are on RL
		$redlink_images = $this->getImageRLMetadata( $entity_post->ID );
		$this->assertCount( 0, $redlink_images );

		//
		Wordlift_Unit_Test_Case::turn_off_entity_push();

	}

	function getImageRLMetadata( $entity_id ) {

		// Get the post Redlink URI.
		$uri     = wl_get_entity_uri( $entity_id );
		$uri_esc = Wordlift_Sparql_Service::escape( $uri );

		// Prepare the SPARQL query to select images url.
		$sparql = "SELECT ?o WHERE { <$uri_esc> <http://schema.org/image> ?o . }";

		// Send the query and get the response.
		$response = rl_sparql_select( $sparql );

		if (is_wp_error( $response )) {
			var_dump($response);
		}

		$this->assertFalse( is_wp_error( $response ) );

		$lines = array();
		foreach ( explode( "\n", $response['body'] ) as $line ) {
			if ( empty( $line ) ) {
				continue;
			}
			$lines[] = preg_replace( '/\s+/', '', $line );
		}
		array_shift( $lines );

		return $lines;

	}

}