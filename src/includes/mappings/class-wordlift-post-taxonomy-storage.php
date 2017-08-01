<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:10
 */

class Wordlift_Post_Taxonomy_Storage extends Wordlift_Storage {
	/**
	 * @var
	 */
	private $meta_key;
	/**
	 * @var
	 */
	private $taxonomy;

	/**
	 * @param $taxonomy
	 */
	public function __construct( $taxonomy ) {

		$this->taxonomy = $taxonomy;
	}

	public function get( $post_id ) {

		return $post = get_post_meta( $post_id, $this->meta_key );
	}

}
