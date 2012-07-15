<?php

class PlugInService {
    
    const REQUIRES = "requires";
    const VERSION = "version";
    
    const LOAD = "load";

    const COMPATIBLE_VERSION = -1;
    const NOT_COMPATIBLE_VERSION = 0;
    const NO_VERSION = 1;
    
    const WP_ACTION_INIT = "init";
    const WP_ADMIN_NOTICES = "admin_notices";
    
    public static function load($className) {        
        add_action(
            self::WP_ACTION_INIT,
            create_function('$arguments', __CLASS__ . "::loadCallback('$className', \$arguments);")
        );
    }
    
    public static function loadCallback($className, $arguments) {
        $xRayClass = XRayService::scan($className);
        $parameters = &$xRayClass[$className][XRayService::DESCRIPTORS];
        
        foreach ($parameters as $parameter) {
            if (self::REQUIRES === $parameter[XRayService::KEY]) {

                $requiredClass = $parameter[XRayService::VALUE];
                $requiredClass = explode(" ", $requiredClass);
                $requiredVersion = $version = $requiredClass[1];
                $class = $requiredClass[0];

                if (false === class_exists($class)) {
                    echo "The class $className does not exist.";
                    return;
                }
                
                // $version will receive the actual plug-in version.
                switch (self::satisfiesVersion($class, $version)) {
                    case self::NO_VERSION:
                        self::showMessage("The plug-in $class does not export a version. Can't load $className.");
                        return;
                        break;
                    
                    case self::COMPATIBLE_VERSION:
                        // echo "The class $class is compatible.";
                        break;
                        
                    case self::NOT_COMPATIBLE_VERSION:
                        self::showMessage("The class $class $version is not compatible ($requiredVersion). Can't load $className.");
                        return;
                        break;
                }
            }
        }
        
        // load ajax services from the plug-in.
        AjaxService::load($xRayClass);
        
        if (null !== $xRayClass[$className][XRayService::METHODS][self::LOAD]) {
            call_user_func(
                array($className, self::LOAD),
                null
            );
        }
    }
    
    
    private static function satisfiesVersion($className, &$classVersion = null) {
        
        $xRayClass = XRayService::scan($className);
        $descriptors = &$xRayClass[$className][XRayService::DESCRIPTORS];
        
        foreach ($descriptors as $descriptor) {
            if (self::VERSION === $descriptor[XRayService::KEY]) {
                $requiredVersion = $classVersion;
                $classVersion = $descriptor[XRayService::VALUE];
                if (0 === preg_match("/$requiredVersion/", $classVersion))
                    return self::NOT_COMPATIBLE_VERSION;
                else
                    return self::COMPATIBLE_VERSION;
            }
        }
        
        return self::NO_VERSION;
    }
    
    private static function showMessage($message, $severity = "error") {
        add_action(self::WP_ADMIN_NOTICES, create_function('$severity',
<<<EOD
            echo "<div id=\"message\" class=\"$severity\"><p><strong>$message</strong></p></div>";
EOD
        ));
    }
}

?>