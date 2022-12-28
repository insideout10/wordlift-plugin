<?php
/**
 * Services: Entity Types Taxonomy Service.
 *
 * @since 3.1.0
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the Wordlift_Entity_Type_Taxonomy_Service class.
 *
 * @since 3.1.0
 */
class Wordlift_Entity_Type_Taxonomy_Service {

	/**
	 * The WordPress taxonomy name.
	 *
	 * @since 1.0.0
	 */
	const TAXONOMY_NAME = 'wl_entity_type';

	/**
	 * Register the taxonomies.
	 *
	 * @since 3.23.6 we hook to `wp_get_object_terms` to ensure that a term is returned when a post is queries for the
	 *               `wl_entity_type` taxonomy.
	 * @since 3.18.0
	 */
	public function init() {

		$labels = array(
			'name'              => _x( 'Entity Types', 'taxonomy general name', 'wordlift' ),
			'singular_name'     => _x( 'Entity Type', 'taxonomy singular name', 'wordlift' ),
			'search_items'      => __( 'Search Entity Types', 'wordlift' ),
			'all_items'         => __( 'All Entity Types', 'wordlift' ),
			'parent_item'       => __( 'Parent Entity Type', 'wordlift' ),
			'parent_item_colon' => __( 'Parent Entity Type:', 'wordlift' ),
			'edit_item'         => __( 'Edit Entity Type', 'wordlift' ),
			'update_item'       => __( 'Update Entity Type', 'wordlift' ),
			'add_new_item'      => __( 'Add New Entity Type', 'wordlift' ),
			'new_item_name'     => __( 'New Entity Type', 'wordlift' ),
			'menu_name'         => __( 'Entity Types', 'wordlift' ),
		);

		// Take away GUI for taxonomy editing.
		// TODO: read capabilities when editing of the WL <-> schema.org mapping is possible.
		$capabilities = array(
			// We enable editors to change the title/description of terms:
			//
			// @see https://github.com/insideout10/wordlift-plugin/issues/398.
			'manage_terms' => 'manage_options',
			'edit_terms'   => 'wl_entity_type_edit_term',
			'delete_terms' => 'wl_entity_type_delete_term',
			'assign_terms' => 'edit_posts',
		);

		$args = array(
			'labels'             => $labels,
			'capabilities'       => $capabilities,
			'hierarchical'       => true,
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			'show_admin_column'  => apply_filters( 'wl_feature__enable__entity-types-taxonomy', true ),
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			'show_in_rest'       => apply_filters( 'wl_feature__enable__entity-types-taxonomy', true ),
			'show_in_quick_edit' => false,
			'publicly_queryable' => false,
		);

		/*
		 * If `All Entity Types` is enabled, use the new metabox.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 * @since 3.20.0
		 */
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( apply_filters( 'wl_feature__enable__all-entity-types', WL_ALL_ENTITY_TYPES ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			$args['meta_box_cb'] = apply_filters( 'wl_feature__enable__entity-types-taxonomy', true ) ? array(
				'Wordlift_Admin_Schemaorg_Taxonomy_Metabox',
				'render',
			) : false;
		}

		register_taxonomy(
			self::TAXONOMY_NAME, // Taxonomy name.
			Wordlift_Entity_Service::valid_entity_post_types(), // Taxonomy post types.
			$args // Taxonomy args.
		);

		/**
		 * Register meta wl_entities_gutenberg for use in Gutenberg
		 */
		register_meta(
			'post',
			'wl_entities_gutenberg',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);

		/**
		 * Register meta _wl_alt_label for use in Gutenberg
		 */
		// register_meta( 'post', Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY, array(
		// 'object_subtype' => '',
		// 'show_in_rest'   => true,
		// 'single'         => false,
		// 'type'           => 'string',
		// 'auth_callback'  => function () {
		// return current_user_can( 'edit_posts' );
		// }
		// ) );

		// see #1364: add custom fields support for CPTs that are valid entity post types in order to be able to
		// query for synonyms in Block Editor.
		add_filter(
			'register_post_type_args',
			function ( $args, $post_type ) {
				if ( in_array( $post_type, Wordlift_Entity_Service::valid_entity_post_types(), true ) ) {
					if ( ! isset( $args['supports'] ) ) {
						$args['supports'] = array();
					}
					$args['supports'][] = 'custom-fields';
				}

				return $args;
			},
			10,
			2
		);

		// Add filter to change the metabox CSS class.
		add_filter( 'postbox_classes_entity_wl_entity_typediv', 'wl_admin_metaboxes_add_css_class' );

		// Add a filter to preset the object term if none is set.
		//
		// DO NOT hook to `wp_get_object_terms`, because `wp_get_object_terms` returns imploded values for SQL queries.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/995
		// @since 3.23.6
		add_filter( 'get_object_terms', array( $this, 'get_object_terms' ), 10, 4 );

		/**
		 * Exclude sitemap creation for wl_entity_type taxonomy in Yoast
		 *
		 * @since 3.30.1
		 */
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( $this, 'wpseo_sitemap_exclude_taxonomy' ), 10, 2 );

		$this->hide_posts_count();
	}

	private function hide_posts_count() {
		$name = self::TAXONOMY_NAME;
		add_filter(
			"manage_edit-{$name}_columns",
			function ( $columns ) {
				unset( $columns['posts'] );
				return $columns;
			}
		);
	}

	/**
	 * Hook to the `get_object_terms` filter.
	 *
	 * We check if our taxonomy is requested and whether a term has been returned. If no term has been returned we
	 * preset `Article` for posts/pages and 'Thing' for everything else and we query the terms again.
	 *
	 * @param array    $terms Array of terms for the given object or objects.
	 * @param int[]    $object_ids Array of object IDs for which terms were retrieved.
	 * @param string[] $taxonomies Array of taxonomy names from which terms were retrieved.
	 * @param array    $args Array of arguments for retrieving terms for the given
	 *                                object(s). See get_object_terms() for details.
	 *
	 * @return array|WP_Error
	 * @since 3.23.6
	 */
	public function get_object_terms( $terms, $object_ids, $taxonomies, $args ) {
		// Get our entity type.
		$entity_type = self::TAXONOMY_NAME;

		// Check if this is a query for our entity type, that no terms have been found and that we have an article
		// term to preset in case.
		if ( ! taxonomy_exists( $entity_type )
			 || array( $entity_type ) !== (array) $taxonomies
			 || ! empty( $terms )
			 || ! term_exists( 'article', $entity_type )
			 || ! term_exists( 'thing', $entity_type ) ) {

			// Return the input value.
			return $terms;
		}

		// Avoid nested calls in case of issues.
		remove_filter( 'get_object_terms', array( $this, 'get_object_terms' ), 10 );

		// Set the default term for all the queried object.
		foreach ( (array) $object_ids as $object_id ) {
			$post_type = get_post_type( $object_id );
			if ( Wordlift_Entity_Type_Service::is_valid_entity_post_type( $post_type ) ) {
				// Set the term to article for posts and pages, or to thing for everything else.
				$uris = Wordlift_Entity_Type_Adapter::get_entity_types( $post_type );
				foreach ( $uris as $uri ) {
					// set the uri based on post type.
					if ( 'http://schema.org/Article' === $uri || 'http://schema.org/Thing' === $uri ) {
						$uri = Wordlift_Entity_Service::TYPE_NAME === $post_type ?
							'http://schema.org/Thing' : 'http://schema.org/Article';
					}
					Wordlift_Entity_Type_Service::get_instance()->set( $object_id, $uri );
				}
			}
		}

		// Finally return the object terms.
		$terms = wp_get_object_terms( $object_ids, $taxonomies, $args );

		// Re-enable nested calls in case of issues.
		add_filter( 'get_object_terms', array( $this, 'get_object_terms' ), 10, 4 );

		return $terms;
	}

	public function wpseo_sitemap_exclude_taxonomy( $exclude, $tax ) {
		if ( self::TAXONOMY_NAME === $tax ) {
			return true;
		}

		return $exclude;
	}

}
