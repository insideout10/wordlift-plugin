<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.insideout.io
Description: WordLift is a WordPress plugin that will add semantic intelligence to your site.
Version: <version>2.0b</version>
Author: InsideOut10
Author URI: http://wordlift.insideout.io
License: APL
*/

// bootstrap the WordPress framework.
require_once("php/insideout/wordpress/bootstrap.php");

WordPress_XmlApplication::setUp( dirname(__FILE__), "/wordlift.xml", "/log4php.xml" );

?>