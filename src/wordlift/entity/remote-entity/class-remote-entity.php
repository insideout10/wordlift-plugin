<?php

namespace Wordlift\Entity\Remote_Entity;

interface Remote_Entity {

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @return string
	 */
	public function get_description();

	/**
	 * @return array<string>
	 */
	public function get_same_as();

	/**
	 * @return array<string>
	 */
	public function get_types();

}
