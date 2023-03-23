<?php

namespace Wordlift\Modules\Dashboard;

use Wordlift\Modules\Dashboard\Stats\Stats;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;
use Wordlift\Object_Type_Enum;

class Plugin_App {
	/**
	 * @var Synchronization_Service
	 */
	private $synchronization_service;
	/**
	 * @var Stats
	 */
	private $stats;

	/**
	 * @param $stats Stats
	 * @param $synchronization_service Synchronization_Service
	 */
	public function __construct( $stats, $synchronization_service ) {
		$this->stats = $stats;
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_action( '_wl_dashboard__main', array( $this, 'dashboard__main' ) );
	}

	public function dashboard__main() {
		$iframe_src = esc_url( plugin_dir_url( __DIR__ ) . 'app/index.html' );
		$params     = wp_json_encode(
			array(
				'synchronization' => array(
					'state'     => 'idle',
					'last_sync' => date_create( '2022-01-31 23:45:23' )->getTimestamp(),
					'next_sync' => date_create( '2024-01-31 23:45:23' )->getTimestamp(),
				),
				'api_url'         => rest_url( '/wl-dashboard/v1' ),
				'liftedItems'     => $this->get_lifted_items(),
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "
			<style>
			    #wlx-plugin-app {
			      margin-left: -20px;
			      width: calc(100% + 20px);
			      min-height: 1500px;
			    }
		    </style>
			<script type=\"text/javascript\">window._wlPluginAppSettings = $params</script>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
		";
	}

	private function get_lifted_items() {

		$ingredient_stats = $this->stats->taxonomy( 'wprm_ingredient' );
		$recipe_stats     = $this->stats->post_type( 'wprm_recipe' );
		$last_sync =  $this->synchronization_service->load();
		$updated_at = null;
		if ( $last_sync ) {
			$updated_at = $last_sync->get_stopped_at()->format('l, M j, Y');
		}


		return array(
			array(
				'description'   => __( 'Boosted Ingredient are the ones Wordlift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
				'title'         => __( 'Lifted Ingredients', 'wordlift' ),
				'total'         => $ingredient_stats['total'],
				'color'         => '#0076f6',
				'show_all_link' => '../ingredients', // @TODO should this be the concern of plugin to route ?
				'lifted'        => $ingredient_stats['lifted'],
				'updated_at'    => $updated_at,
			),
			array(
				'description'   => __( 'Boosted Recipes are the ones Wordlift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
				'title'         => __( 'Lifted Recipes', 'wordlift' ),
				'color'         => '#00c48c',
				'show_all_link' => '../recipes', // @TODO should this be the concern of plugin to route ?
				'total'         => $recipe_stats['total'],
				'lifted'        => $recipe_stats['lifted'],
				'updated_at'    => $updated_at,
			),
		);
	}

	private function get_recipe_data( $taxonomy ) {
		global $wpdb;
		$sql = "SELECT count(1) as total, count(e.about_jsonld) as lifted FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
                  WHERE e.content_type = %d AND tt.taxonomy = %s";

		return array(
			'total'  => '',
			'lifted' => '',
		);

	}


	private function get_taxonomy_data( $taxonomy ) {

	}

}
