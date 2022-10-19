<?php

namespace Wordlift\Modules\Food_Kg\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;

class Main_Ingredient_List_Table extends WP_List_Table {

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

		// Pagination.
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = $this->count();

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p1.ID AS recipe_ID,
					    p1.post_title AS recipe_name,
					    p2.ID AS post_ID,
					    p2.post_title
						FROM $wpdb->postmeta pm1
						    INNER JOIN $wpdb->posts p1
						        ON p1.ID = pm1.post_ID AND p1.post_type = 'wprm_recipe'
							INNER JOIN $wpdb->postmeta pm2
								ON pm2.post_ID = pm1.post_ID AND pm2.meta_key = 'wprm_parent_post_id'
						    INNER JOIN $wpdb->posts p2"
				// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
				. " ON p2.post_status = 'publish' AND p2.ID = pm2.post_ID
							WHERE pm1.meta_key = '_wl_main_ingredient_jsonld'
					LIMIT %d
					OFFSET %d",
				$per_page,
				( $current_page - 1 ) * $per_page
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	private function count() {
		global $wpdb;

		$count = get_transient( '_wl_main_ingredient_list_table__count' );

		if ( ! $count ) {

			$count = $wpdb->get_var(
				"SELECT COUNT(1)
						FROM $wpdb->postmeta pm1
						    INNER JOIN $wpdb->posts p1
						        ON p1.ID = pm1.post_ID AND p1.post_type = 'wprm_recipe'
							INNER JOIN $wpdb->postmeta pm2
								ON pm2.post_ID = pm1.post_ID AND pm2.meta_key = 'wprm_parent_post_id'
						    INNER JOIN $wpdb->posts p2"
				// The following ignore rule is used against the `LIKE CONCAT`. We only have const values.
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.LikeWildcardsInQuery
				. " ON p2.post_status = 'publish' AND p2.ID = pm2.post_ID
							WHERE pm1.meta_key = '_wl_main_ingredient_jsonld'"
			);

			set_transient( '_wl_main_ingredient_list_table__count', $count, 60 );
		}

		return $count;
	}

	public function no_items() {
		esc_html_e( 'No main ingredients found.', 'wordlift' );
	}

	public function get_columns() {
		return array(
			'ingredient_name' => __( 'Ingredient Name', 'wordlift' ),
			'recipe_name'     => __( 'Recipe Name', 'wordlift' ),
			'post_title'      => __( 'Post Title', 'wordlift' ),
			'url'             => __( 'Post URL', 'wordlift' ),
			'actions'         => '',
		);
	}

	public function column_ingredient_name( $item ) {
		$recipe_json_ld = get_post_meta( $item->recipe_ID, '_wl_main_ingredient_jsonld', true ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$recipe         = json_decode( $recipe_json_ld, true );

		return $recipe ? $recipe['name'] : 'null';
	}

	public function column_recipe_name( $item ) {
		return $item->recipe_name;
	}

	public function column_post_title( $item ) {
		return sprintf( '<a href="%s">%s</a>', get_edit_post_link( $item->post_ID ), $item->post_title );
	}

	public function column_url( $item ) {
		return get_permalink( $item->post_ID );
	}

	public function column_actions( $item ) {

		$url = admin_url(
			sprintf( 'admin.php?page=wl_ingredients&modal_window=true&id=%d&TB_iframe=true', $item->recipe_ID )  // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		);

		return sprintf(
			'<a href="%s" class="button alignright thickbox open-plugin-details-modal" data-title="%s" type="button">%s</a>',
			$url,
			esc_attr( $item->post_title ),
			esc_html__( 'JSON-LD', 'wordlift' )
		);
	}

}

