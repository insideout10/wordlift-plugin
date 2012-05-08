<?php

// the autoload code is deliberately taken from the log4php project.
if (function_exists('__autoload')) {
	trigger_error("WordLiftPlugin: It looks like your code is using an __autoload() function. WordLiftPlugin uses spl_autoload_register() which will bypass your __autoload() function and may break autoloading.", E_USER_WARNING);
}

spl_autoload_register(array('WordLiftPlugin', 'autoload'));

/**
 * @version 2.0.0
 * @requires WordPressFramework [01].
 */
class WordLiftPlugin {
    
    const POST_TYPE = "io-wordlift-entity";

    const FIELD_PREFIX = "io-wordlift-";
    const SCHEMA_TYPE = "io-wordlift-schema-type";

    const ACCEPTED_POSTS = "_io-wordlift-posts-accepted";

	// the list of classes part of this framework, for autoloading.
	private static $_classes = array(
	);

	/**
	 * Class autoloader. This method is provided to be invoked within an
	 * __autoload() magic method.
	 * @param string $className The name of the class to load.
	 */
	public static function autoload($className) {
		if(isset(self::$_classes[$className])) {
			include dirname(__FILE__) . self::$_classes[$className];
		}
	}

}

?>