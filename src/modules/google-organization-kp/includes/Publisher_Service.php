<?php

/**
 * Module:  Google Organization Knowledge Panel
 * Class:   Publisher_Service
 *
 * A class that sets and sets the data for the Google Organization Panel API endpoints.
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use PHPUnit\Util\Exception;

class Publisher_Service {
	/**
	 * @var \Wordlift_Publisher_Service
	 */
	private $publisher_service;

	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_service;

	/**
	 * @var Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @var \Wordlift_Schema_Service
	 */
	private $schema_service;

	/**
	 * @param \Wordlift_Publisher_Service     $publisher_service
	 * @param \Wordlift_Entity_Type_Service   $entity_service
	 * @param \Wordlift_Configuration_Service $configuration_service
	 * @param \Wordlift_Schema_Service        $schema_service
	 */
	public function __construct(
		$publisher_service,
		$entity_service,
		$configuration_service,
		$schema_service
	) {
		$this->publisher_service     = $publisher_service;
		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
		$this->schema_service        = $schema_service;
	}

	/**
	 * Get relevant useful data if a publisher already exists
	 *
	 * @return array Publisher data.
	 *
	 * @since 3.53.0
	 */
	public function get() {
		$publisher_id = $this->configuration_service->get_publisher_id();
		if ( ! isset( $publisher_id ) || ! is_numeric( $publisher_id ) ) {
			return array();
		}

		$publisher_post = get_post( $publisher_id );
		if ( ! $publisher_post ) {
			return array();
		}

		// Add the publisher fields.

		$data = array();

		$schema_service   = $this->schema_service;
		$publisher_entity = $this->entity_service->get( $publisher_id );
		$publisher_logo   = $this->publisher_service->get_publisher_logo( $publisher_id );

		$data['id']          = $publisher_id;
		$data['name']        = $publisher_post->post_title;
		$data['type']        = $publisher_entity['label'];
		$data['description'] = $publisher_entity['description'];
		$data['alt_name']    = $this->configuration_service->get_alternate_name();
		$data['legal_name']  = get_post_meta( $publisher_id, $schema_service::FIELD_LEGAL_NAME, true );

		if ( ! empty( $publisher_logo ) ) {
			$data['image'] = $publisher_logo['url'];
		}

		$data['same_as']       = get_post_meta( $publisher_id, $schema_service::FIELD_SAME_AS, false );
		$data['address']       = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS, true );
		$data['locality']      = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_LOCALITY, true );
		$data['region']        = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_REGION, true );
		$data['country']       = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_COUNTRY, true );
		$data['postal_code']   = get_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_POSTAL_CODE, true );
		$data['telephone']     = get_post_meta( $publisher_id, $schema_service::FIELD_TELEPHONE, true );
		$data['email']         = get_post_meta( $publisher_id, $schema_service::FIELD_EMAIL, true );
		$data['no_employees']  = get_post_meta( $publisher_id, $schema_service::FIELD_NO_OF_EMPLOYEES, true );
		$data['founding_date'] = get_post_meta( $publisher_id, $schema_service::FIELD_FOUNDING_DATE, true );
		$data['iso_6523']      = get_post_meta( $publisher_id, $schema_service::FIELD_ISO_6523_CODE, true );
		$data['naics']         = get_post_meta( $publisher_id, $schema_service::FIELD_NAICS, true );
		$data['global_loc_no'] = get_post_meta( $publisher_id, $schema_service::FIELD_GLOBAL_LOCATION_NO, true );
		$data['vat_id']        = get_post_meta( $publisher_id, $schema_service::FIELD_VAT_ID, true );
		$data['tax_id']        = get_post_meta( $publisher_id, $schema_service::FIELD_TAX_ID, true );

		return $data;
	}

	/**
	 * Set the Organization data.
	 *
	 * @param array $params The parameters sent via POST.
	 *
	 * @return array Saved data.
	 *
	 * @throws Exception Throws exception if something went wrong.
	 *
	 * @since 3.53.0
	 */
	public function save( $params ) {
		// Break out if no parameters were provided.
		if ( empty( $params ) ) {
			throw new Exception( 'No parameters provided.' );
		}

		// Valid Publisher types.
		$publisher_service = $this->publisher_service;
		$publisher_types      = array_values( $publisher_service::VALID_PUBLISHER_TYPES );

		// Try to get the Publisher
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Publisher doesn't exist, create one.
		if ( ! isset( $publisher_id ) || $publisher_id === '(none)' ) {
			// Parameters required to create a new publisher are missing, so return an error.
			if ( empty( $params['type'] ) || empty( $params['name'] ) || empty( $params['image'] ) ) {
				throw new Exception( 'Required parameters not provided.' );
			}

			$this->publisher_service->save( $params['name'], $params['type'], $params['image'] );
		}

		// Get the Publisher ID.
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Update the Publisher title.
		if ( isset( $params['title'] ) ) {
			$post_array = array(
				'ID'         => $publisher_id,
				'post_title' => sanitize_text_field( $params['title'] ),
			);

			wp_update_post( $post_array );
		}

		// Update the Publisher description.
		if ( isset( $params['description'] ) ) {
			$post_array = array(
				'ID'           => $publisher_id,
				'post_content' => sanitize_text_field( $params['description'] ),
			);

			wp_update_post( $post_array );
		}

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

		// Update Alternate Name.
		if ( isset( $params['alt_name'] ) ) {
			$this->configuration_service->set_alternate_name( sanitize_text_field( $params['alt_name'] ) );
		}

		// Set the entity logo.
		if ( isset( $params['image'] ) && is_numeric( $params['image'] ) ) {
			set_post_thumbnail( $publisher_id, $params['image'] );
		}

		// Update fields.

		$schema_service = $this->schema_service;

		if ( isset( $params['same_as'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_SAME_AS, $params['same_as'] );
		}

		if ( isset( $params['address'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS, $params['address'] );
		}

		if ( isset( $params['locality'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_LOCALITY, $params['locality'] );
		}

		if ( isset( $params['region'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_REGION, $params['region'] );
		}

		if ( isset( $params['country'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_COUNTRY, $params['country'] );
		}

		if ( isset( $params['postal_code'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ADDRESS_POSTAL_CODE, $params['postal_code'] );
		}

		if ( isset( $params['telephone'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_TELEPHONE, $params['telephone'] );
		}

		if ( isset( $params['email'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_EMAIL, $params['email'] );
		}

		if ( isset( $params['no_employees'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_NO_OF_EMPLOYEES, $params['no_employees'] );
		}

		if ( isset( $params['founding_date'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_FOUNDING_DATE, $params['founding_date'] );
		}

		if ( isset( $params['iso_6523'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ISO_6523_CODE, $params['iso_6523'] );
		}

		if ( isset( $params['naics'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_ISO_6523_CODE, $params['naics'] );
		}

		if ( isset( $params['global_loc_no'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_GLOBAL_LOCATION_NO, $params['global_loc_no'] );
		}

		if ( isset( $params['vat_id'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_VAT_ID, $params['vat_id'] );
		}

		if ( isset( $params['tax_id'] ) ) {
			update_post_meta( $publisher_id, $schema_service::FIELD_TAX_ID, $params['tax_id'] );
		}

		// Return updated data.
		return $this->get();
	}
}
