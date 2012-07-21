<?php

class WordLift_JobAjax {

    public $logger;

    public function progress( $requestBody ) {
        $this->logger->trace( "A message has been received (" . strlen($requestBody) . " bytes)." );

    }

    public function complete( $requestBody ) {
        $this->logger->trace( "A message has been received (" . strlen($requestBody) . " bytes)." );
    }

}

?>