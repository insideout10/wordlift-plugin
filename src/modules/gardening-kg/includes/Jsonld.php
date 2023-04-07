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
		// Note: priority set to 11 in order to run after the term jsonld is generated.
		add_filter( 'wl_term_jsonld_array', array( $this, 'term_jsonld' ), 11, 2 );
	}

	public function post_jsonld( $jsonld, $post_id ) {

		if ( empty( $jsonld ) ) {
			return $jsonld;
		}

		$this->add_about_jsonld( $jsonld, Wordpress_Content_Id::create_post( $post_id ) );

		return $jsonld;
	}

	public function term_jsonld( $arr, $term_id ) {
		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		if ( count( $jsonld ) === 0 ) {
			return $arr;
		}

		// Remove first item from term jsonld.
		$term_jsonld = array_shift( $jsonld );

		$this->add_about_jsonld( $term_jsonld, Wordpress_Content_Id::create_term( $term_id ) );

		// Add back the term jsonld.
		array_unshift( $jsonld, $term_jsonld );

		return array(
			'jsonld'     => $jsonld,
			'references' => $references,
		);
	}

	/**
	 * @param $jsonld_item array A single jsonld entry ( not an numeric array )
	 * @param $content_id \Wordlift\Content\Wordpress\Wordpress_Content_Id
	 *
	 * @return void
	 */
	private function add_about_jsonld( &$jsonld_item, $content_id ) {
		$about_jsonld   = $this->content_service->get_about_jsonld(
			$content_id
		);
		$decoded_jsonld = json_decode( $about_jsonld, true );
		$value          = wp_is_numeric_array( $decoded_jsonld ) ? $decoded_jsonld : array( $decoded_jsonld );

		$jsonld_item['about'] = isset( $jsonld_item['about'] ) ? $jsonld_item['about'] : array();
		$jsonld_item['about'] = array_merge( $jsonld_item['about'], $value );

	}

}
