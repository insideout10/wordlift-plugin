<?php
/*
Plugin Name: WordLift - Semantic Tagging and schema.org
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Semantic Tagging and schema.org - a brand new way to publish your contents on the Linked Open Data cloud.
Version: {version}
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

// bootstrap the WordPress framework.
require_once("php/insideout/wordpress/bootstrap.php");

WordPress_XmlApplication::setUp( dirname(__FILE__), "/wordlift.xml", "/log4php.xml" );

?>
