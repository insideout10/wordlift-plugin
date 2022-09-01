<?php
/**
 * Wordlift_Autocomplete_Adapter class.
 *
 * The {@link Wordlift_Autocomplete_Adapter} class create requests to external API's.
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @since      3.15.0
 */

use Wordlift\Autocomplete\Autocomplete_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create autocomplete request to external API's and return the result if there is such.
 *
 * @since 3.15.0
 */
class Wordlift_Autocomplete_Adapter {

	/**
	 * The {@link Autocomplete_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var Autocomplete_Service $configuration_service The {@link Autocomplete_Service} instance.
	 */
	private $autocomplete_service;

	/**
	 * Wordlift_Autocomplete_Adapter constructor.
	 *
	 * @param Autocomplete_Service $autocomplete_service The {@link Autocomplete_Service} instance.
	 *
	 * @since 3.14.2
	 */
	public function __construct( $autocomplete_service ) {
		$this->autocomplete_service = $autocomplete_service;
	}

	/**
	 * Handle the autocomplete ajax request.
	 *
	 * @since 3.15.0
	 */
	public function wl_autocomplete() {

		check_ajax_referer( 'wl_autocomplete' );

		// Return error if the query param is empty.
		if ( ! empty( $_REQUEST['query'] ) ) { // Input var okay.
			$query = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) ); // Input var okay.
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The query param is empty.', 'wordlift' ),
				)
			);
		}

		// Get the exclude parameter.
		$exclude = ! empty( $_REQUEST['exclude'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['exclude'] ) ) : '';

		$scope = ! empty( $_REQUEST['scope'] )
			? sanitize_text_field( wp_unslash( $_REQUEST['scope'] ) ) : WL_AUTOCOMPLETE_SCOPE;

		/**
		 * @since 3.26.1
		 * Providing a way for term pages to show and save local entities.
		 */
		$show_local_entities = false;

		if ( isset( $_REQUEST['show_local_entities'] )
			 && ! empty( $_REQUEST['show_local_entities'] ) ) { // Make request.
			$show_local_entities = filter_var( wp_unslash( $_REQUEST['show_local_entities'] ), FILTER_VALIDATE_BOOLEAN );
		}

		// Add the filter to check if we need to show local entities or not.
		add_filter(
			'wl_show_local_entities',
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			function ( $state ) use ( $show_local_entities ) {
				return $show_local_entities;
			}
		);

		$results = $this->autocomplete_service->query( $query, $scope, $exclude );

		// Clear any buffer.
		ob_clean();

		wp_send_json_success( $results );

	}
}
