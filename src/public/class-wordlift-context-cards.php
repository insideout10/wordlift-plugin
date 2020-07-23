<?php
/**
 * Context Cards Service
 *
 * @since      3.22.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

class Wordlift_Context_Cards_Service {

	public function enqueue_scripts() {

		$show_context_cards     = apply_filters( 'wl_context_cards_show', true );
		$context_cards_base_url = apply_filters( 'wl_context_cards_base_url', get_rest_url( null, WL_REST_ROUTE_DEFAULT_NAMESPACE . '/jsonld' ) );

		if ( $show_context_cards ) {
			wp_enqueue_script( 'wordlift-cloud' );
			wp_localize_script( 'wordlift-cloud', '_wlCloudSettings', array(
				'selector' => 'a.wl-entity-page-link',
				'url'      => $context_cards_base_url
			) );
		}

		add_filter( 'wl_anchor_data_attributes', array( $this, 'anchor_data_attributes' ), 10, 2 );
	}

	public function anchor_data_attributes( $attributes, $post_id ) {

		$supported_types   = Wordlift_Entity_Service::valid_entity_post_types();
		$post_type         = get_post_type( $post_id );
		$enabled_templates = apply_filters( 'wl_context_cards_enabled_templates', array( 'product' ) );

		if ( in_array( $post_type, $supported_types ) && in_array( $post_type, $enabled_templates ) ) {

			$additional_attributes = array( 'post-type-template' => $post_type );

			switch($post_type){
				case 'product':
					$product = wc_get_product( $post_id );
					$additional_attributes['template-payload'] = json_encode(
						array(
							'regular_price'   => $product->get_regular_price(),
							'currency_symbol' => get_woocommerce_currency_symbol(),
							'discount'        => ($product->get_sale_price() && $product->get_regular_price() > 0) ? round( 1 - ( $product->get_sale_price() / $product->get_regular_price() ), 2 ) : 0,
							'average_rating'  => $product->get_average_rating(),
							'rating_count'    => $product->get_rating_count(),
							'rating_html'     => wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() )
						)
					);
			}

			return $attributes + $additional_attributes;
		}

		return $attributes;
	}

}
