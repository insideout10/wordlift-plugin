<?php

namespace Wordlift\Shipping_Data;

use WC_Shipping_Method;
use WCML\Multicurrency\Shipping\ShippingModeProvider;

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

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function add_available_delivery_method( &$jsonld ) {

	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function add_shipping_rate( &$offer_shipping_details ) {

	}

	public function add_transit_time( &$shipping_delivery_time ) {

		$prefix   = "wcsdt_transit_m{$this->wc_shipping_method->get_instance_id()}";
		$property = 'transitTime';

		$option = get_option( 'wpsso_options' );

		if ( empty( $option[ "{$prefix}_unit_code" ] )
			 || ! isset( $option[ "{$prefix}_minimum" ] )
			 || ! isset( $option[ "{$prefix}_maximum" ] )
			 || ! is_numeric( $option[ "{$prefix}_minimum" ] )
			 || ! is_numeric( $option[ "{$prefix}_maximum" ] ) ) {
			return;
		}

		$unit_code = $option[ "{$prefix}_unit_code" ];
		$minimum   = $option[ "{$prefix}_minimum" ];
		$maximum   = $option[ "{$prefix}_maximum" ];

		if ( 'HUR' === $unit_code ) {
			$minimum = floor( $minimum / 24.0 );
			$maximum = ceil( $maximum / 24.0 );
		}

		$shipping_delivery_time[ $property ] = array(
			'@type'    => 'QuantitativeValue',
			'minValue' => intval( $minimum ),
			'maxValue' => intval( $maximum ),
		);

	}

	protected function change_to_manual_currency( &$shipping_rate ) {
		// WCML not available.
		if ( ! class_exists( 'WCML\Multicurrency\Shipping\ShippingModeProvider' ) ) {
			return;
		}

		// Manual pricing not enabled.
		if ( ! ShippingModeProvider::get( $this->wc_shipping_method->id )->isManualPricingEnabled( $this->wc_shipping_method ) ) {
			return;
		}

		// Get the first manual price.
		foreach ( $this->wc_shipping_method->instance_settings as $key => $value ) {
			if ( preg_match( '@^cost_(\w{3})$@', $key, $matches ) ) {
				$shipping_rate['value']    = $value;
				$shipping_rate['currency'] = $matches[1];

				return;
			}
		}
	}

}
