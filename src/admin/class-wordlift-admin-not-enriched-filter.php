<?php

class Wordlift_Admin_Not_Enriched_Filter {

	const PARAMETER_NAME = 'wl_enriched';

	public function __construct() {

		// Hook to `views_edit-post`.
		add_filter( 'views_edit-post', array( $this, 'view_edit' ) );

		// Add the `posts_where` filter if the filter is active.
		if ( $this->is_filter_active() ) {
			add_filter( 'posts_where', array( $this, 'posts_where' ) );
		}

	}

	public function posts_where( $where ) {
		global $wpdb;

		return $where .
			   " AND {$wpdb->posts}.ID NOT IN ( SELECT DISTINCT subject_id FROM {$wpdb->prefix}wl_relation_instances )";
	}

	public function view_edit( $views ) {
		global $wpdb;

		$url = add_query_arg(
			array(
				self::PARAMETER_NAME => 'no',
				'post_type'          => 'post',
			),
			'edit.php'
		);

		$not_enriched_count = $wpdb->get_var(
			"
SELECT COUNT( 1 ) FROM $wpdb->posts p
 WHERE p.post_type = 'post'
   AND p.post_status <> 'trash' 
   AND p.post_status <> 'auto-draft'
   AND p.ID NOT IN ( SELECT DISTINCT subject_id FROM {$wpdb->prefix}wl_relation_instances )
"
		);

		$link = '<a href="'
				. esc_url( $url ) . '"'
				. ( $this->is_filter_active() ? ' class="current" aria-current="page"' : '' )
				. '>' . esc_html( __( 'Not enriched', 'wordlift' ) ) . '</a> (' . $not_enriched_count . ')';

		$views['wl_not_enriched'] = $link;

		return $views;
	}

	public function is_filter_active() {
		return 'no' === filter_input( INPUT_GET, self::PARAMETER_NAME );
	}

}
