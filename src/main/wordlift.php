<?php
/*
Plugin Name: WordLift 2.0
Plugin URI: http://wordlift.insideout.io
Description: WordLift 2.0
Version: 0.2.0
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

// bootstrap the WordPress framework.
require_once("php/insideout/wordpress/bootstrap.php");

WordPress_XmlApplication::setUp( dirname(__FILE__), "/wordlift.xml", "/log4php.xml" );

?>
