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

use Exception;

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

		// Incorrectly set Publisher
		if ( ! is_numeric( $publisher_id ) ) {
			return array();
		}

		$publisher_post = get_post( $publisher_id );

		// No Publisher Post exists
		if ( ! $publisher_post ) {
			return array();
		}

		// Fix any spaces in img path.
		$publisher_img_url = esc_url( get_the_post_thumbnail_url( $publisher_id ) );

		// Load the about page if set and get the title
		$about_page_id = $this->configuration_service->get_about_page_id();
		if ( $about_page_id ) {
			$page = get_page( $about_page_id );
		}

		// About page ID if set, or empty.
		$about_page = ! empty( $page )
			? array(
				'id'    => (string) $page->ID,
				'title' => $page->post_title,
			)
			: '';

		// Load Publisher Entity.
		$publisher_entity = $this->entity_service->get( $publisher_id );

		// Return Publisher data.
		return array(
			'page'          => $about_page,
			'type'          => $publisher_entity['label'],
			'name'          => $publisher_post->post_title,
			'alt_name'      => $this->configuration_service->get_alternate_name(),
			'legal_name'    => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_LEGAL_NAME, true ),
			'description'   => $publisher_post->post_content,
			'image'         => $publisher_img_url,
			'url'           => $this->configuration_service->get_override_website_url(),
			'same_as'       => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_SAME_AS, false ),
			'address'       => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS, true ),
			'locality'      => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_LOCALITY, true ),
			'region'        => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_REGION, true ),
			'country'       => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_COUNTRY, true ),
			'postal_code'   => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ADDRESS_POSTAL_CODE, true ),
			'telephone'     => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TELEPHONE, true ),
			'email'         => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_EMAIL, true ),
			'no_employees'  => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_NO_OF_EMPLOYEES, true ),
			'founding_date' => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_FOUNDING_DATE, true ),
			'iso_6523'      => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_ISO_6523_CODE, true ),
			'naics'         => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_NAICS, true ),
			'global_loc_no' => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_GLOBAL_LOCATION_NO, true ),
			'vat_id'        => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_VAT_ID, true ),
			'tax_id'        => get_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_TAX_ID, true ),
		);
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
			if ( empty( $params['type'] ) || empty( $params['name'] ) ) {
				throw new Exception( 'Required parameters not provided.' );
			}

			// Incorrect publisher type provided.
			if ( ! in_array( $params['type'], $valid_publisher_types, true ) ) {
				throw new Exception( 'Publisher type not valid.' );
			}

			// Create the publisher
			$this->publisher_service->save( $params['name'], $params['type'] );

			// Get the new Publisher ID.
			$publisher_id = $this->configuration_service->get_publisher_id();
		}

		// Update the About Page ID.
		if ( ! empty( $params['page'] ) && isset( $params['page']['id'] ) ) {
			$this->configuration_service->set_about_page_id( $params['page']['id'] );
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

		// Update entity logo.
		if ( ! empty( $params['image'] ) ) {
			$image_file = $params['image'];
			$image_name = basename( $image_file['name'] );

			$upload_dir = wp_upload_dir();
			$image_url  = esc_url( $upload_dir['url'] . '/' . $image_name );
			$image_path = $upload_dir['path'] . '/' . $image_name;

			// Check if the attachment already exists
			$attachment_id = attachment_url_to_postid( $image_url );

			if ( ! $attachment_id ) {
				// Attachment doesn't exist, create it and set as thumbnail
				move_uploaded_file( $image_file['tmp_name'], $image_path );

				$attachment = array(
					'post_title'     => $image_name,
					'post_mime_type' => $image_file['type'],
					'post_status'    => 'inherit',
					'post_parent'    => $publisher_id,
				);

				$attachment_id = wp_insert_attachment( $attachment, $image_path, $publisher_id );

				if ( ! is_wp_error( $attachment_id ) ) {
					set_post_thumbnail( $publisher_id, $attachment_id );
				}
			} else {
				// Attachment for provided image already exists, set it as thumbnail
				set_post_thumbnail( $publisher_id, $attachment_id );
			}
		}

		// Update URL
		if ( ! empty( $params['url'] ) ) {
			$this->configuration_service->set_override_website_url( sanitize_text_field( $params['url'] ) );
		}

		// Update Same As
		if ( ! empty( $params['same_as'] ) && is_array( $params['same_as'] ) ) {
			$meta_key     = \Wordlift_Schema_Service::FIELD_SAME_AS;
			$same_as_urls = array_map( 'sanitize_url', array_filter( $params['same_as'] ) );

			// Clear old values.
			delete_post_meta( $publisher_id, \Wordlift_Schema_Service::FIELD_SAME_AS );

			foreach ( $same_as_urls as $url ) {
				// Avoid duplicates
				delete_post_meta( $publisher_id, $meta_key, $url );
				add_post_meta( $publisher_id, $meta_key, $url );
			}
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
				\Wordlift_Schema_Service::FIELD_NAICS,
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
