<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	Organization_Knowledge_Panel_Service
 *
 * A class that sets and sets the data for the Google Organization Panel API endpoints.
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use Wordlift_Schema_Service;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

Class Organization_Knowledge_Panel_Service {

	/**
	 * Constant for the number of pages that should be returned in the paginated /page GET request.
	 *
	 * @var PAGINATION_NUM_OF_PAGES
	 */
    const PAGINATION_NUM_OF_PAGES = 100;

	/**
	 * @var Wordlift_Countries
	 */
	private $countries;

	/**
	 * @var Organization_Extra_Fields_Service
	 */
	private $extra_fields_service;

	/**
	 * @var Wordlift_Publisher_Service
	 */
	private $publisher_service;

	/**
	 * @var Wordlift_Entity_Type_Service
	 */
	private $entity_service;

	/**
	 * @var Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @var Wordlift_Schema_Service
	 */
	private $schema_service;

	/**
	 * @param Wordlift_Countries                $countries
	 * @param Organization_Extra_Fields_Service $extra_fields_service
	 * @param Wordlift_Publisher_Service        $publisher_service
	 * @param Wordlift_Entity_Type_Service      $entity_service
	 * @param Wordlift_Configuration_Service    $configuration_service
	 * @param Wordlift_Schema_Service           $schema_service
	 */
	public function __construct(
		$countries,
		$extra_fields_service,
		$publisher_service,
		$entity_service,
		$configuration_service,
		$schema_service
	) {
		$this->countries             = $countries;
		$this->extra_fields_service  = $extra_fields_service;
		$this->publisher_service     = $publisher_service;
		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
		$this->schema_service        = $schema_service;
	}
	
	/**
	 * Get a list of pages.
	 * 
	 * @param int    $pagination 		Pagination offset.
	 * @param string $title_starts_with Case-insensitive filter for page titles.
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
        $countries = $this->countries->get_countries();

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
//	    $schema_service = $this->schema_service;
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
	 * @param array $params The parameters sent via POST.
	 *
	 * @return WP_Error|WP_REST_Response Response with status code and message.
	 *
	 * @since 3.53.0
	 */
    public function set_form_data( $params ) {
		// Break out if no parameters were provided.
		if ( empty( $params ) ) {
			return new WP_Error(
				'400',
				'No parameters provided.',
				array( 'status' => 400 )
			);
		}

		// Publisher doesn't exist, create one.
		if ( ! $this->publisher_service->is_publisher_set() ) {
			// Parameters required to create a new publisher are missing, so return an error.
			if ( empty( $params['id'] ) || empty( $params['type'] ) || empty( $params['name'] ) || empty( $params['image'] ) ) {
				return new WP_Error(
					'400',
					'Required parameters not provided.',
					array( 'status' => 400 )
				);
			}

			// Set the type URI, http://schema.org/ + Person, Organization, localBusiness or Organization.
			$type_uri = sprintf( 'http://schema.org/%s', $params['type'] );

			// Create an entity for the publisher.
			$publisher_post_id = $this->entity_service->create( $params['name'], $type_uri, $params['image'], 'publish' );

			// Store the publisher entity post id in the configuration.
			$this->configuration_service->set_publisher_id( $publisher_post_id );

			// Needed because of possible change to the entity base path.
			flush_rewrite_rules();
		}

		// @todo: Does this make sense?
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return new WP_Error(
				'400',
				'Unable to set publisher.',
				array( 'status' => 400 )
			);
		}

	    // Get the Publisher ID.
	    $publisher_id = $this->configuration_service->get_publisher_id();

		// @todo: Set individual fields if they exist.

	    // Update the Publisher title.
	    if ( isset( $params['title'] ) ) {
		    $title = sanitize_text_field( ['title'] );
		    update_post_meta( $publisher_id, 'title', $title );
	    }

	    // Valid Publisher types.
	    $publisher_types = array(
		    'Person',
		    'Organization',
		    'localBusiness',
		    'onlineBusiness'
	    );

		// Update the Publisher Entity Type.
		if ( isset( $params['type'] ) && in_array( $params['type'], $publisher_types ) ) {
			// Set the type URI, http://schema.org/ + Person, Organization, localBusiness or Organization.
			$type_uri = sprintf( 'http://schema.org/%s', $params['type'] );

			$this->entity_service->set(
				$publisher_id,
				$type_uri,
				true
			);
		}

		// Return success
	    // @todo: Does it make sense to return Publisher ID here?
		return new WP_REST_Response;
    }
}