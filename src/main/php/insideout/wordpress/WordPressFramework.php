<?php

if (false === class_exists("WordPressFramework", true)) {

    // the autoload code is deliberately taken from the log4php project.
    if (function_exists('__autoload')) {
    	trigger_error("WordPressFramework: It looks like your code is using an __autoload() function. WordPressFramework uses spl_autoload_register() which will bypass your __autoload() function and may break autoloading.", E_USER_WARNING);
    }

    /**
     * @version 0.1
     */
    class WordPressFramework {

        const POST_TYPE = "post_type";
        const POST_TITLE = "post_title";
        const POST_NAME = "post_name";

    	// the list of classes part of this framework, for autoloading.
    	private static $_classes = array(
    			'CategoryService' => '/services/CategoryService.php',
    			'PostService' => '/services/PostService.php',
    			'AjaxService' => '/services/AjaxService.php',
    			'JsonService' => '/services/JsonService.php',
    			'XRayService' => '/services/XRayService.php',
    			'PlugInService' => '/services/PlugInService.php',
    			'OptionsPageService' => '/services/OptionsPageService.php',
    			'FormService' => '/services/FormService.php',
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

    	public static function loadWordPress($wpload = 'wp-load.php') {
    		ob_start(); // avoid wp-load printing empty characters.
    		require_once($wpload);
    		ob_end_clean();
    	}

    	/**
    	 * Returns the real directory of a plugin
    	 * @param string $guid A unique identified for the plugin, must match a filename in the plugin folder.
    	 * @param string $wpPluginDir The root folder for the plugins. If not provided will default to WP_PLUGIN_DIR
    	 * @return string The real-path to the plugin directory.
    	 */
    	public static function getPluginDir($guid, $wpPluginDir = WP_PLUGIN_DIR) {
    		if (false === is_dir($wpPluginDir))
    			return null;
    		if (false === ($handle = opendir($wpPluginDir)))
    			return null;

    		$fullpath = null;
	
    		while (false !== ($entry = readdir($handle))) {
    			$fullpath = $wpPluginDir . DIRECTORY_SEPARATOR . $entry . DIRECTORY_SEPARATOR;
    			if (true === is_dir($fullpath) && true == file_exists($fullpath . $guid)) {
    				break;
    			}
    		}		
    		closedir($handle);
	
    		return $fullpath;
    	}

    }

    spl_autoload_register(array('WordPressFramework', 'autoload'));

}
?>
