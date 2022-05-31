<?php

namespace Wordlift\Modules\Plugin_Installer;

use mysql_xdevapi\Exception;

interface Plugin {

	function get_slug();

	/**
	 * @throws Exception
	 * @return string
	 */
	function get_zip_url();

}