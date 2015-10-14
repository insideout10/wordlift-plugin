<?php

class WordLift_OptionAjaxService
{

	public function get($key, $defaultValue = null)
	{
		return get_option($key, $defaultValue);
	}

	public function set($key, $value)
	{
		return update_option($key, $value);
	}

}
?>