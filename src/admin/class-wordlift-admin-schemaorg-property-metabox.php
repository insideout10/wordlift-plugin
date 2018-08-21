<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 07.08.18
 * Time: 10:22
 */

class Wordlift_Admin_Schemaorg_Property_Metabox {

	/**
	 * The following properties are the properties defined by the `Thing` entity type.
	 *
	 * Any change here must be reflected in {@link Wordlift_Entity_Post_To_Jsonld_Converter::convert()}, where
	 * the `Thing` properties are loaded using the entity type's `custom_fields` entry provided by the
	 * {@link Wordlift_Schema_Service}.
	 *
	 * @since 3.20.0
	 */
	const UNSUPPORTED_PROPERTIES = array(
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

	const SUPPORTED_RANGES = array(
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

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 1 );
		add_action( 'wp_ajax_wl_schemaorg_property', array( $this, 'schemaorg_property' ) );

	}

	public function add_meta_boxes( $post_type, $post ) {

		// Bail out if the `post_type` isn't a valid entity post type.
		if ( ! Wordlift_Entity_Type_Service::is_valid_entity_post_type( $post_type ) ) {
			return;
		}

		add_meta_box(
			'wl-schemaorg-property',
			__( 'Schema.org Properties', 'wordlift' ),
			array( $this, 'render' ),
			$post_type,
			'normal',
			'default'
		);

	}

	public function save_post( $post_id ) {
//		// Add nonce for security and authentication.
//		$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
//		$nonce_action = 'custom_nonce_action';
//
//		// Check if nonce is set.
//		if ( ! isset( $nonce_name ) ) {
//			return;
//		}
//
//		// Check if nonce is valid.
//		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
//			return;
//		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// check if there was a multisite switch before
		if ( is_multisite() && ms_is_switched() ) {
			return;
		}

//		foreach ( $_POST as $key => $value ) {
//			var_dump( $key );
//		}
//
//
//		$props = array_reduce( array_keys( $_POST ), function ( $carry, $key ) use ( $_POST ) {
//			if ( 0 !== strpos( Wordlift_Schemaorg_Property_Service::PREFIX, $key ) ) {
//				return $carry;
//			}
//
//			return $carry + array( $key => $_POST[ $key ] );
//		}, array() );

		$keys = array_filter( array_unique( array_keys( get_post_meta( $post_id ) ) ), function ( $item ) {
			return 0 === strpos( $item, '_wl_prop_' );
		} );

		foreach ( $keys as $key ) {
			delete_post_meta( $post_id, $key );
		}

		foreach ( $_POST['_wl_prop'] as $name => $instances ) {
			foreach ( $instances as $uuid => $meta ) {
				foreach ( $meta as $meta_key => $meta_value ) {
					if ( ! empty( $meta_value ) ) {
						add_post_meta( $post_id, "_wl_prop_{$name}_{$uuid}_{$meta_key}", $meta_value );
					}
				}
			}
		}

	}

	public function schemaorg_property() {

		// Check nonce, we don't send back a valid nonce if this one isn't valid, of course.
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wl_schemaorg_property' ) ) {
			wp_send_json_error( array(
				'message' => '`nonce` missing or invalid.',
			) );
		}

		$next_nonce = wp_create_nonce( 'wl_schemaorg_property' );

		if ( empty( $_REQUEST['class'] ) ) {
			wp_send_json_error( array(
				'_wpnonce' => $next_nonce,
				'message'  => '`class` missing or invalid.',
			) );
		}

		$classes               = $_REQUEST['class'];
		$classes_as_json_array = wp_json_encode( $classes );

		$query = "query {
	schemaProperties(classes: $classes_as_json_array) {
		name
		label
		description
		weight
        ranges {
            name
            label
        }
	}
}";

		$reply = wp_remote_post( 'http://turin.wordlift.it:41660/graphql', array(
			'headers' => array(
				'Content-Type' => 'application/json; charset=UTF-8',
			),
			'body'    => wp_json_encode( array(
				'query'     => $query,
				'variables' => null,
			) ),
		) );

		if ( empty( $reply['body'] ) ) {
			wp_send_json_error();
		}


		$json = json_decode( $reply['body'], true );


		// Remove unsupported ranges.
		$json['schemaProperties'] = array_map( function ( $item ) {
			// Remove unsupported ranges.
			$item['ranges'] = array_values( array_filter( $item['ranges'], function ( $range ) {
				return in_array( $range['name'], Wordlift_Admin_Schemaorg_Property_Metabox::SUPPORTED_RANGES );
			} ) );

			return $item;
		}, $json['schemaProperties'] );

		// Remove unwanted properties (properties from the `Thing` entity type).
		$json['schemaProperties'] = array_values( array_filter( $json['schemaProperties'], function ( $item ) {
			return 0 < count( $item['ranges'] ) && ! in_array( $item['name'], Wordlift_Admin_Schemaorg_Property_Metabox::UNSUPPORTED_PROPERTIES );
		} ) );

		/**
		 * Filter: wl_schemaorg_properties_for_classes.
		 *
		 * @since 3.20.0
		 *
		 * @param array $json A json instance as array.
		 * @param array $classes An array of Schema.org classes.
		 */
		$properties = apply_filters( 'wl_schemaorg_properties_for_classes', $json, $classes );

		header( 'Content-Type: application/json; charset=UTF-8' );
		echo( '{ "success": true, "data": ' );
		echo( wp_json_encode( $properties ) );
		echo( '}' );

		if ( wp_doing_ajax() ) {
			wp_die( '', '', array(
				'response' => null,
			) );
		} else {
			die;
		}

	}

	public function render() {
		?>
        <div id="wl-schema-properties-form"></div>
		<?php
	}

}