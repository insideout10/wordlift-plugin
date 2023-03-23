<?php

namespace Wordlift\Modules\Dashboard;

class Plugin_App {

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

		return array(
			array(
				'description'   => 'Boosted Ingredient are the ones Wordlift matched with KG. Some Explanation how it helps them.',
				'title'         => 'Lifted Ingredients',
				'total'         => 120,
				'color'         => '#0076f6',
				'show_all_link' => 'https://foo.com/bar',
				'lifted'        => 70,
				'updated_at'    => 'Wednesday, Feb 22, 2023',
			),
			array(
				'description'   => 'Boosted Recipes are the ones Wordlift matched with KG. Some Explanation how it helps them.',
				'title'         => 'Lifted Recipes',
				'total'         => 70,
				'color'         => '#00c48c',
				'show_all_link' => 'https://foo.com/bar',
				'lifted'        => 25,
				'updated_at'    => 'Wednesday, Feb 22, 2023',
			),
		);
	}

	private function get_taxonomy_data( $taxonomy ) {
		global $wpdb;
		$sql = "SELECT e.content_id as match_id, t.name,  e.id FROM {$wpdb->prefix}wl_entities e
                  LEFT JOIN {$wpdb->prefix}terms t ON e.content_id = t.term_id
                  INNER JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
                  WHERE e.content_type = %d AND tt.taxonomy = %s";

		return array(
			'total'  => '',
			'lifted' => '',
		);

	}

}
