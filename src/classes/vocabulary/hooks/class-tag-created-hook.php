<?php

namespace Wordlift\Vocabulary\Hooks;

use Wordlift\Vocabulary\Analysis_Background_Service;

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
		$taxonomies                        = get_taxonomies( array( 'public' => true ) );
		foreach ( $taxonomies as $taxonomy ) {
			add_action( "created_${taxonomy}", array( $this, 'created_term' ) );
		}
	}

	public function created_term() {
		$this->analysis_background_service->start();
	}
}
