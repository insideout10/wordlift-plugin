<?php

namespace Wordlift\Shipping_Data;

use WC_Shipping_Method;
use WCML_Multi_Currency;

class Shipping_Method {

	private $wcml;

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
	public function add_shipping_rate( &$offer_shipping_details, $jsonld ) {
		if ( ! isset( $offer_shipping_details['shippingRate'] ) ) {
			$offer_shipping_details['shippingRate'] = array();
		}

		$shipping_rate = $this->get_shipping_rate();

		$this->change_to_manual_currency( $shipping_rate );

		// Only add the shipping rate if the currency matches the offer currency.
		$currency = self::get_offer_price_currency( $jsonld );
		if ( $currency && $currency === $shipping_rate['currency'] ) {
			$offer_shipping_details['shippingRate'][] = $shipping_rate;
		}

	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	protected function get_shipping_rate() {
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
		if ( ! $this::wcml_requirements() ) {
			return;
		}

		// Get WCML.
		if ( ! isset( $this->wcml ) ) {
			$this->wcml = new WCML_Multi_Currency();
		}

		$currencies = $this->wcml->get_currencies();
		if ( empty( $currencies ) ) {
			return;
		}

		// Manual pricing not enabled.
		$instance = $this->wc_shipping_method->instance_settings;
		if ( ! isset( $instance['wcml_shipping_costs'] ) || 'manual' !== $instance['wcml_shipping_costs'] ) {
			return;
		}

		$currency_codes = array_keys( $currencies );

		$this->set_value_with_currency_codes( $shipping_rate, $instance, $currency_codes );

	}

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	protected function set_value_with_currency_codes( &$shipping_rate, $instance, $currency_codes ) {
		// Override.
	}

	/**
	 * @param array $jsonld {
	 *     An `Offer` structure.
	 *
	 * @type string $priceCurrency The price currency.
	 * @type array $priceSpecification {
	 *         A `PriceSpecification` structure.
	 *
	 * @type string $priceCurrency The price currency.
	 *     }
	 * }
	 *
	 * @return string|false The price currency or false.
	 */
	protected static function get_offer_price_currency( $jsonld ) {

		if ( isset( $jsonld['priceCurrency'] ) ) {
			return $jsonld['priceCurrency'];
		}

		if ( isset( $jsonld['priceSpecification']['priceCurrency'] ) ) {
			return $jsonld['priceSpecification']['priceCurrency'];
		}

		return false;
	}

	public static function wcml_requirements() {
		return class_exists( 'WCML_Multi_Currency' )
			   && function_exists( 'wcml_is_multi_currency_on' )
			   && wcml_is_multi_currency_on();
	}

}
