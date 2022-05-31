<?php

namespace Wordlift\Modules\Plugin_Installer;


interface Plugin {

	function get_slug();

	/**
	 * @throws \Exception
	 * @return string
	 */
	function get_zip_url();

}