<?php
/**
 * User: David Riccitelli
 * Date: 27/08/12 16:44
 */

/**
 * Manage Jobs using AJAX services.
 */
class WordLift_JobAjaxService {

    public $logger;

    /** @var WordLift_JobRequestService $jobRequestService */
    public $jobRequestService;

    /** @var WordLift_JobService $jobService */
    public $jobService;

    /**
     * Create a new Job for the post with postID.
     * @param $postID The ID of the post.
     * @return mixed
     */
    public function createJob( $postID ) {

        $this->logger->trace( "[ postID :: $postID ]." );

        $post = get_post( $postID );

        // check if the post exists.
        if ( NULL === $post ) {
            $this->logger->error( "A post was not found [ postID :: $postID ]." );
            return WordPress_AjaxProxy::CALLBACK_RETURN_ERROR;
        }

        $jobRequest = $this->jobRequestService->createJobRequest( $post->post_content );
        $jobResponse = $this->jobRequestService->sendJobRequest( $jobRequest );

        $result = $this->jobService->setJobForPost( $postID, $jobResponse->jobID, WordLift_JobService::RUNNING );

        if ( false === $result )
            $this->logger->error( "An error occurred saving the job ID to the post [ jobID :: $jobResponse->jobID ][ postID :: $postID ]." );
        else
            $this->logger->info( "A new job has been created and submitted [ postID :: $postID ][ jobID :: $jobResponse->jobID ]." );

    }

    public function getJob( $jobID ) {

        $this->logger->trace( "[ jobID :: $jobID ]." );

    }

    public function updateJob( $jobID, $jobState ) {

        $this->logger->trace( "[ jobID :: $jobID ][ jobState :: $jobState ]." );

    }

}

?>