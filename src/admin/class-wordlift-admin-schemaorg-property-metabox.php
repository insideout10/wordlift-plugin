<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 07.08.18
 * Time: 10:22
 */

class Wordlift_Admin_Schemaorg_Property_Metabox {

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

		var_dump( $_POST['_wl_prop'] );
		wp_die();

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

		$classes = wp_json_encode( $_REQUEST['class'] );

		$query = "query {
	schemaProperties(classes: $classes) {
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

		header( 'Content-Type: application/json; charset=UTF-8' );
		echo( '{ "success": true, "data": ' );
		echo( $reply['body'] );
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