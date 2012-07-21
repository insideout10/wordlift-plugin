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
        $isRevision = !wp_is_post_revision( $postID );

        $job = $this->jobService->getJob( $postID );

        $this->logger->trace( "A post [$postID][revision :: $isRevision] has been saved; a job is $job->state [$job->id]." );

        $post = get_post( $postID );
        $content = &$post->post_content;

        $this->jobRequestService->postText( $content );
    }

}

?>