<?php
/**
 * Services: Schema.org Sync Service.
 *
 * Provide the function to synchronize the Schema.org hierarchy with the local taxonomy.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/includes/schemaorg
 */

/**
 * Define the Wordlift_Schemaorg_Sync_Service class.
 *
 * @since 3.20.0
 */
class Wordlift_Schemaorg_Sync_Service {

	private static $instance;

	private $log;

	public function __construct() {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		add_action( 'wp_ajax_wl_sync_schemaorg', array( $this, 'load' ) );

		self::$instance = $this;

	}

	public static function get_instance() {

		return self::$instance;
	}

	public function load_from_file() {

		$contents = file_get_contents( dirname( __FILE__ ) . '/schema-classes.json' );

		return $this->load( $contents );
	}

	public function load_from_url() {

		$reply = wp_remote_post( 'http://turin.wordlift.it:41660/graphql', array(
			'method'  => 'POST',
			'headers' => array(
				'content-type' => 'application/json; charset=UTF-8',
			),
			'body'    => wp_json_encode( array(
				'query'     => "
					query {
					schemaClasses {
						name
						dashname: name(format: DASHED)
						description
						children {
							dashname: name(format: DASHED)
						}
					}
				}'",
				'variables' => null,
			) ),
		) );

		if ( is_wp_error( $reply ) ) {
			// Error.
			return false;
		}

		if ( ! isset( $reply['response']['code'] )
		     || ! is_numeric( $reply['response']['code'] ) ) {
			// Error: response code not set or invalid.
			return false;
		}

		if ( 2 !== (int) $reply['response']['code'] / 100 ) {
			// Error: status code not OK.
			return false;
		}

		if ( ! isset( $reply['body'] ) ) {
			// Error: body not set.
			return false;
		}

		return $this->load( $reply['body'] );
	}

	private function load( $contents ) {

		$json = json_decode( $contents, true );

		if ( null === $json ) {
			// Error: invalid body.
			return false;
		}

		if ( ! isset( $json['schemaClasses'] ) ) {
			// Error: invalid json.
			return false;
		}

		foreach ( $json['schemaClasses'] as $schema_class ) {
			$slug = $schema_class['dashname'];
			$term = term_exists( $slug, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

			$args = array(
				'parent'      => 0,
				'description' => $schema_class['description'],
				'slug'        => $schema_class['dashname'],
			);
			if ( null !== $term ) {
				wp_update_term( $term['term_id'], Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $args );
			} else {
				$term = wp_insert_term( $schema_class['name'], Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, $args );
			}

			// Update the parents/children relationship.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::PARENT_OF_META_KEY );
			foreach ( $schema_class['children'] as $child ) {
				add_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::PARENT_OF_META_KEY, $child['dashname'] );
			}

			// Update the term name.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::NAME_META_KEY );
			update_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::NAME_META_KEY, $schema_class['name'] );

			// Update the term URI.
			delete_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::URI_META_KEY );
			update_term_meta( $term['term_id'], Wordlift_Schemaorg_Class_Service::URI_META_KEY, "http://schema.org/{$schema_class['name']}" );

		}

		return true;
	}

}
