<?php

namespace Wordlift\Entity\Remote_Entity;

interface Remote_Entity {

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @return string
	 */
	function getDescription();

	/**
	 * @return array<string>
	 */
	function getSameAs();

	/**
	 * @return array<string>
	 */
	function getTypes();

}