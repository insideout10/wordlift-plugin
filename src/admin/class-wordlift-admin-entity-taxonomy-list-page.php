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
		$actions['wl-seo'] = '<a href="' . esc_url( $url ) . '">' . __( 'SEO Settings', 'wordlift' ) . '</a>';

		// Hacks for pre 4.7 compatibility:
		// * remove the quick edit.
		unset( $actions['inline hide-if-no-js'] );
		// * remove the edit link.
		unset( $actions['edit'] );

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
	 * @return array    Array containing the do_not_allow capability for super admin
	 *                  when editing and deleting entity type terms capabilities
	 *                    are being "approved"
	 */
	function restrict_super_admin( $caps, $cap, $user_id, $args ) {

		switch ( $cap ) {
			case 'wl_entity_type_edit_term':
			case 'wl_entity_type_delete_term':
				$caps[] = 'do_not_allow';
		}

		return $caps;
	}

	/**
	 * Override the capabilities related to managing the entity type terms
	 * required for WordPress < 4.7 to get access to the admin page.
	 *
	 * Before 4.7, WordPress checks whether the user has permission to access
	 * the `edit-tags.php` page. So we need to provide temporary the permission
	 * otherwise the user will get an `access not allowed` when trying to access
	 * the page.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/512
	 *
	 * @since 3.12.0
	 *
	 * @param array  $caps    The user's current capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 *
	 * @return array    Array containing the manage_options capability
	 */
	function enable_admin_access_pre_47( $caps, $cap, $user_id, $args ) {

		// Bail out if we're not dealing with the `wl_entity_type_edit_term`
		// capability.
		if ( 'wl_entity_type_edit_term' !== $cap ) {
			return $caps;
		}

		$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		// Bail out if the `path` cannot be determined.
		if ( null === $path ) {
			return $caps;
		}

		// Bail out if a specific term is being handled.
		if ( isset( $_REQUEST['action'] ) ) {
			return $caps;
		}

		// Split the path and check if the page is the `edit-tags.php`. We can't
		// use WP's own function because this is called very early.
		$components = explode( '/', $path );
		if ( 'edit-tags.php' !== $components[ count( $components ) - 1 ] ) {
			return $caps;
		}

		// Give permission to the user.
		$mapped[] = 'manage_options';

		return $mapped;
	}

}
