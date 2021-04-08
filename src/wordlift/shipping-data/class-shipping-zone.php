<?php


namespace Wordlift\Shipping_Data;


use WC_Shipping_Zone;

class Shipping_Zone {

	/**
	 * @var WC_Shipping_Zone
	 */
	private $wc_shipping_zone;

	/**
	 * @var Shipping_Method[]
	 */
	private $methods;

	/**
	 * Shipping_Zone constructor.
	 *
	 * @param WC_Shipping_Zone $wc_shipping_zone
	 */
	public function __construct( $wc_shipping_zone ) {

		$this->wc_shipping_zone = $wc_shipping_zone;

	}

	public function add_available_delivery_method( &$jsonld ) {

		$this->methods = array_map( 'Wordlift\Shipping_Data\Shipping_Method::from_wc_shipping_method',
			$this->wc_shipping_zone->get_shipping_methods( true ) );

		foreach ( $this->methods as $method ) {
			$method->add_available_delivery_method( $jsonld );
		}

	}

	public static function from_wc_shipping_zone( $wc_shipping_zone ) {

		return new self( $wc_shipping_zone );
	}

}