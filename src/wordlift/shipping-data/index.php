<?php

// Bail out if `WC_Shipping_Zones` isn't found.
use Wordlift\Shipping_Data\Offer_Structured_Data;
use Wordlift\Shipping_Data\Shipping_Zones;

if ( ! class_exists( '\WC_Shipping_Zones' ) ) {
	return;
}

$shipping_zones        = new Shipping_Zones();
$offer_structured_data = new Offer_Structured_Data( $shipping_zones );
