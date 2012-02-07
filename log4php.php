<?php
require_once('private/log4php/Logger.php');
$logger = Logger::getLogger("main");
$logger->info("foo");
$logger->warn("bar");
?>
