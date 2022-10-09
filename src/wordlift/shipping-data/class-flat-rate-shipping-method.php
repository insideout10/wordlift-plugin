<?php

namespace Wordlift\Shipping_Data;

use WCML\Multicurrency\Shipping\ShippingModeProvider;

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

	public function add_shipping_rate( &$offer_shipping_details ) {

		if ( ! isset( $offer_shipping_details['shippingRate'] ) ) {
			$offer_shipping_details['shippingRate'] = array();
		}

		$description = isset( $this->wc_shipping_method->instance_settings['description'] )
			? wp_strip_all_tags( $this->wc_shipping_method->instance_settings['description'] ) : '';

		$shipping_rate = array(
			'@type'       => 'MonetaryAmount',
			'name'        => $this->wc_shipping_method->get_title(),
			'description' => $description,
			'value'       => $this->wc_shipping_method->get_option( 'cost' ),
			'currency'    => get_woocommerce_currency(),
		);

		$this->change_to_manual_currency( $shipping_rate );

		$offer_shipping_details['shippingRate'][] = $shipping_rate;

	}



}
