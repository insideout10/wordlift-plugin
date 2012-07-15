<?php

/**
 * class
 *  - meta
 *  - constructors
 *  - properties
 *  - methods
 *    - meta
 *    - parameters
 */
class XRayService {
    
    const PATTERN = "/@(.*?) (.*)/";
    const DESCRIPTORS = "descriptors";
    const METHODS = "methods";
    const PROPERTIES = "properties";
    const PARAMETERS = "parameters";

    const KEY = 1;
    const VALUE = 2;
    
    public static function scan($className) {

        try {
           $reflectionClass = new ReflectionClass($className);
        } catch (Exception $e) {
            return NULL;
        }

        // get the methods descriptors.
        $properties = array();
        
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $propertyName = $reflectionProperty->name;

            $descriptors = self::getDescriptors(
                $reflectionProperty->getDocComment()
            );
            
            // save the methods and their parameters to an array.
            $properties[$propertyName] = array(
                self::DESCRIPTORS => $descriptors
            );
            // $properties[] = array(
            //     $propertyName => array(
            //         self::DESCRIPTORS => $descriptors
            //     )
            // );
        }

        // get the methods descriptors.
        $methods = array();
        
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $methodName = $reflectionMethod->name;

            $descriptors = self::getDescriptors(
                $reflectionMethod->getDocComment()
            );

            // get the method parameters.
            $parameters = array();
            
            foreach ($reflectionMethod->getParameters() as $parameter)
                $parameters[] = $parameter->name;
            
            // save the methods and their parameters to an array.
            $methods = array_merge(
                $methods,
                array(
                    $methodName => array(
                        self::DESCRIPTORS => $descriptors,
                        self::PARAMETERS => $parameters
                    )
                )
            );
        }

        // get the class descriptors.
        $descriptors = self::getDescriptors(
            $reflectionClass->getDocComment()
        );

        $class = array(
            $className => array(
                self::DESCRIPTORS => $descriptors,
                self::METHODS => $methods,
                self::PROPERTIES => $properties
            )
        );

        return $class;
    }
    
    private static function getDescriptors($docComment) {
        $matches = array();
        preg_match_all(self::PATTERN, $docComment, $matches, PREG_SET_ORDER);

        $matchesWithKeys = array();

        foreach ($matches as $match) {
            if (false === array_key_exists($match[self::KEY], $matchesWithKeys) )
                $matchesWithKeys[$match[self::KEY]] = array( $match );
            else
                $matchesWithKeys[$match[self::KEY]] = array_merge($matchesWithKeys[$match[self::KEY]], $match);
        }

        return $matchesWithKeys;
    }

    public static function getValuesByDescritor(&$xRayClass, $key) {
        
        $descriptors = &$xRayClass[XRayService::DESCRIPTORS];
        
        $values = array();

        foreach ($descriptors as $descriptor)
            if ($key === $descriptor[XRayService::KEY])
                $values[] = $descriptor[XRayService::VALUE];

        return $values;
    }
}

?>