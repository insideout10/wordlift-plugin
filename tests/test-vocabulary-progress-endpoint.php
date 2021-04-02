<?php


use Wordlift\Vocabulary\Api\Api_Config;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Vocabulary_Loader;

/**
 * @since 3.30.0
 * @group vocabulary
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Reconcile_Progress extends \Wordlift_Vocabulary_Unit_Test_Case {

	private $reconcile_progress_route;

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
		$this->reconcile_progress_route = '/' . Api_Config::REST_NAMESPACE . '/reconcile_progress/progress';
	}

	public function test_should_return_items_completed_correctly() {

		// create 5 tags.
		for ( $i = 0; $i < 5; $i++ ) {
			$term_data = wp_insert_term("tag ${i}", 'post_tag');
			$term_id = $term_data['term_id'];
			update_term_meta($term_id, \Wordlift\Vocabulary\Analysis_Background_Service::ENTITIES_PRESENT_FOR_TERM, 1);
			// mark as completed for 2 items
			if ( $i < 2 ) {
				update_term_meta( $term_id, Entity_Rest_Endpoint::IGNORE_TAG_FROM_LISTING, 1);
			}

		}


		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$request  = new WP_REST_Request( 'POST', $this->reconcile_progress_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Reconcile progress endpoint should be registered' );
		$data = $response->get_data();
		$this->assertArrayHasKey('completed', $data);
		$this->assertArrayHasKey('total', $data);

		$this->assertEquals( 5, $data['total']);
		$this->assertEquals( 2, $data['completed']);


	}

}