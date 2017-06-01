<?php

/**
 * Provide entity-related services.
 *
 * @since 3.1.0
 */
class Wordlift_Entity_Service {

	/**
	 * The Log service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Log_Service $log The Log service.
	 */
	private $log;

	/**
	 * The UI service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_UI_Service $ui_service The UI service.
	 */
	private $ui_service;

	/**
	 * The entity post type name.
	 *
	 * @since 3.1.0
	 */
	const TYPE_NAME = 'entity';

	/**
	 * The alternative label meta key.
	 *
	 * @since 3.2.0
	 */
	const ALTERNATIVE_LABEL_META_KEY = '_wl_alt_label';

	/**
	 * The alternative label input template.
	 *
	 * @since 3.2.0
	 */
	// TODO: this should be moved to a class that deals with HTML code.
	const ALTERNATIVE_LABEL_INPUT_TEMPLATE = '<div class="wl-alternative-label">
                <label class="screen-reader-text" id="wl-alternative-label-prompt-text" for="wl-alternative-label">Enter alternative label here</label>
                <input name="wl_alternative_label[]" size="30" value="%s" id="wl-alternative-label" type="text">
                <button class="button wl-delete-button">%s</button>
                </div>';

	/**
	 * A singleton instance of the Entity service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Entity_Service $instance A singleton instance of the Entity service.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Entity_Service instance.
	 *
	 * @since 3.2.0
	 *
	 * @param \Wordlift_UI_Service $ui_service The UI service.
	 */
	public function __construct( $ui_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Service' );

		// Set the UI service.
		$this->ui_service = $ui_service;

		// Set the singleton instance.
		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Entity service.
	 *
	 * @since 3.2.0
	 * @return \Wordlift_Entity_Service The singleton instance of the Entity service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Determines whether a post is an entity or not.
	 *
	 * @since 3.1.0
	 *
	 * @param int $post_id A post id.
	 *
	 * @return bool Return true if the post is an entity otherwise false.
	 */
	public function is_entity( $post_id ) {

		return ( self::TYPE_NAME === get_post_type( $post_id ) );
	}

	/**
	 * Get the proper classification scope for a given entity post
	 *
	 * @since 3.5.0
	 *
	 * @param integer $post_id An entity post id.
	 *
	 * @return string Returns an uri.
	 */
	public function get_classification_scope_for( $post_id ) {

		if ( false === $this->is_entity( $post_id ) ) {
			return null;
		}
		// Retrieve the entity type
		$entity_type_arr = wl_entity_type_taxonomy_get_type( $post_id );
		$entity_type     = str_replace( 'wl-', '', $entity_type_arr['css_class'] );
		// Retrieve classification boxes configuration
		$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );
		foreach ( $classification_boxes as $cb ) {
			if ( in_array( $entity_type, $cb['registeredTypes'] ) ) {
				return $cb['id'];
			}
		}

		// or null
		return null;

	}


	public function is_used( $post_id ) {

		if ( false === $this->is_entity( $post_id ) ) {
			return null;
		}
		// Retrieve the post
		$entity = get_post( $post_id );

		global $wpdb;
		// Retrieve Wordlift relation instances table name
		$table_name = wl_core_get_relation_instances_table_name();

		// Check is it's referenced / related to another post / entity
		$stmt = $wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE  object_id = %d",
			$entity->ID
		);

		// Perform the query
		$relation_instances = (int) $wpdb->get_var( $stmt );
		// If there is at least one relation instance for the current entity, then it's used
		if ( 0 < $relation_instances ) {
			return true;
		}

		// Check if the entity uri is used as meta_value
		$stmt = $wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id != %d AND meta_value = %s",
			$entity->ID,
			wl_get_entity_uri( $entity->ID )
		);
		// Perform the query
		$meta_instances = (int) $wpdb->get_var( $stmt );

		// If there is at least one meta that refers the current entity uri, then current entity is used
		if ( 0 < $meta_instances ) {
			return true;
		}

		// If we are here, it means the current entity is not used at the moment
		return false;
	}

	/**
	 * Determines whether a given uri is an internal uri or not.
	 *
	 * @since 3.3.2
	 *
	 * @param int $uri An uri.
	 *
	 * @return true if the uri internal to the current dataset otherwise false.
	 */
	public function is_internal_uri( $uri ) {

		return ( 0 === strrpos( $uri, wl_configuration_get_redlink_dataset_uri() ) );
	}

	/**
	 * Find entity posts by the entity URI. Entity as searched by their entity URI or same as.
	 *
	 * @since 3.2.0
	 *
	 * @param string $uri The entity URI.
	 *
	 * @return WP_Post|null A WP_Post instance or null if not found.
	 */
	public function get_entity_post_by_uri( $uri ) {

		// Check if we've been provided with a value otherwise return null.
		if ( empty( $uri ) ) {
			return null;
		}

		$query_args = array(
			'posts_per_page' => 1,
			'post_status'    => 'any',
			'post_type'      => self::TYPE_NAME,
			'meta_query'     => array(
				array(
					'key'     => WL_ENTITY_URL_META_NAME,
					'value'   => $uri,
					'compare' => '=',
				),
			),
		);

		// Only if the current uri is not an internal uri, entity search is
		// performed also looking at sameAs values.
		//
		// This solve issues like https://github.com/insideout10/wordlift-plugin/issues/237
		if ( ! $this->is_internal_uri( $uri ) ) {

			$query_args['meta_query']['relation'] = 'OR';
			$query_args['meta_query'][]           = array(
				'key'     => Wordlift_Schema_Service::FIELD_SAME_AS,
				'value'   => $uri,
				'compare' => '=',
			);
		}

		$query = new WP_Query( $query_args );

		// Get the matching entity posts.
		$posts = $query->get_posts();

		// Return null if no post is found.
		if ( 0 === count( $posts ) ) {
			return null;
		}

		// Return the found post.
		return $posts[0];
	}

	/**
	 * Fires once a post has been saved. This function uses the $_REQUEST, therefore
	 * we check that the post we're saving is the current post.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/363
	 *
	 * @since 3.2.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function save_post( $post_id, $post, $update ) {

		// Avoid doing anything if post is autosave or a revision.

		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return;
		}

		// We're setting the alternative label that have been provided via the UI
		// (in fact we're using $_REQUEST), while save_post may be also called
		// programmatically by some other function: we need to check therefore if
		// the $post_id in the save_post call matches the post id set in the request.
		//
		// If this is not the current post being saved or if it's not an entity, return.
		if ( ! isset( $_REQUEST['post_ID'] ) || $_REQUEST['post_ID'] != $post_id || ! $this->is_entity( $post_id ) ) {
			return;
		}

		// Get the alt labels from the request (or empty array).
		$alt_labels = isset( $_REQUEST['wl_alternative_label'] ) ? $_REQUEST['wl_alternative_label'] : array();

		// Set the alternative labels.
		$this->set_alternative_labels( $post_id, $alt_labels );

	}

	/**
	 * Set the alternative labels.
	 *
	 * @since 3.2.0
	 *
	 * @param int   $post_id    The post id.
	 * @param array $alt_labels An array of labels.
	 */
	public function set_alternative_labels( $post_id, $alt_labels ) {

		// Force $alt_labels to be an array
		if ( ! is_array( $alt_labels ) ) {
			$alt_labels = array( $alt_labels );
		}

		$this->log->debug( "Setting alternative labels [ post id :: $post_id ][ alt labels :: " . implode( ',', $alt_labels ) . " ]" );

		// Delete all the existing alternate labels.
		delete_post_meta( $post_id, self::ALTERNATIVE_LABEL_META_KEY );

		// Set the alternative labels.
		foreach ( $alt_labels as $alt_label ) {
			if ( ! empty( $alt_label ) ) {
				add_post_meta( $post_id, self::ALTERNATIVE_LABEL_META_KEY, $alt_label );
			}
		}

	}

	/**
	 * Retrieve the alternate labels.
	 *
	 * @since 3.2.0
	 *
	 * @param int $post_id Post id.
	 *
	 * @return mixed An array  of alternative labels.
	 */
	public function get_alternative_labels( $post_id ) {

		return get_post_meta( $post_id, self::ALTERNATIVE_LABEL_META_KEY );
	}

	/**
	 * Retrieve the labels for an entity, i.e. the title + the synonyms.
	 *
	 * @since 3.12.0
	 *
	 * @param int $post_id The entity {@link WP_Post} id.
	 *
	 * @return array An array with the entity title and labels.
	 */
	public function get_labels( $post_id ) {

		return array_merge( (array) get_the_title( $post_id ), $this->get_alternative_labels( $post_id ) );
	}

	/**
	 * Fires before the permalink field in the edit form (this event is available in WP from 4.1.0).
	 *
	 * @since 3.2.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function edit_form_before_permalink( $post ) {

		// If it's not an entity, return.
		if ( ! $this->is_entity( $post->ID ) ) {
			return;
		}

		// Print the input template.
		$this->ui_service->print_template( 'wl-tmpl-alternative-label-input', $this->get_alternative_label_input() );

		// Print all the currently set alternative labels.
		foreach ( $this->get_alternative_labels( $post->ID ) as $alt_label ) {

			echo $this->get_alternative_label_input( $alt_label );

		};

		// Print the button.
		$this->ui_service->print_button( 'wl-add-alternative-labels-button', __( 'Add more titles', 'wordlift' ) );

	}

	/**
	 * Get the URI for the entity with the specified post id.
	 *
	 * @since 3.6.0
	 *
	 * @param int $post_id The entity post id.
	 *
	 * @return null|string The entity URI or NULL if not found or the dataset URI is not configured.
	 */
	public function get_uri( $post_id ) {

		// If a null is given, nothing to do
		if ( null == $post_id ) {
			return null;
		}

		$uri = get_post_meta( $post_id, WL_ENTITY_URL_META_NAME, true );

		// If the dataset uri is not properly configured, null is returned
		if ( '' === wl_configuration_get_redlink_dataset_uri() ) {
			return null;
		}

		// Set the URI if it isn't set yet.
		$post_status = get_post_status( $post_id );
		if ( empty( $uri ) && 'auto-draft' !== $post_status && 'revision' !== $post_status ) {
			$uri = wl_build_entity_uri( $post_id );
			wl_set_entity_uri( $post_id, $uri );
		}

		return $uri;
	}


	/**
	 * Get the alternative label input HTML code.
	 *
	 * @since 3.2.0
	 *
	 * @param string $value The input value.
	 *
	 * @return string The input HTML code.
	 */
	private function get_alternative_label_input( $value = '' ) {

		return sprintf( self::ALTERNATIVE_LABEL_INPUT_TEMPLATE, esc_attr( $value ), __( 'Delete', 'wordlift' ) );
	}

	/**
	 * Get the number of entity posts published in this blog.
	 *
	 * @since 3.6.0
	 *
	 * @return int The number of published entity posts.
	 */
	public function count() {

		$count = wp_count_posts( self::TYPE_NAME );

		return $count->publish;
	}

	/**
	 * Create a new entity.
	 *
	 * @since 3.9.0
	 *
	 * @param string $name     The entity name.
	 * @param string $type_uri The entity's type URI.
	 * @param null   $logo     The entity logo id (or NULL if none).
	 * @param string $status   The post status, by default 'publish'.
	 *
	 * @return int|WP_Error The entity post id or a {@link WP_Error} in case the `wp_insert_post` call fails.
	 */
	public function create( $name, $type_uri, $logo = null, $status = 'publish' ) {

		// Create an entity for the publisher.
		$post_id = wp_insert_post( array(
			'post_type'    => self::TYPE_NAME,
			'post_title'   => $name,
			'post_status'  => $status,
			'post_content' => '',
		) );

		// Return the error if any.
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Set the entity logo.
		if ( $logo && is_numeric( $logo ) ) {
			set_post_thumbnail( $post_id, $logo );
		}

		// Set the entity type.
		Wordlift_Entity_Type_Service::get_instance()->set( $post_id, $type_uri );

		return $post_id;
	}

	/**
	 * Get the entities related to the one with the specified id. By default only
	 * published entities will be returned.
	 *
	 * @since 3.10.0
	 *
	 * @param int    $id          The post id.
	 * @param string $post_status The target post status (default = publish).
	 *
	 * @return array An array of post ids.
	 */
	public function get_related_entities( $id, $post_status = 'publish' ) {

		return wl_core_inner_get_related_entities( 'post_ids', $id, null, $post_status );
	}

	/**
	 * Get the list of entities.
	 *
	 * @since 3.12.2
	 *
	 * @param array $params Custom parameters for WordPress' own {@link get_posts} function.
	 *
	 * @return array An array of entity posts.
	 */
	public function get( $params = array() ) {

		// Set the defaults.
		$defaults = array( 'post_type' => 'entity' );

		// Merge the defaults with the provided parameters.
		$args = wp_parse_args( $params, $defaults );

		// Call the `get_posts` function.
		return get_posts( $args );
	}

}
