<?php
/**
 * Shortcodes: Faceted Search Shortcode.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Faceted_Search_Shortcode} class which provides the
 * `wl_faceted_search` implementation.
 *
 * @since      3.20.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Faceted_Search_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_faceted_search';

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
			'title'       => __( 'Related articles', 'wordlift' ),
			'limit'       => 4,
			'post_id'     => '',
			'template_id' => '',
			'uniqid'      => uniqid( 'wl-faceted-widget-' ),
		), $atts );

		return $shortcode_atts;
	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in web mode.
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

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post       = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( sanitize_text_field( $shortcode_atts['post_id'] ) ) ) : get_post();
		$limit      = sanitize_text_field( $shortcode_atts['limit'] );
		$faceted_id = sanitize_text_field( $shortcode_atts['uniqid'] );

		$rest_url = $post ? rest_url( sprintf( "wordlift/v1/faceted-search?post_id=%s&limit=%s", $post->ID, $limit ) ) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		wp_enqueue_script( 'wordlift-cloud' );
		$json_faceted_id = wp_json_encode( $faceted_id );
		echo "<script type='application/javascript'>window.wlFaceteds = window.wlFaceteds || []; wlFaceteds.push( $json_faceted_id );</script>";

		return sprintf(
			'<div id="%s" class="%s" data-rest-url="%s" data-title="%s" data-template-id="%s"></div>',
			$faceted_id,
			'wl-faceted',
			$rest_url,
			sanitize_text_field( $shortcode_atts['title'] ),
			sanitize_text_field( $shortcode_atts['template_id'] )
		);
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

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post       = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( sanitize_text_field( $shortcode_atts['post_id'] ) ) ) : get_post();
		$limit      = sanitize_text_field( $shortcode_atts['limit'] );
		$faceted_id = sanitize_text_field( $shortcode_atts['uniqid'] );

		$rest_url = $post ? rest_url( sprintf( "wordlift/v1/faceted-search?amp&post_id=%s&limit=%s", $post->ID, $limit ) ) : false;

		// avoid building the widget when no valid $rest_url
		if ( ! $rest_url ) {
			return;
		}

		// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
		// This is a hackish way, but this works for http and https URLs
		$rest_url = str_replace( array(
			'http:',
			'https:',
		), '', $rest_url );

		if ( ! empty( $shortcode_atts['template_id'] ) ) {
			$template_id = sanitize_text_field( $shortcode_atts['template_id'] );
		} else {
			$template_id = "template-" . $faceted_id;
			// Enqueue amp specific styles
			wp_enqueue_style( 'wordlift-amp-custom', plugin_dir_url( dirname( __FILE__ ) ) . '/css/wordlift-amp-custom.min.css' );
		}

		return <<<HTML
		<div id="{$faceted_id}" class="wl-amp-faceted">
			<h2 class="wl-headline">{$shortcode_atts['title']}</h2>
			<amp-state id="referencedPosts">
				<script type="application/json">
					[]
				</script>
			</amp-state>
			<amp-state id="activeEntities">
				<script type="application/json">
					[]
				</script>
			</amp-state>
			<amp-state id="allPostsEntities" src="{$rest_url}"></amp-state>
			<section class="chips">
				<amp-list 
					height="32"
					layout="fixed-height"
					src="{$rest_url}"
					[src]="allPostsEntities.entities.sort((a, b) => activeEntities.includes(a.id) ? -1 : 1)"
					items="entities">
					<template type="amp-mustache">
						<span [class]="activeEntities.includes('{{id}}') ? 'chip active' : 'chip'" on="tap:AMP.setState({
							referencedPosts: referencedPosts.includes({{referencedPosts}}) ? referencedPosts.filter(item => item != {{referencedPosts}}) : referencedPosts.concat({{referencedPosts}}),
							activeEntities: activeEntities.includes('{{id}}') ? activeEntities.filter(item => item != '{{id}}') : activeEntities.concat('{{id}}')
						})">{{label}}</span>
					</template>	
				</amp-list>
			</section>
			<section class="cards">
				<amp-list 
					height="200"
					layout="fixed-height"
					src="{$rest_url}"
					[src]="{values: allPostsEntities.posts[0].values.sort((a, b) => referencedPosts.includes(a.ID) ? -1 : 1)}"
					template="{$template_id}"
					items="posts">
					<template type="amp-mustache" id="template-{$faceted_id}">
						<amp-carousel 
						  media="(min-width: 380px)"
						  height="200"
					      layout="fixed-height"
					      type="carousel">
					      {{#values}}
							<article class="card" style="width: 25%">
								<a href="{{permalink}}">
									<amp-img
				                        width="16"
				                        height="9"
										layout="responsive"
				                        src="{{thumbnail}}"></amp-img>
									<div class="card-content"><h3 class="title">{{post_title}}</h3></div>
								</a>
							</article>
						  {{/values}}
						</amp-carousel>
						<amp-carousel 
						  media="(max-width: 380px)"
						  height="250"
					      layout="fixed-height"
					      type="slides">
					      {{#values}}
							<article class="card" style="width: 100%">
								<a href="{{permalink}}">
									<amp-img
				                        width="16"
				                        height="9"
										layout="responsive"
				                        src="{{thumbnail}}"></amp-img>
									<div class="card-content"><h3 class="title">{{post_title}}</h3></div>
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
