<?php
/**
 * Define the menu entry and the page for 'Download Your Data'.
 *
 * @todo       transition this page to {@link Wordlift_Admin_Page}.
 *
 * @since      3.6.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

use Wordlift\Api\Default_Api_Service;

/**
 * The Wordlift_Admin_Download_Your_Data_Page class.
 *
 * @since 3.6.0
 */
class Wordlift_Admin_Download_Your_Data_Page {
	/**
	 * Used to check if the requested file is supported.
	 *
	 * @since  3.16.0
	 * @access private
	 * @var $allowed_formats array Allowed formats.
	 */
	private $allowed_formats = array(
		'json',
		'rdf',
		'ttl',
		'n3',
	);

	/**
	 * The list of headers allowed by the endpoint.
	 *
	 * @since 3.28.2
	 * @var string[]
	 */
	private $allowed_headers = array(
		'json' => 'application/ld+json',
		'rdf'  => 'application/rdf+xml',
		'n3'   => 'text/n3',
		'ttl'  => 'text/turtle',
	);

	/**
	 * Hook to 'admin_menu' to add the 'Download Your Data' page.
	 *
	 * @since 3.6.0
	 */
	public function admin_menu() {

		// Add a callback to our 'page' function.
		add_submenu_page(
			'wl_admin_menu',
			_x( 'Download Your Data', 'Page title', 'wordlift' ),
			_x( 'Download Your Data', 'Menu title', 'wordlift' ),
			'manage_options',
			'wl_download_your_data',
			array( $this, 'page' )
		);

	}

	/**
	 * The admin menu callback to render the page.
	 *
	 * @since 3.6.0
	 */
	public function page() {

		// Include the partial.
		include plugin_dir_path( __FILE__ ) . 'partials/wordlift-admin-download-your-data.php';

	}

	/**
	 * Ajax call to redirect to a download URL.
	 *
	 * @since 3.6.0
	 */
	public function download_your_data() {

		$default_api_service = Default_Api_Service::get_instance();

		// Avoid PHP notices when buffer is empty.
		if ( ob_get_contents() ) {
			ob_end_clean();
		}

		// Use json suffix by default.
		$suffix = 'json';

		// Check if there is suffix.
		if ( isset( $_GET['out'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$suffix = sanitize_text_field( wp_unslash( $_GET['out'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		// Create filename.
		$filename = 'dataset.' . $suffix;

		if ( ! in_array( $suffix, $this->allowed_formats, true ) ) {
			// The file type is not from allowed types.
			wp_die( esc_html__( 'The format is not supported.', 'wordlift' ) );
		}

		$accept_header_format = $this->allowed_headers[ $suffix ];

		$headers = array(
			'Accept' => $accept_header_format,
		);

		$response = $default_api_service->get( '/dataset/export', $headers );

		$response = $response->get_response();

		if (
			is_wp_error( $response ) ||
			200 !== (int) $response['response']['code']
		) {
			// Something is not working properly, so display error message.
			wp_die( esc_html__( 'There was an error trying to connect to the server. Please try again later.', 'wordlift' ) );
		}

		// Get response body.
		$body     = wp_remote_retrieve_body( $response );
		$type     = wp_remote_retrieve_header( $response, 'content-type' );
		$filename = 'dataset-' . gmdate( 'Y-m-d-H-i-s' ) . '.' . $suffix;

		// Add proper file headers.
		header( "Content-Disposition: attachment; filename=$filename" );
		header( "Content-Type: $type" );

		/*
		 * Echo the response body. As this is not HTML we can not escape it
		 * and neither sanitize it, therefor turning off the linter notice.
		 */
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This is an RDF file which is downloaded to the client (see the `Content-Disposition: attachment` header above).

		// Exit in both cases.
		exit;
	}
}
