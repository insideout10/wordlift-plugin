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

		$permalink = get_permalink( $this->post_id );

		$post_content_response = wp_remote_get(
			$permalink,
			array(
				'timeout' => 30,
			)
		);

		$page_body = wp_remote_retrieve_body( $post_content_response );

		return array(
			'html'     => array( 'page' => $page_body ),
			'language' => \Wordlift_Configuration_Service::get_instance()->get_language_code(),
			'scope'    => 'all',
			'matches'  => 1,
			'links'    => 'no',
		);

	}

}
