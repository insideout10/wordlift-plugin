<?php
/**
 * @since 3.31.0
 * @author
 * Class to call up webhooks and send them requested message
 */

namespace Wordlift\Webhooks\Api;

use WP_REST_Server;


class Rest_Controller {

    /**
     * Registering the actions to call up sync_many or sync_delete methods
     */

    public function __construct() {
	    add_action( 'wl_sync_many', array( $this, 'register_sync_many' ), 10, 2 );
	    add_action( 'wl_sync_delete', array( $this, 'register_sync_delete' ), 10, 3 );
    }

    /**
     * Method to call up webhook with post requested
     * @param array $payloads
     * @param array $object
     * @return json
     */

	public function register_sync_many( $payloads, $object ) {

        $webhook_urls = get_option('wl_webhook_url');
        if( ! empty( $webhook_urls ) ) {
            foreach( $webhook_urls as $webhook_url ) {
                $handle = curl_init( $webhook_url ); //'https://ensh04p5m7cs4hw.m.pipedream.net';

                $data = [
                    'payload'   => $payloads,
                    'object'    => $object
                ];

                $encodedData = json_encode( $data );

                curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, "POST" );
               // curl_setopt($handle, CURLOPT_POST, 1);
                curl_setopt( $handle, CURLOPT_POSTFIELDS, $encodedData );
                curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen( $encodedData ) )
                );

                $result = curl_exec( $handle );
                return $result;
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

        // Experimentl chnges for 1496 done by Nishit
        $this->write_log( "Inside register_sync_delete callback function of Rest_Controller  Nishit: " );
        // Experimentl chnges ends
        $this->write_log( get_option('wl_webhook_url') );

        $webhook_urls = get_option('wl_webhook_url');
        // Experimentl chnges ends
        if( ! empty( $webhook_urls ) ) {
            foreach( $webhook_urls as $webhook_url ) {
                $handle = curl_init( $webhook_url );

                $data = [
                    'type'          => $type,
                    'object_id'     => $object_id,
                    'uri'           => $uri
                ];

                $encodedData = json_encode( $data );

                curl_setopt( $handle, CURLOPT_CUSTOMREQUEST, "DELETE" );
               // curl_setopt($handle, CURLOPT_POST, 1);
                curl_setopt( $handle, CURLOPT_POSTFIELDS, $encodedData );
                curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen( $encodedData ) )
                );

                $result = curl_exec( $handle );
                return $result;
            }
        }
    }
}

