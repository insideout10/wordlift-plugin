<?php
/**
 *
 * This file provides access to v2 analysis service.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

class No_Editor_Analysis_Request {


	private $post_id;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * @return array
	 */
	public function get_data() {

		$preview_link = get_preview_post_link( $this->post_id );

		$post_content = wp_remote_get( $preview_link );

		return array(
			'html'     => array( 'page' => $post_content ),
			'language' => \Wordlift_Configuration_Service::get_instance()->get_language_code(),
			'scope'    => 'all',
			'matches'  => 1,
			'links'    => 'no',
		);

	}

}
