<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Content\Content_Service;
use Wordlift\Object_Type_Enum;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Permalink_Content_Service implements Content_Service {

	private static $instance = null;

	protected function __construct() {
	}

	/**
	 * The singleton instance.
	 *
	 * @return Content_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param $uri string In the form https://example.org/path/post#post/1
	 *
	 * @return Wordpress_Content|null
	 */
	public function get_by_entity_id( $uri ) {
		if ( ! preg_match( '@.*#(\w+)/(\d+)@', $uri, $matches ) ) {
			return null;
		}

		$type_name = $matches[1];
		$id        = $matches[2];
		switch ( Object_Type_Enum::from_string( $type_name ) ) {
			case Object_Type_Enum::POST:
				return new Wordpress_Content( get_post( $id ) );
			case Object_Type_Enum::TERM:
				return new Wordpress_Content( get_term( $id ) );
			case Object_Type_Enum::USER:
				return new Wordpress_Content( get_user_by( 'ID', $id ) );
		}

		return null;
	}

	/**
	 * Get a
	 *
	 * @throws Exception in case of error.
	 */
	public function get_by_entity_id_or_same_as( $uri ) {
		// If the URL is in the local site URL, then try to find a corresponding post.
		if ( 0 === strpos( $uri, site_url() ) ) {
			return $this->get_by_entity_id( $uri );
		}

		// Otherwise look in sameAs.
		global $wpdb;

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"
			SELECT content_type, content_id
			FROM (
			    SELECT %d AS content_type, post_id AS content_id
			    FROM $wpdb->postmeta
			    WHERE meta_key = 'entity_same_as' AND meta_value = %s
			    UNION
			    SELECT %d AS content_type, term_id AS content_id
			    FROM $wpdb->termmeta
			    WHERE meta_key = 'entity_same_as' AND meta_value = %s
			    UNION
			    SELECT %d AS content_type, user_id AS content_id
			    FROM $wpdb->usermeta
			    WHERE meta_key = 'entity_same_as' AND meta_value = %s
			) _tmp_same_as 
			LIMIT 1
		",
				Object_Type_Enum::POST,
				$uri,
				Object_Type_Enum::TERM,
				$uri,
				Object_Type_Enum::USER,
				$uri
			)
		);

		if ( ! isset( $row ) ) {
			return null;
		}

		switch ( (int) $row->content_type ) {
			case Object_Type_Enum::POST:
				return new Wordpress_Content( get_post( $row->content_id ) );
			case Object_Type_Enum::TERM:
				return new Wordpress_Content( get_term( $row->content_id ) );
			case Object_Type_Enum::USER:
				return new Wordpress_Content( get_user_by( 'ID', $row->content_id ) );
			default:
				return null;
		}

	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|void|null
	 */
	public function get_entity_id( $content_id ) {
		$type = $content_id->get_type();
		$id   = $content_id->get_id();

		switch ( $type ) {
			case Object_Type_Enum::POST:
				$base_uri = get_permalink( $id );
				break;
			case Object_Type_Enum::TERM:
				$base_uri = get_term_link( $id );
				break;
			case Object_Type_Enum::USER:
				$base_uri = get_author_posts_url( $id );
				break;
			default:
				return null;
		}

		$type_name = Object_Type_Enum::to_string( $type );

		return "$base_uri#$type_name/$id";
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function set_entity_id( $content_id, $uri ) {
		// do nothing.
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool|void
	 */
	public function supports( $content_id ) {
		return in_array(
			$content_id->get_type(),
			array(
				Object_Type_Enum::POST,
				Object_Type_Enum::TERM,
				Object_Type_Enum::USER,
			),
			true
		);
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function delete( $content_id ) {
		// Ignore, we don't store any data.
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function get_about_jsonld( $content_id ) {
		// This is not implemented as of now
	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function set_about_jsonld( $content_id, $value ) {
		// This is not implemented as of now
	}
}
