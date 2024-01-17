<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	Organization_Extra_Fields_Service
 *
 * @package Wordlift/modules/google-organization-kp
 *
 * @since 3.53.0
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use Wordlift_Configuration_Service;
use Wordlift_Publisher_Service;

class Organization_Extra_Fields_Service {

	/**
	 * Define slugs for custom fields
	 *
	 * @since 3.53.0
	 */
	const FIELD_NO_OF_EMPLOYEES = "_wl_no_of_employees";
	const FIELD_FOUNDING_DATE = "_wl_founding_date";
	const FIELD_ISO_6523_CODE = "_wl_iso_6523_code";
	const FIELD_NAICS = "_wl_naics";
	const FIELD_GLOBAL_LOCATION_NO = "_wl_global_location_no";
	const FIELD_VAT_ID = "_wl_vat_id";
	const FIELD_TAX_ID = "_wl_tax_id";

	/**
	 * Map custom fields slugs to Schema property labels
	 *
	 * @since 3.53.0
	 */
	const FIELD_LABEL_MAP = array(
		self::FIELD_NO_OF_EMPLOYEES    => 'numberOfEmployees',
		self::FIELD_FOUNDING_DATE      => 'foundingDate',
		self::FIELD_ISO_6523_CODE      => 'iso6523Code',
		self::FIELD_NAICS              => 'naics',
		self::FIELD_GLOBAL_LOCATION_NO => 'globalLocationNumber',
		self::FIELD_VAT_ID             => 'vatID',
		self::FIELD_TAX_ID             => 'taxID'
	);

	/**
	 * An instance of Wordlift_Configuration_Service class.
	 *
	 * @since 3.53.0
	 */
	private $configuration_service;

	/**
	 * An instance of Wordlift_Publisher_Service class.
	 *
	 * @since 3.53.0
	 */
	private $publisher_service;

	/**
	 * Reference to self.
	 *
	 * @since 3.53.0
	 */
	private static $instance;

	public function __construct() {
		$this->configuration_service = Wordlift_Configuration_Service::get_instance();
		$this->publisher_service     = Wordlift_Publisher_Service::get_instance();
		self::$instance              = $this;
	}

	/**
	 * Get an instance of this class.
	 *
	 * @since 3.53.0
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Get an array with all the custom field slugs.
	 *
	 * @return string[]
	 *
	 * @since 3.53.0
	 */
	public function get_all_field_slugs() {
		return array(
			self::FIELD_NO_OF_EMPLOYEES,
			self::FIELD_FOUNDING_DATE,
			self::FIELD_ISO_6523_CODE,
			self::FIELD_NAICS,
			self::FIELD_GLOBAL_LOCATION_NO,
			self::FIELD_VAT_ID,
			self::FIELD_TAX_ID
		);
	}

	/**
	 * Get the Schema property label for a given slug.
	 *
	 * @param $field_slug string The slug for which we should get the label.
	 *
	 * @return string
	 *
	 * @since 3.53.0
	 */
	public function get_field_label( $field_slug ) {
		return self::FIELD_LABEL_MAP[ $field_slug ];
	}

	/**
	 * Gets the stored field data for a given custom field slug.
	 *
	 * @param $field_slug string The slug for which we should get the data.
	 *
	 * @return array|null
	 *
	 * @since 3.53.0
	 */
	public function get_field_data( $field_slug ) {
		// If a Publisher isn't set or isn't valid, return null
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return null;
		}

		$publisher_id = $this->configuration_service->get_publisher_id();

		return array(
			"label" => $this->get_field_label( $field_slug ),
			"value" => get_post_meta( $publisher_id, $field_slug, true )
		);
	}

	/**
	 * Gets the field data for all the custom fields.
	 *
	 * @return array
	 *
	 * @since 3.53.0
	 */
	public function get_all_field_data() {
		// If a Publisher isn't set or isn't valid, return empty array
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return array();
		}

		$data = array();

		// Return only fields that have a value set.
		foreach ( $this->get_all_field_slugs() as $field_slug ) {
			$field_data = $this->get_field_data( $field_slug );

			if ( empty( $field_data["value"] ) ) {
				continue;
			}

			$data[$field_slug] = $field_data["value"];
		}

		return $data;
	}

	/**
	 * Set the data for a custom field.
	 *
	 * @param $field_slug
	 * @param $field_data
	 *
	 * @since 3.53.0
	 */
	public function set_field_data( $field_slug, $field_data ) {
		// If a Publisher isn't set or isn't valid, do nothing
		if ( ! $this->publisher_service->is_publisher_set() ) {
			return;
		}

		$publisher_id = $this->configuration_service->get_publisher_id();

		// Set the field value.
		if ( $this->get_field_data( $field_slug ) ) {
			update_post_meta( $publisher_id, $field_slug, $field_data );
		} else {
			add_post_meta( $publisher_id, $field_slug, $field_data );
		}
	}
}