<?php
/**
 * Customization of the entity list in wp-admin/edit.php
 *
 * @since   3.3.0
 * @package WordLift
 */

/**
 * Wordlift_Entity_List_Service Class
 *
 * Handles the edit entities views.
 */
class Wordlift_Entity_List_Service {

	/**
	 * Size of the entity thumbnail in pixels
	 *
	 * @since  3.3.0
	 */
	const THUMB_SIZE = 50;

	/**
	 * A {@link Wordlift_Rating_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 */
	private $rating_service;

	/**
	 * Create a Wordlift_Entity_List_Service.
	 *
	 * @since 3.3.0
	 *
	 * @param \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 */
	public function __construct( $rating_service ) {

		$this->rating_service = $rating_service;

	}

	/**
	 * Register custom columns for entity listing in backend.
	 *
	 * @see   https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
	 *
	 * @since 3.3.0
	 *
	 * @param array $columns the default columns.
	 *
	 * @return array Enhanced columns array.
	 */
	public function register_custom_columns( $columns ) {

		// Take away first column and keep a reference,
		// so we can later insert the thumbnail between the first and the rest of columns.
		$columns_cb = $columns['cb'];
		unset( $columns['cb'] );

		// Thumbnails column is inserted in second place, while the related posts on the end.
		$columns = array_merge(
			array( 'cb' => $columns_cb ),                      // re-add first column
			array( 'wl_column_thumbnail' => __( 'Image', 'wordlift' ) ),        // thumb
			$columns,                                                               // default columns (without the first)
			array( 'wl_column_related_posts' => __( 'Related Posts', 'wordlift' ) ), // related posts
			array( 'wl_column_rating' => __( 'Rating', 'wordlift' ) ) // related posts
		);

		return $columns;
	}

	/**
	 * Render custom columns.
	 *
	 * @see   https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
	 *
	 * @since 3.3.0
	 *
	 * @param string $column    the current column.
	 * @param int    $entity_id An entity post id.
	 */
	public function render_custom_columns( $column, $entity_id ) {

		switch ( $column ) {

			case 'wl_column_related_posts':
				echo count( wl_core_get_related_post_ids( $entity_id ) );
				break;

			case 'wl_column_thumbnail':

				$edit_link = get_edit_post_link( $entity_id );
				$thumb     = get_the_post_thumbnail( $entity_id, array(
					self::THUMB_SIZE,
					self::THUMB_SIZE,
				) );

				if ( ! $thumb ) {
					$thumb = "<img src='" . WL_DEFAULT_THUMBNAIL_PATH . "' width='" . self::THUMB_SIZE . "' />";
				}
				echo "<a href='" . esc_url( $edit_link ) . "'>$thumb</a>";
				break;

			case 'wl_column_rating':

				$rating = $this->rating_service->get_rating_for( $entity_id );
				echo '<i class="wl-traffic-light wl-tl-' . $rating['traffic_light_score'] . '">' . $rating['percentage_score'] . '%</i>';
				break;
		}

	}

	/**
	 * Add wl-classification-scope select box before the 'Filter' button.
	 *
	 * @since 3.3.0
	 */
	public function restrict_manage_posts_classification_scope() {

		// Return safely if get_current_screen() is not defined (yet)
		if ( false === function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		// If there is any valid screen nothing to do
		if ( null === $screen ) {
			return;
		}

		if ( Wordlift_Entity_Service::TYPE_NAME !== $screen->post_type ) {
			return;
		}

		// Was a W already selected?
		$selected = isset( $_GET['wl-classification-scope'] ) ?
			$_GET['wl-classification-scope'] : '';

		// Print select box with the 4W
		$all_w = array(
			"All 'W'" => __( "All 'W'", 'wordlift' ),
			WL_WHAT_RELATION => __( 'What', 'wordlift' ),
			WL_WHO_RELATION => __( 'Who', 'wordlift' ),
			WL_WHERE_RELATION => __( 'Where', 'wordlift' ),
			WL_WHEN_RELATION => __( 'When', 'wordlift' ),
		);
		echo '<select name="wl-classification-scope" id="wl-dropdown-classification-scope">';
		foreach ( $all_w as $v => $w ) {
			$default = ( $selected === $v ) ? 'selected' : '';
			echo sprintf( '<option value="%s" %s >%s</option>', esc_attr( $v ), $default, esc_html( $w ) );
		}
		echo '</select>';
	}

	/**
	 * Server side response operations for the classification filter set in *restrict_manage_posts_classification_scope_filter*
	 *
	 * @since 3.3.0
	 *
	 * @param array $clauses WP main query clauses.
	 *
	 * @return array Modified clauses.
	 */
	public function posts_clauses_classification_scope( $clauses ) {

		// Run only on admin page.
		if ( ! is_admin() ) {
			return $clauses;
		}

		// Return safely if get_current_screen() is not defined (yet).
		if ( false === function_exists( 'get_current_screen' ) ) {
			return $clauses;
		}

		// Only apply on entity list page, only if this is the main query and if the wl-classification-scope query param is set.
		$screen = get_current_screen();

		// If there is any valid screen nothing to do.
		if ( null === $screen ) {
			return $clauses;
		}

		if ( ! ( $screen->post_type === Wordlift_Entity_Service::TYPE_NAME && is_main_query() && isset( $_GET['wl-classification-scope'] ) ) ) {
			return $clauses;
		}

		// Check a valid W was requested.
		$requested_w = $_GET['wl-classification-scope'];

		$all_w = array(
			WL_WHAT_RELATION,
			WL_WHO_RELATION,
			WL_WHERE_RELATION,
			WL_WHEN_RELATION,
		);

		if ( ! in_array( $requested_w, $all_w ) ) {
			return $clauses;
		}

		global $wpdb;
		$wl_relation_table = wl_core_get_relation_instances_table_name();

		// Change WP main query clauses.
		$clauses['join'] .= "INNER JOIN {$wl_relation_table} ON {$wpdb->posts}.ID = {$wl_relation_table}.object_id";
		$clauses['where'] .= $wpdb->prepare( "AND {$wl_relation_table}.predicate = %s", $requested_w );
		$clauses['distinct'] .= "DISTINCT";

		return $clauses;
	}

}
