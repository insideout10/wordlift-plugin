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
	 * @since      3.20.0
	 *
	 */
	private function make_shortcode_atts( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'       => __( 'Related articles', 'wordlift' ),
			'limit'       => 4,
			'offset'      => 0,
			'template_id' => '',
			'post_id'     => '',
			'uniqid'      => uniqid( 'wl-navigator-widget-' ),
			'order_by'    => 'ID ASC'
		), $atts );

		return $shortcode_atts;
	}

	/**
	 * Function in charge of diplaying the [wl-navigator] in web mode.
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

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$navigator_id = $shortcode_atts['uniqid'];
		$rest_url     = $post ? admin_url( sprintf( 'admin-ajax.php?action=wl_navigator&uniqid=%s&post_id=%s&limit=%s&offset=%s&order_by=%s', $navigator_id, $post->ID, $shortcode_atts['limit'], $shortcode_atts['offset'], $shortcode_atts['order_by'] ) ) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		wp_enqueue_script( 'wordlift-cloud' );
		$json_navigator_id = json_encode( $navigator_id );
		wp_add_inline_script( 'wordlift-cloud', "window.wlNavigators = window.wlNavigators || []; wlNavigators.push( $json_navigator_id );" );

		return sprintf(
			'<div id="%s" class="%s" data-rest-url="%s" data-title="%s" data-template-id="%s" data-limit="%s"></div>',
			$navigator_id,
			'wl-navigator',
			$rest_url,
			$shortcode_atts['title'],
			$shortcode_atts['template_id'],
			$shortcode_atts['limit']
		);
	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in amp mode.
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

		// Enqueue amp specific styles
		wp_enqueue_style( 'wordlift-amp-custom', plugin_dir_url( dirname( __FILE__ ) ) . '/css/wordlift-amp-custom.min.css' );

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$navigator_id = $shortcode_atts['uniqid'];

		$wp_json_base = get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE;

		$navigator_query = array(
			'uniqid'  => $navigator_id,
			'post_id' => $post->ID,
			'limit'   => $shortcode_atts['limit'],
			'offset'  => $shortcode_atts['offset'],
			'order_by'=> 'ID ASC'
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

		return <<<HTML
		<div id="{$navigator_id}" class="wl-navigator-widget" style="width: 100%">
			<h3 class="wl-headline">{$shortcode_atts['title']}</h3>
			<amp-list 
				media="(min-width: 461px)"
				width="auto"
				height="320"
				layout="fixed-height"
				src="{$wp_json_url_posts}">
				<template type="amp-mustache">  
					<amp-carousel 
						class="wl-amp-carousel"
						height="320"
						layout="fixed-height"
						type="carousel">
					{{#values}}
						<div class="wl-card" style="min-width: 400px">
							<h6 class="wl-card-header"><a href="{{entity.permalink}}">{{entity.label}}</a></h6>
                            <div class="fixed-container" style="height: 220px">
                                <amp-img class="cover"
                                	layout="fill"
                                    src="{{post.thumbnail}}"></amp-img>
                            </div>
							<div class="wl-card-title"><a href="{{post.permalink}}">{{post.title}}</a></div> 
						</div>	
					{{/values}}
					</amp-carousel>
				</template>
			</amp-list>
			<amp-list 
				media="(max-width: 460px)"
				width="auto"
				height="350"
				layout="fixed-height"
				src="{$wp_json_url_posts}">
				<template type="amp-mustache">  
					<amp-carousel 
						class="wl-amp-carousel"
						height="350"
						layout="fixed-height"
						type="slides">
					{{#values}}
						<div class="wl-card" style="min-width: 400px">
							<h6 class="wl-card-header"><a href="{{entity.permalink}}">{{entity.label}}</a></h6>
                            <div class="fixed-container" style="height: 250px">
                                <amp-img class="cover"
                                	layout="fill"
                                    src="{{post.thumbnail}}"></amp-img>
                            </div>
							<div class="wl-card-title"><a href="{{post.permalink}}">{{post.title}}</a></div>  
						</div>	
					{{/values}}
					</amp-carousel>
				</template>
			</amp-list>	
		</div>
HTML;
	}

}
