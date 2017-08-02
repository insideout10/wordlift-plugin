<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:10
 */

class Wordlift_Post_Meta_Storage extends Wordlift_Storage {
	/**
	 * @var
	 */
	private $meta_key;

	/**
	 * @param $meta_key
	 */
	public function __construct( $meta_key ) {

		$this->meta_key = $meta_key;

	}

	public function get( $post_id ) {

		return get_post_meta( $post_id, $this->meta_key );
	}

}
