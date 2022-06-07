<?php

namespace Wordlift\Modules\Acf4so;


interface Plugin {

	function get_slug();

	/**
	 * @return string
	 * @throws \Exception
	 */
	function get_zip_url();


	function is_plugin_installed();

	function is_plugin_activated();

}