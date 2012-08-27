<?php

class WordLift_PlainTextJobRequestService implements WordLift_JobRequestService {

    public $logger;

    public $url;
    public $completeAction;
    public $progressAction;

    public $onCompleteURL;
    public $onProgressURL;
    public $chainName;

    public $jobService;

    public $consumerKey;
    public $callbackURL;
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
            "consumerKey" => $this->consumerKey,
            "callbackURL" => $this->callbackURL,
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

        $params = array(
            $this->requestProtocol => array(
                "method" => $this->requestHttpMethod,
                "header"  => array(
                    "Content-type: $this->requestContentTypeHeader",
                    "Accept: $this->requestAcceptHeader"
                ),
                "content" => json_encode( $request )
        ));

        // create the context and open the connection.
        $context = stream_context_create($params);
        $fileHandle = @fopen( $this->requestURL, "rb", false, $context);
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

        // decode the response to a job response.
        return json_decode( $response );

//        update_post_meta($postID, $this->metaKeyJobID, $jobResponse->jobID );
//
//        $this->logger->trace( "[ jobID :: $jobResponse->jobID ]" );

    }

    public function create( $text, $onCompleteURL = NULL, $onProgressURL = NULL, $chainName = NULL ) {

        if ( NULL === $onCompleteURL )
            $onCompleteURL = admin_url("admin-ajax.php?action=$this->completeAction" );

        if ( NULL === $onProgressURL )
            $onProgressURL = admin_url("admin-ajax.php?action=$this->progressAction" );

        if ( NULL === $chainName )
            $chainName = $this->chainName;

        return new WordLift_JobRequest( $text, $onCompleteURL, $onProgressURL, $chainName );
    }

    public function postText( $text ) {
        return $this->post( $this->create( $text ) );
    }

    public function post ( $jobRequest ) {

        $this->logger->trace( "Sending a job-request to $this->url [complete :: $jobRequest->onCompleteUrl][progress :: $jobRequest->onProgressUrl]." );

        $parameters = array(
            "method" => "POST",
            "timeout" => 45,
            "redirection" => 5,
            "httpversion" => "1.0",
            "blocking" => true,
            "headers" => array(
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ),
            "body" => json_encode( $jobRequest ),
            "cookies" => array()
        );

        $return = wp_remote_post( $this->url, $parameters );

        if ( is_wp_error($return) ) {
            $this->logger->error('An error occurred: '.$return->get_error_message());
            return NULL;
        }

        $jsonBody = json_decode( $return["body"] );

        return $this->jobService->createJob( $jsonBody->id, WordLift_JobService::IN_PROGRESS );
    }

}

?>