<?php
/**
 * Pages: Admin Status Page.
 *
 * A page which reports WordLift's status, currently checking for duplicated entities.
 * This class is WIP and useful to delete entities that have been created out of
 * revisions.
 *
 * @since      3.9.8
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Status_Page} class.
 *
 * @since 3.6.0
 */
class Wordlift_Admin_Status_Page extends Wordlift_Admin_Page {


//	private function delete_entity_branches() {
//
//		global $wpdb;
//
//		$results = $wpdb->get_results(
//			"SELECT DISTINCT p.id" .
//			" FROM $wpdb->posts p" .
//			// Get the post revisions.
//			" WHERE p.post_parent > 0" .
//			"  AND p.post_type = 'entity'"
//		);
//
//		foreach ( $results as $result ) {
//			wp_delete_post( $result->id, true );
//		}
//
//		return sizeof( $results );
//	}
//
//	private function delete_entity_revisions() {
//
//		global $wpdb;
//
//		$results = $wpdb->get_results(
//			"SELECT DISTINCT r.id" .
//			" FROM $wpdb->posts p, $wpdb->posts r" .
//			" WHERE r.post_type = 'revision'" .
//			"  AND p.post_type = 'entity'" .
//			"  AND p.id = r.post_parent"
//		);
//
//		foreach ( $results as $result ) {
//			wp_delete_post_revision( $result->id );
//		}
//
//		return sizeof( $results );
//	}

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.12.2
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;
	/**
	 * @var
	 */
	private $sparql_service;

	/**
	 * Create a {@link Wordlift_Admin_Status_Page} instance.
	 *
	 * @since 3.12.2
	 *
	 * @param \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Sparql_Service $sparql_service The {@link Wordlift_Sparql_Service} instance.
	 */
	public function __construct( $entity_service, $sparql_service ) {

		$this->entity_service = $entity_service;
		$this->sparql_service = $sparql_service;

	}

	/**
	 * Get the list of entity URIs.
	 *
	 * @since 3.12.2
	 * @return array An array of entity URIs.
	 */
	protected function get_entity_uris() {

		// Get the entity IDs.
		$ids = $this->entity_service->get( array(
			'numberposts' => - 1,
			'fields'      => 'ids',
			'post_status' => 'publish',
		) );

		// Create a reference to the entity service for the closure.
		$entity_service = $this->entity_service;

		// Combine IDs with URIs.
		return array_combine( $ids, array_map( function ( $item ) use ( $entity_service ) {
			return $entity_service->get_uri( $item );
		}, $ids ) );
	}

	/**
	 * Get the list of URIs in the Linked Data Cloud.
	 *
	 * @since 3.12.2
	 * @return array|null An array of URIs.
	 */
	protected function get_linked_data_uris() {

		// Prepare the query to get the URIs from the Linked Data Cloud.
		$query = Wordlift_Query_Builder::new_instance()
		                               ->select( 'DISTINCT ?s' )
		                               ->statement( '?s', Wordlift_Query_Builder::RDFS_TYPE_URI, '?o' )
		                               ->build();

		// Execute the query.
		$response = $this->sparql_service->select( $query );

		// If the response is an error, return null.
		if ( is_a( $response, 'WP_Error' ) ) {
			return null;
		}

		// Split the response into single URIs.
		$uris = preg_split( "/(\r\n|\n|\r)/", $response['body'] );

		// Remove the header.
		unset( $uris[0] );

		// Finally return the URIs.
		return $uris;
	}

	/**
	 * Get the page title. Will be translated.
	 *
	 * @since 3.11.0
	 *
	 * @return string The page title.
	 */
	function get_page_title() {

		return _x( 'Status Report', 'Page title', 'wordlift' );
	}

	/**
	 * Get the menu title. Will be translated.
	 *
	 * @since 3.11.0
	 *
	 * @return string The menu title.
	 */
	function get_menu_title() {

		return _x( 'Status Report', 'Menu title', 'wordlift' );
	}

	/**
	 * Get the menu slug.
	 *
	 * @since 3.11.0
	 *
	 * @return string The menu slug.
	 */
	function get_menu_slug() {

		return 'wl_status_report';
	}

	/**
	 * Get the partial file name, used in the {@link render} function.
	 *
	 * @since 3.11.0
	 *
	 * @return string The partial file name.
	 */
	function get_partial_name() {


		return 'wordlift-admin-status-page.php';
	}

}
