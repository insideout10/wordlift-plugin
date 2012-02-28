<?php

class EnhancerJobService {

    private $logger;
    
    function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }
    
    function requestJob( $job_request, $url = ENHANCE_TEXT_URL ) {
        
        $this->logger->debug("Going to request content analysis to [$url].");

        // get a reference to the post.
        $post = get_post( $this->post_id );
        
        $response = wp_remote_post( $url, array(
        	'method' => 'POST',
        	'timeout' => 45,
        	'redirection' => 5,
        	'httpversion' => '1.0',
        	'blocking' => true,
        	'headers' => array('Content-Type' => 'application/json'),
        	'body' => json_encode($job_request),
        	'cookies' => array()
            )
         );
         
         $this->logger->debug("Received a response: ".var_export($response, true));

         return json_decode( $response['body'] );
    }

}

?>