<?php
/**
 * @since 3.27.7.3
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Widgets\Faceted_Search\Faceted_Search_Template_Endpoint;

/**
 * Class Faceted_Search_Widget_Test
 * @group widget
 */
class Faceted_Search_Widget_Test extends Wordlift_Unit_Test_Case {

	private $template_route = '/wordlift/v1/faceted-search/template';

	public function setUp() {
		parent::setUp();
		global $wp_rest_server, $wp_filter;
		// Resetting global filters, since we want our test
		// to run independently without global state.
		$wp_filter      = array();
		$instance       = new \Wordlift\Widgets\Async_Template_Decorator( new Wordlift_Faceted_Search_Shortcode() );
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}


	public function test_template_end_point_should_return_200() {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => 'foo' ) );
		$request->set_body( $json_data );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status(), 'Faceted search template endpoint not registered' );
	}

	public function test_when_registered_template_via_filter_should_get_the_filter_on_the_endpoint() {

		$template_id = 'foo';

		$template = 'my-template';

		add_filter( 'wordlift_faceted_search_templates', function ( $templates ) use ( $template, $template_id ) {
			$templates[ $template_id ] = $template;

			return $templates;
		} );

		$request = $this->create_template_request( $template_id );
		/**
		 * @var $response WP_REST_Response
		 */
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey( 'template', $data );
		/**
		 * Now that we posted the template id we should have the template
		 * in the response.
		 */
		$this->assertEquals( $template, $data['template'], 'Faceted search template not received' );

	}

	/**
	 * when we post a non registered template id, then we expect it to return
	 * a empty string.
	 */
	public function test_when_non_registered_template_should_receive_empty_string() {

		$template_id = 'foo';

		$request = $this->create_template_request( $template_id );
		/**
		 * @var $response WP_REST_Response
		 */
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey( 'template', $data );
		$this->assertEquals( '', $data['template'], 'Faceted search template should be empty' );

	}

	/**
	 * @param $template_id
	 *
	 * @return WP_REST_Request
	 */
	private function create_template_request( $template_id ) {
		$request = new WP_REST_Request( 'POST', $this->template_route );
		$request->set_header( 'content-type', 'application/json' );
		$json_data = json_encode( array( 'template_id' => $template_id ) );
		$request->set_body( $json_data );

		return $request;
	}


	public function test_on_do_shortcode_should_have_template_url() {
		new Wordlift_Faceted_Search_Shortcode();
		$post_id = $this->factory()->post->create();
		$post = get_post( $post_id );
		$result = do_shortcode("[wl_faceted_search template_id='foo' post_id=$post_id]");
		$template_url = "?rest_route=/wordlift/v1/faceted-search/template";
		$this->assertTrue( strpos( $result, $template_url) !== false, "Template url should be present in the faceted search, but got $result " );
	}


	public function  test_given_post_id_html_attributes_should_be_escaped_for_faceted_search_url() {
		$post_id = $this->factory()->post->create();
		$html = do_shortcode("[wl_faceted_search limit=10 post_id=$post_id]");
		$expected_url_output = "wordlift/v1/faceted-search&amp;post_id=$post_id&amp;limit=10";
		$this->assertTrue( strpos($html, $expected_url_output) !== false);
	}

}