<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	Organization_Knowledge_Panel_Service
 *
 * A class that sets and sets the data for the Google Organization Panel API endpoints.
 *
 * @since      
 * @package Wordlift/modules/google-organization-kp
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Wordlift_Countries;
use Wordlift_Entity_Type_Service;
use Wordlift_Configuration_Service;
use Wordlift_Publisher_Service;
use Wordlift_Storage_Factory;
use Wordlift_Schema_Service;

Class Organization_Knowledge_Panel_Service {

    const PAGINATION_NUM_OF_PAGES = 100;

	private $extra_fields_service;

	private $publisher_service;

	private $entity_service;

	private $configuration_service;

	public function __construct( Organization_Extra_Fields_Service $extra_fields_service ) {
		$this->extra_fields_service  = $extra_fields_service;
		$this->publisher_service     = Wordlift_Publisher_Service::get_instance();
		$this->entity_service        = Wordlift_Entity_Type_Service::get_instance();
		$this->configuration_service = Wordlift_Configuration_Service::get_instance();
	}
	
	/**
	 * Get a list of pages
	 * 
	 * @param int    $pagination 		The pagination offset.
	 * @param string $title_starts_with Case instensitive filter for page titles.
	 *
	 * @return array Array of page IDs and titles.
	 * 
	 * @since 3.53.0
	 */
    public function get_pages( $pagination, $title_starts_with ) {
        // Get a number of pages starting at a given offset.
	    $pagination_no_of_pages = self::PAGINATION_NUM_OF_PAGES;

		$pages = get_pages(
			array(
				'number' => $pagination_no_of_pages,
				'offset' => (int) $pagination * $pagination_no_of_pages
			)
		);
        
		$data = array();

		// Arrange the data.
		foreach( $pages as $page ) {
			$data[] = array(
				'id'	=> $page->ID,
				'title' => $page->post_title
			);
		}

        // Filter the array to only contain items whose title starts with the `title_starts_with` param.
		if ( count($data) > 0 && isset( $title_starts_with ) ) {
			$filter_str = strtolower( $title_starts_with );
			$data = array_filter( 
				$data,
				function( $page ) use ( $filter_str ) {
					// Check that the start of the string matches the filter string.
					return substr( strtolower($page['title']), 0, strlen($filter_str)) === $filter_str;
				}
			);
		}

		return $data;
    }

	/**
	 * Get an associative array of country codes and country names in the language of the website.
	 * 
	 * @return array Country codes and names.
	 *
	 * @since 3.53.0
	 */
    public function get_countries() {
		// Get the list of countries in the current site language.
        $countries = Wordlift_Countries::get_countries();

		$data = array();

		// Arrange the data.
		foreach( $countries as $country_code => $country_name ) {
			$data[] = array(
				'code' => $country_code,
				'name' => $country_name
			);
		}

        return $data;
    }

	/**
	 * Get relevant useful data if a publisher already exists
	 * 
	 * @return array Publisher data.
	 * 
	 * @since 3.53.0
	 */
    public function get_form_data() {
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return array();
		}

	    $publisher_id = $this->configuration_service->get_publisher_id();

        $data = array();

		$publisher_post      = get_post( $publisher_id );
		$publisher_entity    = $this->entity_service->get( $publisher_id );
		$publisher_logo      = $this->publisher_service->get_publisher_logo( $publisher_id );
//		$storage_factory     = Wordlift_Storage_Factory::get_instance();

		// Add base Publisher fields
		$data['id']          = $publisher_id;                    // ID.
		$data['name']        = $publisher_post->post_title;      // Name
		$data['type']        = $publisher_entity['label'];       // Type
		$data['description'] = $publisher_entity['description']; // Description

		// Add the logo
		if ( ! empty( $publisher_logo ) ) {
			$data['logo'] = $publisher_logo['url'];
		}

		// Add custom fields.
	    $custom_fields = array(
			Wordlift_Schema_Service::FIELD_SAME_AS,
		    Wordlift_Schema_Service::FIELD_ADDRESS,
		    Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY,
		    Wordlift_Schema_Service::FIELD_ADDRESS_REGION,
		    Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY,
		    Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE,
		    Wordlift_Schema_Service::FIELD_TELEPHONE,
		    Wordlift_Schema_Service::FIELD_EMAIL
	    );

		foreach ( $custom_fields as $field_slug ) {
			// @todo: Is there a downside to using get_post_meta as opposed to Wordlift_Storage_Factory?
//			$field_data = $storage_factory
//				->post_meta( $field_key )
//				->get( $publisher_id )[0];
			$field_data = get_post_meta( $publisher_id, $field_slug, true );

			if ( ! empty( $field_data ) ) {
				$data[$field_slug] = $field_data;
			}
		}

		// Get extra organization fields.
	    $organization_extra_field_data = $this->extra_fields_service->get_all_field_data();

		// Return all field data.
		return array_merge( $data, $organization_extra_field_data );
    }

	/**
	 * Set the Organization data.
	 *
	 * @return WP_Error|WP_REST_Response Response with status code and message.
	 *
	 * @since 3.53.0
	 */
    public function set_form_data( $params ) {

		// Publisher doesn't exist, create one.
		if ( ! $this->publisher_service->is_publisher_set() && ! empty( $params['id'] ) ) {

			// Parameters required to create a new publisher are missing, so return an error.
			if ( empty( $params['type'] ) || empty( $params['name'] ) || empty( $params['image'] ) ) {
				return new WP_Error( '400', 'Required paramaters not provided.', array( 'status' => 400 ) );
			}

			// Set the type URI, http://schema.org/ + Person, Organization, localBusiness or Organization.
			$type_uri = sprintf( 'http://schema.org/%s', $params['type'] );

			// Create an entity for the publisher.
			$publisher_post_id = $this->entity_service->create( $params['name'], $type_uri, $params['image'], 'publish' );

			// Store the publisher entity post id in the configuration.
			$this->configuration_service->set_publisher_id( $publisher_post_id );

			flush_rewrite_rules(); // Needed because of possible change to the entity base path.
		}

		// @todo: Does this make sense?
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return new WP_Error( '400', 'Unable to set publisher.', array( 'status' => 400 ) );
		}

		// Success, return 200 with the Publisher ID in Data.
		return new WP_REST_Response;
    }
}