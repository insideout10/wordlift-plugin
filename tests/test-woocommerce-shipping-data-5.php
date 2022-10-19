<?php

/**
 * Class Woocommerce_Shipping_Data_Test
 *
 * Use Case #5
 * @link https://docs.google.com/spreadsheets/d/1cFpGjB6oJeGV2h0L3VLMs_IgCCHf7wxIY6t0a2jqZ1Y/edit#gid=0
 * @group woocommerce
 */
class Woocommerce_Shipping_Data_Test_5 extends WP_UnitTestCase {

	/**
	 * To install required plugins:
	 *
	 * ./bin/install-wp-tests.sh wordpress wordpress password db 5.6 true
	 */

	function test() {
		$this->skip_if_plugins_not_active();

		$this->add_zone_italy();
		$this->add_zone_canada_and_united_states();
		$this->set_no_shipping_class_handling_time();
		$product_id = $this->add_product();

		$jsonld = apply_filters( 'wl_entity_jsonld', array(
			'@type'  => 'Product',
			'offers' => array(
				'@type'         => 'Offer',
				'priceCurrency' => 'GBP',
			)
		), $product_id, array() );

		$this->assertEqualSets( array(
			'@type'    => 'QuantitativeValue',
			'minValue' => 0,
			'maxValue' => 2,
		), $jsonld['offers'][0]['shippingDetails'][1]['deliveryTime']['transitTime'] );

	}

	private function add_zone_italy() {

		$zone = new WC_Shipping_Zone();
		$zone->save();

		$zone->add_location( 'IT:RM', 'state' );
		$zone->add_location( 'IT:MI', 'state' );

		$zone->add_shipping_method( 'free_shipping' );

		$shipping_method_id        = $zone->add_shipping_method( 'flat_rate' );
		$flat_rate_shipping_method = WC_Shipping_Zones::get_shipping_method( $shipping_method_id );
		$flat_rate_shipping_method->add_rate( array( 'label' => 'Flat Rate', 'cost' => 10, ) );

		update_option( "woocommerce_flat_rate_{$shipping_method_id}_settings", array(
			'title'         => 'Flat rate',
			'tax_status'    => 'taxable',
			'cost'          => '10',
			'class_costs'   => '',
			'no_class_cost' => '',
			'type'          => 'class'
		), true );

		$zone->save();

		/*
		 * Add the transit time.
		 */
		$wpsso_options = get_option( 'wpsso_options' );
		$prefix        = "wcsdt_transit_m{$shipping_method_id}";

		$wpsso_options["{$prefix}_minimum"]   = 8;
		$wpsso_options["{$prefix}_maximum"]   = 36;
		$wpsso_options["{$prefix}_unit_code"] = 'HUR';

		update_option( 'wpsso_options', $wpsso_options );

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
			'title'      => 'Local pickup',
			'tax_status' => 'none',
			'cost'       => '10',
		), true );

		$zone->save();

	}

	private function set_no_shipping_class_handling_time() {

		$option = get_option( 'wpsso_options' );

		$option['wcsdt_handling_c0_minimum']   = 0;
		$option['wcsdt_handling_c0_maximum']   = 0;
		$option['wcsdt_handling_c0_unit_code'] = 'DAY';

		update_option( 'wpsso_options', $option );

	}

	private function add_product() {

		$term = wp_create_term( 'custom-shipping-class', 'product_shipping_class' );
		$this->set_shipping_class_handling_time( $term['term_id'] );

		$product_id = $this->factory()->post->create( array(
			'post_type' => 'product'
		) );

		$wc_product = wc_get_product( $product_id );
		$wc_product->set_shipping_class_id( $term['term_id'] );
		$wc_product->save();

		return $product_id;
	}

	private function set_shipping_class_handling_time( $term_id ) {

		$option = get_option( 'wpsso_options' );

		$option["wcsdt_handling_c{$term_id}_minimum"]   = 1;
		$option["wcsdt_handling_c{$term_id}_maximum"]   = 3;
		$option["wcsdt_handling_c{$term_id}_unit_code"] = 'DAY';

		update_option( 'wpsso_options', $option );

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
