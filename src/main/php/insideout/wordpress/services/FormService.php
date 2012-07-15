<?php

class FormService {
    
    const SERVICE = "service";
    const FORM = "form";
    const ACTION = "action";
    const __ACTION = "__action";
    
    public static function load($xRayClass) {

        if (false === is_array($xRayClass))
            $xRayClass = XRayService::scan($xRayClass);

        $className = key($xRayClass);

        $service = XRayService::getValuesByDescritor($xRayClass[$className], self::SERVICE);
        
        if (0 === sizeof($service) || self::FORM !== $service[0])
            return;

        // get the form action.
        $action = XRayService::getValuesByDescritor($xRayClass[$className], self::ACTION);

        if (0 === sizeof($action))
            return;

        $action = $action[0];
        
        $properties = $xRayClass[$className][XRayService::PROPERTIES];

        // handle the post.
        if (key_exists(self::__ACTION, $_POST)) {
            $form = new $className();
            
            foreach ($properties as $property) {
                $propertyName = key($property);
                $form->$propertyName = $_POST[$propertyName];
            }

            $returnValue = call_user_func(
                array($form, $action),
                $_POST
            );
        }

        FormRenderingService::begin($action);

        foreach ($properties as $property) {
            $propertyName = key($property);
            $propertyValue = (key_exists($propertyName, $_POST) ? $_POST[$propertyName] : null);
            FormRenderingService::renderProperty(
                $propertyName,
                $property[$propertyName][XRayService::DESCRIPTORS],
                $propertyValue
            );
        }
        
        FormRenderingService::end();
    }
}

class FormRenderingService {
    
    const TYPE = "type";
    const MAX_SIZE = "maxSize";
    const MIN_SIZE = "minSize";
    const LABEL = "label";
    const REQUIRED = "required";
    const YES = "yes";
    
    public static function begin($action) {
        echo "<form method=\"POST\">";
        echo "<input type=\"hidden\" name=\"__action\" value=\"$action\" />";
    }

    public static function end() {
        echo "</form>";
    }
    
    public static function renderProperty($propertyName, &$descriptors, $propertyValue = null) {
        $output = "<input name=\"$propertyName\" ";

        if (null !== $propertyValue)
            $output .= "value=\"$propertyValue\" ";

        foreach ($descriptors as $descriptor) {
            $key = $descriptor[XRayService::KEY];
            $value = &$descriptor[XRayService::VALUE];
            
            switch ($key) {
                case self::TYPE:
                    $output .= "type=\"$value\" ";
                    break;

                case self::MIN_SIZE:
                    $output .= "minSize=\"$value\" ";
                    break;

                case self::MAX_SIZE:
                    $output .= "maxsize=\"$value\" ";
                    break;
                
                case self::LABEL:
                    $output = "<label for=\"$propertyName\">$value</label>" . $output;
                    break;

                case self::REQUIRED:
                    if (self::YES === $value)
                        $output .= "required ";
            }
        }

        $output .= "/>";
        
        echo "<div>$output</div>";
    }
    
}

?>