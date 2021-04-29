<?php

use Wordlift\Api\Response;
use Wordlift\Cache\Ttl_Cache;
use Wordlift\Vocabulary\Analysis_Background_Process;
use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Analysis_Service;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Api\Tag_Rest_Endpoint;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Sync_State;
use Wordlift\Vocabulary\Vocabulary_Loader;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Tag_Endpoint_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	private $template_route = '/cafemediakg/v1/tags';
	/**
	 * @var WP_REST_Server
	 */
	private $server;


	public function setUp() {
		parent::setUp();
		global $wp_rest_server, $wp_filter;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
		Ttl_Cache::flush_all();
	}


	public function test_tag_endpoint_should_return_200() {
		$response = $this->send_request_to_endpoint();
		$this->assertEquals( 200, $response->get_status(), 'Tag endpoint should be registered' );
	}

	public function test_tag_endpoint_should_return_correct_tags() {

		global $wp_filter;
		$wp_filter         = array();
		$term_data         = wp_insert_term( 'test', 'post_tag' );
		$tag               = get_term( $term_data['term_id'] );
		$api_service_mock  = $this->build_mock_api_service( false );
		$cache_service     = \Wordlift\Vocabulary\Cache\Cache_Service_Factory::get_cache_service();
		$analysis_service  = new Analysis_Service( $api_service_mock, $cache_service );
		$term_data_factory = new Term_Data_Factory( $analysis_service );
		$endpoint          = new Tag_Rest_Endpoint( $term_data_factory );
		$endpoint->register_routes();
		$this->reset_rest_server();


		$received_mock_entities = $this->get_mock_entities();
		$cache_key              = $tag->term_id;
		// the data should be present on options cache before we send the request.
		$cache_service->put( $cache_key, $received_mock_entities );

		update_term_meta( $term_data['term_id'], Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );

		$response = $this->send_request_to_endpoint();

		$expected_response = array(
			array(
				'tagId'          => $tag->term_id,
				'tagName'        => 'test',
				'tagDescription' => '',
				'tagLink'        => get_edit_term_link( $tag->term_id ),
				'entities'       => $received_mock_entities,
				'tagPostCount'   => 0
			),

		);

		$data = $response->get_data();


		$this->assertEquals( $expected_response, $data );

		// we should have the term data cached.
		$this->assertEquals( $received_mock_entities,
			$cache_service->get( $term_data['term_id'] ),
			'We should have the data in cache' );

	}


	private function reset_rest_server() {
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}

	private function send_request_to_endpoint() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'limit' => 2, 'offset' => 0 ) );
		$request->set_body( $json_data );

		return $this->server->dispatch( $request );
	}


	public function test_when_tag_is_accepted_should_not_be_added_to_list() {
		global $wp_filter;
		$wp_filter         = array();
		$term_data         = wp_insert_term( 'test', 'post_tag' );
		$tag               = get_term( $term_data['term_id'] );
		$api_service_mock  = $this->build_mock_api_service( false );
		$cache_service     = new Ttl_Cache( "wordlift-cmkg", 8 * 60 * 60 );
		$analysis_service  = new Analysis_Service( $api_service_mock, $cache_service );
		$term_data_factory = new Term_Data_Factory( $analysis_service );
		$endpoint          = new Tag_Rest_Endpoint( $term_data_factory );
		$endpoint->register_routes();
		$this->reset_rest_server();
		// set the tag as ignored from ui.
		update_term_meta( $term_data['term_id'], Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1 );
		update_term_meta( $term_data['term_id'], Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );
		// now send the request, we should receive empty response.
		$response = $this->send_request_to_endpoint();
		$data     = $response->get_data();
		// mock service should not be called
		$this->assertEquals( array(), $data );

	}

	/**
	 * @return array[]
	 */
	public static function get_mock_entities() {
		return array(
			0 =>
				array(
					'entityId' => 'https://knowledge.cafemedia.com/food/entity/pie',

					'confidence' => 1,
					'mainType'   => 'thing',
					'types'      =>
						array(
							0 => 'thing',
						),
					'label'      => 'pie',
					'images'     =>
						array(),
					'sameAs'     =>
						array(
							0  => 'http://dbpedia.org/resource/Pie',
							1  => 'http://fr.dbpedia.org/resource/Tourte_(plat)',
							2  => 'http://id.dbpedia.org/resource/Pastei',
							3  => 'http://ja.dbpedia.org/resource/パイ',
							4  => 'http://ko.dbpedia.org/resource/파이',
							5  => 'http://pl.dbpedia.org/resource/Pieróg',
							6  => 'http://purl.obolibrary.org/obo/FOODON_03401296',
							7  => 'http://rdf.freebase.com/ns/m.0mjqn',
							8  => 'http://wikidata.dbpedia.org/resource/Q13360264',
							9  => 'http://www.wikidata.org/entity/Q13360264',
							10 => 'https://en.wikipedia.org/wiki/Pie',
						),
					'meta'       => array(
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
					),

				),
			1 =>
				array(
					'entityId'   => 'https://knowledge.cafemedia.com/food/entity/cherry',
					'confidence' => 1,

					'mainType' => 'thing',
					'types'    =>
						array(
							0 => 'thing',
						),
					'label'    => 'cherry',
					'images'   =>
						array(),

					'sameAs' =>
						array(
							0  => 'http://cs.dbpedia.org/resource/Třešně',
							1  => 'http://dbpedia.org/resource/Cherry',
							2  => 'http://el.dbpedia.org/resource/Κεράσι',
							3  => 'http://es.dbpedia.org/resource/Cereza',
							4  => 'http://fr.dbpedia.org/resource/Cerise',
							5  => 'http://it.dbpedia.org/resource/Ciliegia',
							6  => 'http://ja.dbpedia.org/resource/サクランボ',
							7  => 'http://ko.dbpedia.org/resource/버찌',
							8  => 'http://nl.dbpedia.org/resource/Kers_(fruit)',
							9  => 'http://purl.obolibrary.org/obo/FOODON_03301240',
							10 => 'http://rdf.freebase.com/ns/m.0f8sw',
							11 => 'http://rdf.freebase.com/ns/m.0hs32',
							12 => 'http://wikidata.dbpedia.org/resource/Q196',
							13 => 'http://www.wikidata.org/entity/Q196',
							14 => 'https://en.wikipedia.org/wiki/Cherry',
						),
					'meta'   => array(
						'@context'         => 'http://schema.org',
						'@id'              => 'https://knowledge.cafemedia.com/food/entity/cherry',
						'@type'            => 'Thing',
						'description'      => 'A cherry is the fruit of many plants of the genus Prunus, and is a fleshy drupe (stone fruit). The cherry fruits of commerce usually are obtained from a limited number of species such as cultivars of the sweet cherry, Prunus avium. The name \'cherry\' also refers to the cherry tree, and is sometimes applied...',
						'mainEntityOfPage' => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/cherry/',
						'name'             => 'cherry',
						'alternateName'    =>
							array(
								0 => 'cherry',
								1 => 'drupe',
							),
						'sameAs'           =>
							array(
								0  => 'https://en.wikipedia.org/wiki/Cherry',
								1  => 'http://purl.obolibrary.org/obo/FOODON_03301240',
								2  => 'http://www.wikidata.org/entity/Q196',
								3  => 'http://dbpedia.org/resource/Cherry',
								4  => 'http://cs.dbpedia.org/resource/Třešně',
								5  => 'http://ko.dbpedia.org/resource/버찌',
								6  => 'http://dbpedia.org/resource/Cherry',
								7  => 'http://es.dbpedia.org/resource/Cereza',
								8  => 'http://ja.dbpedia.org/resource/サクランボ',
								9  => 'http://rdf.freebase.com/ns/m.0f8sw',
								10 => 'http://fr.dbpedia.org/resource/Cerise',
								11 => 'http://nl.dbpedia.org/resource/Kers_(fruit)',
								12 => 'http://www.wikidata.org/entity/Q196',
								13 => 'http://it.dbpedia.org/resource/Ciliegia',
								14 => 'http://el.dbpedia.org/resource/Κεράσι',
								15 => 'http://wikidata.dbpedia.org/resource/Q196',
								16 => 'http://rdf.freebase.com/ns/m.0hs32',
							),
						'url'              => 'https://app.wordlift.io/knowledge-cafemedia-com-food/entity/cherry/',

					),
				),
		);
	}

	/**
	 * @return array
	 */
	private function get_mock_analysis_datta() {
		$mock_entities = array(
			'https://knowledge.cafemedia.com/food/entity/pie'    => array(
				'entityId'   => 'https://knowledge.cafemedia.com/food/entity/pie',
				'confidence' => 1.0,
				'mainType'   => 'thing',
				'types'      => array( 0 => 'thing', ),
				'label'      => 'pie',
				'images'     => array(),
				'sameAs'     => array(
					0  => 'http://dbpedia.org/resource/Pie',
					1  => 'http://fr.dbpedia.org/resource/Tourte_(plat)',
					2  => 'http://id.dbpedia.org/resource/Pastei',
					3  => 'http://ja.dbpedia.org/resource/パイ',
					4  => 'http://ko.dbpedia.org/resource/파이',
					5  => 'http://pl.dbpedia.org/resource/Pieróg',
					6  => 'http://purl.obolibrary.org/obo/FOODON_03401296',
					7  => 'http://rdf.freebase.com/ns/m.0mjqn',
					8  => 'http://wikidata.dbpedia.org/resource/Q13360264',
					9  => 'http://www.wikidata.org/entity/Q13360264',
					10 => 'https://en.wikipedia.org/wiki/Pie',
				),
			),
			'https://knowledge.cafemedia.com/food/entity/cherry' => array(
				'entityId'   => 'https://knowledge.cafemedia.com/food/entity/cherry',
				'confidence' => 1.0,
				'mainType'   => 'thing',
				'types'      => array( 0 => 'thing', ),
				'label'      => 'cherry',
				'images'     => array(),
				'sameAs'     => array(
					0  => 'http://cs.dbpedia.org/resource/Třešně',
					1  => 'http://dbpedia.org/resource/Cherry',
					2  => 'http://el.dbpedia.org/resource/Κεράσι',
					3  => 'http://es.dbpedia.org/resource/Cereza',
					4  => 'http://fr.dbpedia.org/resource/Cerise',
					5  => 'http://it.dbpedia.org/resource/Ciliegia',
					6  => 'http://ja.dbpedia.org/resource/サクランボ',
					7  => 'http://ko.dbpedia.org/resource/버찌',
					8  => 'http://nl.dbpedia.org/resource/Kers_(fruit)',
					9  => 'http://purl.obolibrary.org/obo/FOODON_03301240',
					10 => 'http://rdf.freebase.com/ns/m.0f8sw',
					11 => 'http://rdf.freebase.com/ns/m.0hs32',
					12 => 'http://wikidata.dbpedia.org/resource/Q196',
					13 => 'http://www.wikidata.org/entity/Q196',
					14 => 'https://en.wikipedia.org/wiki/Cherry',
				),
			),
		);

		$mock_analysis_data = array(
			'response' => array(
				'code' => 200,
			),
			'body'     => json_encode( array(
				'entities'    => $mock_entities,
				'annotations' => array(
					'urn:enhancement-1' => array(
						'annotationId'  => 'urn:enhancement-1',
						'start'         => 10,
						'end'           => 16,
						'text'          => 'cherry',
						'entityMatches' => array(
							0 => array(
								'confidence' => 1.0,
								'entityId'   => 'https://knowledge.cafemedia.com/food/entity/cherry',
							),
						),
					),
					'urn:enhancement-2' => array(
						'annotationId'  => 'urn:enhancement-2',
						'start'         => 17,
						'end'           => 20,
						'text'          => 'pie',
						'entityMatches' => array(
							0 => array(
								'confidence' => 1.0,
								'entityId'   => 'https://knowledge.cafemedia.com/food/entity/pie',
							),
						),
					),
				),
			) ),
		);

		return $mock_analysis_data;
	}

	/**
	 * @return mixed
	 */
	private function build_mock_api_service( $perform_assertion = true ) {
		$api_service_mock = $this->getMockBuilder( 'Wordlift\Api\Api_Service' )
		                         ->disableOriginalConstructor()
		                         ->getMock();


		$mock_analysis_data = $this->get_mock_analysis_datta();

		if ( $perform_assertion ) {
			$this->perform_assertion( $api_service_mock, $mock_analysis_data );
		} else {
			$api_service_mock
				->method( 'request' )->willReturn( new Response( $mock_analysis_data ) );
		}

		return $api_service_mock;
	}

	/**
	 * @param $api_service_mock
	 * @param array $mock_analysis_data
	 */
	private function perform_assertion( $api_service_mock, array $mock_analysis_data ) {
		$api_service_mock
			->expects( $this->once() )
			->method( 'request' )->willReturn( new Response( $mock_analysis_data ) );
	}


	public function test_when_tags_added_should_start_the_background_process() {

		// we made the state to be unknown

		update_option( Analysis_Background_Process::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );
		// the state should now be updated to running.
		wp_insert_term( 'random_tag', 'post_tag' );
		/**
		 * @var $sync_state Sync_State
		 */
		$sync_state = get_option( Analysis_Background_Process::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );

		$this->assertNotEquals( $sync_state, Sync_State::unknown() );

	}


	public function test_tags_linked_to_more_posts_should_be_shown_first() {
		$post_1 = $this->factory()->post->create();
		$post_2 = $this->factory()->post->create();
		$post_3 = $this->factory()->post->create();


		$tag_2 = wp_insert_term( 'test_1', 'post_tag' );
		$tag_2 = $tag_2['term_id'];
		$tag_1 = wp_insert_term( 'test_2', 'post_tag' );
		$tag_1 = $tag_1['term_id'];
		$tag_3 = wp_insert_term( 'test_3', 'post_tag' );
		$tag_3 = $tag_3['term_id'];

		wp_add_post_tags( $post_1, array( $tag_1 ) );
		wp_add_post_tags( $post_2, array( $tag_1, $tag_2, $tag_3 ) );
		wp_add_post_tags( $post_3, array( $tag_1, $tag_3 ) );


		update_term_meta( $tag_1, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );
		update_term_meta( $tag_2, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );
		update_term_meta( $tag_3, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );


		// we need get $tag_1 as first item since it is linked to more posts.
		$endpoint = new Tag_Rest_Endpoint( null );
		$tags     = $endpoint->get_tags_from_db( 10, 0 );

		$this->assertCount( 3, $tags, 'Should return all the tags' );
		/**
		 * @var $first_tag WP_Term
		 */
		$first_tag = $tags[0];
		$this->assertEquals( $first_tag->term_id, $tag_1 );
	}
}
