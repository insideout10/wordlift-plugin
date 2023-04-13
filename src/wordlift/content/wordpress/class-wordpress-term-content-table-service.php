<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Term_Content_Table_Service extends Abstract_Wordpress_Content_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Term_Content_Table_Service
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

		if ( ! isset( $row ) || Object_Type_Enum::TERM !== (int) $row->content_type ) {
			return null;
		}

		return new Wordpress_Content( get_term( $row->content_id ) );
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

		global $wpdb;

		$term_id = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT tm.term_id
			FROM $wpdb->termmeta tm
			WHERE tm.meta_key IN ( 'entity_url', 'entity_same_as' ) AND tm.meta_value = %s
			LIMIT 1
		",
				$uri
			)
		);

		if ( isset( $term_id ) ) {
			return new Wordpress_Content( get_term( $term_id ) );
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
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::TERM, '`content_id` must be of type term.' );

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
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::TERM, '`content_id` must be of type term.' );
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
		return $content_id->get_type() === Object_Type_Enum::TERM;
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
