<?php

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;

/**
 * Class Wordlift_Content_Parsing_Test
 * @backend
 */
class Wordlift_Content_Parsing_Test extends Wordlift_Unit_Test_Case {

	function createSampleEntity() {
		$entity_post_id = wl_create_post( 'Lorem Ipsum', 'honda', 'Honda', 'publish', 'entity' );
		wl_schema_set_value( $entity_post_id, 'sameAs', 'http://dbpedia.org/resource/Honda' );
	}

	function testContentParsing() {

		// Create the sample entity for testing.
		$this->createSampleEntity();

		$content = '<span class="textannotation highlight organization disambiguated" id="urn:enhancement-16e9b0f6-e792-5b75-ffb7-ec40916d8753" itemid="http://dbpedia.org/resource/Honda" itemscope="itemscope" itemtype="organization">Honda</span> is recalling nearly 900,000 minivans for a defect that could increase fire risk.';

		$matches = array();
		if ( 0 < preg_match_all( '/ itemid="([^"]+)"/im', $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$item_id = $match[1];

				$content_service = Wordpress_Content_Service::get_instance();
				$content         = $content_service->get_by_entity_id_or_same_as( $item_id );
				$this->assertInstanceOf( '\WP_Post', $content->get_bag() );

				$post = $content->get_bag();
				$uri  = $content_service->get_entity_id( Wordpress_Content_Id::create_post( $post->ID ) );
				$this->assertNotNull( $uri );
				$this->assertNotEquals( $item_id, $uri );

			}
		}

		$this->assertTrue( 0 < count( $matches ) );
	}

}
