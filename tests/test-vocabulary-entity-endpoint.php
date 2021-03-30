<?php

use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Entity_List\Entity_List_Factory;
use Wordlift\Vocabulary\Vocabulary_Loader;


/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Accept_Reject_Entity_Endpoint_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	private $accept_route;

	private $reject_route;

	private $no_match_route;

	public function setUp() {
		parent::setUp();
		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );

		$this->accept_route = Api_Config::REST_NAMESPACE . '/entity/accept';

		$this->reject_route = Api_Config::REST_NAMESPACE . '/entity/undo';

		$this->no_match_route = Api_Config::REST_NAMESPACE . '/entity/no_match';

		$this->reject_route = Api_Config::REST_NAMESPACE . '/entity/reject';

	}


	public function test_accept_endpoint_should_return_200_and_create_data_for_term_meta() {
		// create a term
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];

		$entity = $this->getMockEntityData();

		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );


		$response = $this->send_accept_entity_request( $entity, $term_id );


		$this->assertEquals( 200, $response->get_status(), 'Accept endpoint should be registered' );

		$entity         = Entity_List_Factory::get_instance( $term_id );
		$entities       = $entity->get_jsonld_data();
		$current_entity = $entities[0];

		// Check if we have all values in term meta.
		$this->assertCount( 14, $current_entity['sameAs'] );
		$this->assertCount( 1, $current_entity['alternateName'] );
		$this->assertNotNull( $current_entity['description'] );
		$this->assertNotNull( $current_entity['@type'] );
		//$this->assertNotNull( 1, get_term_meta( $term_id, Entity_Rest_Endpoint::EXTERNAL_ENTITY_META_KEY ) );
		$this->assertEquals( 1, get_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) );
	}


	public function test_should_store_data_for_multiple_entity_matches() {
		// create a term
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];
		$entity    = $this->getMockEntityData();
		$user_id   = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$response = $this->send_accept_entity_request( $entity, $term_id );
		// Dispatch the request twice, we should have 2 entities in meta by now.
		$this->send_accept_entity_request( $entity, $term_id );

		$this->assertEquals( 200, $response->get_status(), 'Accept endpoint should be registered' );

		$entity   = Entity_List_Factory::get_instance( $term_id );
		$entities = $entity->get_jsonld_data();
		$this->assertCount( 2, $entities );
		$current_entity = $entities[0];

		// Check if we have all values in term meta.
		$this->assertCount( 14, $current_entity['sameAs'] );
		$this->assertCount( 1, $current_entity['alternateName'] );
		$this->assertNotNull( $current_entity['description'] );
		$this->assertNotNull( $current_entity['@type'] );
		//$this->assertNotNull( 1, get_term_meta( $term_id, Entity_Rest_Endpoint::EXTERNAL_ENTITY_META_KEY ) );
		$this->assertEquals( 1, get_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) );
	}

	public function testMarkedAsNoMatchShoulReturn200() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];
		$request   = new WP_REST_Request( 'POST', $this->no_match_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array(
			'term_id'    => $term_id,
			'entity_uri' => 'https://knowledge.cafemedia.com/food/entity/pie'
		) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );

		$request = new WP_REST_Request( 'POST', $this->no_match_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status(), 'No match endpoint should be registered' );
		$results = get_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING );
		$this->assertCount( 2, $results );
		$this->assertEquals( 1, $results[0] );

	}




	public function test_when_the_entity_is_rejected_should_remove_only_the_entity_from_jsonld() {
		// accept 2 entities.
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];
		$entity    = $this->getMockEntityData();
		$user_id   = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Dispatch the request twice, we should have 2 entities in meta by now.
		$this->send_accept_entity_request( $entity, $term_id );
		$this->send_accept_entity_request( $entity, $term_id );

		// reject 1 entity
		$this->send_reject_entity_request($entity, $term_id);

		// check the meta, now we should have 1 entity.
		$entity   = Entity_List_Factory::get_instance( $term_id );
		$entities = $entity->get_jsonld_data();
		$this->assertCount( 1, $entities, "Single entity should be present since one got deleted" );
	}

	/**
	 * @param array $entity
	 * @param $term_id
	 *
	 * @return mixed
	 */
	private function send_accept_entity_request(  $entity, $term_id ) {
		$request = new WP_REST_Request( 'POST', $this->accept_route );
		$request->set_header( 'content-type', 'application/json' );
		return $this->dispatch_entity_request( $entity, $term_id, $request );
	}

	/**
	 * @param array $entity
	 * @param $term_id
	 *
	 * @return mixed
	 */
	private function send_reject_entity_request(  $entity, $term_id ) {
		$request = new WP_REST_Request( 'POST', $this->reject_route );
		$request->set_header( 'content-type', 'application/json' );
		return $this->dispatch_entity_request( $entity, $term_id, $request );
	}

	/**
	 * @param array $entity
	 * @param $term_id
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 */
	private function dispatch_entity_request( array $entity, $term_id, WP_REST_Request $request ) {
		$json_data = json_encode( array( 'entity' => $entity, 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );

		return $response;
	}


}