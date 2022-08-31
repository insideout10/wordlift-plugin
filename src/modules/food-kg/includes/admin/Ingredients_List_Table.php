<?php

namespace Wordlift\Modules\Food_Kg\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;

class Ingredients_List_Table extends WP_List_Table {

	public function prepare_items() {
		global $wpdb; // This is used only if making any database queries

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
			$wpdb->prepare(
				"SELECT t.term_id, t.name, tm.meta_value
			FROM $wpdb->term_taxonomy tt
			INNER JOIN $wpdb->terms t
			    ON t.term_id = tt.term_id
			INNER JOIN $wpdb->termmeta tm
				ON tm.term_id = t.term_id
					AND tm.meta_key = '_wl_jsonld'
			WHERE tt.taxonomy = %s;",
				'wprm_ingredient'
			)
		);
	}

	public function no_items() {
		esc_html_e( 'No ingredients found.', 'wordlift' );
	}

	public function get_columns() {
		return array(
			'name'    => __( 'Name', 'wordlift' ),
			'actions' => '',
		);
	}

	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', get_edit_term_link( $item->term_id ), $item->name );
	}

	public function column_actions( $item ) {

		$url = admin_url(
			sprintf( 'admin.php?page=wl_ingredients&modal_window=true&term_id=%d&TB_iframe=true', $item->term_id )
		);

		return sprintf(
			'<a href="%s" class="button alignright thickbox open-plugin-details-modal" data-title="%s" type="button">%s</a>',
			$url,
			esc_attr( $item->name ),
			esc_html__( 'JSON-LD', 'wordlift' )
		);
	}

}

