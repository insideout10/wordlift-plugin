<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');

class StanbolJob {
    
    public $status;

    public $status_location;
    public $output_location;
    
    private $post_id;
    
    const status_location_key        = 'wordlift_status_location';
    const status_key                 = 'wordlift_status';
    const output_location_key        = 'wordlift_output_location';

    private $logger;
    
    function __construct( $post_id ) {
        $this->post_id = $post_id;
        
        $this->status_location  = get_post_meta( $this->post_id, self::status_location_key, true);
        $this->status           = get_post_meta( $this->post_id, self::status_key, true);
        $this->output_location  = get_post_meta( $this->post_id, self::output_location_key, true);

        $this->logger = Logger::getLogger(__CLASS__);
    }
    
    function enhance( $url = STANBOL_URL ) {
        
        $this->logger->debug("Going to request content analysis to [$url].");

        // get a reference to the post.
        $post = get_post( $this->post_id );
        
        $response = wp_remote_post( $url, array(
        	'method' => 'POST',
        	'timeout' => 45,
        	'redirection' => 5,
        	'httpversion' => '1.0',
        	'blocking' => true,
        	'headers' => array(),
        	'body' => array( 'content' => strip_tags( $post->post_content) ),
        	'cookies' => array()
            )
         );
         
         var_dump($response);

         $this->location = $response['headers']['location'];
         
         // set the status location in W/P.
         update_post_meta( $this->post_id, self::status_location_key, $this->location );
    }

    function status() {
        $this->logger->debug("Going to request status to [$this->status_location].");

        $response = wp_remote_get( $this->status_location );
	
        var_dump($response);

        preg_match('/"status": "(.*?)"/', $response['body'], $matches);
        $this->status = $matches[1];

        preg_match('/"outputLocation": "(.*?)"/', $response['body'], $matches);
        $this->output_location = $matches[1];

        // set the status in W/P.
        update_post_meta( $this->post_id, self::status_key, $this->status );

        // set the output location in W/P.
        update_post_meta( $this->post_id, self::output_location_key, $this->output_location );

    }
    
    function result() {
        return wp_remote_get( $this->output_location );
    }
}

?>
