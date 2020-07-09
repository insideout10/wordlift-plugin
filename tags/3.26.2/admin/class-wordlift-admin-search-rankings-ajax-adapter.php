<?php
/**
 * AJAX Adapters: Search Rankings AJAX Adapter.
 *
 * Provides AJAX access to the {@link Wordlift_Admin_Search_Ranking_Service}.
 *
 * @since 3.20.0
 */

/**
 * Define the {@link Wordlift_Admin_Search_Rankings_Ajax_Adapter} class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Search_Rankings_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Admin_Search_Rankings_Service The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 */
	private $service;

	/**
	 * Wordlift_Admin_Search_Rankings_Ajax_Adapter constructor.
	 *
	 * @param $service \Wordlift_Admin_Search_Rankings_Service The {@link Wordlift_Admin_Search_Rankings_Service} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct( $service ) {

		$this->service = $service;

		add_action( 'wp_ajax_wl_search_rankings', array( $this, 'search_rankings' ) );

	}

	/**
	 * Get the Search Rankings.
	 *
	 * The Search Rankings are printed to the output as `application/json`.
	 *
	 * @since 3.20.0
	 */
	public function search_rankings() {

		$value = $this->service->get();

		if ( is_wp_error( $value ) ) {
			wp_send_json_error( $value );
		} else {
			wp_send_json_success( $value );
		}

		// We use this to maintain compatibility with WP 4.4.0.
		if ( apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_die();
		} else {
			die;
		}

	}

}
