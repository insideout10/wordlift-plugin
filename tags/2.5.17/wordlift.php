<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 2.5.17
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

// bootstrap the WordPress framework.
require_once("php/insideout/wordpress/bootstrap.php");

WordPress_XmlApplication::setUp(
    dirname(__FILE__),
    "/wordlift.xml",
    "/log4php.xml"
);

function wordlift_activate()
{
    delete_option("wordlift_consumer_key");
    delete_option("wordlift_site_key");
}

function wordlift_deactivate()
{
    delete_option("wordlift_consumer_key");
    delete_option("wordlift_site_key");
}

register_activation_hook(__FILE__, "wordlift_activate");
register_deactivation_hook(__FILE__, "wordlift_deactivate");
?>
