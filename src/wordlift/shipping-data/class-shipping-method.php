<?php


namespace Wordlift\Shipping_Data;


use WC_Shipping_Method;

class Shipping_Method {

	/**
	 * @var WC_Shipping_Method $wc_shipping_method
	 */
	protected $wc_shipping_method;

	/**
	 * Shipping_Method constructor.
	 *
	 * @param WC_Shipping_Method $wc_shipping_method
	 */
	public function __construct( $wc_shipping_method ) {
		$this->wc_shipping_method = $wc_shipping_method;
	}

	/**
	 * @param WC_Shipping_Method $wc_shipping_method
	 *
	 * @return Shipping_Method
	 */
	public static function from_wc_shipping_method( $wc_shipping_method ) {

		switch ( get_class( $wc_shipping_method ) ) {
			case 'WC_Shipping_Local_Pickup':
				return new Local_Pickup_Shipping_Method( $wc_shipping_method );

			case 'WC_Shipping_Flat_Rate':
				return new Flat_Rate_Shipping_Method( $wc_shipping_method );

			case 'WC_Shipping_Free_Shipping':
				return new Free_Shipping_Shipping_Method( $wc_shipping_method );

			default:
				return new self( $wc_shipping_method );
		}

	}

	public function add_available_delivery_method( &$jsonld ) {

	}

}