<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Content\Content_Service;
use Wordlift\Object_Type_Enum;

class Wordpress_Permalink_Content_Service implements Content_Service {

	private static $instance = null;

	protected function __constructor() {
	}

	/**
	 * The singleton instance.
	 *
	 * @return Content_Service
	 * @throws Exception
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	function get_by_entity_id( $uri ) {
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
	 * @throws Exception
	 */
	function get_by_entity_id_or_same_as( $uri ) {
		return $this->get_by_entity_id( $uri );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|void|null
	 */
	function get_entity_id( $content_id ) {
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

	function set_entity_id( $content_id, $uri ) {
		throw new Exception( 'Not supported' );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool|void
	 */
	function supports( $content_id ) {
		return in_array( $content_id->get_type(), array(
			Object_Type_Enum::POST,
			Object_Type_Enum::TERM,
			Object_Type_Enum::USER,
		) );
	}

}
