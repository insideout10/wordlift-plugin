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
class Wordlift_Admin_Status_Page {

	/**
	 * Hook to 'admin_menu' to add the 'Status Report' page.
	 *
	 * @since 3.9.8
	 */
	public function admin_menu() {

		// Add a callback to our 'page' function.
		add_submenu_page(
			'wl_admin_menu',
			_x( 'Status Report', 'Page title', 'wordlift' ),
			_x( 'Status Report', 'Menu title', 'wordlift' ),
			'manage_options',
			'wl_status_report',
			array( $this, 'page', )
		);

	}

	/**
	 * The admin menu callback to render the page.
	 *
	 * @since 3.9.8
	 */
	public function page() {

		$branches  = $this->delete_entity_branches();
		$revisions = $this->delete_entity_revisions();

		// Include the partial.
		include( 'partials/wordlift-admin-status-page.php' );

	}

	private function delete_entity_branches() {

		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT DISTINCT p.id" .
			" FROM $wpdb->posts p" .
			// Get the post revisions.
			" WHERE p.post_parent > 0" .
			"  AND p.post_type = 'entity'"
		);

		foreach ( $results as $result ) {
			wp_delete_post( $result->id, true );
		}

		return sizeof( $results );
	}

	private function delete_entity_revisions() {

		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT DISTINCT r.id" .
			" FROM $wpdb->posts p, $wpdb->posts r" .
			" WHERE r.post_type = 'revision'" .
			"  AND p.post_type = 'entity'" .
			"  AND p.id = r.post_parent"
		);

		foreach ( $results as $result ) {
			wp_delete_post_revision( $result->id );
		}

		return sizeof( $results );
	}

}
