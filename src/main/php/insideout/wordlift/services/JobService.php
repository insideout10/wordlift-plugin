<?php

interface WordLift_JobService {

    const IDLE = "idle";
    const COMPLETED = "completed";
    const RUNNING = "running";

    public function getPostByJobID( $jobID );

    public function setJobForPost( $postID, $jobID, $jobState );

}

?>