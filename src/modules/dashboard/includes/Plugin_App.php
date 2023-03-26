<?php

namespace Wordlift\Modules\Dashboard;

use Wordlift\Modules\Common\Date_Utils;
use Wordlift\Modules\Dashboard\Stats\Stats;
use Wordlift\Modules\Dashboard\Synchronization\Synchronization_Service;

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
		$this->stats                   = $stats;
		$this->synchronization_service = $synchronization_service;
	}

	public function register_hooks() {
		add_action( '_wl_dashboard__main', array( $this, 'dashboard__main' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function dashboard__main() {
		$iframe_src = esc_url( plugin_dir_url( __DIR__ ) . 'app/iframe.html' );

		$params = wp_json_encode(
			array(
				'synchronization' => array(
					'state'     => 'idle',
					'last_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_last_sync() ),
					'next_sync' => Date_Utils::to_iso_string( $this->synchronization_service->get_next_sync() ),
				),
				'liftedItems'     => $this->get_lifted_items(),
				// @see https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/
				'api_nonce'       => wp_create_nonce( 'wp_rest' ),
				'api_base'        => untrailingslashit( rest_url() ),
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

	public function admin_enqueue_scripts() {
		// Required to support notices that close themselves (like the `WooCommerce needs to be installed ...` message).
		wp_enqueue_script( 'wp-util' );
	}

	private function get_lifted_items() {

		$ingredient_stats = $this->stats->taxonomy( 'wprm_ingredient' );
		$recipe_stats     = $this->stats->post_type( 'wprm_recipe' );
		$last_sync        = $this->synchronization_service->load();
		$updated_at       = null;
		if ( $last_sync && $last_sync->get_stopped_at() ) {
			$updated_at = $last_sync->get_stopped_at()->format( 'l, M j, Y' );
		}

		return array(
			array(
				'description'   => __( 'Boosted Ingredient are the ones Wordlift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
				'title'         => __( 'Lifted Ingredients', 'wordlift' ),
				'label'         => __( 'Ingredients', 'wordlift' ),
				'total'         => (int) $ingredient_stats['total'],
				'color'         => '#0076f6',
				'show_all_link' => '../ingredients',
				// @TODO should this be the concern of plugin to route ?
				'lifted'        => (int) $ingredient_stats['lifted'],
				'updated_at'    => $updated_at,
			),
			array(
				'description'   => __( 'Boosted Recipes are the ones Wordlift matched with KG. Some Explanation how it helps them.', 'wordlift' ),
				'title'         => __( 'Lifted Recipes', 'wordlift' ),
				'label'         => __( 'Recipes', 'wordlift' ),
				'color'         => '#00c48c',
				'show_all_link' => '../recipes',
				// @TODO should this be the concern of plugin to route ?
				'total'         => (int) $recipe_stats['total'],
				'lifted'        => (int) $recipe_stats['lifted'],
				'updated_at'    => $updated_at,
			),
		);
	}

}
