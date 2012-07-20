<?php
/**
 * User: david
 * Date: 20/07/12 20:22
 */

class WordLift_JobMetaBox implements WordPress_IMetaBox {

    public $logger;

    public $jobService;

    public function getHtml( $post ) {

        $job = $this->jobService->getJob( $post->ID );

        echo "A job for this post is <strong>$job->state</strong> [$job->id].";
    }

}