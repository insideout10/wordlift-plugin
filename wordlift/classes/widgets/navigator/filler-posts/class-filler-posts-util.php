<?php

namespace Wordlift\Widgets\Navigator\Filler_Posts;

use Wordlift\Widgets\Srcset_Util;

/**
 * @since 3.27.8
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Filler_Posts_Util {

	/**
	 * @var array<Filler_Posts>
	 */
	private $sources = array();

	public function __construct( $post_id, $alternate_post_type = null ) {

		$post_type = $alternate_post_type ? $alternate_post_type : get_post_type( $post_id );

		if ( 'post' === $post_type || ( is_array( $post_type ) && in_array( 'post', $post_type, true ) ) ) {
			$this->sources = array(
				new Same_Category_Filler_Posts( $post_id ),
				new Same_Post_Type_Filler_Posts( $post_id ),
			);
		} elseif ( 'product' === $post_type || ( is_array( $post_type ) && in_array( 'product', $post_type, true ) ) ) {
			$this->sources = array(
				new Same_Post_Type_Same_Category_Posts( $post_id, 'product' ),
				new Same_Post_Type_Filler_Posts( $post_id, 'product' ),
			);
		} else {
			$this->sources = array(
				new Same_Post_Type_Filler_Posts( $post_id ),
			);
		}
	}

	/**
	 * @param $posts array<\WP_Post>
	 *
	 * @return array<int>
	 */
	private function extract_post_ids( $posts ) {
		return array_map(
			function ( $post ) {
				/**
				 * @var $post \WP_Post
				 */
				return $post->ID;
			},
			$posts
		);
	}

	public function get_filler_posts( $filler_count, $post_ids_to_be_excluded ) {

		$filler_posts = array();

		foreach ( $this->sources as $source ) {

			if ( $filler_count <= 0 ) {
				break;
			}
			/**
			 * @var Filler_Posts $source
			 */
			$source->post_ids_to_be_excluded = $post_ids_to_be_excluded;
			$source->filler_count            = $filler_count;

			$posts    = $source->get_posts( $filler_count, $post_ids_to_be_excluded );
			$post_ids = $this->extract_post_ids( $posts );

			// Update the post ids, filler posts and filler count
			$post_ids_to_be_excluded = array_merge( $post_ids_to_be_excluded, $post_ids );
			$filler_count            = $filler_count - count( $posts );
			$filler_posts            = array_merge( $filler_posts, $posts );
		}
		$filler_posts = $this->add_additional_properties_to_filler_posts( $filler_posts );

		return $filler_posts;

	}

	/**
	 * @param $posts array<\WP_Post>
	 *
	 * @return array $posts array<\WP_Post>
	 */
	private function add_additional_properties_to_filler_posts( $posts ) {
		return array_map(
			function ( $post ) {
				$post->thumbnail  = get_the_post_thumbnail_url( $post->ID, 'medium' );
				$post->permalink  = get_permalink( $post->ID );
				$post->post_title = html_entity_decode( $post->post_title, ENT_QUOTES, 'UTF-8' );

				return $post;
			},
			$posts
		);
	}

	/**
	 * Called by wordlift navigator, converts all the posts to response format.
	 *
	 * @param $filler_count
	 * @param $post_ids_to_be_excluded
	 *
	 * @return array
	 */
	public function get_filler_response( $filler_count, $post_ids_to_be_excluded ) {
		$filler_posts = $this->get_filler_posts( $filler_count, $post_ids_to_be_excluded );
		// Add thumbnail and permalink to filler posts
		$filler_response = array();
		foreach ( $filler_posts as $post_obj ) {
			$thumbnail         = get_the_post_thumbnail_url( $post_obj, 'medium' );
			$filler_response[] = array(
				'post'   => array(
					'id'        => $post_obj->ID,
					'permalink' => get_permalink( $post_obj->ID ),
					'thumbnail' => ( $thumbnail ) ? $thumbnail : WL_DEFAULT_THUMBNAIL_PATH,
					'title'     => $post_obj->post_title,
					'srcset'    => Srcset_Util::get_srcset( $post_obj->ID, Srcset_Util::NAVIGATOR_WIDGET ),
				),
				'entity' => array(
					'id' => 0,
				),
			);
		}

		return $filler_response;
	}

	/**
	 * Called by wordlift navigator, converts all the posts to response format.
	 *
	 * @param $filler_count
	 * @param $post_ids_to_be_excluded
	 *
	 * @return array
	 */
	public function get_product_navigator_response( $filler_count, $post_ids_to_be_excluded ) {
		$filler_posts = $this->get_filler_posts( $filler_count, $post_ids_to_be_excluded );
		// Add thumbnail and permalink to filler posts
		$filler_response = array();
		foreach ( $filler_posts as $post_obj ) {
			$product           = wc_get_product( $post_obj->ID );
			$filler_response[] = array(
				'product' => array(
					'id'              => $post_obj->ID,
					'permalink'       => get_permalink( $post_obj->ID ),
					'title'           => $post_obj->post_title,
					'thumbnail'       => get_the_post_thumbnail_url( $post_obj->ID, 'medium' ),
					'regular_price'   => $product->get_regular_price(),
					'sale_price'      => $product->get_sale_price(),
					'price'           => $product->get_price(),
					'currency_symbol' => get_woocommerce_currency_symbol(),
					'discount_pc'     => ( $product->get_sale_price() && ( $product->get_regular_price() > 0 ) ) ? round( 1 - ( $product->get_sale_price() / $product->get_regular_price() ), 2 ) * 100 : 0,
					'average_rating'  => $product->get_average_rating(),
					'rating_count'    => $product->get_rating_count(),
					'rating_html'     => wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ),
				),
				'entity'  => array(),
			);
		}

		return $filler_response;
	}

}
