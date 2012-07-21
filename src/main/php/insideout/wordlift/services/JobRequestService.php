<?php

class WordLift_JobRequestService {

    public $logger;

    public $url;
    public $completeAction;
    public $progressAction;

    public $onCompleteURL;
    public $onProgressURL;
    public $chainName;

    public $jobService;

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