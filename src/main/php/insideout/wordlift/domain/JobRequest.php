<?php
/**
 * User: david
 * Date: 20/07/12 21:23
 */

class WordLift_JobRequest {

    public $text;
    public $onCompleteURL;
    public $onProgressURL;
    public $chainName;

    function __construct( $text, $onCompleteURL, $onProgressURL, $chainName ) {
        $this->text 		 = $text;
        $this->onCompleteURL = $onCompleteURL;
        $this->onProgressURL = $onProgressURL;
        $this->chainName	 = $chainName;
    }

}

?>