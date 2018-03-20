<?php
/**
 * Define the {@link Wordlift_Uri_Service} responsible for managing entity URIs
 * (for posts, entities, authors, ...).
 */

/**
 */
class Wordlift_Uri_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The global WordPress database connection.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \wpdb $wpdb The global WordPress database connection.
	 */
	private $wpdb;

	/**
	 * Create an instance of Wordlift_Uri_Service.
	 *
	 * @since 3.6.0
	 *
	 * @param wpdb $wpdb The global WordPress database connection.
	 */
	public function __construct( $wpdb ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Uri_Service' );

		$this->wpdb = $wpdb;

	}

	/**
	 * Delete all generated URIs from the database.
	 *
	 * @since 3.6.0
	 */
	public function delete_all() {

		// Delete URIs associated with posts/entities.
		$this->wpdb->delete( $this->wpdb->postmeta, array( 'meta_key' => 'entity_url' ) );

		// Delete URIs associated with authors.
		$this->wpdb->delete( $this->wpdb->usermeta, array( 'meta_key' => '_wl_uri' ) );

	}

}
