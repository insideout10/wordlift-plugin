<?php


namespace Wordlift\Shipping_Data;


class Local_Pickup_Shipping_Method extends Shipping_Method {

	public function add_available_delivery_method( &$jsonld ) {

		if ( ! isset( $jsonld['availableDeliveryMethod'] ) ) {
			$jsonld['availableDeliveryMethod'] = array();
		}

		if ( ! is_array( $jsonld['availableDeliveryMethod'] ) ) {
			$jsonld['availableDeliveryMethod'] = array( $jsonld['availableDeliveryMethod'] );
		}

		if ( ! in_array( 'OnSitePickup', $jsonld['availableDeliveryMethod'] ) ) {
			array_push( $jsonld['availableDeliveryMethod'], 'OnSitePickup' );
		}

	}

	public function add_shipping_rate( &$offer_shipping_details ) {

		if ( ! isset( $offer_shipping_details['shippingRate'] ) ) {
			$offer_shipping_details['shippingRate'] = array();
		}

		$offer_shipping_details['shippingRate'][] = array(
			'@type'       => 'MonetaryAmount',
			'name'        => $this->wc_shipping_method->get_title(),
			'description' => wp_strip_all_tags( $this->wc_shipping_method->get_method_description() ),
			'value'       => $this->wc_shipping_method->get_option( 'cost' ) ?: '0',
			'currency'    => get_woocommerce_currency(),
		);

	}

}
