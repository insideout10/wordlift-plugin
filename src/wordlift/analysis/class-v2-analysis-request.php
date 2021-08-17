<?php
/**
 *
 * This file provides access to v2 analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

class V2_Analysis_Request {


	private $post_id;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * @return array
	 */
	public function get_data() {

		$preview_link = get_permalink( $this->post_id );

		// @TODO: remove this code.
		add_filter('https_ssl_verify', '__return_false');
		//  @TODO: remove this code.


		$post_content_response = wp_remote_get( $preview_link );

		$body = wp_remote_retrieve_body( $post_content_response );

		return array(
			'html'     => array( 'page' => $body ),
			'language' => \Wordlift_Configuration_Service::get_instance()->get_language_code(),
			'scope'    => 'all',
			'matches'  => 1,
			'links'    => 'no',
		);

	}

}
