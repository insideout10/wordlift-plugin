<?php

/**
 * Any schema must implement this interface.
 */
interface ISchema {
	
	/**
	 * Provides a friendly name for this schema.
	 */
	public static function getFriendlyName();
	
}

?>