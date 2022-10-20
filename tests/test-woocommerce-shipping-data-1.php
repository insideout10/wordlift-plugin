<?php

/**
 * Class Woocommerce_Shipping_Data_Test
 *
 * Use Case #1
 * @link https://docs.google.com/spreadsheets/d/1cFpGjB6oJeGV2h0L3VLMs_IgCCHf7wxIY6t0a2jqZ1Y/edit#gid=0
 * @group woocommerce
 */
class Woocommerce_Shipping_Data_Test_1 extends WP_UnitTestCase {

	/**
	 * To install required plugins:
	 *
	 * ./bin/install-wp-tests.sh wordpress wordpress password db 5.6 true
	 */

	function test() {

		$this->skip_if_plugins_not_active();

		$jsonld = apply_filters( 'wl_entity_jsonld', array(
			'@type'  => 'Product',
			'offers' => array(
				'@type'         => 'Offer',
				'priceCurrency' => 'GBP',
			)
		), - 1, array() );

		$this->assertFalse( isset( $jsonld['offers'][0]['availableDeliveryMethod'] ), 'Property found in ' . var_export( $jsonld, true ) );

	}

	private function skip_if_plugins_not_active() {

		foreach (
			array(
				'woocommerce/woocommerce.php',
				'wpsso/wpsso.php',
				'wpsso-wc-shipping-delivery-time/wpsso-wc-shipping-delivery-time.php',
			) as $plugin_name
		) {
			if ( ! is_plugin_active( $plugin_name ) ) {
				$this->markTestSkipped( "{$plugin_name} is not active" );
			}

		}

	}

}
