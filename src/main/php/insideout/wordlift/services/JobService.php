<?php

interface WordLift_JobService {

    const IDLE = "idle";
    const COMPLETED = "completed";
    const RUNNING = "running";

    public function getPostByJobID( $jobID );

    public function getByJobID( $jobID );

    public function getByPostID( $postID );

    public function setJob( $postID, $jobID, $jobState );

}

?>