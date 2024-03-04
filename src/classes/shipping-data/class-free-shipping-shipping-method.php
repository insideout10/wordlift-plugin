<?php

namespace Wordlift\Shipping_Data;

class Free_Shipping_Shipping_Method extends Shipping_Method {

	public function add_available_delivery_method( &$jsonld ) {

		if ( ! is_array( $jsonld ) ) {
			return;
		}

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
			'value'       => '0',
			'currency'    => get_woocommerce_currency(),
		);

	}

	protected function set_value_with_currency_codes( &$shipping_rate, $instance, $currency_codes ) {

		if ( ! empty( $currency_codes ) ) {
			$shipping_rate['currency'] = $currency_codes[0];
		}

	}

}
