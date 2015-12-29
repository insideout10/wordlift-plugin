<?php

class WordPress_AjaxService implements WordPress_IAjaxService {

    const WP_AJAX_NOPRIV = "wp_ajax_nopriv_";
    const WP_AJAX = "wp_ajax_";
    const INVOKE = "invoke";

    public $logger;

    public $jsonService;

    private static $proxies = array();

    public function bindAction( $instance, $method, $action, $authentication = false, $capabilities = "any", $compression = true, $httpMethods = "GET", $cors = NULL ) {

        $httpMethods = explode( ",", $httpMethods );

        foreach ( $httpMethods as $httpMethod)
            $this->bindSingleAction( $instance, $method, $action, $authentication, $capabilities, $compression, $httpMethod, $cors );
    }

    public function bindSingleAction( $instance, $method, $action, $authentication = false, $capabilities = "any", $compression = true, $httpMethod = "GET", $cors = NULL ) {
        if ( !array_key_exists( $action, self::$proxies ) ) {
            // $this->logger->trace( "Creating an Ajax Proxy [ action :: $action ]." );
            self::$proxies[ $action ] = new WordPress_AjaxProxy( $action, $this->jsonService, $this->logger );

            // enable public access to the ajax end-point.
            if ( !$authentication ) {
                // bind the action to the function.
                do_action(self::WP_AJAX_NOPRIV . $action);
                add_action(self::WP_AJAX_NOPRIV . $action, array( self::$proxies[ $action ], self::INVOKE ) );
            }

            // enable protected access to the ajax end-point.
            do_action(self::WP_AJAX . $action);
            add_action(self::WP_AJAX . $action, array( self::$proxies[ $action ], self::INVOKE ) );
        }

        // $this->logger->trace( "Binding $action to method $method [ authentication :: $authentication ][ capabilities :: $capabilities ][ compression :: $compression ][ httpMethod :: $httpMethod ]." );
        self::$proxies[ $action ]->add( $instance, $method, $authentication, $capabilities, $httpMethod, $cors );

    }
}

?>