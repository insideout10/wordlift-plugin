<?php


namespace Wordlift\Shipping_Data;


use WC_Shipping_Zones;

class Shipping_Zones {

	/**
	 * @var Shipping_Zone[]
	 */
	private $zones;

	public function __construct() {

		$this->zones = array();

	}

	public function add_available_delivery_method( &$jsonld ) {

		$zone_ids          = array_keys( WC_Shipping_Zones::get_zones() );
		$wc_shipping_zones = array( WC_Shipping_Zones::get_zone( 0 ) );
		foreach ( $zone_ids as $zone_id ) {
			$wc_shipping_zones[] = WC_Shipping_Zones::get_zone( $zone_id );
		}
		$this->zones = array_map( 'Wordlift\Shipping_Data\Shipping_Zone::from_wc_shipping_zone', $wc_shipping_zones );

		foreach ( $this->zones as $zone ) {
			$zone->add_available_delivery_method( $jsonld );
		}

	}

}
