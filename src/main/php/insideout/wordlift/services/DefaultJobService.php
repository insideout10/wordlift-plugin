<?php
/**
 * User: david
 * Date: 20/07/12 20:55
 */

class WordLift_DefaultJobService implements WordLift_JobService {

    const IN_PROGRESS = "in progress";

    public $logger;

    public $jobID;
    public $jobState;

    public $metaKeyJobID;
    public $metaKeyJobStatus;

    public function getPostByJobID( $jobID ) {

        return get_posts( array(
            "numberposts" => 1,
            "post_type" => "post",
            "meta_key" => $this->metaKeyJobID,
            "meta_value" => $jobID,
            "post_status" => "any"
        ));

    }

    /**
     * Set the job ID for the post ID.
     * @param $jobID The job ID.
     * @param $postID The post ID.
     * @return mixed The result of the update_post_meta call.
     */
    public function setJobForPost( $postID, $jobID, $jobState ) {

        $this->logger->trace( "Setting a job for a post [ postID :: $postID ][ jobID :: $jobID ][ jobState :: $jobState ][ metaKeyJobID :: $this->metaKeyJobID ][ metaKeyJobStatus :: $this->metaKeyJobStatus ]." );

        $jobID = update_post_meta( $postID, $this->metaKeyJobID, $jobID );
        $jobState = update_post_meta( $postID, $this->metaKeyJobStatus, $jobState );

        return ( $jobID && $jobState );

    }

    public function createJob( $id, $state, $postID = NULL) {
        return new WordLift_Job( $id, $state, $postID );
    }

    public function getJob( $postID ) {

        $jobID = get_post_meta( $postID, $this->jobID, true );
        $jobState = get_post_meta( $postID, $this->jobState, true );

        return new WordLift_Job( $jobID, $jobState, $postID );

    }

    public function getJobByUUID( $uuid ) {

        $posts = get_posts( array(
            "numberposts" => 1,
            "post_status" => array( "any" ),
            "meta_key" => $this->jobID,
            "meta_value" => $uuid
        ));

        if ( 0 === count($posts) )
            return NULL;

        return $this->getJob( $posts[0]->ID );
    }

    public function save( $job ) {
        update_post_meta( $job->postID , $this->jobID, $job->id );
        update_post_meta( $job->postID , $this->jobState, $job->state );
    }

    public function markCompleted( $job ) {
        update_post_meta( $job->postID , $this->jobID, $job->id );
        update_post_meta( $job->postID , $this->jobState, self::COMPLETED );
    }

}

?>