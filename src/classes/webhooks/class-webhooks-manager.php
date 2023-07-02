<?php
/**
 * @since 3.34.0
 *
 * Class to call up webhooks and send them requested message
 */

namespace Wordlift\Webhooks;

use Wordlift\Dataset\Sync_Object_Adapter;

class Webhooks_Manager {

	/**
	 * Registering the actions to call up sync_many or sync_delete methods
	 */
	public function __construct() {
		add_action( 'wl_sync__sync_many', array( $this, 'sync_many' ), 10 );
		add_action( 'wl_sync__delete_one', array( $this, 'sync_delete' ), 10, 3 );
	}

	/**
	 * Method to call up webhook with post requested
	 *
	 * @param array $hashes
	 */
	public function sync_many( $hashes ) {

		$urls = explode( "\n", get_option( Webhooks_Loader::URLS_OPTION_NAME, '' ) );
		if ( empty( $urls ) ) {
			return;
		}

		/**
		 * Allow 3rd parties to filter out the objects that we want to send via webhooks.
		 *
		 * @since 3.34.0
		 * @var Sync_Object_Adapter[] $filtered_objects
		 */
		$filtered_hashes = apply_filters( 'wl_webhooks__sync_many__objects', $hashes );

		foreach ( $urls as $url ) {
			foreach ( $filtered_hashes as $hash ) {
				$jsonld       = $hash[2];
				$filtered_url = apply_filters( 'wl_webhooks__sync_many__url', $url, $hash );
				wp_remote_request(
					$filtered_url,
					apply_filters(
						'wl_webhooks__sync_many__args',
						array(
							'blocking' => false,
							'method'   => 'PUT',
							'headers'  => array( 'content-type' => 'application/json; ' . get_bloginfo( 'charset' ) ),
							'body'     => $jsonld,
						),
						$hash
					)
				);
			}
		}
	}

	/**
	 * Method to call up webhook with delete requested
	 *
	 * @param string $type
	 * @param int    $object_id
	 * @param string $uri
	 */
	public function sync_delete( $type, $object_id, $uri ) {

		$urls = explode( "\n", get_option( Webhooks_Loader::URLS_OPTION_NAME, '' ) );
		if ( empty( $urls ) ) {
			return;
		}

		if ( ! apply_filters( 'wl_webhooks__sync_delete', true, $type, $object_id, $uri ) ) {
			return;
		}

		foreach ( $urls as $template_url ) {
			$url          = add_query_arg( array( 'uri' => $uri ), $template_url );
			$filtered_url = apply_filters( 'wl_webhooks__sync_delete__url', $url, $type, $object_id, $uri );
			wp_remote_request(
				$filtered_url,
				apply_filters(
					'wl_webhooks__sync_delete__args',
					array(
						'blocking' => false,
						'method'   => 'DELETE',
					)
				)
			);
		}

	}

}
