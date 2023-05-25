<?php

namespace Wordlift\Shipping_Data;

class Flat_Rate_Shipping_Method extends Shipping_Method {

	public function add_available_delivery_method( &$jsonld ) {

		if ( ! isset( $jsonld['availableDeliveryMethod'] ) ) {
			$jsonld['availableDeliveryMethod'] = array();
		}

		if ( ! is_array( $jsonld['availableDeliveryMethod'] ) ) {
			$jsonld['availableDeliveryMethod'] = array( $jsonld['availableDeliveryMethod'] );
		}

		if ( ! in_array( 'ParcelDelivery', $jsonld['availableDeliveryMethod'], true ) ) {
			array_push( $jsonld['availableDeliveryMethod'], 'ParcelDelivery' );
		}

	}

	protected function get_shipping_rate() {

		$description = isset( $this->wc_shipping_method->instance_settings['description'] )
			? wp_strip_all_tags( $this->wc_shipping_method->instance_settings['description'] ) : '';

		return array(
			'@type'       => 'MonetaryAmount',
			'name'        => $this->wc_shipping_method->get_title(),
			'description' => $description,
			'value'       => $this->wc_shipping_method->get_option( 'cost' ),
			'currency'    => get_woocommerce_currency(),
		);

	}

	protected function set_value_with_currency_codes( &$shipping_rate, $instance, $currency_codes ) {

		// Get the first manual price.
		foreach ( $currency_codes as $code ) {
			if ( isset( $instance[ "cost_$code" ] ) ) {
				$shipping_rate['value']    = $instance[ "cost_$code" ];
				$shipping_rate['currency'] = $code;

				return;
			}
		}

	}

}
