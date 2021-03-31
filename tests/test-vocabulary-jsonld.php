<?php

use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Vocabulary_Jsonld_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	/**
	 * @var WP_REST_Server
	 */
	private $server;

	public function setUp() {
		parent::setUp();
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}
	
	public function test_given_post_with_legacy_term_match_data_should_generate_valid_jsonld() {
		$term_id = $this->create_unmatched_tag( "foo" );
		wp_update_term( $term_id, 'post_tag', array(
			'description' => 'test_term_description'
		) );
		$term = get_term( $term_id );
		update_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1 );
		$post_id = $this->factory()->post->create();
		$tags    = array( $term->slug );
		wp_add_post_tags( $post_id, $tags );
		$same_as          = array( 'https://google.com', 'https://foo.com' );
		$alternate_labels = array( 'label 1', 'label 2' );

		foreach ( $same_as as $item ) {

			add_term_meta( $term_id, Entity_Rest_Endpoint::SAME_AS_META_KEY, $item );
		}

		foreach ( $alternate_labels as $item ) {
			add_term_meta( $term_id, Entity_Rest_Endpoint::ALTERNATIVE_LABEL_META_KEY, $item );
		}

		update_term_meta( $term_id, Entity_Rest_Endpoint::DESCRIPTION_META_KEY, 'test_entity_description' );
		update_term_meta( $term_id, Entity_Rest_Endpoint::TYPE_META_KEY, 'Product' );

		$data   = array(
			'jsonld'     => array(),
			'references' => array()
		);
		$result = apply_filters( 'wl_post_jsonld_array', $data, $post_id );
		$jsonld = $result['jsonld'];
		$this->assertArrayHasKey( 'mentions', $jsonld );
		$mentioned_entity = $jsonld['mentions'][0];
		$result_same_as = $mentioned_entity['sameAs'];
		$this->assertEquals( $result_same_as, $same_as );
		$this->assertEquals( $mentioned_entity['alternateName'], $alternate_labels );
		$this->assertEquals( $mentioned_entity['description'], 'test_term_description' );
		$this->assertEquals( $mentioned_entity['@type'], 'Product' );
	}


	public function test_given_post_with_new_jsonld_data_should_generate_valid_jsonld() {
		// accept 2 entities.
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];
		$entity    = $this->getMockEntityData();
		$user_id   = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$term = get_term( $term_id );
		update_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1 );
		$post_id = $this->factory()->post->create();
		$tags    = array( $term->slug );
		wp_add_post_tags( $post_id, $tags );

		$request = new WP_REST_Request( 'POST', '/' . Api_Config::REST_NAMESPACE . '/entity/accept' );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'entity' => $entity, 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$this->server->dispatch( $request );
		$this->server->dispatch( $request );

		$data   = array(
			'jsonld'     => array(),
			'references' => array()
		);
		$result = apply_filters( 'wl_post_jsonld_array', $data, $post_id );
		$jsonld = $result['jsonld'];
		$this->assertArrayHasKey( 'mentions', $jsonld );
		$this->assertCount(2, $jsonld['mentions'], 'Should have 2 entities in jsonld');

	}

}