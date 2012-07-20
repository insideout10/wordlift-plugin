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

    function __construct( $text, $onCompleteUrl, $onProgressUrl, $chainName ) {
        $this->text 		 = $text;
        $this->onCompleteUrl = $onCompleteUrl;
        $this->onProgressUrl = $onProgressUrl;
        $this->chainName	 = $chainName;
    }

}

?>