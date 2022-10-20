<?php

/**
 * Class Woocommerce_Shipping_Data_Test
 *
 * Use Case #3
 * @link https://docs.google.com/spreadsheets/d/1cFpGjB6oJeGV2h0L3VLMs_IgCCHf7wxIY6t0a2jqZ1Y/edit#gid=0
 * @group woocommerce
 */
class Woocommerce_Shipping_Data_Test_3 extends WP_UnitTestCase {

	/**
	 * To install required plugins:
	 *
	 * ./bin/install-wp-tests.sh wordpress wordpress password db 5.6 true
	 */

	function test() {
		$this->skip_if_plugins_not_active();

		$this->add_zone_italy();
		$this->add_zone_canada_and_united_states();

		$jsonld = apply_filters( 'wl_entity_jsonld', array(
			'@type'  => 'Product',
			'offers' => array(
				'@type'         => 'Offer',
				'priceCurrency' => 'GBP'
			)
		), - 1, array() );

		$this->assertEqualSets( array(
			'@type'               => 'OfferShippingDetails',
			'shippingDestination' => array(
				'@type'          => 'DefinedRegion',
				'addressCountry' => 'IT',
				'addressRegion'  => array( 'RM', 'MI', )
			),
			'shippingRate'        => array(
				array(
					'@type'       => 'MonetaryAmount',
					'name'        => 'Custom Free shipping Label',
					'description' => '',
					'value'       => '0',
					'currency'    => 'GBP'
				),
			),
		), $jsonld['offers'][0]['shippingDetails'][0] );

		$this->assertEqualSets( array(
			'@type'               => 'OfferShippingDetails',
			'shippingDestination' => array(
				'@type'          => 'DefinedRegion',
				'addressCountry' => 'IT',
				'addressRegion'  => array( 'RM', 'MI', )
			),
			'shippingRate'        => array(
				array(
					'@type'       => 'MonetaryAmount',
					'name'        => 'Custom Flat Rate Label',
					'description' => '',
					'value'       => '10',
					'currency'    => 'GBP'
				)
			),
		), $jsonld['offers'][0]['shippingDetails'][1] );

		$this->assertEqualSets( array(
			'@type'               => 'OfferShippingDetails',
			'shippingDestination' => array(
				'@type'          => 'DefinedRegion',
				'addressCountry' => 'CA'
			),
			'shippingRate'        => array(
				array(
					'@type'       => 'MonetaryAmount',
					'name'        => 'Custom Local Pickup Label',
					'description' => '',
					'value'       => '10',
					'currency'    => 'GBP'
				)
			)
		), $jsonld['offers'][0]['shippingDetails'][2] );

	}

	private function add_zone_italy() {

		$zone = new WC_Shipping_Zone();
		$zone->save();

		$zone->add_location( 'IT:RM', 'state' );
		$zone->add_location( 'IT:MI', 'state' );

		$free_shipping_method_id = $zone->add_shipping_method( 'free_shipping' );
		update_option( "woocommerce_free_shipping_{$free_shipping_method_id}_settings", array(
			'title' => 'Custom Free shipping Label',
		), true );

		$shipping_method_id        = $zone->add_shipping_method( 'flat_rate' );
		$flat_rate_shipping_method = WC_Shipping_Zones::get_shipping_method( $shipping_method_id );
		$flat_rate_shipping_method->add_rate( array( 'label' => 'Flat Rate', 'cost' => 10, ) );

		update_option( "woocommerce_flat_rate_{$shipping_method_id}_settings", array(
			'title'         => 'Custom Flat Rate Label',
			'tax_status'    => 'taxable',
			'cost'          => '10',
			'class_costs'   => '',
			'no_class_cost' => '',
			'type'          => 'class'
		), true );

		$zone->save();

	}

	private function add_zone_canada_and_united_states() {

		$zone = new WC_Shipping_Zone();
		$zone->save();

		$zone->add_location( 'CA', 'country' );
		$zone->add_location( 'US', 'country' );

		$shipping_method_id           = $zone->add_shipping_method( 'local_pickup' );
		$local_pickup_shipping_method = WC_Shipping_Zones::get_shipping_method( $shipping_method_id );
		$local_pickup_shipping_method->add_rate( array( 'label' => 'Local Pickup', 'cost' => 10, ) );

		update_option( "woocommerce_local_pickup_{$shipping_method_id}_settings", array(
			'title'      => 'Custom Local Pickup Label',
			'tax_status' => 'none',
			'cost'       => '10',
		), true );

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
