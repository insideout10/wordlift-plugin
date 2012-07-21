<?php
/**
 * User: david
 * Date: 20/07/12 20:47
 */

class WordLift_PostAnalyzer {

    public $logger;

    public $jobService;
    public $jobRequestService;

    public function analyze( $postID ) {
        $isRevision = wp_is_post_revision( $postID );

        $job = $this->jobService->getJob( $postID );

        if ( $isRevision && WordLift_JobService::IN_PROGRESS === $job->state) {
            $this->logger->trace( "Will not trigger analysis [revision :: $isRevision][job-state :: $job->state][job-id :: $job->id][post-id :: $postID]." );
            return;
        }

        $this->logger->trace( "A post [$postID][revision :: $isRevision] has been saved; a job is $job->state [$job->id]." );

        $post = get_post( $postID );
        $content = strip_tags( $post->post_content );

        $job = $this->jobRequestService->postText( $content );
        $job->postID = $postID;

        $this->jobService->save( $job );

        $this->logger->trace( "A job has been created [id :: $job->id][state :: $job->state]" );
    }

}

?>