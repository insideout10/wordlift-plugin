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

		return wp_get_post_terms( $post_id, $this->taxonomy, array(
			'hide_empty' => false,
			// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
			// An issue has been opened with the AAM plugin author as well.
			//
			// see https://github.com/insideout10/wordlift-plugin/issues/334
			// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
			'fields'     => 'all',
		) );
	}

}
