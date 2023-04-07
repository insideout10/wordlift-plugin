<?php

namespace Wordlift\Modules\Gardening_Kg;

use Wordlift\Content\Content_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;

class Jsonld {

	/**
	 * @var Content_Service $content_service
	 */
	private $content_service;

	public function __construct( Content_Service $content_service ) {
		$this->content_service = $content_service;
	}

	public function register_hooks() {
		add_action( 'wl_post_jsonld', array( $this, 'post_jsonld' ), 10, 2 );
		add_action( 'wl_entity_jsonld', array( $this, 'post_jsonld' ), 10, 2 );
	}

	public function post_jsonld( $jsonld, $post_id ) {
		$about_jsonld = $this->content_service->get_about_jsonld(
			Wordpress_Content_Id::create_post( $post_id )
		);
		if ( empty( $jsonld ) ) {
			return $jsonld;
		}

		$jsonld['about'] = isset( $jsonld['about'] ) ? $jsonld['about'] : array();
		$jsonld['about'] = array_merge( $jsonld['about'], array( json_decode( $about_jsonld, true ) ) );
		return $jsonld;
	}

}
