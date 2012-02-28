<?php
require_once('private/log4php/Logger.php');

// Tell log4php to use our configuration file.
// temporary change the current directory in order to give a well-known context to the log4php configuration.
$current_directory = getcwd();
chdir(__DIR__);
Logger::configure(__DIR__.'/private/config/log4php.xml');
// return to the initial working directory.
chdir($current_directory);

// register a global logger.
$logger = Logger::getLogger('global');
?>
