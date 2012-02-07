<?php

require_once( 'wp-load.php' );
require_once( 'stanbolJob.php' );

$post_id = 4;
$job_url = 'http://localhost:8080/enhancerjobs/';

$job = new StanbolJob($post_id);
$job->enhance( $job_url );

header("Location: status.php?p=".urlencode($post_id));

/* Make sure that code below does not get executed when we redirect. */
exit;

?> 
