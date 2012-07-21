<?php
/**
 * User: david
 * Date: 20/07/12 20:54
 */

class WordLift_Job {

    public $id;
    public $state;
    public $postID;

    function __construct( $id, $state, $postID = NULL ) {
        $this->id = $id;
        $this->state = $state;
        $this->postID = $postID;
    }

}

?>