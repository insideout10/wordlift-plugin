<?php

/**
 * Class Woocommerce_Shipping_Data_Test
 *
 * Use Case #2
 * @link https://docs.google.com/spreadsheets/d/1cFpGjB6oJeGV2h0L3VLMs_IgCCHf7wxIY6t0a2jqZ1Y/edit#gid=0
 * @group woocommerce
 */
class Woocommerce_Shipping_Data_Test_2 extends WP_UnitTestCase {

	/**
	 * To install required plugins:
	 *
	 * ./bin/install-wp-tests.sh wordpress wordpress password db 5.6 true
	 */

	function test() {
		$this->skip_if_plugins_not_active();

		$this->add_zone();

		$jsonld = apply_filters( 'wl_entity_jsonld', array(
			'@type'  => 'Product',
			'offers' => array(
				'@type'         => 'Offer',
				'priceCurrency' => 'GBP',
			)
		), - 1, array() );

		$this->assertTrue( isset( $jsonld['offers'][0]['shippingDetails'] ) );

		$shipping_details = $jsonld['offers'][0]['shippingDetails'];
		$this->assertCount( 2, $jsonld['offers'][0]['shippingDetails'] );

		$this->assertEqualSets( array(
			'@type'            => 'DefinedRegion',
			'addressCountry'   => 'US',
			'postalCode'       => array( '10000' ),
			'postalCodePrefix' => array( '400' ),
			'postalCodeRanges' => array(
				array(
					'postalCodeBegin' => '20000',
					'postalCodeEnd'   => '30000',
				)
			)
		), $shipping_details[0]['shippingDestination'] );

		$this->assertEqualSets( array(
			'@type'            => 'DefinedRegion',
			'addressCountry'   => 'IT',
			'addressRegion'    => array( 'RM' ),
			'postalCode'       => array( '10000' ),
			'postalCodePrefix' => array( '400' ),
			'postalCodeRanges' => array(
				array(
					'postalCodeBegin' => '20000',
					'postalCodeEnd'   => '30000',
				)
			)
		), $shipping_details[1]['shippingDestination'] );

	}

	private function add_zone() {

		$zone = new WC_Shipping_Zone();
		$zone->save();

		$zone->add_location( 'US', 'country' );
		$zone->add_location( 'IT:RM', 'state' );
		$zone->add_location( '10000', 'postcode' );
		$zone->add_location( '20000...30000', 'postcode' );
		$zone->add_location( '400*', 'postcode' );
		$zone->save();

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
