<?php
/**
 * User: david
 * Date: 15/07/12 11:45
 */

// add logging support.
if ( !class_exists( "Logger" ) )
    require_once( dirname(dirname(dirname(__FILE__))) . "/log4php/Logger.php");

// load the WordPressFramework.
if ( !class_exists( "WordPress_XmlApplication" ) )
    require_once( dirname(__FILE__) . "/XmlApplication.php");

?>