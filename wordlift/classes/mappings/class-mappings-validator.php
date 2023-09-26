<?php

/**
 * Define the {@link Mappings_Validator} class.
 *
 * Validates the mapping for single post and return the  * schema.org properties mapped to ACF, custom field or text
 * to be used for JSON-LD output.
 *
 * @since      3.25.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/sync-mappings
 */

namespace Wordlift\Mappings;

use Wordlift\Mappings\Validators\Rule_Groups_Validator;

final class Mappings_Validator {
	const TRASH_CATEGORY  = 'trash';
	const ACTIVE_CATEGORY = 'active';

	/**
	 * The {@link Mappings_DBO} instance to test.
	 *
	 * @since  3.25.0
	 * @access private
	 * @var Mappings_DBO $dbo The {@link Mappings_DBO} instance to test.
	 */
	private $dbo;

	/**
	 * @var Rule_Groups_Validator
	 */
	private $rule_groups_validator;

	/**
	 * Constructor for Wordlift_Mapping_Validator.
	 *
	 * @param Mappings_DBO          $dbo The {@link Mappings_DBO} instance.
	 * @param Rule_Groups_Validator $rule_groups_validator
	 */
	public function __construct( $dbo, $rule_groups_validator ) {

		$this->dbo                   = $dbo;
		$this->rule_groups_validator = $rule_groups_validator;

	}

	/**
	 * This method is used to filter properties based on presence
	 * of certain key values.
	 *
	 * @param $items array Array of properties.
	 *
	 * @return array
	 */
	private static function filter_properties_for_required_keys( $items ) {
		return array_filter(
			$items,
			function ( $item ) {
				/**
				 * Since the properties might also be passed
				 * by external plugins, we might need to check if
				 * they have correct data format.
				 */
				if ( ! array_key_exists( 'property_name', $item ) ||
					 ! array_key_exists( 'field_type', $item ) ||
					 ! array_key_exists( 'field_name', $item ) ||
					 ! array_key_exists( 'transform_function', $item )
				) {
					// If these keys doesnt exist, then dont process.
					return false;
				} else {
					// If the keys exist, then filter it.
					return true;
				}
			}
		);
	}

	/**
	 * Validates two values based on the passed logic
	 * a single rule passes the user defined logic.
	 *
	 * @param string $key The key which every object has mapped to our value.
	 * @param array  $items The array of items.
	 * @param string $status The value which the items should have.
	 *
	 * @return array
	 */
	private static function get_property_item_by_status( $key, $items, $status ) {
		return array_filter(
			$items,
			function ( $item ) use ( $key, $status ) {
				return $item[ $key ] === (string) $status;
			}
		);
	}

	/**
	 * Validates a post id with the list of active mapping items and check if
	 * a mapping can be applied.
	 *
	 * @param int    $identifier The post id or term id based on type.
	 *
	 * @param string $type Post or term.
	 *
	 * @return array
	 */
	public function validate( $identifier, $type ) {
		// Reset the valid property items before making the validation.
		$properties = array();

		// Filter registered properties
		$filter_registered_properties = array();

		// Get active mappings.
		$mappings = $this->dbo->get_active_mappings();
		/**
		 * Apply this filter to get mappings from external plugins.
		 *
		 * @param $mappings array Array of mappings from database.
		 * @param $identifier int The post id or term id based on type.
		 */
		$mappings = apply_filters( 'wl_mappings_post', $mappings, $identifier );

		// Get all active rule groups for the mapping items.
		foreach ( $mappings as $mapping ) {
			if ( array_key_exists( 'mapping_id', $mapping ) ) {
				$rule_groups          = $this->dbo->get_rule_groups_by_mapping( (int) $mapping['mapping_id'] );
				$should_apply_mapping = $this->rule_groups_validator->is_valid( $identifier, $rule_groups, $type );
				if ( $should_apply_mapping ) {
					$mapping_item_properties = $this->dbo->get_properties( $mapping['mapping_id'] );
					$properties              = array_merge( $properties, $mapping_item_properties );
				}
			} else {
				/**
				 * This is a programmatically defined mapping,
				 * so we will have the rule groups and the properties in the array keys
				 */
				if ( array_key_exists( 'properties', $mapping ) &&
					 is_array( $mapping['properties'] ) ) {
					$filter_registered_properties = array_merge( $filter_registered_properties, $mapping['properties'] );
				}
			}
		}
		// Filter all registered properties based on required key values.
		$filter_registered_properties = self::filter_properties_for_required_keys( $filter_registered_properties );
		$active_properties            = self::get_property_item_by_status(
			'property_status',
			$properties,
			self::ACTIVE_CATEGORY
		);

		// Merge ui defined properties with filter registered properties.
		return array_merge( $active_properties, $filter_registered_properties );
	}

}
