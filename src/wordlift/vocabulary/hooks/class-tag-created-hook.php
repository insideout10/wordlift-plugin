<?php

namespace Wordlift\Vocabulary\Hooks;

use Cafemedia_Knowledge_Graph\Analysis_Background_Service;

class Tag_Created_Hook {

	/**
	 * @var $analysis_background_service Analysis_Background_Service
	 */
	private $analysis_background_service;

	/**
	 * Tag_Created_Hook constructor.
	 *
	 * @param $analysis_background_service Analysis_Background_Service
	 */
	public function __construct( $analysis_background_service ) {
		$this->analysis_background_service = $analysis_background_service;
		add_action( 'created_post_tag', array( $this, 'created_post_tag' ) );
	}

	public function created_post_tag() {
		$this->analysis_background_service->start();
	}

}