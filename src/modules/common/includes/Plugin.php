<?php

namespace Wordlift\Modules\Common;

interface Plugin {

	public function get_slug();

	public function get_name();

	/**
	 * @return string
	 * @throws \Exception when an error occurs.
	 */
	public function get_zip_url();

	public function is_plugin_installed();

	public function is_plugin_activated();

}
