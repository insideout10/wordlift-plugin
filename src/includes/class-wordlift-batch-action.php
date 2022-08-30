<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24.09.18
 * Time: 16:57
 */

class Wordlift_Batch_Action {

	public static function process( $post_type, $offset, $query, $callback ) {

		$posts_per_page = 10;

		$args = array_merge(
			self::get_args( $post_type, $query ),
			array(
				'offset'         => $offset,
				'posts_per_page' => $posts_per_page,
			)
		);

		$post_ids = get_posts( $args );

		foreach ( $post_ids as $post_id ) {
			call_user_func( $callback, $post_id );
		}

		return array(
			'current' => $offset,
			'next'    => $offset + $posts_per_page,
			'count'   => self::count( $post_type, $query ),
		);
	}

	public static function count( $post_type, $query ) {
		$args = array_merge(
			self::get_args( $post_type, $query ),
			array(
				'posts_per_page' => - 1,
			)
		);

		return count( get_posts( $args ) );
	}

	private static function get_args( $post_type, $query ) {

		return array_merge(
			array(
				'fields'        => 'ids',
				'post_type'     => $post_type,
				'post_status'   => array(
					'publish',
					'future',
					'draft',
					'pending',
					'private',
					'auto-draft',
					'inherit',
				),
				'cache_results' => false,
			),
			$query
		);
	}

}
