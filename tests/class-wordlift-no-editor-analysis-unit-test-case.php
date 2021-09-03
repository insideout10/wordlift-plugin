<?php
/**
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Features\Features_Registry;

abstract class Wordlift_No_Editor_Analysis_Unit_Test_Case extends Wordlift_Unit_Test_Case {


	protected $url_patterns_json_response_map = array();

	const NO_EDITOR_ANALYSIS_POST_TYPE = 'no-editor-analysis';


	public function setUp() {
		parent::setUp();
		add_filter( 'pre_http_request', 'pre_http_request', PHP_INT_MAX, 3 );
		// Reset all global filters.
		global $wp_filter, $wp_scripts, $wp_styles;
		$features_registry = Features_Registry::get_instance();
		$wp_filter         = array();
		$wp_scripts        = null;
		$wp_styles         = null;
		$features_registry->clear_all();
		add_filter( 'wl_feature__enable__no-editor-analysis', '__return_true' );
		// vocabulary terms feature should now be enabled.
		run_wordlift();
		do_action( 'plugins_loaded' );

		// Create a post type with no support for editor.


		register_post_type( 'no-editor-analysis', array(
			'labels'              => array(
				'name'          => 'no-editor-analysis',
				'singular_name' => 'no-editor-analysis',
			),
			'description'         => 'no-editor-analysis',
			'public'              => true,
			'supports'            => array(
				'title',
				'thumbnail',
				'excerpt',
				'custom-fields'
			),
			'has_archive'         => false,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
		) );

		add_action( 'wl_no_editor_analysis_post_types', function ( $post_types ) {
			$post_types[] = 'no-editor-analysis';

			return $post_types;
		} );
		add_filter( 'pre_http_request', array( $this, 'v2_analysis_request_capture' ), PHP_INT_MAX, 3 );
	}

	public function tearDown() {
		parent::tearDown();
		$this->url_patterns_json_response_map = array();
	}

	public function v2_analysis_request_capture( $response, $request, $url ) {


		foreach ( $this->url_patterns_json_response_map as $url_pattern => $json_response ) {
			if ( preg_match( $url_pattern, $url ) ) {
				return array(
					'body'     => $json_response,
					'headers'  => array( 'content-type' => 'application/json' ),
					'response' => array( 'code' => 200, )
				);
			}
		}


		return $response;

	}


}