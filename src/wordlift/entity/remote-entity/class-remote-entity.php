<?php

namespace Wordlift\Entity\Remote_Entity;

interface Remote_Entity {

	/**
	 * @return string
	 */
	function get_name();

	/**
	 * @return string
	 */
	function get_description();

	/**
	 * @return array<string>
	 */
	function get_same_as();

	/**
	 * @return array<string>
	 */
	function get_types();

}