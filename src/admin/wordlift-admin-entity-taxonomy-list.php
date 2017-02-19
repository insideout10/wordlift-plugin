<?php
/**
 * Admin UI: Wordlift_Admin_Entity_Taxonomy_List
 *
 * The {@link Wordlift_Admin_Entity_Taxonomy_List} class handles modifications
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
class Wordlift_Admin_Entity_Taxonomy_List {

	/**
	 * Hook to `admin_print_styles-edit-tags.php` to stretch the term table
	 * to the whole width of the screen for the entity type list
	 *
	 * @since 3.11.0
	 */
	function admin_print_styles_edit_tags() {

		$screen = get_current_screen();

		// if we are in the entity type list admin page, stretch the term
		// table. !important is used as this style is loaded before the wordpress
		// core ones.

		// Since we can not make the term title not being a link, at least style
		// it as if it is not one.

		if ( 'edit-wl_entity_type' == $screen->id ) {
			?>
			<style>
				#col-right {
					width:100% !important;
				}

				.row-title, .row-title:hover {
					color:gray;
					cursor:default;
				}
			<?php
		}
	}

	/**
	 * Hook to `wl_entity_type_row_actions` to add an "action" link to Thread
	 * SEO related settings for the term
	 *
	 * @since 3.11.0
	 */
	function wl_entity_type_row_actions( $actions, $term ) {
		$actions['seo'] = '<a href="#">' . __( 'SEO settings', 'wordlift' ) . '</a>';
		return $actions;
	}
}
