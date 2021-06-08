<?php
/**
 * Shortcodes: Product Navigator Shortcode.
 *
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Products_Navigator_Shortcode} class which provides the
 * `wl_products_navigator` implementation.
 *
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Products_Navigator_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_products_navigator';

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

		add_action( 'init', function () use ( $scope ) {
			if ( ! function_exists( 'register_block_type' ) ) {
				// Gutenberg is not active.
				return;
			}

			register_block_type( 'wordlift/products-navigator', array(
				'editor_script'   => 'wl-block-editor',
				'render_callback' => function ( $attributes ) use ( $scope ) {
					$attr_code = '';
					foreach ( $attributes as $key => $value ) {
						$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
					}

					return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
				},
				'attributes'      => array(
					'title'       => array(
						'type'    => 'string',
						'default' => __( 'Related products', 'wordlift' ),
					),
					'limit'       => array(
						'type'    => 'number',
						'default' => 4,
					),
					'template_id' => array(
						'type' => 'string',
						'default' => '',
					),
					'post_id'     => array(
						'type' => 'number',
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
					'preview_src'     => array(
						'type'    => 'string',
						'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/products-navigator.png',
					),
				),
			) );
		} );
	}

	/**
	 * Shared function used by web_shortcode and amp_shortcode
	 * Bootstrap logic for attributes extraction and boolean filtering
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return array $shortcode_atts
	 * @since      3.27.0
	 *
	 */
	private function make_shortcode_atts( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'       => __( 'Related products', 'wordlift' ),
			'limit'       => 4,
			'offset'      => 0,
			'template_id' => '',
			'post_id'     => '',
			'uniqid'      => uniqid( 'wl-products-navigator-widget-' ),
			'order_by'    => 'ID DESC'
		), $atts );

		return $shortcode_atts;
	}

	/**
	 * Function in charge of diplaying the [wl_products_navigator] in web mode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for web
	 * @since 3.20.0
	 *
	 */
	private function web_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( sanitize_text_field( $shortcode_atts['post_id'] ) ) ) : get_post();
		$title        = esc_attr( sanitize_text_field( $shortcode_atts['title'] ) );
		$template_id  = esc_attr( sanitize_text_field( $shortcode_atts['template_id'] ) );
		$limit        = esc_attr( sanitize_text_field( $shortcode_atts['limit'] ) );
		$offset       = esc_attr( sanitize_text_field( $shortcode_atts['offset'] ) );
		$sort         = esc_attr( sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) ) );
		$navigator_id = ! empty( $shortcode_atts['uniqid'] ) ? esc_attr( sanitize_text_field( $shortcode_atts['uniqid'] ) ) : uniqid( 'wl-products-navigator-widget-' );

		$permalink_structure = get_option( 'permalink_structure' );
		$delimiter           = empty( $permalink_structure ) ? '&' : '?';
		$rest_url            = $post ? rest_url( WL_REST_ROUTE_DEFAULT_NAMESPACE . '/products-navigator' . $delimiter . build_query( array(
				'uniqid'  => $navigator_id,
				'post_id' => $post->ID,
				'limit'   => $limit,
				'offset'  => $offset,
				'sort'    => $sort
			) ) ) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		wp_enqueue_script( 'wordlift-cloud' );

		return <<<HTML
			<!-- Products Navigator {$navigator_id} -->
			<div id="{$navigator_id}" 
				 class="wl-products-navigator" 
				 data-rest-url="{$rest_url}" 
				 data-title="{$title}" 
				 data-template-id="{$template_id}" 
				 data-limit="{$limit}"></div>
			<!-- /Products Navigator {$navigator_id} -->
HTML;
	}

	/**
	 * Function in charge of diplaying the [wl_products_navigator] in amp mode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for amp
	 * @since 3.20.0
	 *
	 */
	private function amp_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( sanitize_text_field( $shortcode_atts['post_id'] ) ) ) : get_post();
		$title        = esc_attr( sanitize_text_field( $shortcode_atts['title'] ) );
		$template_id  = esc_attr( sanitize_text_field( $shortcode_atts['template_id'] ) );
		$limit        = esc_attr( sanitize_text_field( $shortcode_atts['limit'] ) );
		$offset       = esc_attr( sanitize_text_field( $shortcode_atts['offset'] ) );
		$sort         = esc_attr( sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) ) );
		$navigator_id = ! empty( $shortcode_atts['uniqid'] ) ? esc_attr( sanitize_text_field( $shortcode_atts['uniqid'] ) ) : uniqid( 'wl-products-navigator-widget-' );

		$permalink_structure = get_option( 'permalink_structure' );
		$delimiter           = empty( $permalink_structure ) ? '&' : '?';
		$rest_url            = $post ? rest_url( WL_REST_ROUTE_DEFAULT_NAMESPACE . '/products-navigator' . $delimiter . build_query( array(
				'amp'     => true,
				'uniqid'  => $navigator_id,
				'post_id' => $post->ID,
				'limit'   => $limit,
				'offset'  => $offset,
				'sort'    => $sort
			) ) ) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
		// This is a hackish way, but this works for http and https URLs
		$rest_url = str_replace( array( 'http:', 'https:' ), '', $rest_url );

		if ( empty( $template_id ) ) {
			$template_id = "template-" . $navigator_id;
			wp_enqueue_style( 'wordlift-amp-custom', plugin_dir_url( dirname( __FILE__ ) ) . '/css/wordlift-amp-custom.min.css' );
		}

		return <<<HTML
		<div id="{$navigator_id}" class="wl-amp-products-navigator">
			<h3 class="wl-headline">{$title}</h3>
			<section class="cards">
				<amp-list 
					width="auto"
					height="290"
					layout="fixed-height"
					src="{$rest_url}"
					template="{$template_id}">
					<template type="amp-mustache" id="template-{$navigator_id}"> 
						<amp-carousel 
						  media="(min-width: 380px)"
						  height="260"
					      layout="fixed-height"
					      type="carousel">
					      {{#values}}
							<article class="card" style="width: 33.33%">
								<a href="{{product.permalink}}">
									<amp-img
				                        width="16"
				                        height="9"
										layout="responsive"
				                        src="{{product.thumbnail}}"></amp-img>
									<div class="card-content">
										<header class="title">{{product.title}}</header>
										<div class="star-rating" style="display: none"></div>
									</div>
									<footer class="card-footer">
										<div class="col-left">
											<div class="price">{{{product.currency_symbol}}}{{product.price}}</div>
											{{#product.discount_pc}}
				                            <div class="discount">
				                                <span class="regular">{{{product.currency_symbol}}}{{product.regular_price}}</span> |
				                                <span class="percent">{{product.discount_pc}}% OFF</span>
				                            </div>
				                            {{/product.discount_pc}}
				                            {{^product.discount_pc}}
				                            <div class="discount">&nbsp;</div>
				                            {{/product.discount_pc}}										
										</div>
										<div class="col-right">
											{{#product.rating_count}}
											{{{product.rating_html}}}
											<div class="reviews">
					                            {{product.rating_count}} reviews
					                        </div>
					                        {{/product.rating_count}}
										</div>
									</footer>									
								</a>
							</article>
						  {{/values}}
						</amp-carousel>
						<amp-carousel 
						  media="(max-width: 380px)"
						  height="290"
					      layout="fixed-height"
					      type="slides">
					      {{#values}}
							<article class="card" style="width: 100%">
								<a href="{{product.permalink}}">
									<amp-img
				                        width="16"
				                        height="9"
										layout="responsive"
				                        src="{{product.thumbnail}}"></amp-img>
									<div class="card-content">
										<header class="title">{{product.title}}</header>
										<div class="star-rating"></div>
									</div>
									<footer class="card-footer">
										<div class="col-left">
											<div class="price">{{{product.currency_symbol}}}{{product.price}}</div>
											{{#product.discount_pc}}
				                            <div class="discount">
				                                <span class="regular">{{{product.currency_symbol}}}{{product.regular_price}}</span> |
				                                <span class="percent">{{product.discount_pc}}% OFF</span>
				                            </div>
				                            {{/product.discount_pc}}
				                            {{^product.discount_pc}}
				                            <div class="discount">&nbsp;</div>
				                            {{/product.discount_pc}}										
										</div>
										<div class="col-right">
											{{#product.rating_count}}
											{{{product.rating_html}}}
											<div class="reviews">
					                            {{product.rating_count}} reviews
					                        </div>
					                        {{/product.rating_count}}
										</div>
									</footer>
								</a>
							</article>
						  {{/values}}
						</amp-carousel>
					</template>
				</amp-list>
			</section>
		</div>
HTML;
	}

}
