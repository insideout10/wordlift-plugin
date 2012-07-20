<?php
/**
 * User: david
 * Date: 20/07/12 18:57
 */

class WordLift_EntitiesMetaBox implements WordPress_IMetaBox {

    public $logger;

    public function getHtml() {
        $this->logger->trace( "Printing out Html.");
    }

}

?>