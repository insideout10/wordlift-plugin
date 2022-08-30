<?php

/**
 * # create table UFeCMp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 * # create table ufecmp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 */

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;
use Wordlift_Entity_Service;
use Wordlift_Schema_Service;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Post_Content_Table_Service extends Abstract_Wordpress_Content_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Post_Content_Table_Service
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
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

		if ( $this->is_absolute( $uri ) && ! $this->is_internal( $uri ) ) {
			throw new Exception( '`uri` must be within the dataset URI scope.' );
		}

		$rel_uri = $this->make_relative( $uri );

		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"
			SELECT content_type, content_id
			FROM {$wpdb->prefix}wl_entities
			WHERE rel_uri = %s
		",
				$rel_uri
			)
		);

		if ( ! isset( $row ) || Object_Type_Enum::POST !== (int) $row->content_type ) {
			return null;
		}

		return new Wordpress_Content( get_post( $row->content_id ) );
	}

	/**
	 * @throws Exception in case of error. when `$uri` is not a string.
	 */
	public function get_by_entity_id_or_same_as( $uri ) {
		Assertions::is_string( $uri, '`uri` must be a string.' );
		Assertions::not_empty( '`uri` cannot be empty.' );

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

		if ( is_a( $post, 'WP_Post' ) ) {
			return new Wordpress_Content( current( $posts ) );
		}

		return null;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|null The entity ID.
	 * @throws Exception in case of error.
	 */
	public function get_entity_id( $content_id ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::POST, '`content_id` must be of type post.' );

		global $wpdb;
		$rel_uri = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT rel_uri
			FROM {$wpdb->prefix}wl_entities
			WHERE content_id = %d AND content_type = %d
		",
				$content_id->get_id(),
				$content_id->get_type()
			)
		);

		return $rel_uri ? $this->make_absolute( $rel_uri ) : null;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 * @param string               $uri
	 *
	 * @return void
	 * @throws Exception in case of error.
	 */
	public function set_entity_id( $content_id, $uri ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::POST, '`content_id` must be of type post.' );
		Assertions::not_empty( $uri, "`uri` can't be empty" );

		if ( $this->is_absolute( $uri ) && ! $this->is_internal( $uri ) ) {
			throw new Exception( '`uri` must be within the dataset URI scope.' );
		}

		$rel_url = $this->make_relative( $uri );

		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"
			INSERT INTO {$wpdb->prefix}wl_entities( content_id, content_type, rel_uri, rel_uri_hash )
			VALUES( %d, %d, %s, SHA1( %s ) )
			ON DUPLICATE KEY UPDATE rel_uri = VALUES( rel_uri ), rel_uri_hash = SHA1( VALUES( rel_uri ) );
		",
				$content_id->get_id(),
				$content_id->get_type(),
				$rel_url,
				$rel_url
			)
		);
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool
	 */
	public function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::POST;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return void
	 */
	public function delete( $content_id ) {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"
			DELETE FROM {$wpdb->prefix}wl_entities
			WHERE content_id = %d AND content_type = %d
		",
				$content_id->get_id(),
				$content_id->get_type()
			)
		);
	}
}
