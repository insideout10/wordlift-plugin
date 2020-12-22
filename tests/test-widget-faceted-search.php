<?php
/**
 * @since 3.27.7.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

/**
 * Class Faceted_Search_Widget_Test
 * @group widget
 */
class Faceted_Search_Widget_Test extends Wordlift_Unit_Test_Case {

	private $template_route = '/wordlift/v1/faceted-search/template';

	public function setUp() {
		parent::setUp();
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}


	public function test_template_end_point_should_return_200() {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => 'foo' ) );
		$request->set_body( $json_data );
		$response  = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
	}

}