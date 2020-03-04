<?php

namespace Wordlift\Images_Licenses;

class Commons_Image {

	private $data;
	/**
	 * @var array
	 */
	private $posts_as_featured_image;
	/**
	 * @var array
	 */
	private $posts_as_embed;

	public function __construct( $data, $posts_as_featured_image = array(), $posts_as_embed = array() ) {
		$this->data                    = $data;
		$this->posts_as_featured_image = $posts_as_featured_image;
		$this->posts_as_embed          = $posts_as_embed;
	}

	public function get_url() {

		return $this->data['url'];
	}

	public function get_filename() {

		return 'blogger-full-color-copy.png';
//		return $this->data['filename'];
	}

	public function get_license() {

		return $this->data['license'];
	}

	public function get_license_family() {

		return $this->data['license_family'];
	}

	public function get_author() {

		return $this->data['author'];
	}

	public function get_commons_link() {

		return sprintf( 'https://commons.wikimedia.org/wiki/File:%s', rawurlencode( $this->get_filename() ) );
	}

	public function get_posts_as_featured_image() {
	}

	public function get_posts_as_embed() {
	}

}