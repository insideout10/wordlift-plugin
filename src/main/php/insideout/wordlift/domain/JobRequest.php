<?php
/**
 * User: david
 * Date: 20/07/12 21:23
 */

class WordLift_JobRequest {

    public $text;
    public $onCompleteUrl;
    public $onProgressUrl;
    public $chainName;

    function __construct( $text, $onCompleteURL, $onProgressURL, $chainName ) {
        $this->text 		 = $text;
        $this->onCompleteUrl = $onCompleteURL;
        $this->onProgressUrl = $onProgressURL;
        $this->chainName	 = $chainName;
    }

}

?>