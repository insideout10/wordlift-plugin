<?php
/**
 * Factories: Property Getter Factory.
 *
 * @since      3.8.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */

require_once 'class-wordlift-property-getter.php';
require_once 'class-wordlift-simple-property-service.php';
require_once 'class-wordlift-entity-property-service.php';
require_once 'class-wordlift-location-property-service.php';
require_once 'class-wordlift-url-property-service.php';
require_once 'class-wordlift-double-property-service.php';
require_once 'class-wordlift-duration-property-service.php';
require_once 'class-wordlift-required-property-service.php';

/**
 * A Wordlift_Property_Getter_Factory, which instantiate a configured
 * {@link Wordlift_Property_Getter}.
 *
 * @since      3.8.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/properties
 */
class Wordlift_Property_Getter_Factory {

	/**
	 * Create a {@link Wordlift_Property_Getter} instance.
	 *
	 * @return \Wordlift_Property_Getter A {@link Wordlift_Property_Getter} instance.
	 * @since 3.8.0
	 */
	public static function create() {

		$property_getter = new Wordlift_Property_Getter( new Wordlift_Simple_Property_Service() );
		$property_getter->register(
			new Wordlift_Entity_Property_Service(),
			array(
				Wordlift_Schema_Service::FIELD_FOUNDER,
				Wordlift_Schema_Service::FIELD_AUTHOR,
				Wordlift_Schema_Service::FIELD_KNOWS,
				Wordlift_Schema_Service::FIELD_BIRTH_PLACE,
				Wordlift_Schema_Service::FIELD_AFFILIATION,
				Wordlift_Schema_Service::FIELD_PERFORMER,
				Wordlift_Schema_Service::FIELD_OFFERS,
				Wordlift_Schema_Service::FIELD_ITEM_OFFERED,
			)
		);
		$property_getter->register(
			new Wordlift_Location_Property_Service(),
			array(
				Wordlift_Schema_Service::FIELD_LOCATION,
			)
		);
		$property_getter->register( new Wordlift_Url_Property_Service(), array( Wordlift_Url_Property_Service::META_KEY ) );
		$property_getter->register(
			new Wordlift_Double_Property_Service(),
			array(
				Wordlift_Schema_Service::FIELD_GEO_LATITUDE,
				Wordlift_Schema_Service::FIELD_GEO_LONGITUDE,
			)
		);

		$property_getter->register(
			new Wordlift_Duration_Property_Service(),
			array(
				Wordlift_Schema_Service::FIELD_PREP_TIME,
				Wordlift_Schema_Service::FIELD_COOK_TIME,
				Wordlift_Schema_Service::FIELD_TOTAL_TIME,
			)
		);

		add_action(
			'after_setup_theme',
			function () use ( $property_getter ) {
				$property_getter->register( new Wordlift_Required_Property_Service(), apply_filters( 'wl_required_property', array() ) );
			}
		);

		return $property_getter;
	}

}
