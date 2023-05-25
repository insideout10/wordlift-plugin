<?php

use Wordlift\Widgets\Navigator\Filler_Posts\Filler_Posts_Util;

class Wordlift_Products_Navigator_Shortcode_REST extends Wordlift_Shortcode_REST {

	const CACHE_TTL = 3600; // 1 hour

	public function __construct() {
		parent::__construct(
			'/products-navigator',
			array(
				'post_id' => array(
					'description' => __( 'Post ID for which Navigator has to be queried', 'wordlift' ),
					'type'        => 'integer',
					'required'    => true,
				),
				'uniqid'  => array(
					'description' => __( 'Navigator uniqueid', 'wordlift' ),
					'type'        => 'string',
					'required'    => true,
				),
				'limit'   => array(
					'default'           => 4,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'offset'  => array(
					'default'           => 0,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'sort'    => array(
					'default'           => 'ID DESC',
					'sanitize_callback' => 'sanitize_sql_orderby',
				),
				'amp'     => array(
					'sanitize_callback' => 'rest_sanitize_boolean',
				),
			)
		);
	}

	public function get_data( $request ) {

		// Sanitize and set defaults
		$navigator_length = $request['limit'];
		$navigator_offset = $request['offset'];
		$order_by         = $request['sort'];
		$post_id          = $request['post_id'];
		$navigator_id     = $request['uniqid'];
		$amp              = $request['amp'];

		$post = get_post( $post_id );

		// Post ID has to match an existing item
		if ( null === $post ) {
			return new WP_Error( 'rest_invalid_post_id', __( 'Invalid post_id', 'wordlift' ), array( 'status' => 404 ) );
		}

		// Determine navigator type and call respective get_*_results
		if ( get_post_type( $post_id ) === Wordlift_Entity_Service::TYPE_NAME ) {
			$referencing_posts = $this->get_entity_results(
				$post_id,
				array(
					'ID',
					'post_title',
				),
				$order_by,
				$navigator_length,
				$navigator_offset
			);
		} else {
			$referencing_posts = $this->get_post_results(
				$post_id,
				array(
					'ID',
					'post_title',
				),
				$order_by,
				$navigator_length,
				$navigator_offset
			);
		}

		// Fetch directly referencing posts excluding referencing posts via entities
		$directly_referencing_posts = $this->get_directly_referencing_posts(
			$post_id,
			array_map(
				function ( $referencing_post ) {
					return $referencing_post->ID;
				},
				$referencing_posts
			)
		);

		// Combine directly referencing posts and referencing posts via entities
		$referencing_posts = array_merge( $directly_referencing_posts, $referencing_posts );

		// loop over them and take the first one which is not already in the $related_posts
		$results = array();
		foreach ( $referencing_posts as $referencing_post ) {
			$serialized_entity = wl_serialize_entity( $referencing_post->entity_id );
			$product           = wc_get_product( $referencing_post->ID );

			$result = array(
				'product' => array(
					'id'              => $referencing_post->ID,
					'permalink'       => get_permalink( $referencing_post->ID ),
					'title'           => $referencing_post->post_title,
					'thumbnail'       => get_the_post_thumbnail_url( $referencing_post, 'medium' ),
					'regular_price'   => $product->get_regular_price(),
					'sale_price'      => $product->get_sale_price(),
					'price'           => $product->get_price(),
					'currency_symbol' => get_woocommerce_currency_symbol(),
					'discount_pc'     => ( $product->get_sale_price() && ( $product->get_regular_price() > 0 ) ) ? round( 1 - ( $product->get_sale_price() / $product->get_regular_price() ), 2 ) * 100 : 0,
					'average_rating'  => $product->get_average_rating(),
					'rating_count'    => $product->get_rating_count(),
					'rating_html'     => wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ),
				),
				'entity'  => array(
					'id'        => $referencing_post->entity_id,
					'label'     => $serialized_entity['label'],
					'mainType'  => $serialized_entity['mainType'],
					'permalink' => get_permalink( $referencing_post->entity_id ),
				),
			);

			$results[] = $result;

		}

		if ( count( $results ) < $navigator_length ) {
			$results = apply_filters( 'wl_products_navigator_data_placeholder', $results, $navigator_id, $navigator_offset, $navigator_length );
		}

		// Add filler posts if needed
		$filler_count = $navigator_length - count( $results );
		if ( $filler_count > 0 ) {
			$referencing_post_ids = array_map(
				function ( $p ) {
					return $p->ID;
				},
				$referencing_posts
			);
			/**
			 * @since 3.28.0
			 * Filler posts are fetched using this util.
			 */
			$filler_posts_util       = new Filler_Posts_Util( $post_id, 'product' );
			$post_ids_to_be_excluded = array_merge( array( $post_id ), $referencing_post_ids );
			$filler_posts            = $filler_posts_util->get_product_navigator_response( $filler_count, $post_ids_to_be_excluded );
			$results                 = array_merge( $results, $filler_posts );
		}

		// Apply filters after fillers are added
		foreach ( $results as $result_index => $result ) {
			$results[ $result_index ]['product'] = apply_filters( 'wl_products_navigator_data_post', $result['product'], intval( $result['product']['id'] ), $navigator_id );
			$results[ $result_index ]['entity']  = apply_filters( 'wl_products_navigator_data_entity', $result['entity'], intval( $result['entity']['id'] ), $navigator_id );
		}

		$results = apply_filters( 'wl_products_navigator_results', $results, $navigator_id );

		return $amp ? array(
			'items' => array(
				array( 'values' => $results ),
			),
		) : $results;

	}

	private function get_directly_referencing_posts( $post_id, $referencing_post_ids ) {

		$directly_referencing_post_ids = Wordlift_Entity_Service::get_instance()->get_related_entities( $post_id );

		$post__in = array_diff( $directly_referencing_post_ids, $referencing_post_ids );

		$directly_referencing_posts = get_posts(
			array(
				'meta_query'          => array(
					array(
						'key' => '_thumbnail_id',
					),
					array(
						'key'   => '_stock_status',
						'value' => 'instock',
					),
				),
				'post__in'            => $post__in,
				'post_type'           => 'product',
				'ignore_sticky_posts' => 1,
			)
		);

		$results = array();

		foreach ( $directly_referencing_posts as $post ) {
			$result             = new stdClass();
			$result->ID         = $post->ID;
			$result->post_title = $post->post_title;
			$result->entity_id  = $post->ID;
			$results[]          = $result;
		}

		return $results;
	}

	private function get_entity_results(
		$post_id,
		$fields = array(
			'ID',
			'post_title',
		),
		$order_by = 'ID DESC',
		$limit = 10,
		$offset = 0
	) {
		global $wpdb;

		$select = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item AS $item";
				},
				(array) $fields
			)
		);

		$order_by = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item";
				},
				(array) $order_by
			)
		);
// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		/** @noinspection SqlNoDataSourceInspection */
		return $wpdb->get_results(
			$wpdb->prepare(
				"
SELECT %4\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r1.object_id
            AND p2.post_status = 'publish'
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r1.subject_id
            AND p.post_status = 'publish'
    INNER JOIN {$wpdb->term_relationships} tr
     	ON tr.object_id = p.ID
    INNER JOIN {$wpdb->term_taxonomy} tt
     	ON tt.term_taxonomy_id = tr.term_taxonomy_id
      	    AND tt.taxonomy = 'wl_entity_type'
    INNER JOIN {$wpdb->terms} t
        ON t.term_id = tt.term_id
            AND t.slug = 'product'
    -- select only posts with featured images.
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
    -- select only instock products
    INNER JOIN {$wpdb->postmeta} m2
        ON m2.post_id = p.ID
            AND (m2.meta_key = '_stock_status' AND m2.meta_value = 'instock')            
 WHERE r1.object_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
",
				$post_id,
				$limit,
				$offset,
				$select,
				$order_by
			)
		);
	}
// phpcs:enable
	private function get_post_results(
		$post_id,
		$fields = array(
			'ID',
			'post_title',
		),
		$order_by = 'ID DESC',
		$limit = 10,
		$offset = 0
	) {
		global $wpdb;

		$select = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item AS $item";
				},
				(array) $fields
			)
		);

		$order_by = implode(
			', ',
			array_map(
				function ( $item ) {
					return "p.$item";
				},
				(array) $order_by
			)
		);
// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		/** @noinspection SqlNoDataSourceInspection */
		return $wpdb->get_results(
			$wpdb->prepare(
				"
SELECT %4\$s, p2.ID as entity_id
 FROM {$wpdb->prefix}wl_relation_instances r1
    INNER JOIN {$wpdb->prefix}wl_relation_instances r2
        ON r2.object_id = r1.object_id
            AND r2.subject_id != %1\$d
	-- get the ID of the post entity in common between the object and the subject 2. 
    INNER JOIN {$wpdb->posts} p2
        ON p2.ID = r2.object_id
            AND p2.post_status = 'publish'
    INNER JOIN {$wpdb->posts} p
        ON p.ID = r2.subject_id
            AND p.post_status = 'publish'        
    INNER JOIN {$wpdb->term_relationships} tr
     	ON tr.object_id = p.ID
    INNER JOIN {$wpdb->term_taxonomy} tt
     	ON tt.term_taxonomy_id = tr.term_taxonomy_id
      	    AND tt.taxonomy = 'wl_entity_type'
    INNER JOIN {$wpdb->terms} t
        ON t.term_id = tt.term_id
            AND t.slug = 'product'
    -- select only posts with featured images.
    INNER JOIN {$wpdb->postmeta} m
        ON m.post_id = p.ID
            AND m.meta_key = '_thumbnail_id'
    -- select only instock products
    INNER JOIN {$wpdb->postmeta} m2
        ON m2.post_id = p.ID
            AND (m2.meta_key = '_stock_status' AND m2.meta_value = 'instock')       
 WHERE r1.subject_id = %1\$d
 -- avoid duplicates.
 GROUP BY p.ID
 ORDER BY %5\$s
 LIMIT %2\$d
 OFFSET %3\$d
",
				$post_id,
				$limit,
				$offset,
				$select,
				$order_by
			)
		);
	}
// phpcs:enable
}
