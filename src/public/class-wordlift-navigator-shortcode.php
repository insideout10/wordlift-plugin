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
			'order_by'    => 'ID DESC'
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
		$title        = sanitize_text_field( $shortcode_atts['title'] );
		$template_id  = sanitize_text_field( $shortcode_atts['template_id'] );
		$limit        = sanitize_text_field( $shortcode_atts['limit'] );
		$offset       = sanitize_text_field( $shortcode_atts['offset'] );
		$sort         = sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) );
		$navigator_id = sanitize_text_field( $shortcode_atts['uniqid'] );

		$rest_url = $post ? admin_url( 'admin-ajax.php?' . build_query( array(
				'action'  => 'wl_navigator',
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
			<!-- Navigator {$navigator_id} -->
			<script type="application/javascript">
				window.wlNavigators = window.wlNavigators || []; wlNavigators.push({$json_navigator_id});
			</script>
			<div id="{$navigator_id}" 
				 class="wl-navigator" 
				 data-rest-url="{$rest_url}" 
				 data-title="{$title}" 
				 data-template-id="{$template_id}" 
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
	 *
	 */
	private function amp_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// avoid building the widget when no post_id is specified and there is a list of posts.
		if ( empty( $shortcode_atts['post_id'] ) && ! is_singular() ) {
			return;
		}

		$post         = ! empty( $shortcode_atts['post_id'] ) ? get_post( intval( $shortcode_atts['post_id'] ) ) : get_post();
		$title        = sanitize_text_field( $shortcode_atts['title'] );
		$template_id  = sanitize_text_field( $shortcode_atts['template_id'] );
		$limit        = sanitize_text_field( $shortcode_atts['limit'] );
		$offset       = sanitize_text_field( $shortcode_atts['offset'] );
		$sort         = sanitize_sql_orderby( sanitize_text_field( $shortcode_atts['order_by'] ) );
		$navigator_id = sanitize_text_field( $shortcode_atts['uniqid'] );

		$permalink_structure = get_option( 'permalink_structure' );
		$delimiter = empty( $permalink_structure ) ? '&' : '?';
		$rest_url  = $post ? rest_url( WL_REST_ROUTE_DEFAULT_NAMESPACE . '/navigator' . $delimiter . build_query( array(
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
		<div id="{$navigator_id}" class="wl-amp-navigator" style="width: 100%">
			<h3 class="wl-headline">{$title}</h3>
			<amp-list 
				width="auto"
				height="320"
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

}
