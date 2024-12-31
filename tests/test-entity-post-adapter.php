<?php

use Wordlift\Content\WordPress\Wordpress_Content_Id;
use Wordlift\Content\WordPress\Wordpress_Content_Service;

/**
 * Define the Wordlift_Entity_Post_Adapter_Test class.
 *
 * @since 3.29.0
 * @group entity
 */
class Wordlift_Entity_Post_Save_Test extends Wordlift_Unit_Test_Case {

	public function test_when_local_entity_uri_is_on_post_content_but_doesnt_found_dont_create_a_new_entity() {

		$this->assertCount( 0, get_posts( array( 'post_type' => 'entity' ) ), '0 Entities should be present' );

		// lets create a post
		$post_id = $this->factory()->post->create();

		$entity_json_data = <<<EOF
{
  "entities": [
    {
      "annotations": {
        "urn:enhancement-1": {
          "start": 4,
          "end": 7,
          "text": "bar"
        },
        "urn:enhancement-2": {
          "start": 12,
          "end": 15,
          "text": "bar"
        },
        "urn:enhancement-3": {
          "start": 20,
          "end": 23,
          "text": "bar"
        }
      },
      "id": "https://data.localdomain.localhost/dataset/bar",
      "description": "foo bar",
      "label": "bar",
      "mainType": "thing",
      "occurrences": [
        "urn:enhancement-1",
        "urn:enhancement-2",
        "urn:enhancement-3"
      ],
      "sameAs": [
        
      ],
      "types": [
        "thing"
      ]
    }
  ]
}
EOF;

		$entities          = json_decode( $entity_json_data, true );
		$_POST['wl_boxes'] = array( 'test' );
		// create an entity map for classic editor test
		$_POST['wl_entities'] = $this->build_entity_map( $entities['entities'] );

		$post_content = <<<EOF
<!-- wp:wordlift/classification {"entities":[{"annotations":{"urn:enhancement-1":{"start":4,"end":7,"text":"bar"},"urn:enhancement-2":{"start":12,"end":15,"text":"bar"},"urn:enhancement-3":{"start":20,"end":23,"text":"bar"}},"id":"https://data.localdomain.localhost/dataset/bar","description":"foo bar", "label":"bar","mainType":"thing","occurrences":["urn:enhancement-1","urn:enhancement-2","urn:enhancement-3"],"sameAs":[],"types":["thing"]}]} /-->

<!-- wp:paragraph -->
<p>foo <span id="urn:enhancement-1" class="textannotation disambiguated wl-thing" itemid="https://data.localdomain.localhost/dataset/bar">bar</span> <span id="urn:enhancement-1" class="textannotation disambiguated wl-thing" itemid="https://data.localdomain.localhost/dataset/bar">bar</span></p>
<!-- /wp:paragraph -->
EOF;

		// lets update the post content.
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => $post_content,
		) );

		$this->assertCount( 0, get_posts( array( 'post_type' => 'entity' ) ), '0 Entities should be present even after save' );

	}


	public function test_when_non_local_entity_uri_is_on_post_content_should_create_a_new_entity() {

		$content_service = Wordpress_Content_Service::get_instance();

		$this->assertNull( $content_service->get_by_entity_id_or_same_as( 'https://google.com/bar' ),
			'Entity must not exist.' );

		$this->assertCount( 0, get_posts( array( 'post_type' => 'entity' ) ), '0 Entities should be present' );

		// lets create a post
		$post_id = $this->factory()->post->create();
		$this->assertNotEmpty(
			$content_service->get_entity_id( Wordpress_Content_Id::create_post( $post_id ) ),
			'An entity ID is expected.' );

		$post_content = <<<EOF
<!-- wp:wordlift/classification {"entities":[{"annotations":{"urn:enhancement-1":{"start":4,"end":7,"text":"bar"},"urn:enhancement-2":{"start":12,"end":15,"text":"bar"},"urn:enhancement-3":{"start":20,"end":23,"text":"bar"}},"id":"https://google.com/bar","description":"foo bar", "label":"bar","mainType":"thing","occurrences":["urn:enhancement-1","urn:enhancement-2","urn:enhancement-3"],"sameAs":[],"types":["thing"]}]} /-->

<!-- wp:paragraph -->
<p>foo <span id="urn:enhancement-1" class="textannotation disambiguated wl-thing" itemid="https://google.com/bar">bar</span> <span id="urn:enhancement-1" class="textannotation disambiguated wl-thing" itemid="https://data.localdomain.localhost/dataset/bar">bar</span></p>
<!-- /wp:paragraph -->
EOF;

		$entity_json_data = <<<EOF
{
  "entities": [
    {
      "annotations": {
        "urn:enhancement-1": {
          "start": 4,
          "end": 7,
          "text": "bar"
        },
        "urn:enhancement-2": {
          "start": 12,
          "end": 15,
          "text": "bar"
        },
        "urn:enhancement-3": {
          "start": 20,
          "end": 23,
          "text": "bar"
        }
      },
      "id": "https://google.com/bar",
      "description": "foo bar",
      "label": "bar",
      "mainType": "thing",
      "occurrences": [
        "urn:enhancement-1",
        "urn:enhancement-2",
        "urn:enhancement-3"
      ],
      "sameAs": [
        
      ],
      "types": [
        "thing"
      ]
    }
  ]
}
EOF;

		$entities = json_decode( $entity_json_data, true );

		$_POST['wl_boxes'] = array( 'test' );

		// create an entity map for classic editor test
		$_POST['wl_entities'] = $this->build_entity_map( $entities['entities'] );

		// lets update the post content.
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => $post_content,
		) );

		$this->assertNotNull( $content_service->get_by_entity_id_or_same_as( 'https://google.com/bar' ),
			'Entity must exist.' );


	}

	private function build_entity_map( $entities ) {
		$result = array();
		foreach ( $entities as $entity ) {

			$entity['uri']           = $entity['id'];
			$entity['main_type']     = $entity['mainType'];
			$result[ $entity['id'] ] = $entity;
		}

		return $result;
	}

}