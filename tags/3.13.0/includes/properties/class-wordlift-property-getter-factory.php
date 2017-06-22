<?php

require_once( 'class-wordlift-property-getter.php' );
require_once( 'class-wordlift-simple-property-service.php' );
require_once( 'class-wordlift-entity-property-service.php' );
require_once( 'class-wordlift-location-property-service.php' );
require_once( 'class-wordlift-url-property-service.php' );
require_once( 'class-wordlift-double-property-service.php' );

/**
 * A Wordlift_Property_Getter_Factory, which instantiate a configured
 * {@link Wordlift_Property_Getter}.
 *
 * @since 3.8.0
 */
class Wordlift_Property_Getter_Factory {

	/**
	 * Create a {@link Wordlift_Property_Getter} instance.
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 *
	 * @return \Wordlift_Property_Getter A {@link Wordlift_Property_Getter} instance.
	 */
	public static function create( $entity_service ) {

		$property_getter = new Wordlift_Property_Getter( new Wordlift_Simple_Property_Service() );
		$property_getter->register( new Wordlift_Entity_Property_Service( $entity_service ), array(
			Wordlift_Schema_Service::FIELD_FOUNDER,
			Wordlift_Schema_Service::FIELD_AUTHOR,
			Wordlift_Schema_Service::FIELD_KNOWS,
			Wordlift_Schema_Service::FIELD_BIRTH_PLACE,
			Wordlift_Schema_Service::FIELD_AFFILIATION,
		) );
		$property_getter->register( new Wordlift_Location_Property_Service( $entity_service ), array(
			Wordlift_Schema_Service::FIELD_LOCATION,
		) );
		$property_getter->register( new Wordlift_Url_Property_Service(), array( Wordlift_Url_Property_Service::META_KEY ) );
		$property_getter->register( new Wordlift_Double_Property_Service(), array(
			Wordlift_Schema_Service::FIELD_GEO_LATITUDE,
			Wordlift_Schema_Service::FIELD_GEO_LONGITUDE,
		) );

		return $property_getter;
	}

}