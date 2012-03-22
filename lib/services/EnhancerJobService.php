<?php

class EnhancerJobService {

    private $logger;
    
    function __construct() {
        $this->logger = Logger::getLogger(__CLASS__);
    }
    
    function requestJob( $job_request, $url = WORDLIFT_20_URLS_ENHANCE_TEXT ) {
        
        $this->logger->debug("Going to request content analysis to [$url].");

        // get a reference to the post.
        $post = get_post( $this->post_id );
        
        $return = wp_remote_post( $url, array(
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
         
		if (is_wp_error($return)) {
			
			$this->logger->error('An error occurred: '.$return->get_error_message());
			
			return NULL;
		}
		 
        return json_decode( $return['body'] );
    }

}

$enhancer_job_service   = new EnhancerJobService();

?>