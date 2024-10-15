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

		$mentions = array();
		$term_ids = $this->get_term_ids( $post_id );

		foreach ( $term_ids as $term_id ) {
			$this->add_about_jsonld( $mentions, Wordpress_Content_Id::create_term( $term_id ) );
		}

		// Add also the post jsonld.
		$this->add_about_jsonld( $mentions, Wordpress_Content_Id::create_post( $post_id ) );

		$existing_mentions = array_key_exists( 'mentions', $jsonld ) ? $jsonld['mentions'] : array();

		if ( count( $mentions ) > 0 ) {
			$jsonld['mentions'] = array_merge( $existing_mentions, $mentions );
		}
		return $jsonld;
	}

	private function get_term_ids( $post_id ) {
		global $wpdb;
		$result = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT tt.term_id from {$wpdb->prefix}term_relationships r 
    INNER JOIN {$wpdb->prefix}term_taxonomy tt ON r.term_taxonomy_id=tt.term_taxonomy_id WHERE r.object_id= %d",
				$post_id
			)
		);

		return is_array( $result ) ? $result : array();
	}

	/**
	 * @param $list array Adds the jsonld to the list if valid.
	 * @param $content_id \Wordlift\Content\Wordpress\Wordpress_Content_Id
	 *
	 * @return void
	 */
	private function add_about_jsonld( &$list, $content_id ) {
		$about_jsonld   = $this->content_service->get_about_jsonld(
			$content_id
		);
		if ( empty( $about_jsonld ) ) {
			return;
		}

		$decoded_jsonld = json_decode( $about_jsonld, true );

		if ( null === $decoded_jsonld ) {
			return;
		}

		$value = wp_is_numeric_array( $decoded_jsonld ) ? $decoded_jsonld : array( $decoded_jsonld );

		$list = array_merge( $list, $value );

	}

}
