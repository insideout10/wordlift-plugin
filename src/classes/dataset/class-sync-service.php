<?php

namespace Wordlift\Dataset;

use Wordlift\Api\Api_Service;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Object_Type_Enum;

class Sync_Service {
	const JSONLD_HASH = 'jsonld_hash';
	const SYNCED_GMT  = 'synced_gmt';

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * @var Sync_Background_Process
	 */
	private $sync_background_process;

	/**
	 * The number of posts processed in one call.
	 *
	 * @var int The batch size.
	 */
	private $batch_size;

	/**
	 * @var Sync_Object_Adapter_Factory
	 */
	private $sync_object_adapter_factory;

	/**
	 * @var Sync_Service
	 */
	private static $instance;
	private $entity_service;

	/**
	 * Constructor.
	 *
	 * @param Api_Service                 $api_service The {@link Api_Service} used to communicate with the remote APIs.
	 * @param Sync_Object_Adapter_Factory $sync_object_adapter_factory
	 * @param Jsonld_Service              $jsonld_service
	 * @param \Wordlift_Entity_Service    $entity_service
	 */
	public function __construct( $api_service, $sync_object_adapter_factory, $jsonld_service, $entity_service ) {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->api_service                 = $api_service;
		$this->sync_object_adapter_factory = $sync_object_adapter_factory;
		$this->jsonld_service              = $jsonld_service;
		$this->entity_service              = $entity_service;
		$this->batch_size                  = 10;

		// You need to initialize this early, otherwise the Background Process isn't registered in AJAX calls.
		// $this->sync_background_process = new Sync_Background_Process( $this );;

		// Exclude the JSONLD_HASH meta key from those that require a resync.
		add_filter(
			'wl_dataset__sync_hooks__ignored_meta_keys',
			function ( $args ) {
				$args[] = Sync_Service::JSONLD_HASH;
				$args[] = Sync_Service::SYNCED_GMT;

				return $args;
			}
		);

		self::$instance = $this;
	}

	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * @param int $type
	 * @param int $object_id
	 *
	 * @return array|false
	 * @throws \Exception when an error occurs.
	 */
	public function sync_one( $type, $object_id ) {

		$object = $this->sync_object_adapter_factory->create( $type, $object_id );

		return $this->sync_many( array( $object ) );
	}

	/**
	 * @param $type string Post or User.
	 * @param $object_id  int Post or User id
	 * @param $uri string Entity uri , This needs to be supplied before deletion, if we
	 * get it from meta it might not be available.
	 *
	 * @return bool
	 */
	public function delete_one( $type, $object_id, $uri ) {
		// Entity URL isn't set, bail out.
		if ( empty( $uri ) ) {
			return false;
		}

		$response = $this->api_service->request(
			'DELETE',
			sprintf( '/middleware/dataset?uri=%s', rawurlencode( $uri ) )
		);

		// Update the sync date in case of success, otherwise log an error.
		if ( ! $response->is_success() ) {
			return false;
		}

		/**
		 * Allow 3rd parties to run additional sync work.
		 */
		do_action( 'wl_sync__delete_one', $type, $object_id, $uri );

		return true;
	}

	/**
	 * @param Sync_Object_Adapter[] $objects
	 * @param bool                  $force Force synchronization even if the json-ld hash hasn't changed.
	 *
	 * @return bool
	 * @throws \Exception when an error occurs.
	 */
	public function sync_many( $objects, $force = false ) {

		$hashes   = array();
		$payloads = array();
		/** @var Sync_Object_Adapter $object */
		foreach ( $objects as $object ) {
			// Bail out if no payload.
			$payload_as_string = $this->get_payload_as_string( $object );
			if ( empty( $payload_as_string ) ) {
				continue;
			}
			$new_hash = sha1( $payload_as_string );
			$old_hash = $object->get_value( self::JSONLD_HASH );

			// JSON-LD hasn't changed, bail out.
			$should_sync = $force || $new_hash !== $old_hash;
			if ( ! apply_filters( 'wl_dataset__sync_service__sync_item', $should_sync, $object, $payload_as_string ) ) {
				continue;
			}

			// Collect the hashes and the payloads.
			$hashes[]   = array( $object, $new_hash, $payload_as_string );
			$payloads[] = $payload_as_string;
		}

		// Bail out if payloads are empty.
		if ( empty( $payloads ) ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$blocking = apply_filters( 'wl_feature__enable__sync-blocking', false );
		$response = $this->api_service->request(
			'POST',
			'/middleware/dataset/batch',
			array( 'Content-Type' => 'application/json' ),
			// Put the payload in a JSON array w/o decoding/encoding again.
			'[ ' . implode( ', ', $payloads ) . ' ]',
			$blocking ? 60 : 0.001,
			null,
			array( 'blocking' => $blocking )
		);

		// Update the sync date in case of success, otherwise log an error.
		if ( $blocking && ! $response->is_success() ) {
			return false;
		}

		// If successful update the hashes and sync datetime.
		foreach ( $hashes as $hash ) {
			$object   = $hash[0];
			$new_hash = $hash[1];

			$object->set_values(
				array(
					self::JSONLD_HASH => $new_hash,
					self::SYNCED_GMT  => current_time( 'mysql', true ),
				)
			);
		}

		/**
		 * Allow 3rd parties to run additional sync work.
		 */
		do_action( 'wl_sync__sync_many', $hashes );

		return true;
	}

	/**
	 * @param Sync_Object_Adapter $object
	 *
	 * @return false|string
	 * @throws \Exception when an error occurs.
	 */
	private function get_payload_as_string( $object ) {
		$type             = $object->get_type();
		$object_id        = $object->get_object_id();
		$jsonld_as_string = wp_json_encode(
			apply_filters(
				'wl_dataset__sync_service__sync_item__jsonld',
				$this->jsonld_service->get( $type, $object_id ),
				$type,
				$object_id
			),
			64
		); // JSON_UNESCAPED_SLASHES
		$uri              = $this->entity_service->get_uri( $object_id, $type );

		// Entity URL isn't set, bail out.
		if ( empty( $uri ) ) {
			return false;
		}

		return wp_json_encode(
			array(
				'uri'     => $uri,
				'model'   => $jsonld_as_string,
				'private' => ! ( $object->is_public() && $object->is_published() ),
			),
			64
		); // JSON_UNESCAPED_SLASHES
	}

	/**
	 * @param $post_id
	 *
	 * @todo Complete the delete item.
	 */
	public function delete_item( $post_id ) {
		$uri = $this->entity_service->get_uri( $post_id, Object_Type_Enum::POST );

		if ( ! isset( $uri ) ) {
			return;
		}

		// Make a request to the remote endpoint.
		$this->api_service->request(
			'DELETE',
			'/middleware/dataset?uri=' . rawurlencode( $uri ),
			array( 'Content-Type' => 'application/ld+json' )
		);

	}

	public function get_batch_size() {

		return $this->batch_size;
	}

	public function delete_all() {
		$this->api_service->request( 'DELETE', '/middleware/dataset/all' );
	}

}
