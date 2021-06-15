<?php
/**
 * Services: Entity Service.
 *
 * @since 3.1.0
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

use Wordlift\Object_Type_Enum;

/**
 * Provide entity-related services.
 *
 * @since 3.1.0
 * @package Wordlift
 * @subpackage Wordlift/includes
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
	 * The {@link Wordlift_Relation_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 */
	private $relation_service;

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since  3.16.3
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

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
	 * @param \Wordlift_UI_Service $ui_service The UI service.
	 * @param \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.2.0
	 *
	 */
	public function __construct( $ui_service, $relation_service, $entity_uri_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Service' );

		$this->ui_service         = $ui_service;
		$this->relation_service   = $relation_service;
		$this->entity_uri_service = $entity_uri_service;

		// Set the singleton instance.
		self::$instance = $this;
	}

	/**
	 * Get the singleton instance of the Entity service.
	 *
	 * @return \Wordlift_Entity_Service The singleton instance of the Entity service.
	 * @since 3.2.0
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Determines whether a post is an entity or not. Entity is in this context
	 * something which is not an article.
	 *
	 * @param int $post_id A post id.
	 *
	 * @return bool Return true if the post is an entity otherwise false.
	 * @since 3.1.0
	 *
	 */
	public function is_entity( $post_id ) {

		$terms = wp_get_object_terms( $post_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		if ( is_wp_error( $terms ) ) {
			$this->log->error( "Cannot get the terms for post $post_id: " . $terms->get_error_message() );

			return false;
		}

		if ( empty( $terms ) ) {
			return false;
		}

		/*
		 * We don't consider an `article` to be an entity.
		 *
		 * @since 3.20.0 At least one associated mustn't be an `article`.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 */
		foreach ( $terms as $term ) {
			if ( 1 !== preg_match( '~(^|-)article$~', $term->slug ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the proper classification scope for a given entity post
	 *
	 * @param integer $post_id An entity post id.
	 *
	 * @param string $default The default classification scope, `what` if not
	 *                         provided.
	 *
	 * @return string Returns a classification scope (e.g. 'what').
	 * @since 3.5.0
	 *
	 */
	public function get_classification_scope_for( $post_id, $default = WL_WHAT_RELATION ) {

		if ( false === $this->is_entity( $post_id ) ) {
			return $default;
		}

		// Retrieve the entity type
		$entity_type_arr = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );
		$entity_type     = str_replace( 'wl-', '', $entity_type_arr['css_class'] );
		// Retrieve classification boxes configuration
		$classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );
		foreach ( $classification_boxes as $cb ) {
			if ( in_array( $entity_type, $cb['registeredTypes'] ) ) {
				return $cb['id'];
			}
		}

		return $default;
	}

	/**
	 * Check whether a {@link WP_Post} is used.
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return bool|null Null if it's not an entity, otherwise true if it's used.
	 */
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
	 * Find entity posts by the entity URI. Entity as searched by their entity URI or same as.
	 *
	 * @param string $uri The entity URI.
	 *
	 * @return WP_Post|null A WP_Post instance or null if not found.
	 * @deprecated in favor of Wordlift_Entity_Uri_Service->get_entity( $uri );
	 *
	 * @since      3.16.3 deprecated in favor of Wordlift_Entity_Uri_Service->get_entity( $uri );
	 * @since      3.2.0
	 *
	 */
	public function get_entity_post_by_uri( $uri ) {

		return $this->entity_uri_service->get_entity( $uri );
	}

	/**
	 * Fires once a post has been saved. This function uses the $_REQUEST, therefore
	 * we check that the post we're saving is the current post.
	 *
	 * @see   https://github.com/insideout10/wordlift-plugin/issues/363
	 *
	 * @since 3.2.0
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool $update Whether this is an existing post being updated or not.
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

		if ( ( ! empty( $_POST['content'] ) && ! empty( $_POST['post_content'] ) ) || isset( $_REQUEST['wl_alternative_label'] ) ) {
			// This is via classic editor, so set the alternative labels.
			$this->set_alternative_labels( $post_id, $alt_labels );
		}


	}

	/**
	 * Set the alternative labels.
	 *
	 * @param int $post_id The post id.
	 * @param array $alt_labels An array of labels.
	 *
	 * @since 3.2.0
	 *
	 */
	public function set_alternative_labels( $post_id, $alt_labels ) {

		// Bail out if post id is not numeric. We add this check as we found a WP install that was sending a WP_Error
		// instead of post id.
		if ( ! is_numeric( $post_id ) ) {
			return;
		}

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
	 * @param int $post_id Post id.
	 *
	 * @return mixed An array  of alternative labels.
	 * @since 3.2.0
	 *
	 */
	public function get_alternative_labels( $post_id ) {

		return get_post_meta( $post_id, self::ALTERNATIVE_LABEL_META_KEY );
	}

	/**
	 * Retrieve the labels for an entity, i.e. the title + the synonyms.
	 *
	 * @param int $post_id The entity {@link WP_Post} id.
	 *
	 * @return array An array with the entity title and labels.
	 * @since 3.12.0
	 *
	 */
	public function get_labels( $post_id ) {

		return array_merge( (array) get_the_title( $post_id ), $this->get_alternative_labels( $post_id ) );
	}

	/**
	 * Fires before the permalink field in the edit form (this event is available in WP from 4.1.0).
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @since 3.2.0
	 *
	 */
	public function edit_form_before_permalink( $post ) {

		// If it's not an entity, return.
		if ( ! $this->is_entity( $post->ID ) ) {
			return;
		}

		// If disabled by filter, return.
		if ( ! apply_filters( 'wl_feature__enable__add-synonyms', true ) ) {
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
	 * @param int $post_id The entity post id.
	 *
	 * @return null|string The entity URI or NULL if not found or the dataset URI is not configured.
	 * @since 3.6.0
	 *
	 */
	private function get_uri_for_post( $post_id ) {

		$log = Wordlift_Log_Service::get_logger( get_class() );

		// If a null is given, nothing to do
		if ( is_null( $post_id ) ) {
			return null;
		}

		$dataset_uri = wl_configuration_get_redlink_dataset_uri();

		if ( empty( $dataset_uri ) ) {
			// Continue even if the dataset uri is not properly configured. It is handled in function wl_build_entity_uri()
			$log->debug( 'Continuing, dataset uri not configured...' );
		}

		$uri = get_post_meta( $post_id, WL_ENTITY_URL_META_NAME, true );

		/*
		 * Consider the URI invalid if it doesn't start with the dataset URI.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/996
		 */
		if ( empty( $dataset_uri ) || 0 !== strpos( $uri, $dataset_uri ) ) {
			$uri = null;
		}

		// Set the URI if it isn't set yet.
		$post_status = get_post_status( $post_id );
		if ( empty( $uri ) && 'auto-draft' !== $post_status && 'inherit' !== $post_status ) {
			$uri = wl_build_entity_uri( $post_id );
			wl_set_entity_uri( $post_id, $uri );
		}

		return $uri;
	}

	public function get_uri( $object_id, $type = Object_Type_Enum::POST ) {

		if ( Object_Type_Enum::POST === $type ) {
			return $this->get_uri_for_post( $object_id );
		}

		if ( Object_Type_Enum::USER === $type ) {
			$uri = Wordlift_User_Service::get_instance()->get_uri( $object_id );

			return ( false === $uri ? null : $uri );
		}

		return null;
	}

	/**
	 * Get the alternative label input HTML code.
	 *
	 * @param string $value The input value.
	 *
	 * @return string The input HTML code.
	 * @since 3.2.0
	 *
	 */
	private function get_alternative_label_input( $value = '' ) {

		return sprintf( self::ALTERNATIVE_LABEL_INPUT_TEMPLATE, esc_attr( $value ), __( 'Delete', 'wordlift' ) );
	}

	/**
	 * Get the number of entity posts published in this blog.
	 *
	 * @return int The number of published entity posts.
	 * @since 3.6.0
	 *
	 */
	public function count() {
		global $wpdb;

		// Try to get the count from the transient.
		$count = get_transient( '_wl_entity_service__count' );
		if ( false !== $count ) {
			return $count;
		}

		// Query the count.
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT( DISTINCT( tr.object_id ) )"
			. " FROM {$wpdb->term_relationships} tr"
			. " INNER JOIN {$wpdb->term_taxonomy} tt"
			. "  ON tt.taxonomy = %s AND tt.term_taxonomy_id = tr.term_taxonomy_id"
			. " INNER JOIN {$wpdb->terms} t"
			. "  ON t.term_id = tt.term_id AND t.name != %s",
			Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME,
			'article'
		) );

		// Store the count in cache.
		set_transient( '_wl_entity_service__count', $count, 900 );

		return $count;
	}

	/**
	 * Add the entity filtering criterias to the arguments for a `get_posts`
	 * call.
	 *
	 * @param array $args The arguments for a `get_posts` call.
	 *
	 * @return array The arguments for a `get_posts` call.
	 * @since 3.15.0
	 *
	 */
	public static function add_criterias( $args ) {

		// Build an optimal tax-query.
		$tax_query = array(
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
		);

		return $args + array(
				'post_type' => Wordlift_Entity_Service::valid_entity_post_types(),
				/*
				 * Ensure compatibility with Polylang.
				 *
				 * @see https://github.com/insideout10/wordlift-plugin/issues/855.
				 * @see https://wordpress.org/support/topic/parse_query-filter-adds-language-taxonomy-to-query/.
				 *
				 * @since 3.19.5
				 */
				'lang'      => '',
				'tax_query' => $tax_query,
			);
	}

	/**
	 * Create a new entity.
	 *
	 * @param string $name The entity name.
	 * @param string $type_uri The entity's type URI.
	 * @param null $logo The entity logo id (or NULL if none).
	 * @param string $status The post status, by default 'publish'.
	 *
	 * @return int|WP_Error The entity post id or a {@link WP_Error} in case the `wp_insert_post` call fails.
	 * @since 3.9.0
	 *
	 */
	public function create( $name, $type_uri, $logo = null, $status = 'publish' ) {

		// Create an entity for the publisher.
		$post_id = @wp_insert_post( array(
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
	 * @param int $id The post id.
	 * @param string $post_status The target post status (default = publish).
	 *
	 * @return array An array of post ids.
	 * @since 3.10.0
	 *
	 */
	public function get_related_entities( $id, $post_status = 'publish' ) {

		return $this->relation_service->get_objects( $id, 'ids', null, $post_status );
	}

	/**
	 * Get the list of entities.
	 *
	 * @param array $params Custom parameters for WordPress' own {@link get_posts} function.
	 *
	 * @return array An array of entity posts.
	 * @since 3.12.2
	 *
	 */
	public function get( $params = array() ) {

		// Set the defaults.
		$defaults = array( 'post_type' => 'entity' );

		// Merge the defaults with the provided parameters.
		$args = wp_parse_args( $params, $defaults );

		// Call the `get_posts` function.
		return get_posts( $args );
	}

	/**
	 * The list of post type names which can be used for entities
	 *
	 * Criteria is that the post type is public. The list of valid post types
	 * can be overridden with a filter.
	 *
	 * @return array Array containing the names of the valid post types.
	 * @since 3.15.0
	 *
	 */
	static function valid_entity_post_types() {

		// Ignore builtins in the call to avoid getting attachments.
		$post_types = array( 'post', 'page', self::TYPE_NAME, 'product' );

		return apply_filters( 'wl_valid_entity_post_types', $post_types );
	}

}
