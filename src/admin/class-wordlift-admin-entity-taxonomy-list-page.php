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
		$actions['wl_seo'] = '<a href="' . esc_url( $url ) . '">' . __( 'SEO Settings', 'wordlift' ) . '</a>';

		return $actions;
	}

	/**
	 * Override the capabilities related to managing the entity type terms
	 * for multisite super admin to prevent it from manipulating it in any
	 * way.
	 *
	 * @since 3.12.0
	 *
	 * @param array  $caps    The user's current capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 *
	 * @return array 	Array containing the do_not_allow capability for super admin
	 *                  when editing and deleting entity type terms capabilities
	 *					are being "approved"
	 */
	function restrict_super_admin( $caps, $cap, $user_id, $args ) {

		if ( is_super_admin() ) {
			switch ( $cap ) {
				case 'wl_entity_type_edit_term':
				case 'wl_entity_type_delete_term':
					$caps[] = 'do_not_allow';
			}
		}

		return $caps;
	}
}
