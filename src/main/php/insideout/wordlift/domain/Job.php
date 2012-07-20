<?php
/**
 * User: david
 * Date: 20/07/12 20:54
 */

class Job {

    public $id;
    public $state;

    function __construct( $id, $state ) {
        $this->id = $id;
        $this->state = $state;
    }

}

?>