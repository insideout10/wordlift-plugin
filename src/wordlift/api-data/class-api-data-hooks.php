<?php

/**
 *
 * API Data Hooks
 * @author Navdeep Singh <navdeep.er@gmail.com>
 * @package Wordlift\Api_Data
 */

namespace Wordlift\Api_Data;

class Api_Data_Hooks
{
  const API_URL = 'https://api.wordlift.io/data/';

  const META_KEY = 'wl_schema_url';

  public function __construct()
  {
    /**
     * Hook for Post Save
     */
    add_action( 'save_post', array( $this, 'post_save_request_delete_cache' ));
    /**
     * Check for Meta Key change on Post Save
     */
    add_action( 'updated_post_meta', array( $this, 'post_meta_request_delete_cache', 10, 4 ));
  }

  public function post_save_request_delete_cache( $post_id )
  {
    return $this->get_values( $post_id );
  }

  public function post_meta_request_delete_cache( $meta_id, $post_id, $meta_key, $_meta_value )
  {
    if ( self::META_KEY === $meta_key ) {
      return $this->get_values( $post_id );
    }
  }

  /**
   * @param integer $post_id
   * @return
   *
   */
  private function get_values( $post_id )
  {
    /**
     * Get Post Meta Values
     */
    $values = get_post_meta( $post_id, self::META_KEY, false );

    /**
     * Iterate over $values array
     */
    if ( !empty( $values ) && count( $values ) > 1 ) {
      foreach ( $values as $key => $link ) {
        /**
         * Skip the <permalink>
         */
        if ( $key === '<permalink>' ) {
          $link = get_permalink();
        }
        /**
         * Sanitize the link
         */
        $link = $this->sanitize( $link );
        /**
         * Make actual API DELETE cache request
         */
        return $this->api_call_delete_cache( $link );
      }
    }
  }

  /**
   * @desc Sanitize the $link
   * @param string $link
   * @return string
   */
  private function sanitize( $link ) {
    return preg_replace( '/:\//i', '/', $link );
  }

  /**
   * @desc Do a DELETE request with cURL
   * @param string  $path path that goes after the URL eg. "/user/login"
   *
   * @return Obj  $result HTTP response from REST interface in JSON decoded
   */
  private function api_call_delete_cache( $path )
  {
    $url = self::API_URL . $path;
    $port = 443;

    /**
     * Make a non-blocking API call
     */
    $fp = fsockopen( $url, $port, $errno, $errstr, $timeout = 30 );
    if ( !$fp ) {
      return false;
    } else {

      /**
       * send the server request
       */
      fputs( $fp, "DELETE $path HTTP/1.1\r\n" );
      fputs( $fp, "Host: self::API_URL\r\n" );
      fputs( $fp, "Content-type: application/x-www-form-urlencoded\r\n" );
      fputs( $fp, "Connection: close\r\n\r\n" );

      //loop through the response from the server
      while ( !feof( $fp ) ) {
        echo fgets( $fp, 4096 );
      }
      //close fp - we are done with it
      fclose( $fp );
      return true;
    }

  }
}
