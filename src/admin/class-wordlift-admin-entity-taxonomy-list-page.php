<?php
/**
 * Admin UI: Wordlift_Admin_Entity_Taxonomy_List_Page
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
	 * SEO related settings for the term
	 *
	 * @since 3.11.0
	 */
	function wl_entity_type_row_actions( $actions, $term ) {
		$url = admin_url( "admin.php?page=wl_entity_type_settings&amp;tag_ID=$term->term_id" );
		$actions['seo'] = '<a href="' . esc_url( $url ) . '">' . __( 'SEO Settings', 'wordlift' ) . '</a>';
		return $actions;
	}
}
