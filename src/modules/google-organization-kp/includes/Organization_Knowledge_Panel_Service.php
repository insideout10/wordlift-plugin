<?php

/**
 * Module:  Google Organization Knowledge Panel
 * Class:   Organization_Knowledge_Panel_Service
 *
 * A class that sets and sets the data for the Google Organization Panel API endpoints.
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use WP_Error;
use WP_REST_Response;

// @@TODO rename to Publisher_Service
class Organization_Knowledge_Panel_Service {

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
	 * @param Wordlift_Countries             $countries
	 * @param Wordlift_Publisher_Service     $publisher_service
	 * @param Wordlift_Entity_Type_Service   $entity_service
	 * @param Wordlift_Configuration_Service $configuration_service
	 */
	public function __construct(
		$countries,
		$publisher_service,
		$entity_service,
		$configuration_service
	) {
		$this->countries             = $countries;
		$this->publisher_service     = $publisher_service;
		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
	}

	/**
	 * Get a list of pages.
	 *
	 * @param int    $pagination        Pagination offset.
	 * @param string $title_starts_with Case-insensitive filter for page titles.
	 *
	 * @return array Array of page IDs and titles.
	 *
	 * @since 3.53.0
	 */
	public function get_pages( $pagination, $title_starts_with ) {
		// @TODO move to Page_Service

		// Get a number of pages starting at a given offset.
		$pagination_no_of_pages = self::PAGINATION_NUM_OF_PAGES;

		$pages = get_pages(
			array(
				'number' => $pagination_no_of_pages,
				'offset' => (int) $pagination * $pagination_no_of_pages,
			)
		);

		$data = array();

		// Arrange the data.
		foreach ( $pages as $page ) {
			$data[] = array(
				'id'    => $page->ID,
				'title' => $page->post_title,
			);
		}

		// Filter the array to only contain items whose title starts with the `title_starts_with` param.
		if ( count( $data ) > 0 && isset( $title_starts_with ) ) {
			$filter_str = strtolower( $title_starts_with );
			$data       = array_filter(
				$data,
				function ( $page ) use ( $filter_str ) {
					// Check that the start of the string matches the filter string.
					return substr( strtolower( $page['title'] ), 0, strlen( $filter_str ) ) === $filter_str;
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
		// @@TODO move to Country_Service or user Wordlift_Countries.

		// Get the list of countries in the current site language.
		$countries = $this->countries->get_countries();

		$data = array();

		// Arrange the data.
		foreach ( $countries as $country_code => $country_name ) {
			$data[] = array(
				'code' => $country_code,
				'name' => $country_name,
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
		// @@TODO change to `get`
		// @@TODO we said that we don't have this `is_publisher_set`.
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return array();
		}

		$publisher_id = $this->configuration_service->get_publisher_id();
		// @@TODO bail out if null.

		$data = array();

		$publisher_post = get_post( $publisher_id );
		// @@TODO if the post doesn't exist, bail out here.

		$publisher_entity = $this->entity_service->get( $publisher_id );
		$publisher_logo   = $this->publisher_service->get_publisher_logo( $publisher_id );

		// @@TODO please list all of the fields we have on the form and populate them: the API
		// is going to return a very very very simple JSON with all the needed properties (nothing more
		// and nothing less) and their values.
		// Add base Publisher fields
		$data['id']          = $publisher_id;                    // ID.
		$data['name']        = $publisher_post->post_title;      // Name
		$data['type']        = $publisher_entity['label'];       // Type
		$data['description'] = $publisher_entity['description']; // Description
		$data['alt_name']    = $this->configuration_service->get_alternate_name();

		// Add the logo
		if ( ! empty( $publisher_logo ) ) {
			$data['logo'] = $publisher_logo['url'];
		}

		// @@TODO I need to feed the data to a form that has a very well defined UI and number of fields.
		// I don't need to loop through an unknown number of custom fields, just get the ones you need and return
		// a perfectly simple and straightforward JSON that you will you with ease when you develop the Angular
		// client.
		foreach ( array_keys( $publisher_entity['custom_fields'] ) as $field_slug ) {
			$field_data = get_post_meta( $publisher_id, $field_slug );

			if ( ! empty( $field_data ) ) {
				$data['custom_fields'][ $field_slug ] = $field_data;
			}
		}

		return $data;
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
			// @@TODO review error passing, use Exceptions. Only the REST Controller knows what a status code is.
			return new WP_Error(
				'400',
				'No parameters provided.',
				array( 'status' => 400 )
			);
		}

		// Publisher doesn't exist, create one.
		// @@TODO we don't use `is_publisher_set`.
		if ( ! $this->publisher_service->is_publisher_set() ) {

			// @@TODO this part of the code to Publisher_Service->create( ... )
			// Parameters required to create a new publisher are missing, so return an error.
			if ( empty( $params['id'] ) || empty( $params['type'] ) || empty( $params['name'] ) || empty( $params['image'] ) ) {
				// @@TODO review error passing, use Exceptions.
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

			// @@TODO remove
			// Needed because of possible change to the entity base path.
			flush_rewrite_rules();
		}

		// @@TODO remove this.
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return new WP_Error(
				'400',
				'Unable to set publisher.',
				array( 'status' => 400 )
			);
		}

		// Get the Publisher ID.
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Update the Publisher title.
		if ( isset( $params['title'] ) ) {
			// @@TODO use the post->post_title
			update_post_meta( $publisher_id, 'title', sanitize_text_field( $params['title'] ) );
		}

		// Valid Publisher types.
		$publisher_types = array(
			'Person',
			'Organization',
			'LocalBusiness',
			'OnlineBusiness',
		);

		// Update the Publisher Entity Type.
		if ( isset( $params['type'] ) && in_array( $params['type'], $publisher_types, true ) ) {
			// Set the type URI, http://schema.org/ + Person, Organization, localBusiness or Organization.
			$type_uri = sprintf( 'http://schema.org/%s', $params['type'] );

			$this->entity_service->set(
				$publisher_id,
				$type_uri,
				true
			);
		}

		// Update the Publisher description.
		if ( isset( $params['description'] ) ) {
			// @@TODO post_content
			update_post_meta( $publisher_id, 'description', sanitize_text_field( array( 'description' ) ) );
		}

		// Update Alternate Name.
		if ( isset( $params['alt_name'] ) ) {
			$this->configuration_service->set_alternate_name( sanitize_text_field( $params['alt_name'] ) );
		}

		// Set the entity logo.
		if ( isset( $params['logo'] ) && is_numeric( $params['logo'] ) ) {
			set_post_thumbnail( $publisher_id, $params['logo'] );
		}

		// Update the custom fields.
		// @@TODO save the parameters from your form.
		if ( isset( $params['custom_fields'] ) ) {
			foreach ( $params['custom_fields'] as $field_slug => $field_value ) {
				if ( isset( $field_value ) ) {
					update_post_meta( $publisher_id, $field_slug, sanitize_text_field( $field_value ) );
				}
			}
		}

		// Return success
		// @@TODO you return the `get` data as array. This is a service and doesn't know anything
		// about WP_REST
		return new WP_REST_Response();
	}
}
