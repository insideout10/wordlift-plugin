<?php
/**
 * User: david
 * Date: 21/07/12 10:15
 */

if (!function_exists("getallheaders"))
{ 
    function getallheaders()
    {
        foreach($_SERVER as $key=>$value)
        { 
            if (substr($key, 0, 5) === "HTTP_")
            { 
                $key = str_replace(
                    " ",
                    "-",
                    ucwords(
                        strtolower(
                            str_replace("_", " ", substr($key, 5))
                        )
                    )
                );
                $out[$key]=$value; 
            }
            else
            { 
                $out[$key]=$value;
            } 
        } 
        return $out; 
    } 
}

class WordPress_AjaxProxy
{

    const REQUEST_BODY = "requestBody";
    const HEADERS = "headers";
    const PHP_INPUT = "php://input";
    const CALLBACK_RETURN_ERROR = FALSE;
    const CALLBACK_RETURN_NULL = null;

    const CORS_ORIGIN = "origin";
    const CORS_METHODS = "methods";
    const CORS_HEADERS = "headers";

    private $httpMethod;
    private $action;
    private $jsonService;

    private $httpMethods = array();

    private $logger;

    function __construct($action, $jsonService, $logger)
    {
        $this->action = $action;
        $this->jsonService = $jsonService;
        $this->logger = $logger;
    }

//    function __construct( $instance, $method, $httpMethod, $action, $authentication, $capabilities, $compression, &$jsonService, $logger ) {
//        $this->instance = $instance;
//        $this->method = $method;
//        $this->httpMethod = $httpMethod;
//        $this->action = $action;
//        $this->authentication = $authentication;
//        $this->capabilities = $capabilities;
//        $this->compression = $compression;
//        $this->jsonService = $jsonService;
//
//        $this->logger = $logger;
//    }


    public function add( $instance, $method, $authentication, $capabilities, $httpMethod, $cors ) {
        // $this->logger->trace( "Adding an action [ method :: $method ][ authentication :: $authentication ][ capabilities :: $capabilities ][ httpMethod :: $httpMethod ]." );

        if ( array_key_exists( $httpMethod, $this->httpMethods ) ) {
            $this->logger->error( "An action has already been set [ httpMethod :: $httpMethod ]." );
            return;
        }

        $this->httpMethods[ $httpMethod ] = array(
            "instance" => $instance,
            "method" => $method,
            "authentication" => $authentication,
            "capabilities" => $capabilities,
            "cors" => $cors
        );
    }

    private function checkHttpMethod() {
        $httpRequestMethod = $_SERVER['REQUEST_METHOD'];
        // $this->logger->trace( "[ httpMethod :: $this->httpMethod ][ httpRequestMethod :: $httpRequestMethod ]." );

        return ( $httpRequestMethod === $this->httpMethod );
    }

    private function checkCapabilities( $capabilities ) {
        if ( "any" === $capabilities )
            return;

        $capabilities = explode(",", $capabilities);

        foreach ($capabilities as $capability) {
            if (false === current_user_can($capability) ) {
                // TODO: format errors and send them to JSON.
                header("Content-type: application/json");
                echo "{\"error\": \"the current user is lacking the " . $capability . " capability.\"}";
                exit;
            }
        }
    }

    public function invoke() {

        $httpRequestMethod = $_SERVER['REQUEST_METHOD'];

        // return if we don't have a handler for this HTTP method.
        if ( false === array_key_exists( $httpRequestMethod, $this->httpMethods )) {
            $this->logger->warn( "No instance/method bound to this HTTP request method [ httpRequestMethod :: $httpRequestMethod ]." );
            return;
        }

        $handler = $this->httpMethods[ $httpRequestMethod ];

        if ( array_key_exists( "cors", $handler ) && NULL !== $handler[ "cors" ] ) {
            $cors = &$handler[ "cors" ];

            if ( array_key_exists( self::CORS_ORIGIN, $cors ) )
                header( "Access-Control-Allow-Origin: " . $cors[ self::CORS_ORIGIN ] );
            if ( array_key_exists( self::CORS_METHODS, $cors ) )
                header( "Access-Control-Allow-Methods: " . $cors[ self::CORS_METHODS ] );
            if ( array_key_exists( self::CORS_HEADERS, $cors ) )
                header( "Access-Control-Allow-Headers: " . $cors[ self::CORS_HEADERS ] );
        }


        $this->checkCapabilities( $handler[ "capabilities" ] );

        $reflectionClass = new ReflectionClass( get_class( $handler[ "instance" ] ) );
        $reflectionMethod = $reflectionClass->getMethod( $handler[ "method" ] );
        $parameters = $reflectionMethod->getParameters();

        $args = array();

        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();

            if (self::REQUEST_BODY === $parameterName) {
                array_push( $args, file_get_contents(self::PHP_INPUT) );
                continue;
            }

            if (self::HEADERS === $parameterName)
            {
                array_push($args, getallheaders());
                continue;
            }

            if ( !array_key_exists( $parameterName, $_REQUEST ) ) {
                if ( !$parameter->isOptional() )
                    throw new Exception( "Parameter [$parameterName] is required [ action :: " . $handler[ "action"] . " ]." );
                else
                    array_push( $args, $parameter->getDefaultValue() );

                continue;
            }

            array_push( $args, $_REQUEST[$parameterName] );
        }

        $returnValue = call_user_func_array( array( $handler[ "instance" ], $handler[ "method" ] ), $args);

        if (self::CALLBACK_RETURN_ERROR === $returnValue) {
            header( "X-WordPress-Framework-Ajax-Error: true" );
            // error.
            exit;
        }

        if (self::CALLBACK_RETURN_NULL === $returnValue) {
            // no response / maybe the method returned its own.
            exit;
        }

        $this->jsonService->sendResponse( $returnValue, $handler[ "compression" ] );

        exit;
    }

    // private function headers( $rawHeaders ) {

    //     $headers = array();

    //     foreach ( $rawHeaders as $headerLine ) {
    //         // $this->logger->trace( $headerLine );

    //         $header = explode( ": ", $headerLine );
    //         if ( 1 >= count( $header ) )
    //             continue;

    //         $headers[ $header[0] ] = $header[1];
    //     }

    //     return $headers;

    // }
}

?>