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

		// Fix any spaces in img path.
		$publisher_logo_url = get_the_post_thumbnail_url( $publisher_id );
		$publisher_logo_url = str_replace( ' ', '%20', $publisher_logo_url );

		$data['id']            = $publisher_id;
		$data['type']          = $publisher_entity['label'];
		$data['name']          = $publisher_post->post_title;
		$data['alt_name']      = $this->configuration_service->get_alternate_name();
		$data['legal_name']    = get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_LEGAL_NAME, true );
		$data['description']   = $publisher_entity['description'];
		$data['image']         = $publisher_logo_url;
		$data['url']           = $this->configuration_service->get_override_website_url();
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
			// @todo: This method should receive an attachment_id for the image.
			$this->publisher_service->save( $params['name'], $params['type'], $params['image'] );

			// Get the new Publisher ID.
			$publisher_id = $this->configuration_service->get_publisher_id();
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

		// Update the Publisher title.
		if ( ! empty( $params['name'] ) ) {
			$post_array = array(
				'ID'         => $publisher_id,
				'post_title' => sanitize_text_field( $params['name'] ),
			);

			wp_update_post( $post_array );
		}

		// Update Alternate Name.
		if ( ! empty( $params['alt_name'] ) ) {
			$this->configuration_service->set_alternate_name( sanitize_text_field( $params['alt_name'] ) );
		}

		// Update Legal name
		if ( ! empty( $params['legal_name'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_LEGAL_NAME,
				sanitize_text_field( $params['legal_name'] )
			);
		}

		// Update the Publisher description.
		if ( ! empty( $params['description'] ) ) {
			$post_array = array(
				'ID'           => $publisher_id,
				'post_content' => sanitize_text_field( $params['description'] ),
			);

			wp_update_post( $post_array );
		}

		// Set the entity logo.
		// @todo: Should be moved to a separate function as it needs to be reused.
		// @todo: There is a bug here. Image is saved, but subsequently publisher_service->get_publisher_logo() doesn't retrieve it.
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

		// Update URL
		if ( ! empty( $params['url'] ) ) {
			$this->configuration_service->set_override_website_url( sanitize_text_field( $params['url'] ) );
		}

		// Update Same As
		if ( ! empty( $params['same_as'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_SAME_AS,
				sanitize_text_field( $params['same_as'] )
			);
		}

		// Update Address
		if ( ! empty( $params['address'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ADDRESS,
				sanitize_text_field( $params['address'] )
			);
		}

		// Update Locality
		if ( ! empty( $params['locality'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY,
				sanitize_text_field( $params['locality'] )
			);
		}

		// Update Region
		if ( ! empty( $params['region'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ADDRESS_REGION,
				sanitize_text_field( $params['region'] )
			);
		}

		// Update Country
		if ( ! empty( $params['country'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY,
				sanitize_text_field( $params['country'] )
			);
		}

		// Update Postal Code
		if ( ! empty( $params['postal_code'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE,
				sanitize_text_field( $params['postal_code'] )
			);
		}

		// Update Telephone
		if ( ! empty( $params['telephone'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_TELEPHONE,
				sanitize_text_field( $params['telephone'] )
			);
		}

		// Update email
		if ( ! empty( $params['email'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_EMAIL,
				sanitize_text_field( $params['email'] )
			);
		}

		// Update Number of Employees
		if ( ! empty( $params['no_employees'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES,
				sanitize_text_field( $params['no_employees'] )
			);
		}

		// Update Founding Date
		if ( ! empty( $params['founding_date'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_FOUNDING_DATE,
				sanitize_text_field( $params['founding_date'] )
			);
		}

		// Update ISO 6523
		if ( ! empty( $params['iso_6523'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ISO_6523_CODE,
				sanitize_text_field( $params['iso_6523'] )
			);
		}

		// Update naics
		if ( ! empty( $params['naics'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_ISO_6523_CODE,
				sanitize_text_field( $params['naics'] )
			);
		}

		// Update Global Location Number
		if ( ! empty( $params['global_loc_no'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO,
				sanitize_text_field( $params['global_loc_no'] )
			);
		}

		// Update VAT ID
		if ( ! empty( $params['vat_id'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_VAT_ID,
				sanitize_text_field( $params['vat_id'] )
			);
		}

		// Update Tax ID
		if ( ! empty( $params['tax_id'] ) ) {
			update_post_meta(
				$publisher_id,
				\Wordlift_Schema_Service::FIELD_TAX_ID,
				sanitize_text_field( $params['tax_id'] )
			);
		}

		// Return updated data.
		return $this->get();
	}
}
