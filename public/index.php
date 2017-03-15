<?php

chdir(dirname(dirname(__FILE__)));

// Set localtime zone
date_default_timezone_set("America/Bogota");

// Memory limit
ini_set("memory_limit","256M");

// Run application
require_once("Framework/autoload.php");
require_once("Framework/vendor_autoload.php");

$mvc = new Drone_Mvc_Application(include "config/application.config.php");
$mvc->run();