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

}
