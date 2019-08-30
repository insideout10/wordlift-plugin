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
	 * @since      3.20.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return array $shortcode_atts
	 */
	private function make_shortcode_atts( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'             => __( 'Related articles', 'wordlift' ),
			'limit'             => 4
		), $atts );

		return $shortcode_atts;
	}

	/**
	 * Function in charge of diplaying the [wl-navigator] in web mode.
	 *
	 * @since 3.20.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for web
	 */
	private function web_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		/*
		 * Display the navigator only on the single post/page.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/831
		 *
		 * @since 3.19.3 we're using `is_singular` instead of `is_single` to allow the navigator also on pages.
		 */
		// avoid building the widget when there is a list of posts.
		if ( ! is_singular() ) {
			return '';
		}

		$current_post = get_post();

		// Enqueue common shortcode scripts.
		$this->enqueue_scripts();

		// Use the registered style which define an optional dependency to font-awesome.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/699
		//		wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );
		wp_enqueue_style( 'wordlift-ui' );

		$navigator_id = uniqid( 'wl-navigator-widget-' );

		wp_localize_script( 'wordlift-ui', 'wl_navigator_params', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wl_navigator',
				'post_id'  => $current_post->ID,
				'attrs'    => $shortcode_atts,
			)
		);

		return "<div id='$navigator_id' class='wl-navigator-widget'></div>";
	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in amp mode.
	 *
	 * @since 3.20.0
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Shortcode HTML for amp
	 */
	private function amp_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when there is a list of posts.
		if ( ! is_singular() ) {
			return '';
		}

		$current_post = get_post();

		// Inject amp specific styles inline
		add_action( 'amp_post_template_css', array(
			$this,
			'amp_post_template_css',
		) );

		$navigator_id = uniqid( 'wl-navigator-widget-' );

		$wp_json_base = get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE;

		$query_posts = array(
			'post_id' => $current_post->ID,
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
			), '', $wp_json_base ) . '/navigator' . $delimiter . http_build_query( $query_posts );

		return <<<HTML
		<div id="{$navigator_id}" class="wl-navigator-widget">
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
						<div class="wl-card">
							<h6 class="wl-card-header"><a href="{{entity.permalink}}">{{entity.label}}</a></h6>
							<amp-img 
								width="2"
								height="1"
								layout="responsive"
								src="{{post.thumbnail}}"></amp-img>
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
						<div class="wl-card">
							<h6 class="wl-card-header"><a href="{{entity.permalink}}">{{entity.label}}</a></h6>
							<amp-img 
								width="2"
								height="1"
								layout="responsive"
								src="{{post.thumbnail}}"></amp-img>
							<div class="wl-card-title"><a href="{{post.permalink}}">{{post.title}}</a></div>  
						</div>	
					{{/values}}
					</amp-carousel>
				</template>
			</amp-list>	
		</div>
HTML;
	}

	/**
	 * Customize the CSS when in AMP.
	 * Should echo (not return) CSS code
	 *
	 * @since 3.20.0
	 */
	public function amp_post_template_css() {
		echo file_get_contents( dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-amp-custom.min.css' );
	}

}
