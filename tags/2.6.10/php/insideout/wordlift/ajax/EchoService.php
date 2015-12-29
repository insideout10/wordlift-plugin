<?php
/**
 * User: david
 * Date: 21/07/12 10:05
 */

class WordLift_EchoService {

    public $logger;

    public function getPong() {

        $this->logger->trace( "Pong!" );

        return "pong!";

    }

}