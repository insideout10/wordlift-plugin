<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Content\Content_Service;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Object_Type_Enum;
use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

class Wordpress_Post_Content_Legacy_Service extends Abstract_Wordpress_Content_Service {

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Post_Content_Legacy_Service
	 * @deprecated
	 */
	public static function get_instance() {
		return self::$instance;
	}

	private static $instance;

	/**
	 * Create an instance of the {@link Content_Service}.
	 *
	 * @param string $dataset_uri The dataset URI.
	 *
	 * @throws Exception when the arguments are invalid.
	 */
	public function __construct( $dataset_uri ) {
		parent::__construct( $dataset_uri );

		self::$instance = $this;
	}

	/**
	 * @param string $uri An absolute or relative URI. When absolute it must be within the dataset URI scope.
	 *
	 * @return Wordpress_Content|null
	 * @throws Exception when the URI is not within the dataset URI.
	 */
	function get_by_entity_id( $uri ) {
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
				'key'     => WL_ENTITY_URL_META_NAME,
				'value'   => $uri,
				'compare' => '=',
			),
		);

		$posts = get_posts( $query_args );

		// Get the current post or allow 3rd parties to provide a replacement.
		$post = current( $posts ) ?: apply_filters( 'wl_content_service__post__not_found', null, $uri );

		if ( isset( $post ) ) {
			return new Wordpress_Content( current( $posts ) );
		}

		return null;
	}

	/**
	 * @throws Exception when `$uri` is not a string.
	 */
	function get_by_entity_id_or_same_as( $uri ) {
		Assertions::is_string( $uri, '`uri` must be a string.' );

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
				'key'     => Wordlift_Schema_Service::FIELD_SAME_AS,
				'value'   => $uri,
				'compare' => '=',
			),
		);

		$posts = get_posts( $query_args );

		// Get the current post or allow 3rd parties to provide a replacement.
		$post = current( $posts ) ?: apply_filters( 'wl_content_service__post__not_found', null, $uri );

		if ( isset( $post ) ) {
			return new Wordpress_Content( current( $posts ) );
		}

		return null;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|null The entity ID.
	 * @throws Exception
	 */
	function get_entity_id( $content_id ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::POST, '`content_id` must be of type post.' );

		$abs_uri = get_post_meta( $content_id->get_id(), 'entity_url', true ) ?: null;

		if ( ! isset( $abs_uri ) ) {
			$rel_uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			$this->set_entity_id( $content_id, $rel_uri );
			$abs_uri = $this->make_absolute( $rel_uri );
		}

		return $abs_uri;
	}

	function set_entity_id( $content_id, $uri ) {
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
	function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::POST;
	}

}