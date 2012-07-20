<?php

class WordLift_JobRequestService {

    public $logger;

    public $url;
    public $onCompleteURL;
    public $onProgressURL;
    public $chainName;

    function __construct() {
        $this->onCompleteURL = admin_url("admin-ajax.php?action=wordlift.job-complete");
        $this->onProgressURL = admin_url("admin-ajax.php?action=wordlift.job-progress");
        $this->chainName = "default";
    }

    public function create( $text, $onCompleteUrl = NULL, $onProgressUrl = NULL, $chainName = NULL ) {
        if ( NULL === $onCompleteUrl )
            $onCompleteUrl = $this->onCompleteURL;

        if ( NULL === $onProgressUrl )
            $onProgressUrl = $this->onProgressURL;

        if ( NULL === $chainName )
            $chainName = $this->chainName;

        return new WordLift_JobRequest( $text, $onCompleteUrl, $onProgressUrl, $chainName );
    }

    public function postText( $text ) {
        $this->post( $this->create( $text ) );
    }
    public function post ( $jobRequest ) {
        $this->logger->trace( "Sending a job-request to $this->url." );

        $return = wp_remote_post( $this->url, array(
            "method" => "POST",
            "timeout" => 45,
            "redirection" => 5,
            "httpversion" => "1.0",
            "blocking" => true,
            "header" => array("Content-Type" => "application/json"),
            "body" => json_encode($jobRequest),
            "cookies" => array()
        ));

        if ( true === is_wp_error($return) ) {
            $this->logger->error('An error occurred: '.$return->get_error_message());
            return NULL;
        }

        return json_decode( $return['body'] );
    }

}

?>