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

    public function options() {
        // to enable CORS.
    }

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

        $content = strip_tags( do_shortcode( $post->post_content ) );

        $jobRequest = $this->jobRequestService->createJobRequest( $content );
        $transactionId = $this->jobRequestService->sendJobRequest( $jobRequest );

        $result = $this->jobService->setJob( $postID, $transactionId, WordLift_JobService::RUNNING );

        if ( false === $result )
            $this->logger->error( "An error occurred saving the job ID to the post [ transactionId :: $transactionId ][ postID :: $postID ]." );
        else
            $this->logger->info( "A new job has been created and submitted [ postID :: $postID ][ transactionId :: $transactionId ]." );

    }

    public function getJob( $jobID = NULL, $postID = NULL )
    {

        // $this->logger->trace( "[ jobID :: $jobID ][ postID :: $postID ]." );

        if (NULL === $jobID && NULL === $postID)
        {
            return WordPress_AjaxProxy::CALLBACK_RETURN_ERROR;
        }

        if (!empty($jobID))
        {
            return $this->jobService->getByJobID($jobID);
        }

        return $this->jobService->getByPostID($postID);
    }

    public function updateJob( $jobID, $jobState ) {

        $this->logger->trace( "[ jobID :: $jobID ][ jobState :: $jobState ]." );

        $posts = $this->jobService->getPostByJobID( $jobID );

        if ( 0 === count( $posts ) )
            return WordPress_AjaxProxy::CALLBACK_RETURN_ERROR;

        $this->jobService->setJob( $posts[0]->ID, $jobID, $jobState );

        return $this->getJob( NULL, $posts[0]->ID );
    }

}

?>