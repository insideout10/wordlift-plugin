<?php
/**
 * User: David Riccitelli
 * Date: 27/08/12 17:19
 */

interface WordLift_JobRequestService {

    public function createJobRequest( $text );

    public function sendJobRequest( $request );

}

?>