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

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {

		return Wordlift_AMP_Service::is_amp_endpoint() ? $this->amp_shortcode( $atts )
			: $this->web_shortcode( $atts );
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
		$title        = sanitize_text_field( $shortcode_atts['title'] );
		$template_id  = sanitize_text_field( $shortcode_atts['template_id'] );
		$limit        = sanitize_text_field( $shortcode_atts['limit'] );
		$offset       = sanitize_text_field( $shortcode_atts['offset'] );
		$sort         = sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) );
		$navigator_id = sanitize_text_field( $shortcode_atts['uniqid'] );

		$delimiter = empty( get_option( 'permalink_structure' ) ) ? '&' : '?';
		$rest_url  = $post ? rest_url( WL_REST_ROUTE_DEFAULT_NAMESPACE . '/products-navigator' . $delimiter . build_query( array(
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
		$json_navigator_id = wp_json_encode( $navigator_id );

		return <<<HTML
			<!-- Products Navigator {$navigator_id} -->
			<script type="application/javascript">
				window.wlProductsNavigators = window.wlProductsNavigators || []; wlProductsNavigators.push({$json_navigator_id});
			</script>
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

		// avoid building the widget when there is a list of posts.
		if ( ! is_singular() ) {
			return '';
		}

		$current_post = get_post();

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$navigator_id = $shortcode_atts['uniqid'];

		$wp_json_base = get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE;

		$navigator_query = array(
			'uniqid'  => $navigator_id,
			'post_id' => $post->ID,
			'limit'   => $shortcode_atts['limit'],
			'offset'  => $shortcode_atts['offset'],
			// Changed to `sort` for Presslabs cache nodes compatibility.
			'sort'    => $shortcode_atts['order_by']
		);

		if ( strpos( $wp_json_base, 'wp-json/' . WL_REST_ROUTE_DEFAULT_NAMESPACE ) ) {
			$delimiter = '?';
		} else {
			$delimiter = '&';
		}

		// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
		// This is a hackish way, but this works for http and https URLs
		$wp_json_url_posts = str_replace( array(
				'http:',
				'https:',
			), '', $wp_json_base ) . '/navigator' . $delimiter . http_build_query( $navigator_query );

		if ( ! empty( $shortcode_atts['template_id'] ) ) {
			$template_id = $shortcode_atts['template_id'];
		} else {
			$template_id = "template-" . $navigator_id;
			add_action( 'amp_post_template_css', array( $this, 'amp_post_template_css' ) );
		}

		return <<<HTML
		<div id="{$navigator_id}" class="wl-amp-navigator" style="width: 100%">
			<h3 class="wl-headline">{$shortcode_atts['title']}</h3>
			<amp-list 
				width="auto"
				height="320"
				layout="fixed-height"
				src="{$wp_json_url_posts}"
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
		                        width="800"
		                        height="450"
								layout="responsive"
		                        src="{{post.thumbnail}}"></amp-img>
							<div class="card-content"><h3 class="title">{{post.title}}</h3></div>
						</a>
					</article>
				{{/values}}
				</section>
			</div>
		</template>
HTML;
	}

	public function amp_post_template_css() {
		// Default CSS for default template
		echo <<<CSS
	        .wordlift-navigator .title{font-size:16px;margin:0.5rem 0}
	        .wordlift-navigator .cards{display:flex;flex-wrap:wrap}
	        .wordlift-navigator .cards .card{background:white;flex:1 0 300px;box-sizing:border-box;margin:1rem .25em}
	        @media screen and (min-width: 40em){
	            .wordlift-navigator .cards .card{max-width:calc(50% -  1em)}
	        }
	        @media screen and (min-width: 60em){
	            .wordlift-navigator .cards .card{max-width:calc(25% - 1em)}
	        }
	        .wordlift-navigator .cards .card a{color:black;text-decoration:none}
	        .wordlift-navigator .cards .card a:hover{box-shadow:3px 3px 8px #ccc}
	        .wordlift-navigator .cards .card .thumbnail{width:100%;padding-bottom:56.25%;background-size:cover}
	        .wordlift-navigator .cards .card .thumbnail img{display:block;border:0;width:100%;height:auto}
	        .wordlift-navigator .cards .card .card-content .title{font-size:14px;margin:0.3rem 0}
CSS;
	}

}
