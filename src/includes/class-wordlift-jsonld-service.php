<?php
/**
 * Define the Wordlift_Jsonld_Service class to support JSON-LD.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * This class exports an entity using JSON-LD.
 *
 * @since 3.8.0
 */
class Wordlift_Jsonld_Service {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Post_Converter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Post_Converter A {@link Wordlift_Post_Converter} instance.
	 */
	private $converter;

	/**
	 * Create a JSON-LD service.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Post_Converter $converter      A {@link Wordlift_Uri_To_Jsonld_Converter} instance.
	 */
	public function __construct( $entity_service, $converter ) {

		$this->entity_service = $entity_service;
		$this->converter      = $converter;

	}

	/**
	 * Process calls to the AJAX 'wl_jsonld' endpoint.
	 *
	 * @since 3.8.0
	 */
	public function get() {

		// Tell NewRelic to ignore us, otherwise NewRelic customers might receive
		// e-mails with a low apdex score.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/521
		Wordlift_NewRelic_Adapter::ignore_apdex();

		// Clear the buffer to be sure someone doesn't mess with our response.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/406.
		// See https://codex.wordpress.org/AJAX_in_Plugins.
		ob_clean();

		// If no id has been provided return an empty array.
		if ( ! isset( $_REQUEST['id'] ) ) {
			wp_send_json( array() );
		}

		// Get the id.
		$id = $_REQUEST['id'];

		// An array of references which is captured when converting an URI to a
		// json which we gather to further expand our json-ld.
		$references = array();

		// Set a reference to the entity_to_jsonld_converter to use in the closures.
		$entity_to_jsonld_converter = $this->converter;

		// Convert each URI to a JSON-LD array, while gathering referenced entities.
		// in the references array.
		$jsonld = array_merge(
			array( $entity_to_jsonld_converter->convert( $id, $references ) ),
			// Convert each URI in the references array to JSON-LD. We don't output
			// entities already output above (hence the array_diff).
			array_map( function ( $item ) use ( $entity_to_jsonld_converter, $references ) {

				// "2nd level properties" may not output here, e.g. a post
				// mentioning an event, located in a place: the place is referenced
				// via the `@id` but no other properties are loaded.
				return $entity_to_jsonld_converter->convert( $item, $references );
			}, $references ) );

		// Finally send the JSON-LD.
		wp_send_json( $jsonld );

	}

}
