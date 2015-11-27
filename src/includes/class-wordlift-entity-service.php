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
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The UI service.
	 *
	 * @since 3.2.0
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
	 * The alternate label meta key.
	 *
	 * @since 3.2.0
	 */
	const ALTERNATE_LABEL_META_KEY = '_wl_alt_label';

	const ALTERNATIVE_LABEL_INPUT_TEMPLATE = '<div class="wl-alternative-label">
                <label class="screen-reader-text" id="wl-alternative-label-prompt-text" for="wl-alternative-label">Enter alternative label here</label>
                <input name="wl_alternative_label[]" size="30" value="%s" id="wl-alternative-label" type="text">
                <button class="wl-delete-button">%s</button>
                </div>';

	/**
	 * A singleton instance of the Entity service.
	 *
	 * @since 3.2.0
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

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Entity_Service' );

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
	 * Get the entities related to the last 50 posts published on this blog (we're keeping a long function name due to
	 * its specific function).
	 *
	 * @since 3.1.0
	 *
	 * @return array An array of post IDs.
	 */
	public function get_all_related_to_last_50_published_posts() {

		// Global timeline. Get entities from the latest posts.
		$latest_posts_ids = get_posts( array(
			'numberposts' => 50,
			'fields'      => 'ids', //only get post IDs
			'post_type'   => 'post',
			'post_status' => 'publish'
		) );

		if ( empty( $latest_posts_ids ) ) {
			// There are no posts.
			return array();
		}

		// Collect entities related to latest posts
		$entity_ids = array();
		foreach ( $latest_posts_ids as $id ) {
			$entity_ids = array_merge( $entity_ids, wl_core_get_related_entity_ids( $id, array(
				'status' => 'publish'
			) ) );
		}

		return $entity_ids;
	}

	/**
	 * Determines whether a post is an entity or not.
	 *
	 * @since 3.1.0
	 *
	 * @param int $post_id A post id.
	 *
	 * @return true if the post is an entity otherwise false.
	 */
	public function is_entity( $post_id ) {

		return ( self::TYPE_NAME === get_post_type( $post_id ) );
	}

	/**
	 * Fires once a post has been saved.
	 *
	 * @since 3.2.0
	 *
	 * @param int $post_id Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function save_post( $post_id, $post, $update ) {

		// If it's not an entity, return.
		if ( ! $this->is_entity( $post_id ) ) {
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
	 * @param int $post_id The post id.
	 * @param array $alt_labels An array of labels.
	 */
	private function set_alternative_labels( $post_id, $alt_labels ) {

		$this->log_service->debug( "Setting alternative labels [ post id :: $post_id ][ alt labels :: " . implode( ',', $alt_labels ) . " ]" );

		// Delete all the existing alternate labels.
		delete_post_meta( $post_id, self::ALTERNATE_LABEL_META_KEY );

		// Set the alternative labels.
		foreach ( $alt_labels as $alt_label ) {
			add_post_meta( $post_id, self::ALTERNATE_LABEL_META_KEY, $alt_label );
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
	public function get_alternate_labels( $post_id ) {

		return get_post_meta( $post_id, self::ALTERNATE_LABEL_META_KEY );
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
		foreach ( $this->get_alternate_labels( $post->ID ) as $alt_label ) {

			echo $this->get_alternative_label_input( $alt_label );

		};

		// Print the button.
		$this->ui_service->print_button( 'wl-add-alternative-labels-button', __( 'Add more titles', 'wordlift' ) );

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

}
