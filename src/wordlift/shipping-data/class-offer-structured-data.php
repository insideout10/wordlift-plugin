<?php

namespace Wordlift\Shipping_Data;

class Offer_Structured_Data {

	/**
	 * @var Shipping_Zones
	 */
	private $shipping_zones;

	/**
	 * Offer_Structured_Data constructor.
	 *
	 * @param Shipping_Zones $shipping_zones
	 */
	public function __construct( $shipping_zones ) {

		$this->shipping_zones = $shipping_zones;

		add_filter( 'wl_entity_jsonld', array( $this, 'entity_jsonld' ), 10, 2 );

	}

	public function entity_jsonld( $jsonld, $post_id ) {

		// Bail out if it's not a Product or the offers property isn't set.
		if ( ! in_array( 'Product', (array) $jsonld['@type'], true ) || ! isset( $jsonld['offers'] ) ) {
			return $jsonld;
		}

		if ( ! is_array( $jsonld['offers'] ) || ! is_numeric( key( $jsonld['offers'] ) ) ) {
			$jsonld['offers'] = array( $jsonld['offers'] );
		}

		$product = new Product( $post_id );

		foreach ( $jsonld['offers'] as &$offer ) {
			$this->shipping_zones->add_available_delivery_method( $offer );
			$this->shipping_zones->add_offer_shipping_details( $offer, $product );
		}

		return $jsonld;
	}

}
