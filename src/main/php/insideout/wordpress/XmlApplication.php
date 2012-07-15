<?php
/**
 * User: david
 * Date: 15/07/12 12:15
 */

class WordPress_XmlApplication {

    const APPLICATION_NAMESPACE = "http://purl.org/insideout/configuration";
    const WORDPRESS_NAMESPACE = "http://purl.org/insideout/wordpress";

    const LOGGER_PROPERTY = "logger";

    const WP_ACTION_INIT = "init";

    const TARGET_ADMIN = "admin";
    const TARGET_USER = "user";

    private static $logger = null;

    private static $scripts = null;
    private static $styles = null;

    public static function setUp( $rootFolder, $fileName, $loggerConfiguration ) {

        // call back the setup when the INIT action is called by WordPress.
        add_action(
            self::WP_ACTION_INIT,
            create_function("\$arguments", __CLASS__ . "::setUpCallback( '$rootFolder', '$fileName', '$loggerConfiguration');")
        );
    }

    public static function setUpCallback( $rootFolder, $fileName, $loggerConfiguration ) {

        $fileName = $rootFolder . $fileName;
        $loggerConfiguration = $rootFolder . $loggerConfiguration;

        // get the logger.
        self::$logger = self::getLogger( $loggerConfiguration );

        // check that we're in WordPress.
        if ( false === function_exists( "add_filter" ) )
            throw new Exception( "Cannot find the [add_filter] function. Are we in a WordPress environment?" );

        // check if the file exists. if not throw an exception.
        if ( false === file_exists( $fileName ))
            throw new Exception( "Xml Application configuration file [$fileName] is not found." );

        // load the configuration.
        self::$logger->trace( "Creating application from [$fileName]." );
        $xmlConfiguration = simplexml_load_file ( $fileName );

        // register the wordpress namespace.
        $wordpress = "wordpress";
        $xmlConfiguration->registerXPathNamespace( "application", self::APPLICATION_NAMESPACE );
        $xmlConfiguration->registerXPathNamespace( $wordpress, self::WORDPRESS_NAMESPACE );

        // get the post types.
        $types = $xmlConfiguration->xpath( "//$wordpress:postType" );
        self::$logger->trace( count($types) . " type(s) found in file [$fileName]." );

        foreach ($types as $type) {
            $typeName = (string) $type->attributes()->name;
            $classID = (string) $type->attributes()->class;

            if ("" === $typeName || 20 < strlen( $typeName))
                throw new Exception( "A postType configuration element requires a name attribute [$typeName] of maximum 20 characters." );

            if ("" === $classID)
                throw new Exception( "A postType configuration element requires a class attribute." );

            $instance = self::getClass( $rootFolder, $xmlConfiguration, $classID );

            if (NULL === $instance || false === method_exists( $instance, "getArguments"))
                throw new Exception( "A postType configuration is invalid. The referenced class does not exist or does not support the getArguments method." );

            self::$logger->trace( "Registering post custom type [$typeName]." );
            register_post_type( $typeName, $instance->getArguments());
        }

        flush_rewrite_rules(true);

        // get the filters.
        $filters = $xmlConfiguration->xpath( "//$wordpress:filter" );
        self::$logger->trace( count($filters) . " filter(s) found in file [$fileName]." );

        // each filter has a name and optionally a priority and acceptedArguments.
        foreach ( $filters as $filter ) {

            $attributes = $filter->attributes();

            $name = (string) $attributes->name;
            $class = (string) $attributes->class;
            $method = (string) $attributes->method;

            if ( "" === $name )
                throw new Exception( "A filter is missing a name in [$fileName]." );

            if ( "" === $class )
                throw new Exception( "A filter is missing the class attribute in [$fileName]." );

            if ( "" === $method )
                throw new Exception( "A filter is missing the method attribute in [$fileName]." );

            $priority = (string) $attributes->priority;
            $acceptedArguments = (string) $attributes->acceptedArguments;

            self::$logger->trace( "A filter [$name] has been found [priority :: $priority][acceptedArguments :: $acceptedArguments]." );

            $class = self::getClass( $rootFolder, $xmlConfiguration, $class );
            if ( "" !== $priority && "" !== $acceptedArguments )
                add_filter( $name , array( $class, $method), $priority, $acceptedArguments );
            elseif ( "" !== $priority )
                add_filter( $name, array( $class, $method), $priority );
            else
                add_filter( $name, array( $class, $method) );
        }

        // ***** S T Y L E S *****
        // get the filters.
        $styles = $xmlConfiguration->xpath( "//$wordpress:style" );
        self::$logger->trace( count($styles) . " style(s) found in file [$fileName]." );

        self::$styles = array();
        foreach ($styles as $style) {
            array_push( self::$styles, self::getStyle( $style ));
        }

        /***** S C R I P T S *****/
        $scripts = $xmlConfiguration->xpath( "//$wordpress:script" );
        self::$logger->trace( count($scripts) . " script(s) found in file [$fileName]." );

        self::$scripts = array();
        foreach ($scripts as $script) {
            array_push( self::$scripts, self::getScript( $script ) );
        }

        self::$logger->trace( "Hooking enqueue scripts actions." );
        add_action( "admin_enqueue_scripts", create_function("\$arguments", __CLASS__ . "::queueScripts('" . self::TARGET_ADMIN . "');" ) );
        add_action( "wp_enqueue_scripts", create_function("\$arguments", __CLASS__ . "::queueScripts('" . self::TARGET_USER . "');" ) );

    }

    public static function queueScripts( $target ) {

        self::$logger->trace( "Queuing styles for target [$target]." );

        foreach (self::$styles as $style) {
            if ( $target !== $style["target"])
                continue;

            // wp_enqueue_style( $handle, $src, $deps, $ver, $media );
            // rif. http://codex.wordpress.org/Function_Reference/wp_enqueue_style
            if ("" !== $style["media"])
                wp_enqueue_style( $style["name"], $style["url"], $style["dependencies"], $style["version"], $style["media"] );
            else if ("" !== $style["version"])
                wp_enqueue_style( $style["name"], $style["url"], $style["dependencies"], $style["version"] );
            else
                wp_enqueue_style( $style["name"], $style["url"], $style["dependencies"] );
        }


        self::$logger->trace( "Queuing scripts for target [$target]." );

        foreach (self::$scripts as $script) {
            if ( $target !== $script["target"])
                continue;

            // wp_enqueue_script( $handle ,$src ,$deps ,$ver ,$in_footer );
            // rif. http://codex.wordpress.org/Function_Reference/wp_enqueue_script
            if ("" !== $script["footer"])
                wp_register_script( $script["name"], $script["url"], $script["dependencies"], $script["version"], $script["footer"] );
            else if ("" !== $script["version"])
                wp_register_script( $script["name"], $script["url"], $script["dependencies"], $script["version"] );
            else
                wp_register_script( $script["name"], $script["url"], $script["dependencies"] );

            wp_enqueue_script( $script["name"] );
        }

    }

    private static function getStyle( $style ) {

        /*
            <wordpress:style target="user" name="ioio-0.9.1.min.css"
                    version="0.9.1"
                    media="all"
                    url="/wp-content/plugins/wordlift/js/ioio-0.9.1.min.css">
                <dependsOn name="jquery" />
                <dependsOn name="jquery-ui" />
            </wordpress:script>
         */

        $attributes = $style->attributes();

        $name = (string) $attributes->name;
        $version = (string) $attributes->version;
        $media = (string) $attributes->media;
        $url = (string) $attributes->url;
        $target = (string) $attributes->target;

        if ( "" === $name )
            throw new Exception( "A style is missing the name attribute." );

        if ( "" === $url )
            throw new Exception( "A style [$name] is missing the url attribute." );

        if ( "" === $target )
            throw new Exception( "A style [$name] is missing the target attribute." );

        $dependencies = array();
        foreach ($style->dependsOn as $dependsOn) {
            array_push( $dependencies, (string) $dependsOn->attributes()->name);
        }

        return array(
            "name" => $name,
            "version" => $version,
            "media" => $media,
            "url" => $url,
            "target" => $target,
            "dependencies" => $dependencies
        );
    }

    private static function getScript( $script ) {

        /*
            <wordpress:script target="user" name="ioio-0.9.1.min.js"
                    version="0.9.1"
                    footer="false"
                    url="/wp-content/plugins/wordlift/js/ioio-0.9.1.min.js">
                <dependsOn name="jquery" />
                <dependsOn name="jquery-ui" />
            </wordpress:script>
         */

        $attributes = $script->attributes();

        $name = (string) $attributes->name;
        $version = (string) $attributes->version;
        $footer = (string) $attributes->footer;
        $url = (string) $attributes->url;
        $target = (string) $attributes->target;

        if ( "" === $name )
            throw new Exception( "A style is missing the name attribute." );

        if ( "" === $url )
            throw new Exception( "A style [$name] is missing the url attribute." );

        if ( "" === $target )
            throw new Exception( "A style [$name] is missing the target attribute." );

        $dependencies = array();
        foreach ($script->dependsOn as $dependsOn) {
            array_push( $dependencies, (string) $dependsOn->attributes()->name);
        }

        return array(
            "name" => $name,
            "version" => $version,
            "footer" => ("true" === $footer),
            "url" => $url,
            "target" => $target,
            "dependencies" => $dependencies
        );
    }

    private static function getClass( $rootFolder, $xmlConfiguration, $classID ) {

        // get the class definition.
        $classes = $xmlConfiguration->xpath( "//application:class[@id='$classID']" );

        // check that there's only one class defined with that ID, otherwise throw an Exception.
        if (1 !== count($classes) )
            throw new Exception( "There are " . count($classes) . " class(es) configured with name [$classID], expecting 1." );

        // get the class definition: class name and its filename.
        $class = $classes[0];
        $attributes = $class->attributes();
        $className = (string) $attributes->name;
        $fileName = (string) $attributes->filename;

        // load any dependency.
        foreach ( $class->dependsOn as $dependsOn ) {
            $dependsOnFileName = (string) $dependsOn->attributes()->filename;

            if ( false === file_exists($rootFolder . $dependsOnFileName) )
                throw new Exception( "The file [$dependsOnFileName] is not found, it is required by [$classID]." );

            require_once( $rootFolder . $dependsOnFileName);
        }

        // load the class from the filename if the class is not yet defined.
        if ( false === class_exists( $class ) )
            require_once( $rootFolder . $fileName);

        // create a new class instance.
        $instance = new $className();

        // get any properties to assign to the instance.
        $reflectionClass = new ReflectionClass( $instance );

        foreach ($class->property as $property) {
            $propertyName = (string) $property->attributes()->name;
            $propertyValue = (string) $property->attributes()->value;
            $propertyReference = (string) $property->attributes()->reference;

            if (false === $reflectionClass->hasProperty( $propertyName ))
                throw new Exception( "Class [$classID] has no property named [$propertyName]." );

            // set a property reference.
            if ( "" !== $propertyReference) {
                self::$logger->trace( "Looking for [$propertyReference] referenced by [$classID]." );
                $reference = self::getClass( $rootFolder, $xmlConfiguration, $propertyReference );
                $reflectionClass->getProperty( $propertyName )->setValue( $instance, $reference );
            } else {
                // set a property value.
                self::$logger->trace( "Setting [$propertyName] to [$propertyValue] for [$classID]." );
                $reflectionClass->getProperty( $propertyName )->setValue( $instance, $propertyValue );
            }
        }

        // assign a Logger instance if the class has a logger property.
        if (false === $reflectionClass->hasProperty( self::LOGGER_PROPERTY ))
            return $instance;

        $reflectionClass->getProperty( self::LOGGER_PROPERTY )->setValue( $instance, Logger::getLogger( get_class($instance) ) );

        return $instance;
    }

    private static function getLogger( $fileName ) {

        if (false === file_exists( $fileName ))
            throw new Exception( "The Logger configuration file [$fileName] was not found." );

        // configure the logger.
        Logger::configure( $fileName );

        // get a logger.
        return Logger::getLogger(__CLASS__);

    }
}

?>