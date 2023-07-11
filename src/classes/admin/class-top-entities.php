<?php

namespace Wordlift\Admin;

/**
 * @since 3.27.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Top_Entities {

	const CRON_ACTION = 'wl_admin_dashboard_top_entities';

	/**
	 * Option key where the top entities data is stored.
	 */
	const OPTION_KEY = 'wl_admin_dashboard_top_entities_option';

	public function __construct() {
		add_action( self::CRON_ACTION, array( $this, 'save_top_entities' ) );
	}

	public function save_top_entities() {

		global $wpdb;

		$results = $wpdb->get_results(
			"
			SELECT p_as_object.ID
			    , p_as_object.post_title
			    , COALESCE(SUM(CASE WHEN t_as_subject.slug IS NULL THEN 1 END), 0)     entities
			    , COALESCE(SUM(CASE WHEN t_as_subject.slug IS NOT NULL THEN 1 END), 0) posts
			    , COUNT(1) AS total
			FROM {$wpdb->prefix}wl_relation_instances ri
			INNER JOIN {$wpdb->prefix}posts p_as_object
			    ON p_as_object.ID = ri.object_id
			        AND p_as_object.post_status = 'publish'
			INNER JOIN {$wpdb->prefix}posts p_as_subject
			    ON p_as_subject.ID = ri.subject_id
			        AND p_as_subject.post_status = 'publish'
			INNER JOIN {$wpdb->prefix}term_relationships tr_as_subject
				ON tr_as_subject.object_id = p_as_subject.ID
			INNER JOIN {$wpdb->prefix}term_taxonomy tt_as_subject
			    ON tt_as_subject.term_id = tr_as_subject.term_taxonomy_id
			        AND tt_as_subject.taxonomy = 'wl_entity_type'
			LEFT OUTER JOIN {$wpdb->prefix}terms t_as_subject
			    ON t_as_subject.term_id = tt_as_subject.term_id
			        AND 'article' = t_as_subject.slug
			GROUP BY p_as_object.ID, p_as_object.post_title
			ORDER BY total DESC
			LIMIT 20
		"
		);

		update_option( self::OPTION_KEY, $results );
	}

	public static function activate() {
		if ( ! wp_next_scheduled( self::CRON_ACTION ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_ACTION );
		}
	}

	public static function deactivate() {
		$timestamp = wp_next_scheduled( self::CRON_ACTION );
		wp_unschedule_event( $timestamp, self::CRON_ACTION );
	}

}
