<?php
/**
 * User: david
 * Date: 20/07/12 20:55
 */

class WordLift_DefaultJobService implements WordLift_JobService {

    public $logger;

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
    public function setJob( $postID, $jobID, $jobState ) {

        $this->logger->trace( "Setting a job for a post [ postID :: $postID ][ jobID :: $jobID ][ jobState :: $jobState ][ metaKeyJobID :: $this->metaKeyJobID ][ metaKeyJobStatus :: $this->metaKeyJobStatus ]." );

        $jobID = update_post_meta( $postID, $this->metaKeyJobID, $jobID );
        $jobState = update_post_meta( $postID, $this->metaKeyJobStatus, $jobState );

        return ( $jobID && $jobState );

    }

    public function getByJobID( $jobID ) {

        $posts = $this->getPostByJobID( $jobID );

        if ( 0 === count( $posts ) ) {
            $this->logger->warn( "No post found for job [ jobID :: $jobID ]." );
            return array();
        }

        return $this->getByPostID( $posts[0]->ID );

    }

    public function getByPostID( $postID ) {

        $jobID = get_post_meta( $postID, $this->metaKeyJobID, true );

        if ( "" === $jobID ) {
            $this->logger->warn( "No job found for post [ postID :: $postID ]." );
            return array(
                "postID" => $postID
            );
        }

        $jobState = get_post_meta( $postID, $this->metaKeyJobStatus, true );

        return array(
            "jobID" => $jobID,
            "jobState" => $jobState,
            "postID" => $postID
        );

    }

}

?>