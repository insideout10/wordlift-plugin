<?php
/**
 * Admin UI: Admin Entity Taxonomy List Page.
 *
 * The {@link Wordlift_Admin_Entity_Taxonomy_List_Page} class handles modifications
 * to the entity type list admin page
 *
 * @link       https://wordlift.io
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @since      3.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Entity taxonomy list admin page controller.
 *
 * Methods to manipulate whatever is displayed on the admin list page
 * for the entity taxonomy
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Admin_Entity_Taxonomy_List_Page {

	/**
	 * Hook to `wl_entity_type_row_actions` to add an "action" link to Thread
	 * SEO related settings for the term.
	 *
	 * @see   https://developer.wordpress.org/reference/hooks/taxonomy_row_actions/
	 *
	 * @since 3.11.0
	 *
	 * @param array  $actions An array of action links to be displayed. Default
	 *                        'Edit', 'Quick Edit', 'Delete', and 'View'.
	 * @param object $term    Term object.
	 *
	 * @return array  $actions An array of action links to be displayed. Default
	 *                        'Edit', 'Quick Edit', 'Delete', and 'View'.
	 */
	function wl_entity_type_row_actions( $actions, $term ) {

		$url               = admin_url( "admin.php?page=wl_entity_type_settings&tag_ID=$term->term_id" );
		$actions['wl_seo'] = '<a href="' . esc_url( $url ) . '">' . _x( 'SEO Settings', 'wordlift' ) . '</a>';

		return $actions;
	}
}
