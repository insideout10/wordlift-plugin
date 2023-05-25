<?php

namespace Wordlift\Content;

interface Content {

	/**
	 * Get the actual content.
	 *
	 * @return mixed Get the actual content.
	 */
	public function get_bag();

	/**
	 * Get the content id.
	 *
	 * @return mixed
	 */
	public function get_id();

	/**
	 * Get the content type.
	 *
	 * @return mixed
	 */
	public function get_object_type_enum();

	/**
	 * Get the permalink.
	 *
	 * @return string
	 */
	public function get_permalink();

	/**
	 * Get the edit link.
	 *
	 * @return string
	 */
	public function get_edit_link();

}
