<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_User_Content_Legacy_Service extends Abstract_Wordpress_Content_Legacy_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_User_Content_Legacy_Service
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( Object_Type_Enum::USER, 'get_user_meta' );
		}

		return self::$instance;
	}

	public function get_by_entity_id( $uri ) {
		Assertions::is_string( $uri, '`uri` must be a string.' );
		Assertions::not_empty( $uri, '`uri` cannot be empty.' );
		Assertions::not_empty( $this->get_dataset_uri(), '`dataset_uri` cannot be empty.' );

		$abs_uri = $this->make_absolute( $uri );

		Assertions::starts_with( $abs_uri, $this->get_dataset_uri(), '`uri` must be within the dataset URI scope.' );

		global $wpdb;

		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT u.ID 
			FROM $wpdb->users AS u
			INNER JOIN $wpdb->usermeta AS um
			    ON u.ID = um.user_id
			WHERE um.meta_key = 'entity_url' AND um.meta_value = %s
			LIMIT 1
		",
				$abs_uri
			)
		);

		if ( isset( $user_id ) ) {
			return new Wordpress_Content( get_userdata( $user_id ) );
		}

		return null;
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

		$user_id = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT u.ID 
			FROM $wpdb->users AS u
			INNER JOIN $wpdb->usermeta AS um
			    ON u.ID = um.user_id
			WHERE um.meta_key IN ( 'entity_url', 'entity_same_as' ) AND um.meta_value = %s
			LIMIT 1
		",
				$uri
			)
		);

		if ( isset( $user_id ) ) {
			return new Wordpress_Content( get_userdata( $user_id ) );
		}

		return null;
	}

	public function set_entity_id( $content_id, $uri ) {
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
	public function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::USER;
	}

}
