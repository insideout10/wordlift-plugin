<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;
use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Post_Content_Legacy_Service extends Abstract_Wordpress_Content_Legacy_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Post_Content_Legacy_Service
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Object_Type_Enum::POST, 'get_post_meta' );
		}

		return self::$instance;
	}

	/**
	 * @param string $uri An absolute or relative URI. When absolute it must be within the dataset URI scope.
	 *
	 * @return Wordpress_Content|null
	 * @throws Exception in case of error. when the URI is not within the dataset URI.
	 */
	public function get_by_entity_id( $uri ) {
		Assertions::is_string( $uri, '`uri` must be a string.' );
		Assertions::not_empty( $uri, '`uri` cannot be empty.' );
		Assertions::not_empty( $this->get_dataset_uri(), '`dataset_uri` cannot be empty.' );

		$abs_uri = $this->make_absolute( $uri );

		Assertions::starts_with( $abs_uri, $this->get_dataset_uri(), '`uri` must start with dataset URI.' );

		// Look in sameAs.
		$query_args = array(
			// See https://github.com/insideout10/wordlift-plugin/issues/654.
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 1,
			'post_status'         => 'any',
			'post_type'           => Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'          => array(
				array(
					'key'     => WL_ENTITY_URL_META_NAME,
					'value'   => $abs_uri,
					'compare' => '=',
				),
			),
		);

		$posts = get_posts( $query_args );

		// Get the current post or allow 3rd parties to provide a replacement.
		$post = current( $posts );
		$post = $post ? $post : apply_filters( 'wl_content_service__post__not_found', null, $uri );

		if ( is_a( $post, 'WP_Post' ) ) {
			return new Wordpress_Content( current( $posts ) );
		}

		return null;
	}

	/**
	 * @throws Exception in case of error. when `$uri` is not a string.
	 */
	public function get_by_entity_id_or_same_as( $uri ) {
		// ATM we're too strict with the assertions here so we return null if the uri isn't provided.
		// Assertions::is_string( $uri, '`uri` must be a string.' );
		// Assertions::not_empty( '`uri` cannot be empty.' );
		if ( ! is_string( $uri ) || empty( $uri ) ) {
			return null;
		}

		// If it's a relative URI, or it's an internal URI, look in entity ID.
		if ( ! $this->is_absolute( $uri ) || $this->is_internal( $uri ) ) {
			return $this->get_by_entity_id( $uri );
		}

		// Look in sameAs.
		$query_args = array(
			// See https://github.com/insideout10/wordlift-plugin/issues/654.
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 1,
			'post_status'         => 'any',
			'post_type'           => Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'          => array(
				array(
					'key'     => Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => $uri,
					'compare' => '=',
				),
			),
		);

		$posts = get_posts( $query_args );

		// Get the current post or allow 3rd parties to provide a replacement.
		$post = current( $posts );
		$post = $post ? $post : apply_filters( 'wl_content_service__post__not_found', null, $uri );

		if ( is_a( $post, '\WP_Post' ) ) {
			return new Wordpress_Content( current( $posts ) );
		}

		return null;
	}

	public function set_entity_id( $content_id, $uri ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::POST, '`content_id` must be of type post.' );
		Assertions::not_empty( $uri, "`uri` can't be empty" );

		if ( $this->is_absolute( $uri ) && ! $this->is_internal( $uri ) ) {
			throw new Exception( '`uri` must be within the dataset URI scope.' );
		}

		$abs_url = $this->make_absolute( $uri );

		update_post_meta( $content_id->get_id(), WL_ENTITY_URL_META_NAME, $abs_url );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool
	 */
	public function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::POST;
	}

}
