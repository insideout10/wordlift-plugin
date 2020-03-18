<?php

/**
 * Taxonomy JSON LD adapter, intercepts the call to taxonomy page
 * and loads related json ld for the taxonomy.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 */
namespace Wordlift\Jsonld;

class Taxonomy_Jsonld_Adapter {
	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.26.0
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.26.0
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * Wordlift_Term_JsonLd_Adapter constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 * @param \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.26.0
	 *
	 */
	public function __construct( $entity_uri_service, $jsonld_service ) {

		add_action( 'wp_head', array( $this, 'wp_head' ) );
		$this->entity_uri_service = $entity_uri_service;
		$this->jsonld_service     = $jsonld_service;

	}

	public function wp_head() {
		// Bail out if it is not a taxonomy page
		if ( ! is_tax() ) {
			return;
		}
		$jsonld        = array();
		$jsonld_string = wp_json_encode( $jsonld );

		echo "<script type=\"application/ld+json\">$jsonld_string</script>";
	}

}