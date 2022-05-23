<?php

namespace Wordlift\Modules\Food_Kg;

use Wordlift\Api\Api_Service_Ext;
use Wordlift\Modules\Food_Kg_Dependencies\Symfony\Component\Config\Definition\Exception\Exception;

class Module {

	const RUN_EVENT = 'wl_food_kg__run';

	/**
	 * @var Api_Service_Ext
	 */
	private $api_service;

	public function __construct( Api_Service_Ext $api_service ) {
		$this->api_service = $api_service;
	}

	public function register_hooks() {
		add_action( 'wl_key_updated', [ $this, 'key_updated' ] );
		add_action( 'wl_food_kg__run', [ $this, 'run' ] );
	}

	public function key_updated() {
		try {
			$me_response    = $this->api_service->me();
			$has_food_kg    = array_reduce( $me_response->networks, [ $this, '__has_food_kg' ], false );
			$next_scheduled = wp_next_scheduled( self::RUN_EVENT );

			// We're connected to the Food KG, but not yet scheduled.
			if ( $has_food_kg && ! $next_scheduled ) {
				wp_schedule_event( time(), 'daily', self::RUN_EVENT );
			}

			// We're not connected to the Food KG, but we're scheduled.
			if ( ! $has_food_kg && $next_scheduled ) {
				wp_unschedule_event( $next_scheduled, self::RUN_EVENT );
			}

		} catch ( Exception $e ) {

		}
	}

	public function run() {

	}

	private function __has_food_kg( $carry, $item ) {
		return $carry || 'https://knowledge.cafemedia.com/food/' === $item->dataset_uri;
	}

}
