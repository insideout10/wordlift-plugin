<?php
/**
 * @since 3.31.0
 * @author
 * Class to call up webhooks and send them requested message
 */

namespace Wordlift\Webhooks\Api;

class Rest_Controller {

    /**
     * Registering the actions to call up sync_many or sync_delete methods
     */

    public function __construct() {
	    add_action( 'wl_sync__sync_many', array( $this, 'register_sync_many' ), 10, 1 );
	    add_action( 'wl_sync__delete_one', array( $this, 'register_sync_delete' ), 10, 3 );
    }

    /**
     * Method to call up webhook with post requested
     * @param array $payloads
     * @param array $object
     * @return json
     */

	public function register_sync_many( $payloads ) {

        $webhook_urls = get_option('wl_webhook_url');
         if( empty( $webhook_urls ) ) {
            return;
         }

        foreach( $webhook_urls as $webhook_url ) {

            // Check if the $payloads is an array of payload.
            // If true then send each en payload separately
            if( is_array( $payloads ) ) {

                foreach( $payloads as $payload ) {
                    $result = wp_remote_request( $webhook_url,
                        array(
                            'method'    => 'POST',
                            'body'      => json_encode( $payload )
                        )
                    );
                }
                return $result;
            }
            else {
                return wp_remote_request( $webhook_url,
                    array(
                        'method'    => 'POST',
                        'body'      => json_encode( $payloads )
                    )
                );
            }
        }

	}

    /**
     * Method to call up webhook with delete requested
     * @param string $type
     * @param int $object_id
     * @param string $uri
     * @return json
     */

	public function register_sync_delete( $type, $object_id, $uri ) {

         $webhook_urls = get_option('wl_webhook_url');
         if( empty( $webhook_urls ) ) {
            return;
         }

        foreach( $webhook_urls as $webhook_url ) {

            $data = array(
                'type'          => $type,
                'object_id'     => $object_id,
                'uri'           => $uri
            );
            $encoded_data = json_encode( $data );

            $result = wp_remote_request( $webhook_url,
                array(
                    'method'    => 'DELETE',
                    'body'      => $encoded_data
                )
            );

            return $result;
        }
    }
}

