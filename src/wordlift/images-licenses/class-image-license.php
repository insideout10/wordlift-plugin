<?php

namespace Wordlift\Images_Licenses;

use JsonSerializable;

/**
 * Class Image_License
 *
 * WordPress 4.4+ provides a {@link JsonSerializable} compatibility SHIM for PHP < 5.4.
 *
 * @package Wordlift\Images_Licenses
 */
class Image_License implements JsonSerializable {

	private $data;
	private $posts_ids_as_featured_image;
	private $posts_ids_as_embed;
	private $more_info_link;

	public function __construct( $data, $more_info_link, $posts_ids_as_featured_image = array(), $posts_ids_as_embed = array() ) {
		$this->data                        = $data;
		$this->posts_ids_as_featured_image = $posts_ids_as_featured_image;
		$this->posts_ids_as_embed          = $posts_ids_as_embed;
		$this->more_info_link              = $more_info_link;
	}

	public function get_url() {

		return $this->data['url'];
	}

	public function get_filename() {

		return $this->data['filename'];
	}

	public function get_license() {

		return $this->data['license'];
	}

	public function get_license_family() {

		return $this->data['licenseFamily'];
	}

	public function get_author() {

		return $this->data['author'];
	}

	public function get_more_info_link() {

		return $this->more_info_link;
	}

	public function set_more_info_link( $value ) {

		$this->more_info_link = $value;

	}

	public function get_posts_ids_as_featured_image() {

		return $this->posts_ids_as_featured_image;
	}

	public function get_posts_ids_as_embed() {

		return $this->posts_ids_as_embed;
	}

	public function set_posts_ids_as_featured_image( $posts_ids ) {

		$this->posts_ids_as_featured_image = $posts_ids;

	}

	public function set_posts_ids_as_embed( $posts_ids ) {

		$this->posts_ids_as_embed = $posts_ids;

	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize() {

		return array(
			'filename'                    => $this->get_filename(),
			'author'                      => $this->get_author(),
			'license'                     => $this->get_license(),
			'license_family'              => $this->get_license_family(),
			'more_info_link'              => $this->get_more_info_link(),
			'posts_ids_as_embed'          => $this->get_posts_ids_as_embed(),
			'posts_ids_as_featured_image' => $this->get_posts_ids_as_featured_image(),
			'url'                         => $this->get_url(),
		);
	}

}