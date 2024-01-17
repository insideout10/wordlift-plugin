<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	About_Page_Organization_Filter
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use Wordlift\Modules\Google_Organization_Kp\Organization_Extra_Fields_Service;
use Wordlift\Relation\Relations;
use Wordlift_Configuration_Service;
use Wordlift_Entity_Service;
use Wordlift_Post_To_Jsonld_Converter;
use Wordlift_Publisher_Service;
use Wordlift_Storage_Factory;
use Wordlift_Schema_Service;

class About_Page_Organization_Filter {
	/**
	 * Initialize hooks.
	 *
	 * `wl_after_get_jsonld` is the main  filter hook and gets called on almost all JSON-LD conversions.
	 * However, on `WebSite` conversion this hook gets bypassed, so we also hook into `wl_website_jsonld`.
	 *
	 * @since 3.53.0
	 */
	public function init() {
		add_filter( 'wl_website_jsonld', array( $this, '_wl_website_jsonld__add_organization_jsonld' ), 10, 3 );
		add_filter( 'wl_after_get_jsonld', array( $this, '_wl_after_get_jsonld__add_organization_jsonld' ), 10, 3 );
	}


	/**
	 * Callback function for the `wl_website_jsonld` filter hook.
	 *
	 * Forwards to $this->add_organization_jsonld.
	 *
	 * @param $jsonld  array JSON-LD structure.
	 * @param $post_id int   Post ID.
	 *
	 * @return array
	 *
	 * @since 3.53.0
	 */
	public function _wl_website_jsonld__add_organization_jsonld( $jsonld, $post_id ) {
		return $this->add_organization_jsonld( $jsonld, $post_id );
	}

	/**
	 * Callback function for the `wl_after_get_jsonld` filter hook.
	 *
	 * Used so that the extra parameter can be dropped before forwarding to main callback for both hooks.
	 *
	 * Forwards to $this->add_organization_jsonld.
	 *
	 * @param $jsonld  array  JSON-LD structure.
	 * @param $post_id int    Post ID.
	 * @param $context string Schema.org context.
	 *
	 * @return array
	 *
	 * @since 3.53.0
	 */
	public function _wl_after_get_jsonld__add_organization_jsonld( $jsonld, $post_id, $context ) {
		return $this->add_organization_jsonld( $jsonld, $post_id );
	}

	/**
	 * Utility function to check if the provided $post_id is the `About Us` page specific in the options.
	 *
	 * @param $post_id int The post ID.
	 *
	 * @return bool
	 *
	 * @since 3.53.0
	 */
	public function is_about_page( $post_id ) {
		$about_page_id = get_option('wl_about_page_id');

		if ( ! $about_page_id || empty( $about_page_id ) ) {
			return false;
		}

		return $about_page_id === $post_id;
	}

	/**
	 * Utility function to check if the Publisher exists in the JSON-LD structure already.
	 *
	 * @param $jsonld       array JSON-LD structure.
	 * @param $publisher_id int   Publisher Post ID.
	 *
	 * @return bool
	 *
	 * @since 3.53.0
	 */
	public function is_publisher_entity_in_graph( $jsonld, $publisher_id ) {
		$publisher_uri = Wordlift_Entity_Service::get_instance()->get_uri( $publisher_id );

		foreach ( $jsonld as $item ) {
			if ( $item && array_key_exists( '@id', $item ) && $item['@id'] === $publisher_uri ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add the extra fields to the Publisher JSON-LD structure.
	 *
	 * @param &$publisher_jsonld array Reference to the Publisher JSON-LD array within the main JSON-LD array.
	 * @param $publisher_id      int   The Publisher Post ID.
	 *
	 * @since 3.53.0
	 */
	public function expand_publisher_jsonld( &$publisher_jsonld, $publisher_id ) {
		$schema_service                   = Wordlift_Schema_Service::get_instance();
		$storage_factory                  = Wordlift_Storage_Factory::get_instance();
		$organization_extra_field_service = Organization_Extra_Fields_Service::get_instance();

		// Get custom fields.

		// @todo: Some missing here.

		$street_address = $storage_factory->post_meta( $schema_service::FIELD_ADDRESS )->get( $publisher_id )[0];
		$locality       = $storage_factory->post_meta( $schema_service::FIELD_ADDRESS_LOCALITY )->get( $publisher_id )[0];
		$region         = $storage_factory->post_meta( $schema_service::FIELD_ADDRESS_REGION )->get( $publisher_id )[0];
		$country        = $storage_factory->post_meta( $schema_service::FIELD_ADDRESS_COUNTRY )->get( $publisher_id )[0];
		$postal_code    = $storage_factory->post_meta( $schema_service::FIELD_ADDRESS_POSTAL_CODE )->get( $publisher_id )[0];
		$telephone      = $storage_factory->post_meta( $schema_service::FIELD_TELEPHONE )->get( $publisher_id )[0];
		$email          = $storage_factory->post_meta( $schema_service::FIELD_EMAIL )->get( $publisher_id )[0];

		// Set custom fields.

		// If all address fields are available, build the `address` property with its sub properties.
		if (
			! empty( $street_address )
			&& ! empty( $locality )
			&& ! empty( $region )
			&& ! empty( $country )
			&& ! empty( $postal_code )
		) {
			$publisher_jsonld['address'] = array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street_address,
				'addressLocality' => $locality,
				'addressCountry'  => $region,
				'addressRegion'   => $country,
				'postalCode'      => $postal_code
			);
		}

		// Add telephone if set.
		if ( ! empty( $telephone ) ) {
			$publisher_jsonld['telephone'] = $telephone;
		}

		// Add email if set.
		if ( ! empty( $email ) ) {
			$publisher_jsonld['email'] = $email;
		}

		// Set extra fields.

		$extra_fields = $organization_extra_field_service->get_all_field_data();

		foreach( $extra_fields as $field_slug => $field_value ) {
			$field_label = $organization_extra_field_service->get_field_label( $field_slug );
			$publisher_jsonld[$field_label] = $field_value;
		}
	}

	/**
	 * Main callback for the filter hooks.
	 *
	 * Conditionally add the Organization data if we are on the `About Us` page, or if
	 * no `About Us` page is set and we are on the home page.
	 *
	 * @param $jsonld  array JSON-LD structure.
	 * @param $post_id int   Post ID.
	 *
	 * @return mixed
	 *
	 * @since 3.53.0
	 */
	public function add_organization_jsonld( $jsonld, $post_id ) {
		// Exit if the Publisher is not set or correctly configured.
		if ( ! Wordlift_Publisher_Service::get_instance()->is_publisher_set() ) {
			return $jsonld;
		}

		$is_about_us = $this->is_about_page( $post_id );
		$is_homepage = is_home() || is_front_page();

		// Return when we are not looking at `About Us` page, or the `Home Page` when `About Us` is not set.
		if ( ! ( $is_about_us || ! $is_about_us && $is_homepage ) ) {
			return $jsonld;
		}

		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		// Add publisher to the JSON-LD if it doesn't exist in the graph.
		if ( ! $this->is_publisher_entity_in_graph( $jsonld, $publisher_id ) ) {
			// Get the Publisher data
			$references     = array();
			$reference_info = array();
			$relations      = new Relations();

			$publisher_jsonld = Wordlift_Post_To_Jsonld_Converter::get_instance()->convert(
				$publisher_id,
				$references,
				$reference_info,
				$relations
			);

			// Add a reference to the publisher in the main Entity of the JSON-LD.
			$jsonld[0]['publisher'] = array(
				'@id' => $publisher_jsonld['@id']
			);

			$jsonld[] = $publisher_jsonld;
		}

		// Check all items in the JSON-LD array expand Publisher when found.
		$publisher_types = array(
			'Person',
			'Organization',
			'localBusiness',
			'onlineBusiness'
		);

		foreach( $jsonld as &$jsonld_item ) {
			if ( $jsonld_item && array_key_exists( '@type', $jsonld_item ) && in_array( $jsonld_item['@type'], $publisher_types ) ) {
				$this->expand_publisher_jsonld( $jsonld_item, $publisher_id );
			}
		}

		return $jsonld;
	}
}