<?php
/**
 * Test the Content Analysis.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift/Tests
 * @since 3.24.2
 * @group ajax
 */

class Content_Analysis_Test extends Wordlift_Ajax_Unit_Test_Case {

	public function test() {

		// Create an entity, by also setting its entity URL and type.
		$post_id = $this->factory()->post->create( array(
			'post_type'   => 'entity',
			'post_title'  => 'Content Analysis Test 1',
			'post_status' => 'publish',
		) );
		update_post_meta( $post_id, 'entity_url', 'http://example.org/content_analysis_test_1' );
		wp_add_object_terms( $post_id, 'thing', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$_POST = array(
			'action' => 'wl_analyze',
			'_wpnonce' => wp_create_nonce( 'wl_analyze' ),
			'data'   => file_get_contents( dirname( __FILE__ ) . 'assets/content-analysis-request-1.json' ),
		);

		try {
			$this->_handleAjax( 'wl_analyze' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );

		$this->assertTrue( is_array( $response ), 'We expect `response` to be an `array`, instead got: ' . var_export( $this->_last_response, true ) );
		$this->assertArrayHasKey( 'success', $response,
			'We expect the response to contain a `success` key, instead got: ' . var_export( $response, true ) );
		$this->assertTrue( $response['success'], 'Success must be `true`.' );

	}

}
