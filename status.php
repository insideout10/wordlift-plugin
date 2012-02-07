<?php

require_once( 'wp-load.php' );
require_once( 'stanbolJob.php' );

$job = new StanbolJob($_GET['p']);
$job->status();

echo("<pre>job\nstatus: ".$job->status."\noutputLocation: ".$job->output_location."</pre>");

if ("finished" == $job->status) {
    print_r($job->result());
}

?> 
