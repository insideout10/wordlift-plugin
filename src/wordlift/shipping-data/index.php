<?php

// Bail out if `WC_Shipping_Zones` isn't found.
use Wordlift\Shipping_Data\Offer_Structured_Data;
use Wordlift\Shipping_Data\Shipping_Zones;

if ( ! class_exists( '\WC_Shipping_Zones' )
     || ! class_exists( '\WC_Shipping_Zone' )
     || ! class_exists( '\WC_Shipping_Method' )
     || ! function_exists( 'get_woocommerce_currency' )
     || ! function_exists( 'wc_get_product' ) ) {
	return;
}

$shipping_zones        = new Shipping_Zones();
$offer_structured_data = new Offer_Structured_Data( $shipping_zones );
