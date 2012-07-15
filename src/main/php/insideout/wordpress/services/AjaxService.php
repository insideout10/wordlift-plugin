<?php

class AjaxService {
    
    const SERVICE = "service";
    const AUTHENTICATION = "authentication";
    const ACTION = "action";
    const COMPRESSION = "compression";
    const REQUIRE_CAPABILITIES = "requireCapabilities";
    
    const SERVICE_AJAX ="ajax";
    const AUTHENTICATION_REQUIRED = "required";
    
    const CALLBACK_RETURN_ERROR = FALSE;
    const CALLBACK_RETURN_NULL = null;
    
    const WP_AJAX_NOPRIV = "wp_ajax_nopriv_";
    const WP_AJAX = "wp_ajax_";
    
    const REQUEST_BODY = "requestBody";
    
    const PHP_INPUT = "php://input";

    public static function load($xRayClass) {
        // if (class_exists(Logger)) $logger = Logger::getLogger(__CLASS__);

        if (false === is_array($xRayClass))
            $xRayClass = XRayService::scan($xRayClass);
        
        $className = key($xRayClass);

        // $logger->debug("Load Ajax methods for class [className:$className].");

        $methods = &$xRayClass[$className][XRayService::METHODS];
        
        foreach ($methods as $methodName => $method) {

            $descriptors = $method[XRayService::DESCRIPTORS];

            $service = null;
            $authentication = null;
            $action = null;
            $compression = true;
            $requireCapabilities = null;

            foreach ($descriptors as $key => $descriptor) {

                $value = $descriptor[0][XRayService::VALUE];

                switch ($key) {
                    case self::SERVICE:
                        $service = $value;
                        break;

                    case self::AUTHENTICATION:
                        $authentication = $value;
                        break;

                    case self::ACTION:
                        $action = $value;
                        break;

                    case self::COMPRESSION:
                        $compression = (null === $value || 'false' !== $value);
                        break;
                        
                    case self::REQUIRE_CAPABILITIES:
                        $requireCapabilities = $value;
                        break;
                }
            }
            
            if (self::SERVICE_AJAX === $service && null != $action) {
                // $logger->debug("Binding action [action:$action] to method [methodName:$className::$methodName].");

                // echo "hooking $className::$methodName to action $action.\n";

                $compression = var_export($compression, true);

                $proxy = create_function('$arguments',
                             __CLASS__ . "::proxy( '$className', '$methodName', \$arguments, $compression, '$requireCapabilities');");

                // enable public access to the ajax end-point.
                if (null === $authentication || self::AUTHENTICATION_REQUIRED !== $authentication) {
                    // bind the action to the function.
                     do_action(self::WP_AJAX_NOPRIV . $action);   
                     add_action(self::WP_AJAX_NOPRIV . $action, $proxy);
                }

                // enable protected access to the ajax end-point.
                do_action(self::WP_AJAX . $action);
                add_action(self::WP_AJAX . $action, $proxy);
            }
        }        
    }
    
    public static function proxy($className, $methodName, $arguments, $compression, $requireCapabilities = null) {
        // echo "invoking $className::$methodName.\n";
        
        if (null !== $requireCapabilities && '' !== $requireCapabilities) {
            $capabilities = explode(",", $requireCapabilities);
            
            foreach ($capabilities as $capability) {
                if (false === current_user_can($capability) ) {
                    // TODO: format errors and send them to JSON.
                    header("Content-type: application/json");
                    echo "{\"error\": \"the current user is lacking the " . $capability . " capability.\"}"; 
                    exit;
                }
            }
        }
        
        $xRayClass = XRayService::scan($className);
        $parameters = $xRayClass[$className][XRayService::METHODS][$methodName][XRayService::PARAMETERS];

        $args = array();
        foreach ($parameters as $parameter) {
            if (self::REQUEST_BODY === $parameter) {
                $args[] = file_get_contents(self::PHP_INPUT);
                continue;
            }

            if (null === $_REQUEST[$parameter])
                continue;

            $args[] = $_REQUEST[$parameter];
        }
        
        $instance = new $className();
        $returnValue = call_user_func_array( array($instance, $methodName), $args);
        
        if (self::CALLBACK_RETURN_ERROR === $returnValue) {
            // error.
            exit;
        }

        if (self::CALLBACK_RETURN_NULL === $returnValue) {
            // no response / maybe the method returned its own.
            exit;
        }
        
        JsonService::sendResponse($returnValue, $compression);
        
        exit;
    }    
}

?>