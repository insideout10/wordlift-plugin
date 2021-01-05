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

		$query = <<<EOF
SELECT p.ID
     , p.post_title
     , coalesce(sum(case when obj_t.slug is null then 1 end), 0)     entities
     , coalesce(sum(case when obj_t.slug is not null then 1 end), 0) posts
     , count(entity.subject_id) AS                                   total
FROM {$wpdb->prefix}wl_relation_instances entity
       INNER JOIN {$wpdb->prefix}posts p
                  ON p.ID = entity.object_id
       INNER JOIN {$wpdb->prefix}term_relationships tr
                  ON tr.object_id = entity.object_id
       INNER JOIN {$wpdb->prefix}term_taxonomy tt
                  ON tt.term_id = tr.term_taxonomy_id
                    AND tt.taxonomy = 'wl_entity_type'
       INNER JOIN {$wpdb->prefix}terms t
                  ON t.term_id = tt.term_id
                    AND 'article' != t.slug
       INNER JOIN {$wpdb->prefix}term_relationships obj_tr
                  ON obj_tr.object_id = entity.subject_id
       INNER JOIN {$wpdb->prefix}term_taxonomy obj_tt
                  ON obj_tt.term_id = obj_tr.term_taxonomy_id
                    AND obj_tt.taxonomy = 'wl_entity_type'
       LEFT OUTER JOIN {$wpdb->prefix}terms obj_t
                       ON obj_t.term_id = obj_tt.term_id
                         AND 'article' = obj_t.slug
GROUP BY p.ID, p.post_title
ORDER BY total DESC
LIMIT 20;
EOF;

		$results = $wpdb->get_results( $query );

		update_option( self::OPTION_KEY, $results );
	}


	public static function activate() {
		if ( ! wp_next_scheduled( self::CRON_ACTION ) ) {
			wp_schedule_event( time(), 'hourly', self::CRON_ACTION );
		}
	}

	public static function deactivate() {
		$timestamp = wp_next_scheduled( self::CRON_ACTION );
		wp_unschedule_event( $timestamp, self::CRON_ACTION );
	}

}