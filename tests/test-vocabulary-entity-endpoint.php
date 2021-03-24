<?php

use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Entity\Entity_Factory;
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

	}


	public function test_accept_endpoint_should_return_200_and_create_data_for_term_meta() {
		// create a term
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];

		$entity = $this->getMockEntityData();

		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request = new WP_REST_Request( 'POST', $this->accept_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'entity' => $entity, 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Accept endpoint should be registered' );

		$entity         = Entity_Factory::get_instance( $term_id );
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

		$entity = $this->getMockEntityData();

		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request = new WP_REST_Request( 'POST', $this->accept_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'entity' => $entity, 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		// Dispatch the request twice, we should have 2 entities in meta by now.
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Accept endpoint should be registered' );

		$entity   = Entity_Factory::get_instance( $term_id );
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


	public function test_reject_endpoint_should_return_200_and_remove_all_the_data() {

		// create a term
		$term_data = wp_insert_term( 'foo', 'post_tag' );
		$term_id   = $term_data['term_id'];

		$entity = $this->getMockEntityData();

		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );


		$request = new WP_REST_Request( 'POST', $this->accept_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'entity' => $entity, 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );


		// Insert a term meta for no match entity uri.
		add_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 'https://google.com' );


		$request = new WP_REST_Request( 'POST', $this->reject_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'term_id' => $term_id ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Reject endpoint should be registered' );
		// Check if we have removed all values in meta
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::SAME_AS_META_KEY ) );
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::ALTERNATIVE_LABEL_META_KEY ) );
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::DESCRIPTION_META_KEY ) );
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::TYPE_META_KEY ) );
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::EXTERNAL_ENTITY_META_KEY ) );
		$this->assertCount( 0, get_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING ), 'No match should be cleared on undo' );
		$this->assertEquals( '', get_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, true ) );
	}

	public function test_should_store_multiple_entity_data_in_meta() {

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

	/**
	 * @return array
	 */
	private function getMockEntityData() {
		return array(
			'@context'         => 'http://schema.org',
			'@id'              => 'https://knowledge.cafemedia.com/food/entity/pie',
			'@type'            => 'Thing',
			'description'      => 'A pie is a baked dish which is usually made of a pastry dough casing that covers or completely contains a filling of various sweet or savoury ingredients. Pies are defined by their crusts. A filled pie (also single-crust or bottom-crust), has pastry lining the baking dish, and the filling is placed on top of...',
			'mainEntityOfPage' => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/pie/',
			'name'             => 'pie',
			'sameAs'           =>
				array(
					0  => 'https://en.wikipedia.org/wiki/Pie',
					1  => 'http://purl.obolibrary.org/obo/FOODON_03401296',
					2  => 'http://www.wikidata.org/entity/Q13360264',
					3  => 'http://dbpedia.org/resource/Pie',
					4  => 'http://pl.dbpedia.org/resource/Pieróg',
					5  => 'http://rdf.freebase.com/ns/m.0mjqn',
					6  => 'http://ko.dbpedia.org/resource/파이',
					7  => 'http://wikidata.dbpedia.org/resource/Q13360264',
					8  => 'http://dbpedia.org/resource/Pie',
					9  => 'http://id.dbpedia.org/resource/Pastei',
					10 => 'http://www.wikidata.org/entity/Q13360264',
					11 => 'http://ja.dbpedia.org/resource/パイ',
					12 => 'http://fr.dbpedia.org/resource/Tourte_(plat)',
				),
			'url'              => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/pie/',

		);
	}


}