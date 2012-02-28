<?php
require_once('private/config/wordlift.php');
require_once('log4php.php');


$logger = Logger::getLogger("receive");

$logger->debug("received job results: ".var_export(file_get_contents("php://input"),true));

?>