<?php

namespace Wordlift\Modules\Food_Kg\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;
use WPRM_Recipe_Manager;

class Main_Ingredient_List_Table extends WP_List_Table {

	public function prepare_items() {
		global $wpdb; // This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 5;

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $wpdb->get_results(
			"SELECT p.ID, p.post_title, pm.meta_value
			FROM $wpdb->posts p
			INNER JOIN $wpdb->postmeta pm
			    ON pm.post_ID = p.ID
					AND pm.meta_key = '_wl_main_ingredient_jsonld'"
		);
	}

	public function no_items() {
		esc_html_e( 'No main ingredients found.', 'wordlift' );
	}

	public function get_columns() {
		return array(
			'post_title' => __( 'Name', 'wordlift' ),
			'actions'    => '',
		);
	}

	public function column_post_title( $item ) {
		$recipe   = WPRM_Recipe_Manager::get_recipe( $item->ID );
		$edit_url = $recipe->parent_edit_url();

		return empty( $edit_url ) ? $item->post_title : sprintf( '<a href="%s">%s</a>', $recipe->parent_edit_url(), $item->post_title );
	}

	public function column_actions( $item ) {

		$url = admin_url(
			sprintf( 'admin.php?page=wl_ingredients&modal_window=true&id=%d&TB_iframe=true', $item->ID )
		);

		return sprintf(
			'<a href="%s" class="button alignright thickbox open-plugin-details-modal" data-title="%s" type="button">%s</a>',
			$url,
			esc_attr( $item->post_title ),
			esc_html__( 'JSON-LD', 'wordlift' )
		);
	}

}

