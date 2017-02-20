<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 20/02/2017
 * Time: 17:59
 */
class Wordlift_Publisher_Service {

	public function query( $filter ) {

		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT p.id, p.post_title, t.name AS type, m.meta_value AS thumbnail_id" .
			" FROM wp_posts p" .
			"  LEFT JOIN wp_term_relationships tr" .
			"   ON tr.object_id = p.id" .
			"  LEFT JOIN wp_term_taxonomy tt" .
			"   ON tt.term_taxonomy_id = tr.term_taxonomy_id" .
			"  LEFT JOIN wp_terms t" .
			"   ON t.term_id = tt.term_id" .
			"  LEFT JOIN wp_postmeta m" .
			"   ON m.post_id = p.id AND m.meta_key = '_thumbnail_id'" .
			"  WHERE p.post_type = %s" .
			"   AND t.name IN ( 'Organization', 'Person' )" .
			"   AND tt.taxonomy = %s" .
			"   AND p.post_title LIKE %s" .
			" ORDER BY p.post_title",
			Wordlift_Entity_Service::TYPE_NAME,
			Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME,
			'%' . $wpdb->esc_like( $filter ) . '%'
		) );

		$publisher_service = $this;

		return array_map( function ( $item ) use ( $publisher_service ) {
			return array(
				'id'            => $item->id,
				'text'          => $item->post_title,
				'type'          => $item->type,
				'thumbnail_url' => $publisher_service->get_attachment_image_url( $item->thumbnail_id ),
			);
		}, $results );
	}

	public function get_attachment_image_url( $attachment_id, $size = 'thumbnail' ) {

		$image = wp_get_attachment_image_src( $attachment_id, $size );

		return isset( $image['0'] ) ? $image['0'] : false;
	}

}