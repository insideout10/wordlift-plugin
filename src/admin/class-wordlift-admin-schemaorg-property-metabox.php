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

	public function schemaorg_property() {

		// Check nonce, we don't send back a valid nonce if this one isn't valid, of course.
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'wl_schemaorg_property' ) ) {
			wp_send_json_error( array(
				'message' => '`nonce` missing or invalid.',
			) );
		}

		$next_nonce = wp_create_nonce( 'wl_schemaorg_property' );

		if ( empty( $_REQUEST['name'] ) || 1 !== preg_match( '/^[a-z0-9]+$/i', $_REQUEST['name'] ) ) {
			wp_send_json_error( array(
				'nonce'   => $next_nonce,
				'message' => '`name` missing or invalid.',
			) );
		}

		$name = $_REQUEST['name'];

		$query = "query {
	schemaClasses(name: \"$name\") {
		dashname: name(format: DASHED)
		weight
			properties{
				label
				dashname: name(format: DASHED)
				description
			ranges {
				dashname: name(format: DASHED)
				weight
			}
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
			echo( 'error' );

			return;
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

		$query = "query {
	schemaClasses(name: \"Person\") {
		dashname: name(format: DASHED)
		weight
			properties{
				label
				dashname: name(format: DASHED)
				description
			ranges {
				dashname: name(format: DASHED)
				weight
			}
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
			echo( 'error' );

			return;
		}

		$json = json_decode( $reply['body'], true );
		?>
		<div id="wl-schema-properties-form"></div>
		<?php
	}

}