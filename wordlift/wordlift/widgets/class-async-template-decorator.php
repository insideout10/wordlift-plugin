<?php

namespace Wordlift\Widgets;

/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Async_Template_Decorator {

	/**
	 * @var $rest_url_name string
	 */
	private $rest_url_name;

	/**
	 * @var $filter_name string
	 */
	private $filter_name;

	/**
	 * Async_Template_Decorator constructor.
	 *
	 * @param $shortcode_instance \Wordlift_Shortcode
	 */
	public function __construct( $shortcode_instance ) {
		$this->rest_url_name = $this->get_widget_name( $shortcode_instance );
		$this->filter_name   = str_replace( '-', '_', $this->rest_url_name );
		add_action( 'rest_api_init', array( $this, 'register_template_route' ) );
	}

	public function register_template_route() {

		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			"/{$this->rest_url_name}/template/",
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_template' ),
				/**
				 * We want this endpoint to be publicly accessible
				 */
				'permission_callback' => '__return_true',
				'args'                => array(
					'template_id' => array(
						// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
						'validate_callback' => function ( $param, $request, $key ) {
							return is_string( $param ) && $param;
						},
					),
				),
			)
		);
	}

	/**
	 * Shortcode widget makes call to this endpoint to get the template.
	 * Takes the request, checks if template id is registered via filter,
	 * if not it returns empty.
	 *
	 * @param $request \WP_REST_Request
	 *
	 * @return string Returns the template string.
	 */
	public function get_template( $request ) {
		$data        = $request->get_params();
		$template_id = (string) $data['template_id'];
		$templates   = apply_filters( "wordlift_{$this->filter_name}_templates", array() );
		$template    = array_key_exists( $template_id, $templates ) ? $templates[ $template_id ] : '';

		return array( 'template' => $template );
	}

	/**
	 * @param $shortcode_instance \Wordlift_Shortcode
	 *
	 * @return string
	 */
	private static function get_widget_name( $shortcode_instance ) {
		$name = str_replace( 'wl_', '', $shortcode_instance::SHORTCODE );

		return str_replace( '_', '-', $name );
	}

}
