<?php

namespace Wordlift\Entity;

interface Entity {

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