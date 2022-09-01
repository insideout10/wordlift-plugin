<?php
/**
 * Shortcodes: Navigator Shorcode.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Navigator_Shortcode} class which provides the
 * `wl_navigator` implementation.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Navigator_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_navigator';

	public function __construct() {
		parent::__construct();
		$this->register_block_type();
	}

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {
		return Wordlift_AMP_Service::is_amp_endpoint() ? $this->amp_shortcode( $atts )
			: $this->web_shortcode( $atts );
	}

	private function register_block_type() {

		$scope = $this;

		add_action(
			'init',
			function () use ( $scope ) {
				if ( ! function_exists( 'register_block_type' ) ) {
					// Gutenberg is not active.
					return;
				}

				register_block_type(
					'wordlift/navigator',
					array(
						'editor_script'   => 'wl-block-editor',
						'render_callback' => function ( $attributes ) use ( $scope ) {
							$attr_code = '';
							foreach ( $attributes as $key => $value ) {
								$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
							}

							return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
						},
						'attributes'      => $scope->get_navigator_block_attributes(),
					)
				);
			}
		);
	}

	/**
	 * Shared function used by web_shortcode and amp_shortcode
	 * Bootstrap logic for attributes extraction and boolean filtering
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return array $shortcode_atts
	 * @since      3.20.0
	 */
	private function make_shortcode_atts( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts(
			array(
				'title'       => __( 'Related articles', 'wordlift' ),
				'limit'       => 4,
				'offset'      => 0,
				'template_id' => '',
				'post_id'     => '',
				'uniqid'      => uniqid( 'wl-navigator-widget-' ),
				'order_by'    => 'ID DESC',
				'post_types'  => '',
			),
			$atts
		);

		return $shortcode_atts;
	}

	/**
	 * Function in charge of displaying the [wl-navigator] in web mode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for web
	 * @since 3.20.0
	 */
	private function web_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$title        = esc_attr( sanitize_text_field( $shortcode_atts['title'] ) );
		$template_id  = esc_attr( sanitize_text_field( $shortcode_atts['template_id'] ) );
		$limit        = esc_attr( sanitize_text_field( $shortcode_atts['limit'] ) );
		$offset       = esc_attr( sanitize_text_field( $shortcode_atts['offset'] ) );
		$sort         = esc_attr( sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) ) );
		$navigator_id = ! empty( $shortcode_atts['uniqid'] ) ? esc_attr( sanitize_text_field( $shortcode_atts['uniqid'] ) ) : uniqid( 'wl-navigator-widget-' );

		$rest_url = $post ? admin_url(
			'admin-ajax.php?' . build_query(
				array(
					'action'     => 'wl_navigator',
					'uniqid'     => $navigator_id,
					'post_id'    => $post->ID,
					'limit'      => $limit,
					'offset'     => $offset,
					'sort'       => $sort,
					'post_types' => $shortcode_atts['post_types'],
					'_wpnonce'   => wp_create_nonce( 'wl_navigator' ),
				)
			)
		) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		wp_enqueue_script( 'wordlift-cloud' );
		$template_url = get_rest_url( null, '/wordlift/v1/navigator/template' );

		return <<<HTML
			<!-- Navigator {$navigator_id} -->
			<div id="{$navigator_id}" 
				 class="wl-navigator" 
				 data-rest-url="{$rest_url}" 
				 data-title="{$title}" 
				 data-template-id="{$template_id}"
				 data-template-url="{$template_url}"
				 data-limit="{$limit}"></div>
			<!-- /Navigator {$navigator_id} -->
HTML;
	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in amp mode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for amp
	 * @since 3.20.0
	 */
	private function amp_shortcode( $atts ) {
		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$title        = esc_attr( sanitize_text_field( $shortcode_atts['title'] ) );
		$template_id  = esc_attr( sanitize_text_field( $shortcode_atts['template_id'] ) );
		$limit        = esc_attr( sanitize_text_field( $shortcode_atts['limit'] ) );
		$offset       = esc_attr( sanitize_text_field( $shortcode_atts['offset'] ) );
		$sort         = esc_attr( sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) ) );
		$navigator_id = ! empty( $shortcode_atts['uniqid'] ) ? esc_attr( sanitize_text_field( $shortcode_atts['uniqid'] ) ) : uniqid( 'wl-navigator-widget-' );

		$permalink_structure = get_option( 'permalink_structure' );
		$delimiter           = empty( $permalink_structure ) ? '&' : '?';
		$rest_url            = $post ? rest_url(
			WL_REST_ROUTE_DEFAULT_NAMESPACE . '/navigator' . $delimiter . build_query(
				array(
					'uniqid'   => $navigator_id,
					'post_id'  => $post->ID,
					'limit'    => $limit,
					'offset'   => $offset,
					'sort'     => $sort,
					'_wpnonce' => wp_create_nonce( 'wl_navigator' ),
				)
			)
		) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
		// This is a hackish way, but this works for http and https URLs
		$rest_url = str_replace( array( 'http:', 'https:' ), '', $rest_url );

		if ( empty( $template_id ) ) {
			$template_id = 'template-' . $navigator_id;
			wp_enqueue_style( 'wordlift-amp-custom', plugin_dir_url( __DIR__ ) . '/css/wordlift-amp-custom.min.css', array(), WORDLIFT_VERSION );
		}

		return <<<HTML
		<div id="{$navigator_id}" class="wl-amp-navigator" style="width: 100%">
			<h3 class="wl-headline">{$title}</h3>
			<amp-list 
				width="auto"
				height="220"
				layout="fixed-height"
				src="{$rest_url}"
				template="{$template_id}">
			</amp-list>
		</div>
		<template type="amp-mustache" id="template-{$navigator_id}"> 
			<div class="wordlift-navigator">
				<section class="cards">
				{{#values}}
					<article class="card">
						<a href="{{post.permalink}}">
							<amp-img
		                        width="16"
		                        height="9"
								layout="responsive"
		                        src="{{post.thumbnail}}"
		                        srcset="{{post.srcset}}"></amp-img>
							<div class="card-content">
								<header class="title">{{post.title}}</header>
							</div>
						</a>
					</article>
				{{/values}}
				</section>
			</div>
		</template>
HTML;
	}

	/**
	 * @return array
	 */
	public function get_navigator_block_attributes() {
		return array(
			'title'       => array(
				'type'    => 'string',
				'default' => __( 'Related articles', 'wordlift' ),
			),
			'limit'       => array(
				'type'    => 'number',
				'default' => 4,
			),
			'template_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'post_id'     => array(
				'type'    => 'number',
				'default' => '',
			),
			'offset'      => array(
				'type'    => 'number',
				'default' => 0,
			),
			'uniqid'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'order_by'    => array(
				'type'    => 'string',
				'default' => 'ID DESC',
			),
			'preview'     => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'preview_src' => array(
				'type'    => 'string',
				'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/navigator.png',
			),
			'post_types'  => array(
				'type'    => 'string',
				'default' => '',
			),
		);
	}

}
