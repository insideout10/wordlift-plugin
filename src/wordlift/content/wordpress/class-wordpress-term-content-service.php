<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Object_Type_Enum;

class Wordpress_Term_Content_Service extends Abstract_Wordpress_Content_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Term_Content_Service
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @param string $uri The entity id, relative or absolute.
	 *
	 * @return Wordpress_Content|null The term encapsulate within a {@link Wordpress_Content} structure or null.
	 * @throws Exception when the uri is not within the dataset URI scope.
	 */
	function get_by_entity_id( $uri ) {
		$abs_uri = $this->make_absolute( $uri );

		Assertions::starts_with( $abs_uri, $this->get_dataset_uri(), '`uri` must be within the dataset URI scope.' );

		global $wpdb;

		$term_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT t.term_id 
			FROM $wpdb->terms AS t 
			INNER JOIN $wpdb->termmeta AS tm
			    ON t.term_id = tm.term_id
			WHERE tm.meta_key = 'entity_url' AND tm.meta_value = %s
			LIMIT 1
		", $abs_uri ) );

		if ( isset( $term_id ) ) {
			return new Wordpress_Content( get_term( $term_id ) );
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

		global $wpdb;

		$term_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT t.term_id 
			FROM $wpdb->terms AS t 
			INNER JOIN $wpdb->termmeta AS tm
			    ON t.term_id = tm.term_id
			WHERE tm.meta_key IN ( 'entity_url', 'entity_same_as' ) AND tm.meta_value = %s
			LIMIT 1
		", $uri ) );

		if ( isset( $term_id ) ) {
			return new Wordpress_Content( get_term( $term_id ) );
		}

		return null;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|null
	 * @throws Exception if the content is not a term.
	 */
	function get_entity_id( $content_id ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::TERM, 'Content must be of `term` type.' );

		$uri = get_term_meta( $content_id->get_id(), WL_ENTITY_URL_META_NAME, true ) ?: null;

		if ( ! isset( $uri ) ) {
			$uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
			$this->set_entity_id( $content_id, $uri );
		}

		return $uri;
	}

	function set_entity_id( $content_id, $uri ) {
		Assertions::equals( $content_id->get_type(), Object_Type_Enum::TERM, 'Content must be of `term` type.' );
		Assertions::not_empty( $uri, "`uri` can't be empty" );

		if ( $this->is_absolute( $uri ) && ! $this->is_internal( $uri ) ) {
			throw new Exception( '`uri` must be within the dataset URI scope.' );
		}

		$abs_url = $this->make_absolute( $uri );

		update_term_meta( $content_id->get_id(), WL_ENTITY_URL_META_NAME, $abs_url );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return bool
	 */
	function supports( $content_id ) {
		return $content_id->get_type() === Object_Type_Enum::TERM;
	}

}