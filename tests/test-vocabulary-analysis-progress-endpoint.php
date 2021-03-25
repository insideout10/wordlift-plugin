<?php


use Wordlift\Vocabulary\Analysis_Background_Process;
use Wordlift\Vocabulary\Analysis_Background_Service;
use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Cache\Cache_Service_Factory;
use Wordlift\Vocabulary\Sync_State;
use Wordlift\Vocabulary\Vocabulary_Loader;

/**
 * @group vocabulary
 * Class Analysis_Progress_Endpoint_Test
 */
class Analysis_Progress_Endpoint_Test extends \Wordlift_Vocabulary_Unit_Test_Case {

	private $start_analysis_route;

	private $stop_analysis_route;

	private $stats_analysis_route;

	private $restart_analysis_route;
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

		$this->start_analysis_route = Api_Config::REST_NAMESPACE . '/background_analysis/start';

		$this->stop_analysis_route = Api_Config::REST_NAMESPACE . '/background_analysis/stop';

		$this->stats_analysis_route = Api_Config::REST_NAMESPACE . '/background_analysis/stats';

		$this->restart_analysis_route = Api_Config::REST_NAMESPACE . '/background_analysis/restart';


	}

	public function test_when_analysis_start_request_done_analysis_should_start() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request  = new WP_REST_Request( 'POST', $this->start_analysis_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Background analysis start endpoint should be registered' );
		$state = get_option( Analysis_Background_Process::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );
		$this->assertNotEquals( $state, Sync_State::unknown(), 'The state should not be unknown' );
	}

	public function test_when_analysis_start_request_done_analysis_should_stop() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request  = new WP_REST_Request( 'POST', $this->stop_analysis_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Background analysis stop endpoint should be registered' );
		$result = get_transient( "wl_cmkg_analysis_background__analysis__cancel" );
		$this->assertTrue( $result );
	}

	public function test_when_analysis_stats_should_return_data() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request  = new WP_REST_Request( 'POST', $this->stats_analysis_route );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();
		$this->assertEquals( 200, $response->get_status(), 'Background analysis stats endpoint should be registered' );
	}


	public function test_restart_endpoint_should_be_registered() {
		$response = $this->send_restart_analysis_request();
		$this->assertEquals( 200, $response->get_status(), 'Background analysis restart endpoint should be registered' );
	}


	public function test_given_a_list_of_tags_should_remove_the_analysis_done_flag_on_restart() {
		$cache_service = Cache_Service_Factory::get_instance();
		$tag_ids       = $this->factory()->term->create_many( 50, array( 'taxonomy' => 'post_tag' ) );
		// set analysis done flag on all the tags.
		array_map( function ( $tag_id ) use ( $cache_service ) {
			update_term_meta( $tag_id, Analysis_Background_Service::ANALYSIS_DONE_FLAG, 1 );
			update_term_meta( $tag_id, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1 );
			// add some data which will be added by analysis.
			$cache_service->put( $tag_id, array( 'foo' => 'bar' ) );
		}, $tag_ids );
		// create also a tag without flag ( still in processing stage )
		$new_tag_id = wp_insert_term( 'test', 'post_tag' );
		$new_tag_id = $new_tag_id['term_id'];
		// Adding a random term meta, this should be present.
		update_term_meta( $new_tag_id, 'foo', 'bar' );
		update_option( 'foo', 'bar' );

		// send restart analysis request.
		$response = $this->send_restart_analysis_request();
		wp_cache_flush();
		// check if the state is set to started.
		/**
		 * @var $state Sync_State
		 */
		$state = get_option( Analysis_Background_Process::WL_CMKG_ANALYSIS_BACKGROUND_PROCESS, Sync_State::unknown() );
		$this->assertEquals( $state->state, 'started' );
		// check if the flags are removed.
		$that = $this;
		array_map( function ( $tag_id ) use ( $cache_service, $that ) {
			$that->assertFalse( $cache_service->get( $tag_id ) );
			$that->assertEquals( '', get_term_meta( $tag_id, Analysis_Background_Service::ANALYSIS_DONE_FLAG, true ), 'Analysis done flag should be removed' );
			$that->assertEquals( '', get_term_meta( $tag_id, Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, true ), 'Entities present flag should be removed' );
		}, $tag_ids );

		// this meta should be present even after removing the flag.
		$this->assertEquals( 'bar', get_term_meta( $new_tag_id, 'foo', true ) );
		$this->assertEquals( 'bar', get_option( 'foo', false ) );
	}

	/**
	 * @return WP_REST_Response
	 */
	private function send_restart_analysis_request() {
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request  = new WP_REST_Request( 'POST', $this->restart_analysis_route );

		return $this->server->dispatch( $request );
	}


}