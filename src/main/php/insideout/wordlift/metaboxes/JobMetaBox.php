<?php
/**
 * User: david
 * Date: 20/07/12 20:22
 */

class WordLift_JobMetaBox implements WordPress_IMetaBox {

    public $logger;

    /** @var WordLift_JobService $jobService */
    public $jobService;

    public function getHtml( $post ) {

        $job = $this->jobService->getByPostID( $post->ID );

        if ( is_array( $job ) && array_key_exists( "jobState", $job ) )
            echo "Analysis " . $job[ "jobState" ] . " (" . $job[ "jobID" ] . ")";

    }

}