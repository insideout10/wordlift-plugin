<?php
/**
 * This file defines the JSON-LD adapter.
 *
 * The JSON-LD adapter hooks to the WordPress `wp_head` function to publish the JSON-LD in the `<head></head>` fragment.
 * At the same time the `wlSettings` localize script structure turns the JSON-LD off for asynchronous processing.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.25.1
 */

namespace Wordlift\Jsonld;

use Wordlift\Jsonld\Generator\Generator_Factory;
use Wordlift_Jsonld_Service;

/**
 * Class Jsonld_Adapter
 *
 * @package Wordlift\Jsonld
 */
class Jsonld_Adapter {

	/**
	 * @var Wordlift_Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * Jsonld_Adapter constructor.
	 *
	 * @param \Wordlift_Jsonld_Service $jsonld_service
	 */
	public function __construct( $jsonld_service ) {

		$this->jsonld_service = $jsonld_service;

		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'amp_post_template_head', array( $this, 'wp_head' ) );

	}

	public function wp_head() {

		// Bail out if `wl_jsonld_enabled` isn't enabled.
		if ( ! apply_filters( 'wl_jsonld_enabled', true ) ) {
			return;
		}

		$generator = Generator_Factory::get_instance(
			$this->jsonld_service,
			get_the_ID()
		);

		$generator->generate();

	}

}
