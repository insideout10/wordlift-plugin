<?php
/**
 * User: david
 * Date: 20/07/12 20:55
 */

class WordLift_JobService {

    public $logger;

    public $jobID;
    public $jobState;

    public function getJob( $postID ) {

        $jobID = get_post_meta( $postID, $this->jobID, true );
        $jobState = get_post_meta( $postID, $this->jobState, true );

        return new Job( $jobID, $jobState );

    }

}

?>