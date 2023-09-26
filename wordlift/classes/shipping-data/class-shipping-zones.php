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

	private function load_zones() {

		$zone_ids          = array_keys( WC_Shipping_Zones::get_zones() );
		$wc_shipping_zones = array( WC_Shipping_Zones::get_zone( 0 ) );
		foreach ( $zone_ids as $zone_id ) {
			$wc_shipping_zones[] = WC_Shipping_Zones::get_zone( $zone_id );
		}

		$zones = array();
		foreach ( $wc_shipping_zones as $wc_shipping_zone ) {
			$country_codes = $this->get_country_codes( $wc_shipping_zone->get_zone_locations() );

			if ( empty( $country_codes ) ) {
				$zones[] = Shipping_Zone::from_wc_shipping_zone( $wc_shipping_zone );
			} else {
				foreach ( $country_codes as $country_code ) {
					$zones[] = Shipping_Zone::from_wc_shipping_zone( $wc_shipping_zone, $country_code );
				}
			}
		}

		$this->zones = $zones;

	}

	public function add_available_delivery_method( &$jsonld ) {

		$this->load_zones();

		foreach ( $this->zones as $zone ) {
			$zone->add_available_delivery_method( $jsonld );
		}

	}

	/**
	 * @param array   $jsonld
	 * @param Product $product
	 */
	public function add_offer_shipping_details( &$jsonld, $product ) {

		$this->load_zones();

		foreach ( $this->zones as $zone ) {
			$zone->add_offer_shipping_details( $jsonld, $product );
		}

	}

	private function get_country_codes( $wc_shipping_zones ) {
		$countries = array();

		foreach ( $wc_shipping_zones as $wc_shipping_zone ) {
			if ( ! isset( $wc_shipping_zone->type )
				 || ( 'country' !== $wc_shipping_zone->type && 'state' !== $wc_shipping_zone->type ) ) {
				continue;
			}

			$country = substr( $wc_shipping_zone->code, 0, 2 );

			if ( ! in_array( $country, $countries, true ) ) {
				$countries[] = $country;
			}
		}

		return $countries;
	}

}
