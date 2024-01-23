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
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;

	/**
	 * @param \Wordlift_Publisher_Service     $publisher_service
	 * @param \Wordlift_Entity_Type_Service   $entity_service
	 * @param \Wordlift_Configuration_Service $configuration_service
	 */
	public function __construct(
		$publisher_service,
		$entity_service,
		$configuration_service
	) {
		$this->publisher_service     = $publisher_service;
		$this->entity_service        = $entity_service;
		$this->configuration_service = $configuration_service;
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
		if ( ! is_numeric( $publisher_id ) ) {
			return array();
		}

		$publisher_post = get_post( $publisher_id );
		if ( ! $publisher_post ) {
			return array();
		}

		// Add the publisher fields.

		$data = array();

		$publisher_entity = $this->entity_service->get( $publisher_id );
		$publisher_logo   = $this->publisher_service->get_publisher_logo( $publisher_id );

		$data['id']          = $publisher_id;
		$data['name']        = $publisher_post->post_title;
		$data['type']        = $publisher_entity['label'];
		$data['description'] = $publisher_entity['description'];
		$data['alt_name']    = $this->configuration_service->get_alternate_name();
		$data['legal_name']  = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_LEGAL_NAME, true );

		if ( ! empty( $publisher_logo ) ) {
			$data['image'] = $publisher_logo['url'];
		}

		$data['same_as']       = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_SAME_AS, false );
		$data['address']       = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS, true );
		$data['locality']      = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, true );
		$data['region']        = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_REGION, true );
		$data['country']       = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, true );
		$data['postal_code']   = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, true );
		$data['telephone']     = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TELEPHONE, true );
		$data['email']         = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_EMAIL, true );
		$data['no_employees']  = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES, true );
		$data['founding_date'] = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_FOUNDING_DATE, true );
		$data['iso_6523']      = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ISO_6523_CODE, true );
		$data['naics']         = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_NAICS, true );
		$data['global_loc_no'] = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO, true );
		$data['vat_id']        = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_VAT_ID, true );
		$data['tax_id']        = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TAX_ID, true );

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
		$valid_publisher_types = array_values( \Wordlift_Publisher_Service::VALID_PUBLISHER_TYPES );

		// Try to get the Publisher
		$publisher_id = $this->configuration_service->get_publisher_id();

		// Publisher doesn't exist, create one.
		if ( ! is_numeric( $publisher_id ) ) {
			// Parameters required to create a new publisher are missing.
			if ( empty( $params['type'] ) || empty( $params['name'] ) || empty( $params['image'] ) ) {
				throw new Exception( 'Required parameters not provided.' );
			}

			// Incorrect publisher type provided.
			if ( ! in_array( $params['type'], $valid_publisher_types, true ) ) {
				throw new Exception( 'Publisher type not valid.' );
			}

			// @todo: Prepare image to be saved.

			$this->publisher_service->save( $params['name'], $params['type'], $params['image'] );

			// Get the new Publisher ID.
			$publisher_id = $this->configuration_service->get_publisher_id();
		}

		// Update the Publisher title.
		if ( ! empty( $params['name'] ) ) {
			$post_array = array(
				'ID'         => $publisher_id,
				'post_title' => sanitize_text_field( $params['name'] ),
			);

			wp_update_post( $post_array );
		}

		// Update the Publisher description.
		if ( ! empty( $params['description'] ) ) {
			$post_array = array(
				'ID'           => $publisher_id,
				'post_content' => sanitize_text_field( $params['description'] ),
			);

			wp_update_post( $post_array );
		}

		// Update the Publisher Entity Type.
		if ( ! empty( $params['type'] ) && in_array( $params['type'], $valid_publisher_types, true ) ) {
			// Set the type URI, http://schema.org/ + Person, Organization, localBusiness or Organization.
			$type_uri = sprintf( 'http://schema.org/%s', $params['type'] );

			$this->entity_service->set(
				$publisher_id,
				$type_uri,
				true
			);
		}

		// Update Alternate Name.
		if ( ! empty( $params['alt_name'] ) ) {
			$this->configuration_service->set_alternate_name( sanitize_text_field( $params['alt_name'] ) );
		}

		// Set the entity logo.
		if ( ! empty( $params['image'] ) ) {
			$image_file  = $params['image'];
			$upload_dir  = wp_upload_dir();
			$target_dir  = $upload_dir['path'];
			$target_file = $target_dir . '/' . basename( $image_file['name'] );

			if ( move_uploaded_file( $image_file['tmp_name'], $target_file ) ) {
				$attachment = array(
					'post_title'     => basename( $target_file ),
					'post_mime_type' => wp_check_filetype( $target_file )['type'],
					'post_status'    => 'inherit',
					'post_parent'    => $publisher_id,
				);

				$attachment_id = wp_insert_attachment( $attachment, $target_file, $publisher_id );
				set_post_thumbnail( $publisher_id, $attachment_id );
			} else {
				throw new Exception( 'Unable to update the image' );
			}
		}

		// Update fields.

		if ( ! empty( $params['same_as'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_SAME_AS, $params['same_as'] );
		}

		if ( ! empty( $params['address'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS, $params['address'] );
		}

		if ( ! empty( $params['locality'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, $params['locality'] );
		}

		if ( ! empty( $params['region'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_REGION, $params['region'] );
		}

		if ( ! empty( $params['country'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, $params['country'] );
		}

		if ( ! empty( $params['postal_code'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, $params['postal_code'] );
		}

		if ( ! empty( $params['telephone'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TELEPHONE, $params['telephone'] );
		}

		if ( ! empty( $params['email'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_EMAIL, $params['email'] );
		}

		if ( ! empty( $params['no_employees'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES, $params['no_employees'] );
		}

		if ( ! empty( $params['founding_date'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_FOUNDING_DATE, $params['founding_date'] );
		}

		if ( ! empty( $params['iso_6523'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ISO_6523_CODE, $params['iso_6523'] );
		}

		if ( ! empty( $params['naics'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ISO_6523_CODE, $params['naics'] );
		}

		if ( ! empty( $params['global_loc_no'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO, $params['global_loc_no'] );
		}

		if ( ! empty( $params['vat_id'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_VAT_ID, $params['vat_id'] );
		}

		if ( ! empty( $params['tax_id'] ) ) {
			update_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TAX_ID, $params['tax_id'] );
		}

		// Return updated data.
		return $this->get();
	}
}
