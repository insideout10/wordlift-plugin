<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Assertions;
use Wordlift\Content\Content_Service;
use Wordlift\Object_Type_Enum;
use Wordlift_Configuration_Service;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
abstract class Abstract_Wordpress_Content_Service implements Content_Service {

	protected function __construct() {

	}

	protected function get_dataset_uri() {
		return trailingslashit( Wordlift_Configuration_Service::get_instance()->get_dataset_uri() );
	}

	protected function is_absolute( $uri ) {
		return 1 === preg_match( '@^https?://@', $uri );
	}

	protected function is_internal( $uri ) {
		$dataset_uri = $this->get_dataset_uri();

		return ! empty( $dataset_uri ) && 0 === strpos( $uri, $dataset_uri );
	}

	protected function make_absolute( $uri ) {
		Assertions::not_empty( $this->get_dataset_uri(), '`dataset_uri` cannot be empty.' );

		if ( 1 !== preg_match( '@^https?://@', $uri ) ) {
			return $this->get_dataset_uri() . $uri;
		}

		return $uri;
	}

	protected function make_relative( $uri ) {
		$dataset_uri = $this->get_dataset_uri();
		if ( 0 === strpos( $uri, $dataset_uri ) ) {
			return substr( $uri, strlen( $dataset_uri ) );
		}

		return $uri;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return string|null
	 */
	public function get_about_jsonld( $content_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT about_jsonld FROM {$wpdb->prefix}wl_entities
			WHERE content_id = %d AND content_type = %d
			",
				$content_id->get_id(),
				$content_id->get_type()
			)
		);
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 * @param string               $value
	 */
	public function set_about_jsonld( $content_id, $value ) {
		global $wpdb;

		// Cleanup value.
		$value = ( is_string( $value ) && strlen( $value ) > 2 ) ? $value : null;

		// This `hack` is necessary to ensure the entity exists in the entities table, but we
		// should revise how this works really.
		//
		// This is currently needed because rel_uri is required in the table.
		switch ( $content_id->get_type() ) {
			case Object_Type_Enum::POST:
				Wordpress_Dataset_Content_Service_Hooks::insert_post( $content_id->get_id() );
				break;
			case Object_Type_Enum::TERM:
				Wordpress_Dataset_Content_Service_Hooks::created_term( $content_id->get_id() );
				break;
			case Object_Type_Enum::USER:
				Wordpress_Dataset_Content_Service_Hooks::user_register( $content_id->get_id() );
				break;
		}

		/**
		 * As of May 16 2023, $wpdb:prepare doesnt support null
		 * values in about_jsonld, this results in NULL values being populated
		 * as `null` if we directly pass it to the prepare function(). So its necessary
		 * to make the query conditional based on the $value
		 */
		if ( null === $value ) {
			return $wpdb->query(
				$wpdb->prepare(
					"
			UPDATE {$wpdb->prefix}wl_entities
			SET about_jsonld = NULL
			WHERE content_id = %d AND content_type = %d
			",
					$content_id->get_id(),
					$content_id->get_type()
				)
			);
		}

		return $wpdb->query(
			$wpdb->prepare(
				"
			UPDATE {$wpdb->prefix}wl_entities
			SET about_jsonld = %s
			WHERE content_id = %d AND content_type = %d
			",
				$value,
				$content_id->get_id(),
				$content_id->get_type()
			)
		);
	}

}
