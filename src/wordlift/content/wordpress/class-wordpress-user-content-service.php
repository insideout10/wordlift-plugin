<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Object_Type_Enum;

class Wordpress_User_Content_Service extends Abstract_Wordpress_Content_Service {

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_User_Content_Service
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

	function get_by_entity_id( $uri ) {
		// @@todo: implement
		throw new Exception( 'Not implemented' );
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

		// @@todo implement
		throw new Exception( 'Not implemented.' );

		return null;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|null The entity ID.
	 * @throws Exception
	 */
	function get_entity_id( $content_id ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::USER, '`content_id` must be of type user.' );

		$uri = get_user_meta( $content_id->get_id(), WL_ENTITY_URL_META_NAME, true ) ?: null;

		if ( ! isset( $uri ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			$this->set_entity_id( $content_id, $uri );
		}

		return $uri;
	}

	function set_entity_id( $content_id, $uri ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::USER, '`content_id` must be of type user.' );
		Assertions::not_empty( $uri, "`uri` can't be empty" );

		if ( $this->is_absolute( $uri ) && ! $this->is_internal( $uri ) ) {

			throw new Exception( '`uri` must be within the dataset URI scope.' );
		}

		$abs_url = $this->make_absolute( $uri );

		update_user_meta( $content_id->get_id(), WL_ENTITY_URL_META_NAME, $abs_url );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool
	 */
	function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::USER;
	}

}