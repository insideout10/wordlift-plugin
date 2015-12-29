<?php
/**
 * User: david
 * Date: 15/07/12 12:15
 */


class WordPress_XmlApplication {

    const APPLICATION_NAMESPACE = "http://purl.org/insideout/configuration";
    const WORDPRESS_NAMESPACE = "http://purl.org/insideout/wordpress";

    const LOGGER_PROPERTY = "logger";
    const APPLICATION_CONTEXT_PROPERTY = "applicationContext";

    const WP_ACTION_INIT = "widgets_init";
    const WP_REGISTER_META_BOX_CB = "register_meta_box_cb";
    const WP_ADD_META_BOXES = "add_meta_boxes";

    const REGISTER_META_BOX_CALLBACK = "registerMetaBox";

    const TARGET_ADMIN = "admin";
    const TARGET_USER = "user";
    const TARGET_EDITOR = "editor";

    const FILTER = "filter";
    const ACTION = "action";

    // ##### S T A T I C   P R O P E R T I E S #####
    private static $contexts = NULL;


    // ##### I N S T A N C E   P R O P E R T I E S #####
    private $logger = NULL;

    private $propertyPatterns = NULL;
    private $propertyReplacements = NULL;
    private $scripts = NULL;
    private $styles = NULL;
    private $metaBoxes = NULL;
    private $widgets = NULL;
    private $editorProperties = array();
    private $adminMenus = NULL;
    private $subAdminMenus = NULL;
    private $settings = NULL;

    private $rootFolder = NULL;
    private $xmlConfiguration = NULL;

    // ##### S T A T I C   M E T H O D S #####
    public static function setUp( $rootFolder, $fileName, $loggerConfiguration ) {

        // call back the setup when the INIT action is called by WordPress.
        add_action(
            self::WP_ACTION_INIT,
            create_function("\$arguments", __CLASS__ . "::setUpCallback( '$rootFolder', '$fileName', '$loggerConfiguration');")
        );
    }

    public static function setUpCallback( $rootFolder, $fileName, $loggerConfiguration ) {

        new self( $rootFolder, $fileName, $loggerConfiguration );

    }

    public static function addContext( $name, &$context ) {
        if ( NULL === self::$contexts )
            self::$contexts = array();

        if ( array_key_exists( $name, self::$contexts ) )
            throw new Exception( "A context named [$name] already exists. Cannot add another one." );

        self::$contexts[ $name ] = $context;

    }

    public static function getContext( $name ) {
        if ( !is_array( self::$contexts ) || !array_key_exists( $name, self::$contexts ) )
            throw new Exception( "A context named [$name] does not exist." );

        return self::$contexts[ $name ];
    }

    // ##### I N S T A N C E   M E T H O D S #####
    function __construct( $rootFolder, $fileName, $loggerConfiguration ) {

        $this->rootFolder = $rootFolder;

        $fileName = $rootFolder . $fileName;
        $loggerConfiguration = $rootFolder . $loggerConfiguration;

        // get the logger.
        $this->logger = WordPress_XmlApplication::getLogger( $loggerConfiguration );

        // check that we're in WordPress.
        if ( false === function_exists( "add_filter" ) )
            throw new Exception( "Cannot find the [add_filter] function. Are we in a WordPress environment?" );

        // check if the file exists. if not throw an exception.
        if ( false === file_exists( $fileName ))
            throw new Exception( "Xml Application configuration file [$fileName] is not found." );

        // load the configuration.
        // $this->logger->trace( "Creating application from [$fileName]." );
        $xmlConfiguration = simplexml_load_file ( $fileName );

        $this->xmlConfiguration = $xmlConfiguration;

        // register the wordpress namespace.
        $xmlConfiguration->registerXPathNamespace( "application", self::APPLICATION_NAMESPACE );
        $xmlConfiguration->registerXPathNamespace( "wordpress", self::WORDPRESS_NAMESPACE );

        // ***** C O N T E X T   N A M E ***** //
        $contexts = $xmlConfiguration->xpath( "//application:context" );

        if ( 1 === count( $contexts ) ) {
            $context = reset( $contexts );
            $contextName = (string) $context->attributes()->name;

            if ( "" !== $contextName ) {
                // $this->logger->trace( "Found a named context [$contextName]." );
                self::addContext( $contextName, $this );
            }
        }

        // ***** T H U M B N A I L S ***** //
        // get the thumbnails.
        $thumbnails = $xmlConfiguration->xpath( "//wordpress:thumbnail" );
        // $this->logger->trace( count($thumbnails) . " thumbnail(s) found in file [$fileName]." );
        $this->loadThumbnails( $thumbnails );

        // ***** P R O P E R T I E S ***** //
        $properties = $xmlConfiguration->xpath( "//application:property" );
        // $this->logger->trace( count($properties) . " property(ies) found in file [$fileName]." );
        $this->loadProperties( $properties );

        // ***** M E T A B O X E S ***** //
        $metaBoxes = $xmlConfiguration->xpath( "//wordpress:metaBox" );
        // $this->logger->trace( count($metaBoxes) . " meta-boxes(s) found in file [$fileName]." );
        $this->loadMetaBoxes( $metaBoxes );

        // ***** P O S T  T Y P E S ***** //
        // get the post types.
        $types = $xmlConfiguration->xpath( "//wordpress:postType" );
        // $this->logger->trace( count($types) . " type(s) found in file [$fileName]." );

        foreach ($types as $type) {
            $typeName = (string) $type->attributes()->name;
            $classID = (string) $type->attributes()->class;

            if ("" === $typeName || 20 < strlen( $typeName))
                throw new Exception( "A postType configuration element requires a name attribute [$typeName] of maximum 20 characters." );

            if ("" === $classID)
                throw new Exception( "A postType configuration element requires a class attribute." );

            $instance = $this->getClass( $classID, $rootFolder, $xmlConfiguration );

            if (NULL === $instance || false === method_exists( $instance, "getArguments"))
                throw new Exception( "A postType configuration is invalid. The referenced class does not exist or does not support the getArguments method." );

            // $this->logger->trace( "Registering post custom type [$typeName]." );

            $postTypeConfiguration = $instance->getArguments();
            $postTypeConfiguration[ self::WP_REGISTER_META_BOX_CB ] = array( $instance, self::REGISTER_META_BOX_CALLBACK );
            register_post_type( $typeName, $postTypeConfiguration );

            add_filter( "manage_edit-" . $typeName . "_columns", array( $instance, "getColumns") );
            // TODO: make this compatible with WordPress pre-3.1 http://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
            add_action( "manage_posts_custom_column", array( $instance, "getColumnValue"), 10, 2 );
        }

        flush_rewrite_rules(true);

        // ***** F I L T E R S *****
        // get the filters.
        $filters = $xmlConfiguration->xpath( "//wordpress:filter" );
        // $this->logger->trace( count($filters) . " filter(s) found in file [$fileName]." );
        $this->loadActionOrFilter( self::FILTER, $filters );

        // ***** A C T I O N S *****
        $actions = $xmlConfiguration->xpath( "//wordpress:action" );
        // $this->logger->trace( count($actions) . " action(s) found in file [$fileName]." );
        $this->loadActionOrFilter( self::ACTION, $actions );

        // ***** S H O R T C O D E S *****
        $shortCodes = $xmlConfiguration->xpath( "//wordpress:shortCode" );
        // $this->logger->trace( count($filters) . " short-code(s) found in file [$fileName]." );

        // each filter has a name and optionally a priority and acceptedArguments.
        foreach ( $shortCodes as $shortCode ) {

            $attributes = $shortCode->attributes();

            $name = (string) $attributes->name;
            $class = (string) $attributes->class;
            $method = (string) $attributes->method;

            if ( "" === $name )
                throw new Exception( "A short-code is missing a name in [$fileName]." );

            if ( "" === $class )
                throw new Exception( "A short-code is missing the class attribute in [$fileName]." );

            if ( "" === $method )
                throw new Exception( "A short-code is missing the method attribute in [$fileName]." );

            $class = $this->getClass( $class );
            add_shortcode( $name, array( $class, $method) );
        }


        // ***** S T Y L E S *****
        // get the filters.
        $styles = $xmlConfiguration->xpath( "//wordpress:style" );
        // $this->logger->trace( count($styles) . " style(s) found in file [$fileName]." );

        $this->styles = array();
        foreach ($styles as $style) {
            array_push( $this->styles, $this->getStyle( $style ));
        }

        /***** S C R I P T S *****/
        $scripts = $xmlConfiguration->xpath( "//wordpress:script" );
        // $this->logger->trace( count($scripts) . " script(s) found in file [$fileName]." );

        $this->scripts = array();
        foreach ($scripts as $script) {
            array_push( $this->scripts, $this->getScript( $script ) );
        }

        // $this->logger->trace( "Hooking enqueue scripts actions." );
        add_action( "admin_enqueue_scripts", array( $this, "queueAdminScripts" ) );
        add_action( "wp_enqueue_scripts", array( $this, "queueUserScripts" ) );
        add_filter( "mce_css", array( $this, "setEditorStyles" ) );

        // ***** A J A X S E R V I C E S *****
        $ajaxes = $xmlConfiguration->xpath( "//wordpress:ajax" );
        // $this->logger->trace( count($ajaxes) . " ajax service(s) found in file [$fileName]." );

        $this->loadAjax( $ajaxes );

        // ***** E D I T O R *****
        $editorProperties = $xmlConfiguration->xpath( "//wordpress:editor" );
        // $this->logger->trace( count( $editorProperties ) . " editor option(s) found in file [$fileName]." );
        $this->loadEditorProperties( $editorProperties );
        add_filter( "tiny_mce_before_init", array( $this, "initializeEditorConfiguration") );

        // ***** W I D G E T S *****
        $widgets = $xmlConfiguration->xpath("//wordpress:widget");
        require_once( $rootFolder . "/php/insideout/wordpress/services/WidgetProxy.php" );
        $this->loadWidgets( $widgets );

        // ***** A D M I N  M E N U S *****
        $this->adminMenus = $xmlConfiguration->xpath("//wordpress:adminMenu");
        $this->subAdminMenus = $xmlConfiguration->xpath("//wordpress:subAdminMenu");
        add_action( "admin_menu", array( $this, "loadAdminMenus" ) );

        // ***** S E T T I N G S *****
        $this->settings = $xmlConfiguration->xpath("//wordpress:settings");
        add_action( "admin_menu", array( $this, "loadSettings" ) );

        // ***** N O T I C E S *****
        $notices = $xmlConfiguration->xpath("//wordpress:notice");
        foreach ( $notices as $notice ) {
            $class = (string) $notice->attributes()->class;
            $method = (string) $notice->attributes()->method;
            if ( empty( $class ) || empty( $method ) ) {
                $this->logger->error( "The attributes class and method are required (notice)." );
                continue;
            }

            $instance = $this->getClass( $class, $this->rootFolder, $this->xmlConfiguration );

            add_action( "admin_notices", array( $instance, $method ) );    
        }

        // ***** A C T I V A T E  H O O K S *****
        // $activates = $xmlConfiguration->xpath("//wordpress:activate");
        // foreach ( $activates as $activate ) {
        //     $class = (string) $activate->attributes()->class;
        //     $method = (string) $activate->attributes()->method;
        //     if ( empty( $class ) || empty( $method ) ) {
        //         $this->logger->error( "The attributes class and method are required (activate)." );
        //         continue;
        //     }

        //     $instance = $this->getClass( $class, $this->rootFolder, $this->xmlConfiguration );

        //     register_activation_hook( __FILE__, array( $instance, $method ) );
        // }

    }

    public function loadSettings() {

        foreach ( $this->settings as $setting ) {
            $attributes = $setting->attributes();
            $proxy = (string) $attributes->proxy;
            if ( empty( $proxy ) )
                $this->logger->error( "The proxy attribute is required." );


            $pageTitle = (string) $attributes->pageTitle;
            if ( empty( $pageTitle ) )
                $this->logger->error( "The pageTitle attribute is required." );

            $menuTitle = (string) $attributes->menuTitle;
            if ( empty( $menuTitle ) )
                $this->logger->error( "The menuTitle attribute is required." );

            $capability = (string) $attributes->capability;
            if ( empty( $capability ) )
                $this->logger->error( "The capability attribute is required." );


            $menuSlug = (string) $attributes->menuSlug;
            if ( empty( $menuSlug ) )
                $this->logger->error( "The menuSlug attribute is required." );


            $sections = $setting->xpath("wordpress:section");
            $sectionsArray = array();
            foreach ( $sections as $section ) {

                // get the fields.
                $fields = $section->xpath("wordpress:field");
                $fieldsArray = array();
                foreach ( $fields as $field ) {
                    $title = (string) $field->attributes()->title;
                    $id = (string) $field->attributes()->id;
                    $fieldsArray[] = array(
                            "id" => $id,
                            "title" => $title
                        );
                }

                // store the section.
                $id = (string) $section->attributes()->id;
                $title = (string) $section->attributes()->title;
                $sectionsArray[] = array(
                        "id" => $id,
                        "title" => $title,
                        "fields" => $fieldsArray
                    );

            }

            $proxyInstance = $this->getClass( $proxy, $this->rootFolder, $this->xmlConfiguration );
            $proxyInstance->setPageTitle( $pageTitle );
            $proxyInstance->setMenuSlug( $menuSlug );
            $proxyInstance->setSections( $sectionsArray );

            add_options_page( $pageTitle, $menuTitle, $capability, $menuSlug, array( $proxyInstance, "writePage" ) );
        }
    }

    // refer to http://codex.wordpress.org/Function_Reference/add_menu_page
    public function loadAdminMenus() {

        if ( NULL === $this->adminMenus )
            return;

        foreach ( $this->adminMenus as $menu ) {
            $attributes = $menu->attributes();

            $pageTitle = (string) $attributes->pageTitle;
            if ( empty( $pageTitle ) )
                $this->logger->error( "The pageTitle attribute is required." );

            $menuTitle = (string) $attributes->menuTitle;
            if ( empty( $menuTitle ) )
                $this->logger->error( "The menuTitle attribute is required." );

            $capability = (string) $attributes->capability;
            if ( empty( $capability ) )
                $this->logger->error( "The capability attribute is required." );

            $menuSlug = (string) $attributes->menuSlug;
            if ( empty( $menuSlug ) )
                $this->logger->error( "The menuSlug attribute is required." );

            $iconUrl = (string) $attributes->iconUrl;
            $position = (string) $attributes->position;

            $class = (string) $attributes->class;
            $method = (string) $attributes->method;

            if ( ! empty( $class ) && ! empty( $method ) ) {
                $instance = $this->getClass( $class, $this->rootFolder, $this->xmlConfiguration );
                $hookName = add_menu_page( $pageTitle, $menuTitle, $capability, $menuSlug, array( $instance, $method ), $iconUrl, $position );
            }
            else
                $hookName = add_menu_page( $pageTitle, $menuTitle, $capability, $menuSlug, NULL, $iconUrl, $position );

            // $this->logger->trace( "menu added [ pageTitle :: $pageTitle ][ menuTitle :: $menuTitle ][ capability :: $capability ][ menuSlug :: $menuSlug ][ hookName :: $hookName ]." );
        }

        $this->loadSubAdminMenus();
    }

    public function loadSubAdminMenus() {

        if ( NULL === $this->subAdminMenus )
            return;

        foreach ( $this->subAdminMenus as $menu ) {
            $attributes = $menu->attributes();

            $parentSlug = (string) $attributes->parentSlug;
            if ( empty( $parentSlug ) )
                $this->logger->error( "The parentSlug attribute is required." );

            $pageTitle = (string) $attributes->pageTitle;
            if ( empty( $pageTitle ) )
                $this->logger->error( "The pageTitle attribute is required." );

            $menuTitle = (string) $attributes->menuTitle;
            if ( empty( $menuTitle ) )
                $this->logger->error( "The menuTitle attribute is required." );

            $capability = (string) $attributes->capability;
            if ( empty( $capability ) )
                $this->logger->error( "The capability attribute is required." );

            $menuSlug = (string) $attributes->menuSlug;
            if ( empty( $menuSlug ) )
                $this->logger->error( "The menuSlug attribute is required." );

            // $iconUrl = (string) $attributes->iconUrl;
            // $position = (string) $attributes->position;

            $class = (string) $attributes->class;
            $method = (string) $attributes->method;

            // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
            if ( ! empty( $class ) && ! empty( $method ) ) {
                $instance = $this->getClass( $class, $this->rootFolder, $this->xmlConfiguration );
                $hookName = add_submenu_page( $parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, array( $instance, $method ) );
            }
            else
                $hookName = add_submenu_page( $parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, NULL );

            // $this->logger->trace( "menu added [ pageTitle :: $pageTitle ][ menuTitle :: $menuTitle ][ capability :: $capability ][ menuSlug :: $menuSlug ][ hookName :: $hookName ]." );
        }

    }

    private function loadWidgets( $widgets ) {

        // set the applicationContext of the WidgetProxy.
        WordPress_WidgetProxy::$applicationContext = $this;

        foreach ( $widgets as $widget) {
            $classID = (string) $widget->attributes()->class;

            if ( empty( $classID ) ) {
                $this->logger->error( "An editor property is missing the property name." );
                continue;
            }

            $class = $this->getClassDefinition( $classID );
            $this->loadClass( $class );
            $className = $this->getClassName( $class );

            // $this->logger->trace( "Found a widget [ classID :: $classID ][ className:: $className ]." );

            register_widget( $className );
        }

        // $this->logger->trace( count( $this->widgets ) . " widget(s) loaded." );
    }

    private function loadEditorProperties( $properties ) {

        foreach ( $properties as $property) {
            $name = (string) $property->attributes()->property;
            $value = (string) $property->attributes()->value;

            if ( empty( $name ) )
                $this->logger->error( "An editor property is missing the property name." );
            if ( empty( $value ) )
                $this->logger->error( "The editor property [ property :: $name ] is missing the value." );

            if ( array_key_exists( $name, $this->editorProperties ) )
                $this->editorProperties[ $name ] .= ",$value";
            else
                $this->editorProperties[ $name ] = $value;
        }

    }

    public function initializeEditorConfiguration( $configuration ) {

        // $this->logger->trace( "Initializing the Editor configuration." );

        foreach ( $this->editorProperties as $property => $value )
            if ( in_array( $property, $configuration ) && ! empty( $configuration[ $property] ) )
                $configuration[ $property ] .= ",$value";
            else
                $configuration[ $property ] = $value;

        return $configuration;
    }

    private function loadThumbnails( $thumbnails ) {

        if ( 0 === count( $thumbnails ) )
            return;

        add_theme_support( "post-thumbnails" );

        foreach ( $thumbnails as $thumbnail ) {
            $name = (string) $thumbnail->attributes()->name;
            $width = (string)  $thumbnail->attributes()->width;
            $height = (string)  $thumbnail->attributes()->height;
            $crop = (string)  $thumbnail->attributes()->crop;
            $crop = ( "true" === $crop );

            add_image_size( $name, $width, $height, $crop );
        }

    }

    private function loadProperties( $properties ) {
        $this->propertyPatterns = array();
        $this->propertyReplacements = array();

        foreach ( $properties as $property ) {
            $name = (string) $property->attributes()->name;
            $value = (string)  $property->attributes()->value;

            array_push( $this->propertyPatterns, "/\{$name\}/" );
            array_push( $this->propertyReplacements, $value );
        }

        ksort( $this->propertyPatterns );
        ksort( $this->propertyReplacements );
    }

    private function getPropertyValue( $value ) {
        return preg_replace( $this->propertyPatterns, $this->propertyReplacements, $value );
    }

    private function loadAjax( $ajaxes ) {
        // service="ajaxService" action="wordlift.echo" class="echoService" method="getPong" authentication="false" capabilities="any"

        foreach ($ajaxes as $ajax) {

            $attributes = $ajax->attributes();

            $service = (string) $attributes->service;
            $action = (string) $attributes->action;
            $class = (string) $attributes->class;
            $method = (string) $attributes->method;
            $httpMethod = (string) $attributes->httpMethod;
            $httpMethod = ( "" === $httpMethod ? "GET" : $httpMethod );
            $authentication = ( "true" === (string) $attributes->authentication );
            $capabilities = (string) $attributes->capabilities;
            if ( "" === $capabilities )
                $capabilities = "any";
            $compression = ( "false" === (string) $attributes->compression );

            $corsOrigin = (string) $attributes->corsOrigin;
            $corsMethods = (string) $attributes->corsMethods;
            $corsHeaders = (string) $attributes->corsHeaders;

            $cors = array();
            if ( "" !== $corsOrigin )
                $cors[ WordPress_AjaxProxy::CORS_ORIGIN ] = $corsOrigin;
            if ( "" !== $corsMethods )
                $cors[ WordPress_AjaxProxy::CORS_METHODS ] = $corsMethods;
            if ( "" !== $corsHeaders )
                $cors[ WordPress_AjaxProxy::CORS_HEADERS ] = $corsHeaders;

            if ( "" === $service )
                throw new Exception( "An Ajax method is missing its service manager." );

            if ( "" === $action )
                throw new Exception( "An Ajax method is missing an action name." );

            if ( "" === $class )
                throw new Exception( "An Ajax method is missing the class reference." );

            if ( "" === $method )
                throw new Exception( "An Ajax method is missing the method name." );

            // $this->logger->trace( "Binding $action to method $class::$method [authentication :: $authentication][capabilities :: $capabilities][compression :: $compression][ httpMethod :: $httpMethod ]." );

            /** @var WordPress_AjaxService $ajaxService */
            $ajaxService = $this->getClass( $service );
            $instance = $this->getClass( $class );

            $ajaxService->bindAction( $instance, $method, $action, $authentication, $capabilities, $compression, $httpMethod, $cors );
        }
    }

    private function loadActionOrFilter( $type, $items ) {

        $add = ( $type === self::ACTION ? "add_action" : "add_filter" );

        // each filter has a name and optionally a priority and acceptedArguments.
        foreach ( $items as $item ) {

            $attributes = $item->attributes();

            $name = (string) $attributes->name;
            $class = (string) $attributes->class;
            $method = (string) $attributes->method;

            if ( "" === $name )
                throw new Exception( "A $type is missing a name." );

            if ( "" === $class )
                throw new Exception( "A $type is missing the class attribute." );

            if ( "" === $method )
                throw new Exception( "A $type is missing the method attribute." );

            $priority = (string) $attributes->priority;
            $acceptedArguments = (string) $attributes->acceptedArguments;

            // $this->logger->trace( "A $type [$name] has been found [priority :: $priority][acceptedArguments :: $acceptedArguments]." );

            $class = $this->getClass( $class, $this->rootFolder, $this->xmlConfiguration );
            if ( "" !== $priority && "" !== $acceptedArguments )
                $add( $name , array( $class, $method), $priority, $acceptedArguments );
            elseif ( "" !== $priority )
                $add( $name, array( $class, $method), $priority );
            else
                $add( $name, array( $class, $method) );
        }
    }

    public function loadMetaBoxesCallback() {
        // $this->logger->trace( "Adding " . count( $this->metaBoxes ) . " metabox callback(s).");

        foreach ( $this->metaBoxes as $metaBox ) {
            // $this->logger->trace("Adding meta-box [id :: " . $metaBox["id"] . "][title :: " . $metaBox["title"] . "][postType :: " . $metaBox["postType"] . "][context :: " . $metaBox["context"] . "][priority :: " . $metaBox["priority"] . "].");

            add_meta_box( $metaBox["id"], $metaBox["title"], $metaBox["callback"], $metaBox["postType"], $metaBox["context"], $metaBox["priority"] );
        }
    }

    private function loadMetaBoxes( $metaBoxes ) {

        $this->metaBoxes = array();

        foreach ($metaBoxes as $metaBox) {

            $classID = (string) $metaBox->attributes()->class;
            $id = (string) $metaBox->attributes()->id;
            $title = (string) $metaBox->attributes()->title;
            $postType = (string) $metaBox->attributes()->postType;
            $context = (string) $metaBox->attributes()->context;
            if ( "" === $context )
                $context = "normal";
            $priority = (string) $metaBox->attributes()->priority;
            if ( "" === $priority )
                $priority = "default";

            if ("" === $classID )
                throw new Exception( "A metaBox configuration element requires an class attribute." );

            if ("" === $id )
                throw new Exception( "A metaBox configuration element requires an id attribute." );

            if ("" === $title)
                throw new Exception( "A metaBox configuration element requires a type attribute." );

            if ("" === $postType)
                throw new Exception( "A metaBox configuration element requires a postType attribute." );

            $instance = $this->getClass( $classID, $this->rootFolder, $this->xmlConfiguration );

            if (NULL === $instance || false === method_exists( $instance, "getHtml"))
                throw new Exception( "A metaBox configuration is invalid. The referenced class does not exist or does not support the getHtml method." );

            // $this->logger->trace( "Registering meta-box [$id]." );

            array_push(
                $this->metaBoxes,
                array(
                    "id" => $id,
                    "title" => $title,
                    "callback" => array( $instance, "getHtml" ),
                    "postType" => $postType,
                    "context" => $context,
                    "priority" => $priority
                )
            );
        }

        add_action( self::WP_ADD_META_BOXES, array( $this, "loadMetaBoxesCallback" ) );
    }

    public function queueAdminScripts() {
        $this->queueScripts( self::TARGET_ADMIN );
    }

    public function queueUserScripts() {
        $this->queueScripts( self::TARGET_USER );
    }

    private function queueScripts( $target ) {

        // $this->logger->trace( "Queuing styles for target [$target]." );

        foreach ( $this->styles as $style ) {
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


        // $this->logger->trace( "Queuing scripts for target [$target]." );

        foreach ( $this->scripts as $script ) {
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

    /**
     * Sets the stylesheets for the editor. This method is called by the XmlApplication.
     * @param $styles The styles parameter as passed by the mce_css filter call.
     * @return string The updated styles parameter.
     */
    public function setEditorStyles( $styles ) {

        // $this->logger->trace( "Setting styles for the editor." );

        foreach ( $this->styles as $style ) {
            if ( WordPress_XmlApplication::TARGET_EDITOR !== $style[ "target" ] )
                continue;

            if ( ! empty( $styles ) )
                $styles .= ",";

            $styles .= $style[ "url" ];
        }

        return $styles;
    }

    private function getStyle( $style ) {

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

    private function getScript( $script ) {

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

    public function getClassID( $className, &$xmlConfiguration = NULL ) {
        if (NULL === $xmlConfiguration)
            $xmlConfiguration = &$this->xmlConfiguration;

        // get the class definition.
        $classes = $xmlConfiguration->xpath( "//application:class[@name='$className']" );

        if (0 === count($classes) )
            throw new Exception( "Cannot find a class configured with name [$className], expecting 1." );

        // check that there's only one class defined with that ID, otherwise throw an Exception.
        if (1 !== count($classes) )
            throw new Exception( "There are " . count($classes) . " class(es) configured with name [$className], expecting 1." );

        // get the class definition: class name and its filename.
        return (string)$classes[0]->attributes()->id;
    }

    private function getClassDefinition( $classID, &$xmlConfiguration = NULL ) {
        if (NULL === $xmlConfiguration)
            $xmlConfiguration = &$this->xmlConfiguration;

        // get the class definition.
        $classes = $xmlConfiguration->xpath( "//application:class[@id='$classID']" );

        // check that there's only one class defined with that ID, otherwise throw an Exception.
        if (1 !== count($classes) )
            throw new Exception( "There are " . count($classes) . " class(es) configured with name [$classID], expecting 1." );

        // get the class definition: class name and its filename.
        return $classes[0];
    }

    private function getClassName( $class ) {
        return (string) $class->attributes()->name;
    }

    private function loadClass( $class, $rootFolder = NULL ) {
        if (NULL === $rootFolder)
            $rootFolder = $this->rootFolder;

        $attributes = $class->attributes();
        $className = $this->getClassName( $class );
        $fileName = (string) $attributes->filename;

        // load the class from the filename if the class is not yet defined.
        if ( !class_exists( $className ) ) {
            // load any dependency.
            foreach ( $class->dependsOn as $dependsOn ) {
                $dependsOnFileName = (string) $dependsOn->attributes()->filename;

                if ( false === file_exists($rootFolder . $dependsOnFileName) )
                    throw new Exception( "The file [$dependsOnFileName] is not found, it is required by [$classID]." );

                // $this->logger->trace( "Including dependency [$dependsOnFileName]." );

                require_once( $rootFolder . $dependsOnFileName);
            }

            // $this->logger->trace( "Including class filename [$fileName]." );
            require_once( $rootFolder . $fileName);
        }

    }

    public function getClass( $classID, $rootFolder = NULL, $xmlConfiguration = NULL, $instance = NULL ) {

        if (NULL === $rootFolder)
            $rootFolder = $this->rootFolder;

        if (NULL === $xmlConfiguration)
            $xmlConfiguration = $this->xmlConfiguration;

        // $this->logger->trace( "Loading class [$classID][rootFolder :: $rootFolder]." );

        // get the class definition: class name and its filename.
        $class = $this->getClassDefinition( $classID );
        $this->loadClass( $class );

        // $this->logger->trace( "Creating an instance of class [$classID]." );

        // create a new class instance.
        if ( NULL === $instance ) {
            $className = $this->getClassName( $class );
            $instance = new $className();
        }

        // get any properties to assign to the instance.
        $reflectionClass = new ReflectionClass( $instance );

        foreach ($class->property as $property) {
            $propertyName = (string) $property->attributes()->name;
            $propertyValue = (string) $property->attributes()->value;
            $propertyValue = $this->getPropertyValue( $propertyValue );
            $propertyReference = (string) $property->attributes()->reference;

            if (false === $reflectionClass->hasProperty( $propertyName ))
                throw new Exception( "Class [$classID] has no property named [$propertyName]." );

            // set a property reference.
            if ( "" !== $propertyReference) {
                // $this->logger->trace( "Looking for [$propertyReference] referenced by [$classID]." );
                $reference = $this->getClass( $propertyReference, $rootFolder, $xmlConfiguration );
                $reflectionClass->getProperty( $propertyName )->setValue( $instance, $reference );
            } else {
                // set a property value.
                // $this->logger->trace( "Setting [$propertyName] to [$propertyValue] for [$classID]." );
                $reflectionClass->getProperty( $propertyName )->setValue( $instance, $propertyValue );
            }
        }

        // assign a Logger instance if the class has a logger property.
        if (true === $reflectionClass->hasProperty( self::LOGGER_PROPERTY ))
            $reflectionClass->getProperty( self::LOGGER_PROPERTY )->setValue( $instance, Logger::getLogger( get_class($instance) ) );

        // assign the application-context (this class) to the instance if the class has an applicationContext property.
        if (true === $reflectionClass->hasProperty( self::APPLICATION_CONTEXT_PROPERTY ))
            $reflectionClass->getProperty( self::APPLICATION_CONTEXT_PROPERTY )->setValue( $instance, $this );

        // $this->logger->trace( "Returning an instance of class [$classID]." );

        return $instance;
    }

    public static function getLogger( $fileName ) {

        if (false === file_exists( $fileName ))
            throw new Exception( "The Logger configuration file [$fileName] was not found." );

        // configure the logger.
        Logger::configure( $fileName );

        // get a logger.
        return Logger::getLogger(__CLASS__);

    }
}

?>