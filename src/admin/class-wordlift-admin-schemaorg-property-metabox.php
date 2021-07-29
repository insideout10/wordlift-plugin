<?php
/**
 * Metaboxes: Schema.org Property.
 *
 * Display a form with the Schema.org properties.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the Wordlift_Admin_Schemaorg_Property_Metabox class.
 *
 * @since 3.20.0
 */
class Wordlift_Admin_Schemaorg_Property_Metabox {

	/**
	 * The action name used for `nonce` checks.
	 *
	 * @since 3.20.0
	 */
	const ACTION_NAME = '_wl_save_schemaorg_props';

	/**
	 * The `nonce` field name.
	 *
	 * @since 3.20.0
	 */
	const NONCE_NAME = '_wl_save_schemaorg_props_nonce';

	/**
	 * The following properties are the properties defined by the `Thing` entity type.
	 *
	 * Any change here must be reflected in {@link Wordlift_Entity_Post_To_Jsonld_Converter::convert()}, where
	 * the `Thing` properties are loaded using the entity type's `custom_fields` entry provided by the
	 * {@link Wordlift_Schema_Service}.
	 *
	 * @since 3.20.0
	 */
	private static $unsupported_properties = array(
		'additionalType',
		'alternateName',
		'description',
		'disambiguatingDescription',
		'identifier',
		'image',
		'mainEntityOfPage',
		'name',
		'potentialAction',
		'sameAs',
		'subjectOf',
		'url',
	);

	/**
	 * The following array defines the ranges currently supported. In particular we currently support only
	 * the basic data types.
	 *
	 * @since 3.20.0
	 */
	private static $supported_ranges = array(
		'Boolean',
		'False',
		'True',
		'Date',
		'DateTime',
		'Number',
		'Float',
		'Integer',
		'Time',
		'Text',
		'URL',
	);

	/**
	 * The {@link Wordlift_Schemaorg_Property_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Schemaorg_Property_Service $schemaorg_property_service The {@link Wordlift_Schemaorg_Property_Service} instance.
	 */
	private $schemaorg_property_service;

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * Create a {@link Wordlift_Admin_Schemaorg_Property_Metabox} instance.
	 *
	 * @since 3.20.0
	 *
	 * @param \Wordlift_Schemaorg_Property_Service $schemaorg_property_service The {@link Wordlift_Schemaorg_Property_Service} instance.
	 */
	public function __construct( $schemaorg_property_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		// Add a hook to display the Schema.org properties metabox.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Add a hook to save the properties.
		add_action( 'save_post', array( $this, 'save_post' ) );

		// Add a hook to provide an Ajax end-point to load the Schema.org properties.
		add_action( 'wp_ajax_wl_schemaorg_property', array( $this, 'schemaorg_property' ) );

		$this->schemaorg_property_service = $schemaorg_property_service;

	}

	/**
	 * Hook `add_meta_boxes`.
	 *
	 * @since 3.20.0
	 *
	 * @param string $post_type The current post type.
	 */
	public function add_meta_boxes( $post_type ) {

		// Bail out if the `post_type` isn't a valid entity post type.
		if ( ! Wordlift_Entity_Type_Service::is_valid_entity_post_type( $post_type ) ) {
			return;
		}

		// Add our metabox configuration.
		add_meta_box(
			'wl-schemaorg-property',
			__( 'Schema.org Properties', 'wordlift' ),
			array( $this, 'render' ),
			$post_type,
			'normal',
			'default'
		);

	}

	/**
	 * Hook `save_post`.
	 *
	 * The hook will receive the property data in the `$_POST` array.
	 *
	 * @since 3.20.0
	 *
	 * @param int $post_id The post id.
	 */
	public function save_post( $post_id ) {

		//region ## CHECKS.
		// Add nonce for security and authentication.
		$nonce_name = isset( $_POST[ self::NONCE_NAME ] ) ? (string) $_POST[ self::NONCE_NAME ] : '';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			$this->log->warn( '`nonce` not set.' );

			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, self::ACTION_NAME ) ) {
			$this->log->warn( 'Invalid `nonce`.' );

			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			$this->log->warn( "User can't edit posts." );

			return;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			$this->log->trace( "Doing autosave." );

			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			$this->log->trace( "It's an autosave." );

			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			$this->log->trace( "It's a revision." );

			return;
		}

		// check if there was a multisite switch before
		if ( is_multisite() && ms_is_switched() ) {
			$this->log->trace( "It's multisite and has been switched." );

			return;
		}
		//endregion

		// Get only the `_wl_prop` keys.
		$prop_keys = $this->schemaorg_property_service->get_keys( $post_id );

		// Delete the existing properties.
		foreach ( $prop_keys as $key ) {
			delete_post_meta( $post_id, $key );
		}

		// Save the new props. The structure is:
		//  - prop name
		//    - instance uuid
		//      - type: the prop data type.
		//      - language: the prop value language.
		//      - value: the prop value.
		//
		// `_wl_prop` is *not* Wordlift_Schemaorg_Property_Service::PREFIX.
		foreach ( $_POST['_wl_prop'] as $name => $instances ) {
			foreach ( $instances as $uuid => $meta ) {
				foreach ( $meta as $meta_key => $meta_value ) {
					if ( ! empty( $meta_value ) ) {
						add_post_meta( $post_id, Wordlift_Schemaorg_Property_Service::PREFIX . "{$name}_{$uuid}_{$meta_key}", $meta_value );
					}
				}
			}
		}

	}

	/**
	 * The Ajax end-point used to retrieve Schema.org properties data.
	 *
	 * @since 3.20.0
	 */
	public function schemaorg_property() {

		//region ## NONCE VALIDATION.
		// Check nonce, we don't send back a valid nonce if this one isn't valid, of course.
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wl_schemaorg_property' ) ) {
			wp_send_json_error( array(
				'message' => '`nonce` missing or invalid.',
			) );
		}

		$next_nonce = wp_create_nonce( 'wl_schemaorg_property' );
		//endregion

		// Check that a `term_id` has been provided.
		if ( empty( $_REQUEST['term_id'] ) ) {
			wp_send_json_error( array(
				'_wpnonce' => $next_nonce,
				'message'  => '`term_id` missing or invalid.',
			) );
		}

		// Get the class names from the term ids.
		$class_names = array_map( function ( $term_id ) {
			$sanitized = intval( $term_id );

			return get_term_meta( $sanitized, Wordlift_Schemaorg_Class_Service::NAME_META_KEY, true );
		}, $_REQUEST['term_id'] );

		// Encode the class names as a json array.
		$class_names_as_json_array = wp_json_encode( $class_names );

		// Prepare the GraphQL query.
		$query = "query {
	schemaProperties(classes: $class_names_as_json_array) {
		name label description weight ranges {
            name label
        }
	}
}";

		// Send the request to the remote server.
		$reply = wp_remote_post( 'http://turin.wordlift.it:41660/graphql', array(
			'headers' => array(
				'Content-Type' => 'application/json; charset=UTF-8',
			),
			'body'    => wp_json_encode( array(
				'query'     => $query,
				'variables' => null,
			) ),
		) );

		// If the response is empty return an error.
		if ( empty( $reply['body'] ) ) {
			wp_send_json_error();
		}

		// Decode the response.
		$json = json_decode( $reply['body'], true );

		// Remove unsupported ranges.
		$json['schemaProperties'] = array_map( function ( $item ) {
			// Remove unsupported ranges.
			$item['ranges'] = array_values( array_filter( $item['ranges'], function ( $range ) {
				return in_array( $range['name'], Wordlift_Admin_Schemaorg_Property_Metabox::$supported_ranges );
			} ) );

			return $item;
		}, $json['schemaProperties'] );

		// Remove unwanted properties (properties from the `Thing` entity type).
		$json['schemaProperties'] = array_values( array_filter( $json['schemaProperties'], function ( $item ) {
			return 0 < count( $item['ranges'] ) && ! in_array( $item['name'], Wordlift_Admin_Schemaorg_Property_Metabox::$unsupported_properties );
		} ) );

		/**
		 * Filter: wl_schemaorg_properties_for_classes.
		 *
		 * @since 3.20.0
		 *
		 * @param array $json A json instance as array.
		 * @param array $classes An array of Schema.org classes.
		 */
		$properties = apply_filters( 'wl_schemaorg_properties_for_classes', $json, $class_names );

		// Finally output the response.
		wp_send_json_success( $properties );

	}

	/**
	 * Render the metabox.
	 *
	 * @since 3.20.0
	 */
	public function render() {
		?>
        <input type="hidden" name="<?php echo self::NONCE_NAME; ?>"
               value="<?php echo wp_create_nonce( self::ACTION_NAME ); ?>"/>
        <div id="wl-schema-properties-form"></div>
		<?php
	}

}
