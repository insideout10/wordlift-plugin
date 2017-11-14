<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14.11.17
 * Time: 11:47
 */

interface Wordlift_Cache_Service {

	function get_cache( $id );

	function set_cache( $id, $contents );

}
