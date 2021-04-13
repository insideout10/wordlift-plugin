<?php


namespace Wordlift\Shipping_Data;


use WC_Shipping_Zone;

class Shipping_Zone {

	/**
	 * @var WC_Shipping_Zone
	 */
	private $wc_shipping_zone;

	/**
	 * @var Shipping_Method[]
	 */
	private $methods;

	/**
	 * @var string
	 */
	private $country_code;

	/**
	 * @var string[]
	 */
	private $regions = array();

	/**
	 * @var string[]
	 */
	private $postal_codes = array();

	/**
	 * @var string[]
	 */
	private $postal_code_prefixes = array();

	/**
	 * @var string[]
	 */
	private $postal_code_ranges = array();

	/**
	 * Shipping_Zone constructor.
	 *
	 * @param WC_Shipping_Zone $wc_shipping_zone
	 * @param string $country_code
	 */
	public function __construct( $wc_shipping_zone, $country_code ) {

		$this->wc_shipping_zone = $wc_shipping_zone;
		$this->country_code     = $country_code;

		$this->load_zone_locations();

	}

	private function load_zone_locations() {

		$this->regions              = array();
		$this->postal_codes         = array();
		$this->postal_code_prefixes = array();
		$this->postal_code_ranges   = array();

		// Bail out if country code isn't set.
		if ( ! isset( $this->country_code ) ) {
			return;
		}

		foreach ( $this->wc_shipping_zone->get_zone_locations() as $zone_location ) {
			switch ( $zone_location->type ) {
				case 'state':
					if ( 0 === strpos( $zone_location->code, "$this->country_code:" ) ) {
						$this->regions[] = substr( $zone_location->code, 3 );
					}

					break;

				case 'postcode':
					if ( '*' === substr( $zone_location->code, - 1 ) ) {
						$this->postal_code_prefixes[] = $zone_location->code;
					} else if ( - 1 < strpos( $zone_location->code, '...' ) ) {
						$this->postal_code_ranges[] = $zone_location->code;
					} else {
						$this->postal_codes[] = $zone_location->code;
					}

					break;

				default:
			}
		}

	}

	private function load_methods() {

		$this->methods = array_map( 'Wordlift\Shipping_Data\Shipping_Method::from_wc_shipping_method',
			$this->wc_shipping_zone->get_shipping_methods( true ) );

	}

	public function add_available_delivery_method( &$jsonld ) {

		$this->load_methods();

		foreach ( $this->methods as $method ) {
			$method->add_available_delivery_method( $jsonld );
		}

	}

	/**
	 * @param array $jsonld
	 * @param Product $product
	 */
	public function add_offer_shipping_details( &$jsonld, $product ) {

		$this->load_methods();

		// Ignore the default zone if no methods are configured.
		if ( 0 === $this->wc_shipping_zone->get_id() && 0 === count( $this->methods ) ) {
			return;
		}

		$this->make_sure_shipping_details_exists_and_it_is_an_array( $jsonld );

		$offer_shipping_details = array( '@type' => 'OfferShippingDetails', );

		$this->add_shipping_destination( $offer_shipping_details );

		/*
		 * Use Case UC003
		 * 1.4.3
		 *
		 * https://editorial.thenextweb.com/wp-admin/admin-ajax.php?action=wl_ttl_cache_cleaner__flush
		 */
		foreach ( $this->methods as $method ) {
			$method->add_shipping_rate( $offer_shipping_details );
		}

		/*
		 * Use Case UC004
		 */
		$product->add_handling_time( $offer_shipping_details );

		$jsonld['shippingDetails'][] = $offer_shipping_details;

	}

	private function make_sure_shipping_details_exists_and_it_is_an_array( &$jsonld ) {

		if ( ! isset( $jsonld['shippingDetails'] ) ) {
			$jsonld['shippingDetails'] = array();
		}

		if ( ! is_array( $jsonld['shippingDetails'] ) ||
		     ( ! empty( $jsonld['shippingDetails'] ) && ! is_numeric( key( $jsonld['shippingDetails'] ) ) ) ) {
			$jsonld['shippingDetails'] = array( $jsonld['shippingDetails'] );
		}

	}

	private function add_shipping_destination( &$shipping_details ) {

		if ( ! isset( $this->country_code ) ) {
			return;
		}

		$shipping_destination = array(
			'@type'          => 'DefinedRegion',
			'addressCountry' => $this->country_code,
		);

		$this->add_address_region( $shipping_destination );
		$this->add_postal_code( $shipping_destination );
		$this->add_postal_code_prefix( $shipping_destination );
		$this->add_postal_code_range( $shipping_destination );

		$shipping_details['shippingDestination'] = $shipping_destination;


	}

	private function add_address_region( &$shipping_destination ) {

		if ( empty( $this->regions ) ) {
			return;
		}

		$shipping_destination['addressRegion'] = $this->regions;

	}

	private function add_postal_code( &$shipping_destination ) {

		if ( empty( $this->postal_codes ) ) {
			return;
		}

		$shipping_destination['postalCode'] = $this->postal_codes;

	}

	private function add_postal_code_prefix( &$shipping_destination ) {

		if ( empty( $this->postal_code_prefixes ) ) {
			return;
		}

		foreach ( $this->postal_code_prefixes as $postal_code_prefix ) {
			$shipping_destination['postalCodePrefix'][] = substr( $postal_code_prefix, 0, - 1 );
		}

	}

	private function add_postal_code_range( &$shipping_destination ) {

		if ( empty( $this->postal_code_ranges ) ) {
			return;
		}

		$shipping_destination['postalCodeRanges'] = array();
		foreach ( $this->postal_code_ranges as $post_code_range ) {
			$range = explode( '...', $post_code_range );

			$shipping_destination['postalCodeRanges'][] = array(
				'postalCodeBegin' => $range[0],
				'postalCodeEnd'   => $range[1],
			);
		}

	}

	public static function from_wc_shipping_zone( $wc_shipping_zone, $country_code = null ) {

		return new self( $wc_shipping_zone, $country_code );
	}

}