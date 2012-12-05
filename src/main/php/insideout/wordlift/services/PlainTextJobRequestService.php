<?php

class WordLift_PlainTextJobRequestService implements WordLift_JobRequestService {

    public $logger;

    public $jobService;

    public $consumerKeyOptionName;

    public $callbackURL;
    public $applicationId;
    public $requestMimeType;

    public $requestProtocol;
    public $requestHttpMethod;
    public $requestContentTypeHeader;
    public $requestAcceptHeader;

    public $requestURL;

    /**
     * Create a job request with the provided text. The text will be stripped of its html tags.
     * @param $text The text to submit for analysis.
     * @return array A job request array.
     */
    public function createJobRequest( $text ) {

        $this->logger->trace( "Creating a job request [ consumerKey :: $this->consumerKey ][ callbackURL :: $this->callbackURL ][ mimeType :: $this->requestMimeType ]." );

        return array(
            "mimeType" => $this->requestMimeType,
            "text" => strip_tags( $text )
        );

    }

    /**
     * Send a job request to the WordLift Server APIs.
     * @param $request The request (created using the createJobRequest method).
     * @throws Exception
     */
    public function sendJobRequest( $request ) {

        $this->logger->trace( "[ request :: " . var_export( $request, true ) . " ]" );

        $requestURL = $this->requestURL . "job";
        $consumerKey = get_option( $this->consumerKeyOptionName );

        $params = array(
            $this->requestProtocol => array(
                "method" => $this->requestHttpMethod,
                "header"  => array(
                    "Content-type: $this->requestContentTypeHeader",
                    "Accept: $this->requestAcceptHeader",
                    "Application-Id: $this->applicationId",
                    "Consumer-Key: $consumerKey",
                    "Callback-Url: $this->callbackURL",

                ),
                "content" => json_encode( $request )
        ));

        // create the context and open the connection.
        $context = stream_context_create($params);
        $fileHandle = @fopen( $requestURL, "rb", false, $context);
        if ( !$fileHandle ) {
            $this->logger->error( "An error occurred while opening the connection [ requestURL :: $this->requestURL ][ params :: " . var_export( $params, true ) . " ]." );
            return false;
        }

        // get the response.
        $response = @stream_get_contents( $fileHandle );
        if ($response === false) {
            $this->logger->error( "An error occurred while reading the response from the server [ requestURL :: $this->requestURL ]." );
            return false;
        }

        $transactionId = null;

        foreach ( $http_response_header as $headerLine ) {
            $header = explode( ": ", $headerLine );

            if ( "Proxy-Transaction-Id" === $header[0] ) {
                $transactionId = $header[1];
                break;
            }
        }

        // decode the response to a job response.
        return $transactionId;

    }

}

?>