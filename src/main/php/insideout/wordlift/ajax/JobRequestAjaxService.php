<?php
/**
 * User: david
 * Date: 25/08/12 21:28
 */

class WordLift_JobRequestAjaxService {

    public $logger;

    public $metaKeyJobID;

    public function sendJobRequest( $postID ) {

        $post = get_post( $postID );

        $url = "http://localhost:8080/wordlift/api/job";

        $data = json_encode( array(
            "consumerKey" => "123",
            "callbackURL" => "http://localizeme.dyndns.org/wordlift/wp-admin/admin-ajax.php?action=wordlift.jobCallback",
            "mimeType" => "application/rdf+xml",
            "text" => strip_tags( $post->post_content )
        ));

        $params = array('http' => array(
            'method' => 'POST',
            'header'  => array(
                    'Content-type: application/json',
                    'Accept: application/json'
                ),
            'content' => $data
        ));

        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }

        $jobResponse = json_decode( $response );

        update_post_meta($postID, $this->metaKeyJobID, $jobResponse->jobID );

        $this->logger->trace( "[ jobID :: $jobResponse->jobID ]" );

    }

}

?>