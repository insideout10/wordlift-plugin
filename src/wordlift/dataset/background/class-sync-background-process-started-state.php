<?php

namespace Wordlift\Dataset\Background;

use Wordlift\Common\Background_Process\Action_Scheduler\State;
use Wordlift\Dataset\Background\Stages\Sync_Background_Process_Posts_Stage;
use Wordlift\Dataset\Background\Stages\Sync_Background_Process_Stage;
use Wordlift\Dataset\Background\Stages\Sync_Background_Process_Terms_Stage;
use Wordlift\Dataset\Background\Stages\Sync_Background_Process_Users_Stage;
use Wordlift\Dataset\Sync_Object_Adapter_Factory;
use Wordlift\Dataset\Sync_Service;

class Sync_Background_Process_Started_State extends Abstract_Sync_Background_Process_State {

	/**
	 * @var Sync_Background_Process
	 */
	private $context;

	/**
	 * @var Sync_Service
	 */
	private $sync_service;

	/**
	 * @var Sync_Background_Process_Stage[]
	 */
	private $stages;

	private $batch_size = 5;
	/**
	 * @var bool
	 */
	private $reset;

	/**
	 * Sync_Background_Process_Started_State constructor.
	 *
	 * @param Sync_Background_Process     $context
	 * @param Sync_Service                $sync_service
	 * @param Sync_Object_Adapter_Factory $sync_object_adapter_factory
	 * @param bool                        $reset Whether to reset the counters
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function __construct( $context, $sync_service, $sync_object_adapter_factory, $reset = true ) {
		parent::__construct( Sync_Background_Process::STATE_STARTED );

		$this->context      = $context;
		$this->sync_service = $sync_service;

		$this->stages = array(
			new Sync_Background_Process_Posts_Stage( $sync_object_adapter_factory ),
			new Sync_Background_Process_Terms_Stage( $sync_object_adapter_factory ),
			new Sync_Background_Process_Users_Stage( $sync_object_adapter_factory ),
		);
	}

	public function enter() {
		// Delete the KG contents.
		$this->sync_service->delete_all();

		// Clear caches.
		do_action( 'wl_ttl_cache_cleaner__flush' );

		$counts = array_map(
			function ( $item ) {
				return $item->count();
			},
			$this->stages
		);

		update_option( '_wl_sync_background_process_count', $counts, true );
		update_option( '_wl_sync_background_process_stage', 0, true );
		update_option( '_wl_sync_background_process_offset', 0, true );
		update_option( '_wl_sync_background_process_started', time(), true );
		update_option( '_wl_sync_background_process_updated', time(), true );

		$this->context->set_state( Sync_Background_Process::STATE_STARTED );

		$this->resume();
	}

	public function resume() {
		$this->context->schedule();
	}

	public function leave() {
		$this->context->set_state( null );
	}

	/**
	 * @param $args
	 *
	 * @return State
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function task( $args ) {
		$offset     = get_option( '_wl_sync_background_process_offset' );
		$stage      = get_option( '_wl_sync_background_process_stage' );
		$counts     = get_option( '_wl_sync_background_process_count' );
		$batch_size = min( $counts[ $stage ] - $offset, $this->batch_size );

		add_filter( 'wl_api_service__request', array( $this, 'api_service__request' ) );
		try {
			$object_adapters = $this->stages[ $stage ]->get_sync_object_adapters( $offset, $batch_size );
			$this->sync_service->sync_many( $object_adapters, true );
		// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} catch ( \Exception $e ) {
			// ignored.
		}
		remove_filter( 'wl_api_service__request', array( $this, 'api_service__request' ) );

		update_option( '_wl_sync_background_process_updated', time(), true );

		// Increase the offset.
		if ( ( $offset + $batch_size ) < $counts[ $stage ] ) {
			update_option( '_wl_sync_background_process_offset', $offset + $batch_size, true );

			return State::items_in_queue();
		}

		// Increase the stage.
		if ( ( $stage + 1 ) < count( $this->stages ) ) {
			update_option( '_wl_sync_background_process_stage', $stage + 1, true );
			update_option( '_wl_sync_background_process_offset', 0, true );

			return State::items_in_queue();
		}

		return State::complete();
	}

	/**
	 * Hook to provide a request to update the status on the server.
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function api_service__request( $args ) {

		$state_header_value = str_replace( "\n", '', wp_json_encode( $this->context->get_info() ) );

		$args['headers']['X-Wordlift-Dataset-Sync-State-V1'] = $state_header_value;

		return $args;
	}

}
