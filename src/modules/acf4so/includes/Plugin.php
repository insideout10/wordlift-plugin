<?php

namespace Wordlift\Modules\Plugin_Installer;


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