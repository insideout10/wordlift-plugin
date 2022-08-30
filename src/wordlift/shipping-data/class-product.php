<?php

namespace Wordlift\Shipping_Data;

class Product {

	/**
	 * @var false|\WC_Product|null
	 */
	private $wc_product;

	/**
	 * Product constructor.
	 *
	 * @param $post_id
	 */
	public function __construct( $post_id ) {

		$this->wc_product = wc_get_product( $post_id );

	}

	public function add_handling_time( &$shipping_delivery_time ) {

		// Bail out if there's no product.
		if ( empty( $this->wc_product ) ) {
			return;
		}

		$shipping_class_id = $this->wc_product->get_shipping_class_id();

		$option = get_option( 'wpsso_options' );

		if ( empty( $option[ "wcsdt_handling_c{$shipping_class_id}_unit_code" ] )
			 || empty( $option[ "wcsdt_handling_c{$shipping_class_id}_minimum" ] )
			 || empty( $option[ "wcsdt_handling_c{$shipping_class_id}_maximum" ] ) ) {
			return;
		}

		$unit_code = $option[ "wcsdt_handling_c{$shipping_class_id}_unit_code" ];
		$minimum   = $option[ "wcsdt_handling_c{$shipping_class_id}_minimum" ];
		$maximum   = $option[ "wcsdt_handling_c{$shipping_class_id}_maximum" ];

		if ( 'HUR' === $unit_code ) {
			$minimum = floor( $minimum / 24.0 );
			$maximum = ceil( $maximum / 24.0 );
		}

		$shipping_delivery_time['handlingTime'] = array(
			'@type'    => 'QuantitativeValue',
			'minValue' => $minimum,
			'maxValue' => $maximum,
		);

	}

	private function get_handling_time() {

	}

}
