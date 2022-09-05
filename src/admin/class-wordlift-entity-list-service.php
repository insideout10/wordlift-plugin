<?php
/**
 * Services: Entity List Service.
 *
 * Customization of the entity list in wp-admin/edit.php.
 *
 * @since      3.3.0
 * @package    WordLift
 * @subpackage WordLift/admin
 */

/**
 * Define the {@link Wordlift_Entity_List_Service} class.
 *
 * Handles the edit entities views.
 *
 * @since      3.3.0
 * @package    WordLift
 * @subpackage WordLift/admin
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
	 * @param \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 *
	 * @since 3.3.0
	 */
	public function __construct( $rating_service ) {

		$this->rating_service = $rating_service;

	}

	/**
	 * Detects if the entities list admin screen is being displayed
	 *
	 * @return bool True if the screen is being displayed, false otherwis.
	 *
	 * @since 3.15.0
	 */
	private function is_entity_list_screen() {

		// Run only on admin page.
		if ( ! is_admin() ) {
			return false;
		}

		// Return safely if get_current_screen() is not defined (yet).
		if ( false === function_exists( 'get_current_screen' ) ) {
			return false;
		}

		// Only apply on entity list page, only if this is the main query and if the wl-classification-scope query param is set.
		$screen = get_current_screen();

		// If there is any valid screen nothing to do.
		if ( null === $screen ) {
			return false;
		}

		if ( Wordlift_Entity_Service::TYPE_NAME !== $screen->post_type ) {
			return false;
		}

		return true;
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

		// Bail out if custom columns disabled via hook
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( ! apply_filters( 'wl_feature__enable__custom-columns', true ) ) {
			return $columns;
		}

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
	 * @param string $column The current column.
	 * @param int    $entity_id An entity post id.
	 */
	public function render_custom_columns( $column, $entity_id ) {

		switch ( $column ) {

			case 'wl_column_related_posts':
				echo count( wl_core_get_related_post_ids( $entity_id ) );
				break;

			case 'wl_column_thumbnail':
				$edit_link = get_edit_post_link( $entity_id );
				$thumb     = get_the_post_thumbnail(
					$entity_id,
					array(
						self::THUMB_SIZE,
						self::THUMB_SIZE,
					)
				);

				if ( ! $thumb ) {
					$thumb = sprintf( '<img src="%s" width="%d">', WL_DEFAULT_THUMBNAIL_PATH, self::THUMB_SIZE );
				}

				echo wp_kses(
					sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), $thumb ),
					array(
						'a'   => array( 'href' => array() ),
						'img' => array(
							'src'   => array(),
							'width' => array(),
						),
					)
				);
				break;

			case 'wl_column_rating':
				$rating = $this->rating_service->get_rating_for( $entity_id );
				echo '<i class="wl-traffic-light wl-tl-' . esc_attr( $rating['traffic_light_score'] ) . '">' . esc_html( $rating['percentage_score'] ) . '%</i>';
				break;
		}

	}

	/**
	 * Add wl-classification-scope select box before the 'Filter' button.
	 *
	 * @since 3.3.0
	 */
	public function restrict_manage_posts_classification_scope() {

		if ( ! $this->is_entity_list_screen() ) {
			return;
		}

		// Was a W already selected?
		$selected = isset( $_GET['wl-classification-scope'] ) ? //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			sanitize_text_field( wp_unslash( (string) $_GET['wl-classification-scope'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Print select box with the 4W
		$all_w = array(
			"All 'W'"         => __( "All 'W'", 'wordlift' ),
			WL_WHAT_RELATION  => __( 'What', 'wordlift' ),
			WL_WHO_RELATION   => __( 'Who', 'wordlift' ),
			WL_WHERE_RELATION => __( 'Where', 'wordlift' ),
			WL_WHEN_RELATION  => __( 'When', 'wordlift' ),
		);
		echo '<select name="wl-classification-scope" id="wl-dropdown-classification-scope">';
		foreach ( $all_w as $v => $w ) {
			$default = ( $selected === $v ) ? 'selected' : '';
			echo sprintf( '<option value="%s" %s >%s</option>', esc_attr( $v ), esc_html( $default ), esc_html( $w ) );
		}
		echo '</select>';
	}

	/**
	 * Server side response operations for the classification filter set in
	 * *restrict_manage_posts_classification_scope_filter*.
	 *
	 * @param array $clauses WP main query clauses.
	 *
	 * @return array Modified clauses.
	 * @since 3.3.0
	 */
	public function posts_clauses_classification_scope( $clauses ) {

		if ( ! ( $this->is_entity_list_screen() && is_main_query() && isset( $_GET['wl-classification-scope'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $clauses;
		}

		// Check a valid W was requested.
		$requested_w = sanitize_text_field( wp_unslash( (string) $_GET['wl-classification-scope'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$all_w = array(
			WL_WHAT_RELATION,
			WL_WHO_RELATION,
			WL_WHERE_RELATION,
			WL_WHEN_RELATION,
		);

		if ( ! in_array( $requested_w, $all_w, true ) ) {
			return $clauses;
		}

		global $wpdb;

		// Change WP main query clauses.
		$clauses['join']     .= "INNER JOIN {$wpdb->prefix}wl_relation_instances ON {$wpdb->posts}.ID = {$wpdb->prefix}wl_relation_instances.object_id";
		$clauses['where']    .= $wpdb->prepare( "AND {$wpdb->prefix}wl_relation_instances.predicate = %s", $requested_w );
		$clauses['distinct'] .= 'DISTINCT';

		return $clauses;
	}

	/**
	 * Amend the "all entities" list admin screen with entities from other
	 * post types, not only the entities one.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 *
	 * @since 3.15.0
	 */
	public function pre_get_posts( $query ) {

		if ( ! ( $this->is_entity_list_screen() && $query->is_main_query() ) ) {
			return;
		}

		// Add to the post type all the types considered to be valid post types.
		$query->set( 'post_type', Wordlift_Entity_Service::valid_entity_post_types() );

		// Do not show however entities of type `Article`.
		$query->set(
			'tax_query',
			array(
				'relation' => 'AND',
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'operator' => 'EXISTS',
				),
				array(
					'taxonomy' => Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
					'field'    => 'slug',
					'terms'    => 'article',
					'operator' => 'NOT IN',
				),
			)
		);

	}

	/**
	 * An hack to fix wrong post type on the entities list admin screen.
	 *
	 * This is hooked on the admin entity list page load hook and sets the
	 * post type to "entity" as it is expected on that page.
	 *
	 * @since 3.15.0
	 */
	public function load_edit() {

		// Return safely if get_current_screen() is not defined (yet).
		if ( false === function_exists( 'get_current_screen' ) ) {
			return;
		}

		// Only apply on entity list page, only if this is the main query and if the wl-classification-scope query param is set.
		$screen = get_current_screen();

		// If there is any valid screen nothing to do.
		if ( null === $screen ) {
			return;
		}

		if ( ! ( Wordlift_Entity_Service::TYPE_NAME === $screen->post_type && is_main_query() ) ) {
			return;
		}

		/*
		 * The main wp initialization sets the post type to the post type used in the main query,
		 * but the admin edit pages fail to handle such a situation gracefully.
		 * Since this is exactly what we do on the entity page, we have to reset
		 * the global $post_type variable to the "entity" value after the modifications
		 * initialization was finished.
		 */
		add_action(
			'wp',
			function () {
				global $post_type;
				$post_type = Wordlift_Entity_Service::TYPE_NAME; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			},
			10,
			0
		);

	}

}
