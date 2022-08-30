<?php

namespace Wordlift\Modules\Acf4so;

interface Plugin {

	function get_slug();

	function get_name();

	/**
	 * @return string
	 * @throws xception when an error occurs.
	 */
	function get_zip_url();

	function is_plugin_installed();

	function is_plugin_activated();

}
